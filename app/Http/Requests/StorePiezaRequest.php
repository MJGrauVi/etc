<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePiezaRequest extends FormRequest
{
    /**
     * La autorización la controlamos con la policy.
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
           /* 'user_id' => 'required|exists:users,id',*/
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'precio' => 'nullable|numeric|min:0',
        ];
    }
}
