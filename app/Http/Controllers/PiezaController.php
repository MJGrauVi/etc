<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePiezaRequest;
use App\Http\Requests\UpdatePiezaRequest;
use App\Models\Pieza;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PiezaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Llama a PiezaPolicy@viewAny.
        $this->authorize('viewAny', Pieza::class);

        if (auth()->user()->hasRole('Administrador')) {
            return response([
                "error" => false,
                //Usamos with('medias') para traes las fotos de todas las piezas.
                "data" => Pieza::with('medias')->get()
            ], 200);
        }

       return response([
            "error" => false,
            "data" => auth()->user()->piezas()->with('medias')->get()
        ], 200);


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        return response([
            "error" => false,
            "message" => "Pieza creada correctamente.",
            //"data" => $pieza
        ], 201);
       // return $user->hasRole('Usuario');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePiezaRequest $request){
        //Autorización.
        $this->authorize('create', Pieza::class);
        //Validación.
        $data = $request->validated();
       //Asociamos la pieza al usuario autenticado.
        $data['user_id'] = $request->user()->id;

        //Creamos la instancia de la pieza en BD(campos de texto.
        $pieza = Pieza::create($data);
        /*******************Añadimos imagenes a la pieza**********************************/
        //Lógica para procesar las imágenes.
        //Comprobamos si en la petición vienen archivos.
        if($request->hasFile('fotos')){//cuerpo peticion multipart/form-data
            $files = $request->file('fotos');

            //Si solo hay una la convertimos en array para usar el mismo forEach.
            if(!is_array($files)){
                $files = [$files];
            }
            foreach($files as $file){
                //Guardamos el archivo físicamente en 'storage/app/public/imagenes.
                //El método 'store' devuelve la ruta generada automáticamente.
                $path = $file->store('imagenes', 'public');

                //Creamos el registro en la tabla 'medias' usando la relación del modelo, asocia pieza_id automaticamente.
                $pieza->medias()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'path' => $path, //Comprobar que 'medias' tenga la columna 'path'.
                    'tipo' => 'imagen',
                    'es_portada' => true
                ]);
            }
        }
        /*********************************************************************************/
        return response([
            "error" => false,
            "message" => "Pieza creada correctamente.",
            "data" => $pieza->load('medias') //Cargamos la relacion para confirmar que se guardaron.
        ], 201);
    }
    /**
     * Muestra el detalle de una pieza con sus imagenes y redes sociales vinculadas.
     */
    public function show(Pieza $pieza)
    {
        //Seguridad de Laravel.
        //Verificamos que el usuario tiene permiso para ver esta pieza.
        $this->authorize('view', $pieza);

        //Cargamos la relación 'medias' en la instancia actual(añade el array de imagenes al objeto JSON.
        //Las publicaciones de la pieza y las redes de cada una de esas publicaciones.
        $pieza->load([
            'medias',
            'publicacions.reds'
        ]);

        //Muestra cuantas publicaciones hay de esta pieza.
        $pieza->loadCount('publicacions');

        //Retornamos la pieza completa.
        return response()->json([
            'error' => false,
            'data' => $pieza
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pieza $pieza)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePiezaRequest $request, Pieza $pieza)
    {
        $this->authorize('update', $pieza);

        $data = $request->validated();

        $pieza->update($data);

        return response([
            "error" => false,
            "message" => "Pieza actualizada correctamente.",
            "data" => $pieza
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pieza $pieza)
    {
        $this->authorize('delete', $pieza);
        $pieza->delete();
        return response([
            "error" => false,
            "message" => "Pieza eliminada correctamente.",
        ], 200);
    }
}
