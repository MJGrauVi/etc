<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        //Utilizo sometimes porque no estás obligado a enviar todos los campos.
        return [
            'nombre' => 'sometimes|string|max:255',
            'direccion' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:100',
            'rol' => 'sometimes|string|exists:roles,name'//Spatie guarda los roles en tabla roles.
        ];
    }
}
