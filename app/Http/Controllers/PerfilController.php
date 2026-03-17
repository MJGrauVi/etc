<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{


    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function show()
    {
        $perfil = Auth::user()->perfil;

        return response([
            "error"=>false,
            "message"=>"Perfil obtenido correctamente",
            "data"=>$perfil
        ],200);
    }

    /**
     * Actualizar el perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $perfil = Auth::user()->perfil;

        $validated = $request->validate([
            'tipo_documento' => 'nullable|in:nif,cif,nie',
            'documento' => 'nullable|string|max:50',
            'movil' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:255',
            'web' => 'nullable|string|max:255',
            'redes_sociales' => 'nullable|array'
        ]);

        $perfil->update($validated);

        return response([
            "error" => false,
            "message" => "Perfil actualizado correctamente.",
            "data" => $perfil
        ], 200);
    }

    /**
     * Subir o actualizar el logo del usuario.
     */
    public function uploadLogo(Request $request){
        $perfil = Auth::user()->perfil;
        $request->validate([
            'logo'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        //Borrar logo anterior si existe.
        if($perfil->logo){
            Storage::disk('public')->delete($perfil->logo);
        }

        //Guardar nuevo logo.
        $path = $request->file('logo')->store('logos','public');
        $perfil->update([
            'logo' => $path
        ]);
        return response([
            "error" => false,
            "message" => "Logo actualizado correctamente.",
            "data" => $perfil
        ],200);
    }

}
