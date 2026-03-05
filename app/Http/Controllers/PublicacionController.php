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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublicacionRequest $request) {
        $data = $request->validated();
        // 1. Obtener la pieza
        $pieza = Pieza::findOrFail($data['pieza_id']);
        // 2. Autorizar usando la policy
        $this->authorize('create', [Publicacion::class, $pieza]);
        // 3. Crear la publicación.
        $publicacion = Publicacion::create([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'pieza_id' => $data['pieza_id'],
            'media_id' => $data['media_id'] ?? null,
            'user_id' => $request->user()->id
            ]);
        // 4. Relación N:N con redes.
        if (!empty($data['redes'])) {
            $publicacion->reds()->sync($data['redes']);
        } return response()->json([
            'message' => 'Publicación creada correctamente',
            'data' => $publicacion ],
            201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicacion $publicacion)
    {
        //
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
        //
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publicacion $publicacion)
    {
        //
    }
    public function publicar(Publicacion $publicacion)
    {
        $this->authorize('update', $publicacion);

        $publicacion->publicar();

        return response()->json($publicacion);
    }
}
