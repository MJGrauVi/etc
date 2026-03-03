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
    public function index()
    {
        //
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
    public function store(Request $request, Pieza $pieza)
    {
            $this->authorize('create', [Publicacion::class, $pieza]);

    $request->validate([
        'nombre' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string',
        'publicado_en'=> 'required|in:facebook,instagram,tiktok,x'
    ]);

    $publicacion = $pieza->publicacions()->create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'publicado_en' => $request->publicado_en,
        'estado' => 'borrador'
    ]);

    return response()->json($publicacion, 201);
    }

  /*  public function store(StorePublicacionRequest $request){

    }*/
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
