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

        //Si por algún motivo no existiera lo creamos.
        if(!$perfil){
            $perfil = Auth()->user()->perfil()->create([
                'tipo_documento',
                'documento',
                'movil',
                'logo',
                'descripcion',
                'web',
                'redes_sociales'
            ]);
        }

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
        $perfil = Auth::user()->perfil()->firstOrCreate([]);

        $validated = $request->validate([
            'tipo_documento' => 'nullable|in:nif,cif,nie',
            'documento' => 'nullable|string|max:50',
            'movil' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:255',
            'web' => 'nullable|string|max:255',
            'redes_sociales' => 'nullable|array'
        ]);
        //Aquí ya no tocamos el logo,solo los datos de texto.
        $perfil->update($validated);

        return response([
            "error" => false,
            "message" => "Perfil actualizado correctamente.",
            "data" => $perfil //Ahora incluirá 'logo_url' automáticamente.
        ], 200);
    }

    /**
     * Subir o actualizar el logo del usuario.
     */
    public function uploadLogo(Request $request){
        //Busca el perfil del usuario, si no existe, lo crea.
        $perfil = Auth::user()->perfil()->firstorCreate([]);
        $request->validate([
            'logo'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        //Borrar logo anterior si existe(y el archivo si existe).
        if($perfil->logo && Storage::disk('public')->exists($perfil->logo)){
            Storage::disk('public')->delete($perfil->logo);
        }

        // El directorio 'logos' se creará solo dentro de storage/app/public/
        $path = $request->file('logo')->store('logos','public');

        $perfil->update([
            'logo' => $path
        ]);
        return response([
            "error" => false,
            "message" => "Logo actualizado correctamente.",
            "data" => $perfil //Incluirá logo_url al añadir el accessor.
        ],200);
    }

}
