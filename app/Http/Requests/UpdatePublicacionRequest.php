<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublicacionRequest extends FormRequest
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
            'titulo' => 'sometimes|nullable|string|max:255',
            'contenido' => 'sometimes|nullable|string|max:10000',
            'estado' => 'sometimes|string|in:borrador,pendiente,publicado,error',
            'hashtags' => 'sometimes|nullable|string|max:1000',
            'reds' => 'sometimes|array',
            'reds.*' => 'exists:reds,id',
        ];
    }
}
