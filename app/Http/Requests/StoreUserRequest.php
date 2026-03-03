<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Cualquier usuario autenticado puede lanzar la petición.
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
            "nombre"=>'required|string|max:255',
            "direccion"=>'nullable|string|max:255',
            "telefono"=>'nullable|string|max:255',
            "email"=>'required|email|unique:users,email',
            /*"password"=>'required|string|min:6|confirmed',*/
            "password"=>'required|string|min:6'
        ];
    }
}
