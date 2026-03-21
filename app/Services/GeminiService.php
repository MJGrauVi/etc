<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    // Definimos el modelo y la base de la URL como propiedades limpias
    protected $model = "gemini-1.5-flash";
    protected $baseUrl = "https://generativelanguage.googleapis.com/v1beta";

    public function generateCaption($imagePath, $prompt)
    {
        // 1. IMPORTANTE: Asegúrate que config('services.gemini.key')
        // apunte correctamente a env('GEMINI_API_KEY') en tu archivo config/services.php
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return "ERROR: La API Key está vacía. Revisa tu archivo .env y config/services.php";
        }

        try {
            $rawData = file_get_contents($imagePath);
            $imageData = base64_encode($rawData);
            $mimeType = mime_content_type($imagePath);

            // 2. Construcción de la URL dinámica
            // Usamos la variable $apiKey, NO el texto "API_KEY"
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$apiKey}";

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageData
                                    ]
                                ],
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                return "DETALLE_GOOGLE: " . $response->body();
            }

            $res = $response->json();

            if(!isset($res['candidates'])) {
                return "ERROR: Respuesta inesperada: " . json_encode($res, JSON_UNESCAPED_UNICODE);
            }

            $text = '';
            foreach ($res['candidates'] as $candidate) {
                if (!isset($candidate['content']['parts'])) continue;
                foreach ($candidate['content']['parts'] as $part) {
                    if (isset($part['text'])) {
                        $text .= $part['text'] . "\n";
                    }
                }
            }

            return trim($text) ?: "ERROR: Gemini devolvió una respuesta sin texto.";

        } catch (\Exception $e) {
            return "EXCEPCION_CONEXION: " . $e->getMessage();
        }
    }
}
