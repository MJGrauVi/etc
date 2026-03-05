<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicacionRedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'redes' => 'required|array|min:1',
            'redes.*' => 'exists:reds,id'
        ];
    }
}
