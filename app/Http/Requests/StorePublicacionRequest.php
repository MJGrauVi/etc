<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pieza_id' => 'required|exists:piezas,id',
            'titulo' => 'nullable|string|max:255',
            'contenido' => 'nullable|string|max:10000',
            'hashtags' => 'nullable|string|max:1000',
            'redes' => 'nullable|array',
            'redes.*' => 'exists:reds,id',
            //Si el usuario sube la foro en ese instante.
            'imagen' => 'nullable|array',
            'estado' => 'nullable|string|in:borrador,pendiente,publicado,error',
        ];

    }
    public function messages(): array{
        return [
            'pieza_id.exists' => 'La Pieza id no es válida.',
            'titulo.required' => 'El titulo es requerido.',
            'imagen.image' => 'El archivo debe ser una imagen real.',
            'imagen.max' => 'La imagen es muy pesada para el procesamiento local(max 5MB).',
        ];
    }
}
