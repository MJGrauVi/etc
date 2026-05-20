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
            'mensaje' => 'nullable|string|max:5000',
        ];
    }
}
