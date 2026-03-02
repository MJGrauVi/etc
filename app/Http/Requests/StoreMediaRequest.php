<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
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
            'tipo' => 'required|in:image,video',
            'path' => 'required|file|mimes:jpg,png,jpeg,mp4',
            'order' => 'nullable|integer',
            'es_portada' => 'nullable|boolean', ];
    }
}
