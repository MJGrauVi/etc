<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePiezaRequest;
use App\Http\Requests\UpdatePiezaRequest;
use App\Models\Pieza;
use App\Models\User;

class PiezaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Pieza::class);

        if (auth()->user()->hasRole('Administrador')) {
            return Pieza::all();
        }

        return auth()->user()->piezas;
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
    public function store(StorePiezaRequest $request){

        $this->authorize('create', Pieza::class);
        $data = $request->validated();
       //Asociamos la pieza al usuario autenticado.
        $data['user_id'] = $request->user()->id();

        $pieza = Pieza::create($data);
        return response([
            "error" => false,
            "message" => "Pieza creada correctamente.",
            "data" => $pieza
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Pieza $pieza)
    {
        $this->authorize('view', $pieza);

        return $pieza;
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
    }
}
