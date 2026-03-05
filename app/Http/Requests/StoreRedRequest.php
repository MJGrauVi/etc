<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la autorización se controla con la Policy.
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'url_base' => 'nullable|string|max:255'
        ];
    }
}
