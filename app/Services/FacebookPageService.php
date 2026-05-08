<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FacebookPageService
{
    public function publishPhoto(string $imagePath, string $fileName, string $message): array
    {
        $pageId = config('services.facebook.page_id');
        $accessToken = config('services.facebook.page_access_token');
        $version = config('services.facebook.graph_version', 'v25.0');

        if (empty($pageId) || empty($accessToken)) {
            throw new RuntimeException('Faltan FACEBOOK_PAGE_ID o FACEBOOK_PAGE_ACCESS_TOKEN en el archivo .env.');
        }

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
