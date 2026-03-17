<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicacionRequest;
use App\Http\Requests\UpdatePublicacionRequest;
use App\Models\Pieza;
use App\Models\Publicacion;
use Illuminate\Http\Request;


class PublicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() { $user = auth()->user();
        // 1. Autorizar acceso general
        $this->authorize('viewAny', Publicacion::class);
        // 2. Si es admin → ver todas
        if ($user->hasRole('Administrador')) {
            $publicaciones = Publicacion::with('pieza', 'media', 'reds')->get();
        }
        // 3. Si es usuario normal → ver solo las suyas
        else {
            $publicaciones = Publicacion::whereHas('pieza', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }) ->with('pieza', 'media', 'reds') ->get();
        } return response()->json($publicaciones);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Solo para blade.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublicacionRequest $request) {
        $data = $request->validated();
        // 1. Obtener la pieza.
        $pieza = Pieza::findOrFail($data['pieza_id']);
        // 2. Autorizar usando la policy.
        $this->authorize('create', [Publicacion::class, $pieza]);
        // 3. Crear la publicación.
        $publicacion = Publicacion::create([
            'titulo' => $data['titulo'] ?? null,
            'contenido' => $data['descripcion'] ?? null,
            'pieza_id' => $data['pieza_id'],
            'user_id' => $request->user()->id
            ]);
        // 4. Relación N:N con redes.
        if (!empty($data['reds'])) {
            $publicacion->reds()->sync($data['redes']);
        } return response()->json([
            'message' => 'Publicación creada correctamente',
            'data' => $publicacion
            /*'data' => $publicacion->load('piezas', 'medias', 'reds')*/
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicacion $publicacion)
    {
        $this->authorize('view', $publicacion);

        $publicacion->load('pieza','media','reds');

        return response()->json($publicacion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publicacion $publicacion)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePublicacionRequest $request, Publicacion $publicacion)
    {
        $this->authorize('update', $publicacion);

        $data = $request->validated();

        $publicacion->update([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'media_id' => $data['media_id'] ?? null
        ]);

        if(isset($data['redes'])){
            $publicacion->reds()->sync($data['redes']);
        }

        return response()->json([
            'message' => 'Publicación actualizada',
            'data' => $publicacion->load('pieza','media','reds')
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publicacion $publicacion)
    {
        $this->authorize('delete', $publicacion);

        $publicacion->delete();

        return response()->json([
            'message' => 'Publicación eliminada'
        ]);
    }
    public function publicar(Publicacion $publicacion)
    {
        $this->authorize('update', $publicacion);

        $publicacion->publicar();

        return response()->json($publicacion);
    }
    public function generarContenido(Request $request)
    {
        $tema = $request->tema;

        $respuesta = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un generador de contenido para publicaciones.'],
                ['role' => 'user', 'content' => "Genera un texto para una publicación sobre: $tema"],
            ],
        ]);

        return [
            "contenido" => $respuesta['choices'][0]['message']['content']
        ];
    }
}
