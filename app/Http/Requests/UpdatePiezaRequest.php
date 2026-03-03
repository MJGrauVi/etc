<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePiezaRequest extends FormRequest
{
    /**
     * Determina si un usuario esta autorizado a realizar esta peticion.
     */
    public function authorize(): bool
    {
        //La autorización la controlamos con la policy.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //Utilizo sometimes porque en un update no estás obligado a enviar todos los campos.
        return [
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'categoria' => 'sometimes|string|max:100',
            'precio' => 'sometimes|numeric|min:0',
        ];
    }
}
