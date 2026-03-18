<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
class OllamaService
{
    protected $url = 'http://ollama:11344/api/generate';

    public function generateCaption($imagePath, $prompt = "Describe esta pieza artesanal para una publicación en Instagram")
    {
        //Usuario sube imagen y Laravel la recibe en temp, file_get_content.La combierte
        //a cadena base64 para enviarla en json.
        $imageData = base64_encode(file_get_contents($imagePath));
        //Subir tiempo de espera si react da error 504 o Network Error.
        $response = Http::timeout(60)->post($this->url, [
            'model' => 'llava', //Modelo de vision.
            'prompt' => $prompt,
            'images' => [$imageData],
            'stream' => false,//Espera a terminar la frase para empaquetarla.
        ]);
        return $response->json()['response'] ?? 'No se pudo generar el texto.';
    }
}
