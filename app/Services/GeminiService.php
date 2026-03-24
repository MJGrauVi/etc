<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    // Usamos el modelo exacto que funciona para leer la imagen.
    protected $model = "gemini-3-flash-preview";

    // El endpoint base para la v1beta.
    protected $baseUrl = "https://generativelanguage.googleapis.com/v1beta";

    public function generateCaption($imagePath, $prompt)
    {
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return "ERROR: La API Key está vacía. Revisa tu archivo .env y config/services.php";
        }

        try {
            // Preparamos la imagen.
            if (!file_exists($imagePath)) {
                return "ERROR: La imagen no existe en la ruta: {$imagePath}";
            }

            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            // 2. Construcción de la URL limpia (sin la clave al final)
            // Esto es: /models/{model}:generateContent
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

            // 3. Petición HTTP usando Laravel y los Headers del CURL
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    // ESTA ES LA CLAVE: El Header del curl que funciona.
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                // Estructura idéntica al curl, añadiendo la imagen
                                [
                                    'text' => $prompt // La instrucción.
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageData
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                // Si Google nos rechaza, queremos ver EXACTAMENTE por qué
                return "DETALLE_GOOGLE (Error {$response->status()}): " . $response->body();
            }

            $res = $response->json();

            // Verificamos si la respuesta tiene la estructura esperada en 2026.
            if(!isset($res['candidates'][0]['content']['parts'][0]['text'])) {
                // A veces, si no hay candidatos, el modelo devuelve un mensaje de seguridad
                if (isset($res['promptFeedback']['blockReason'])) {
                    return "ERROR: La imagen fue bloqueada por: " . $res['promptFeedback']['blockReason'];
                }
                return "ERROR: Respuesta inesperada de Gemini: " . json_encode($res, JSON_UNESCAPED_UNICODE);
            }

            // Extraemos el texto de la descripción.
            $text = $res['candidates'][0]['content']['parts'][0]['text'];

            if (trim($text) === '') {
                return "ERROR: Gemini devolvió una respuesta sin texto: " . json_encode($res, JSON_UNESCAPED_UNICODE);
            }

            return trim($text);

        } catch (\Exception $e) {
            return "EXCEPCION_CONEXION: " . $e->getMessage();
        }
    }
}
