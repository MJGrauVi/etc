<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublishFacebookRequest;
use App\Models\Publicacion;
use App\Models\Red;
use App\Services\FacebookPageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacebookPublicacionController extends Controller
{
    public function destination(Publicacion $publicacion, FacebookPageService $facebook)
    {
        $publicacion->loadMissing('pieza.user.defaultFacebookPage', 'user.defaultFacebookPage');
        $this->authorize('update', $publicacion);

        $destination = $this->resolveDestination($publicacion, $facebook);

        try {
            $facebook->ensureCredentials($destination['page_id'], $destination['access_token']);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Facebook no esta configurado.',
                'error' => $exception->getMessage(),
                'data' => [
                    'source' => $destination['source'],
                    'page_name' => $destination['page_name'],
                    'requires_confirmation' => $destination['requires_confirmation'],
                    'configured' => false,
                ],
            ], 422);
        }

        unset($destination['access_token']);
        $destination['configured'] = true;

        return response()->json([
            'data' => $destination,
        ]);
    }

    public function publish(
        PublishFacebookRequest $request,
        Publicacion $publicacion,
        FacebookPageService $facebook
    ) {
        $publicacion->loadMissing('pieza.medias', 'pieza.user.defaultFacebookPage', 'user.defaultFacebookPage', 'reds');
        $this->authorize('update', $publicacion);

        if ($publicacion->estado !== 'pendiente') {
            return response()->json([
                'message' => 'Revisa la publicacion y cambia su estado a Lista para publicar antes de publicarla en Facebook.',
            ], 422);
        }

        $message = $request->input('mensaje') ?: $this->buildMessage($publicacion);
        $destination = $this->resolveDestination($publicacion, $facebook);

        try {
            $facebook->ensureCredentials($destination['page_id'], $destination['access_token']);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Facebook no esta configurado.',
                'error' => $exception->getMessage(),
            ], 422);
        }

        if ($destination['source'] === 'demo' && !$request->boolean('confirm_demo')) {
            return response()->json([
                'message' => 'No tienes una pagina de Facebook configurada. Confirma que quieres publicar usando la pagina demo.',
                'facebook_source' => 'demo',
                'requires_demo_confirmation' => true,
                'destination' => [
                    'source' => 'demo',
                    'page_id' => $destination['page_id'],
                    'page_name' => $destination['page_name'],
                    'requires_confirmation' => true,
                ],
            ], 409);
        }

        $media = $publicacion->pieza->medias->firstWhere('es_portada', true)
            ?? $publicacion->pieza->medias->first();

        if (!$media) {
            return response()->json([
                'message' => 'La pieza no tiene imagen para publicar en Facebook.',
            ], 422);
        }

        $imagePath = $media->path;
        $absoluteImagePath = Storage::disk('public')->path($imagePath);
        $fileName = basename($imagePath);
        $facebookRed = Red::where('nombre', 'Facebook')->first();

        if (!Storage::disk('public')->exists($imagePath)) {
            return response()->json([
                'message' => 'La imagen de la pieza no existe en storage.',
            ], 422);
        }

        try {
            $facebookResponse = $facebook->publishPhoto(
                $absoluteImagePath,
                $fileName,
                $message,
                $destination['page_id'],
                $destination['access_token']
            );
        } catch (\Throwable $exception) {
            Log::error('Error publicando en Facebook', [
                'publicacion_id' => $publicacion->id,
                'facebook_source' => $destination['source'],
                'facebook_page_id' => $destination['page_id'],
                'message' => $exception->getMessage(),
            ]);

            if ($facebookRed) {
                $publicacion->reds()->syncWithoutDetaching([
                    $facebookRed->id => [
                        'fecha_vencimiento' => now()->addMonths(3)->toDateString(),
                        'estado_publicacion' => 'error',
                        'imagen_publicada_path' => $imagePath,
                        'error' => $exception->getMessage(),
                    ],
                ]);
            }

            return response()->json([
                'message' => 'No se pudo publicar en Facebook.',
                'error' => $exception->getMessage(),
            ], 422);
        }

        if ($facebookRed) {
            $publicacion->reds()->syncWithoutDetaching([
                $facebookRed->id => [
                    'fecha_vencimiento' => now()->addMonths(3)->toDateString(),
                    'estado_publicacion' => 'publicado',
                    'imagen_publicada_path' => $imagePath,
                    'external_photo_id' => $facebookResponse['id'] ?? null,
                    'external_post_id' => $facebookResponse['post_id'] ?? null,
                    'published_at' => now(),
                    'error' => null,
                ],
            ]);
        }

        $publicacion->update(['estado' => 'publicado']);
        $publicacion->load('pieza.medias', 'reds');

        return response()->json([
            'message' => 'Publicacion publicada en Facebook correctamente.',
            'warning' => $destination['source'] === 'demo'
                ? 'Esta publicacion se ha publicado usando la pagina demo. Configura tu pagina de Facebook para publicar en tu propia pagina.'
                : null,
            'facebook_source' => $destination['source'],
            'facebook_page' => [
                'id' => $destination['page_id'],
                'name' => $destination['page_name'],
            ],
            'data' => $publicacion,
            'facebook' => $facebookResponse,
        ]);
    }

    private function resolveDestination(Publicacion $publicacion, FacebookPageService $facebook): array
    {
        $owner = $publicacion->user ?? $publicacion->pieza?->user;
        $facebookPage = $owner?->defaultFacebookPage ?? $owner?->facebookPages()->first();

        if ($facebookPage) {
            return [
                'source' => 'user_page',
                'page_id' => $facebookPage->facebook_page_id,
                'page_name' => $facebookPage->name,
                'access_token' => $facebookPage->access_token,
                'requires_confirmation' => false,
            ];
        }

        return $facebook->demoCredentials();
    }

    private function buildMessage(Publicacion $publicacion): string
    {
        return collect([
            $publicacion->titulo,
            $publicacion->contenido,
            $publicacion->hashtags,
        ])
            ->filter()
            ->implode("\n\n");
    }
}
