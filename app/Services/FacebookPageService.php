<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FacebookPageService
{
    public function ensureConfigured(): void
    {
        $this->ensureCredentials(
            config('services.facebook.page_id'),
            config('services.facebook.page_access_token')
        );
    }

    public function ensureCredentials(?string $pageId, ?string $accessToken): void
    {
        if (empty($pageId) || empty($accessToken)) {
            throw new RuntimeException('Faltan credenciales de Facebook para publicar.');
        }
    }

    public function demoCredentials(): array
    {
        return [
            'source' => 'demo',
            'page_id' => config('services.facebook.page_id'),
            'page_name' => config('services.facebook.demo_page_name', 'Pagina demo'),
            'access_token' => config('services.facebook.page_access_token'),
            'requires_confirmation' => true,
        ];
    }

    public function publishPhoto(
        string $imagePath,
        string $fileName,
        string $message,
        ?string $pageId = null,
        ?string $accessToken = null
    ): array {
        $pageId = $pageId ?: config('services.facebook.page_id');
        $accessToken = $accessToken ?: config('services.facebook.page_access_token');
        $version = config('services.facebook.graph_version', 'v25.0');

        $this->ensureCredentials($pageId, $accessToken);

        $version = ltrim($version, '/');
        $url = "https://graph.facebook.com/{$version}/{$pageId}/photos";

        $response = Http::attach(
            'source',
            file_get_contents($imagePath),
            $fileName
        )->post($url, [
            'caption' => $message,
            'published' => true,
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("Facebook no pudo publicar la imagen: {$error}");
        }

        return $response->json();
    }
}
