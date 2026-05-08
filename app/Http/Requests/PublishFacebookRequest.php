<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublishFacebookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imagen' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192',
            'mensaje' => 'nullable|string|max:5000',
        ];
    }
}
