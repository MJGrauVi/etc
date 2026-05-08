<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublishFacebookRequest;
use App\Models\Publicacion;
use App\Models\Red;
use App\Services\FacebookPageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FacebookPublicacionController extends Controller
{
    public function publish(
        PublishFacebookRequest $request,
        Publicacion $publicacion,
        FacebookPageService $facebook
    ) {
        $publicacion->loadMissing('pieza.medias', 'reds');
        $this->authorize('update', $publicacion);

        $message = $request->input('mensaje') ?: $this->buildMessage($publicacion);
        $image = $request->file('imagen');
        $fileName = $this->buildFileName($publicacion, $image->extension());
        $imagePath = $image->storeAs('publicaciones/facebook', $fileName, 'public');
        $absoluteImagePath = Storage::disk('public')->path($imagePath);
        $facebookRed = Red::where('nombre', 'Facebook')->first();

        try {
            $facebookResponse = $facebook->publishPhoto($absoluteImagePath, $fileName, $message);
        } catch (\Throwable $exception) {
            Log::error('Error publicando en Facebook', [
                'publicacion_id' => $publicacion->id,
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
            'data' => $publicacion,
            'facebook' => $facebookResponse,
        ]);
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

    private function buildFileName(Publicacion $publicacion, string $extension): string
    {
        $slug = Str::slug($publicacion->titulo ?: 'publicacion');
        $date = now()->format('YmdHis');

        return "publicacion-{$publicacion->id}-{$slug}-{$date}.{$extension}";
    }
}
