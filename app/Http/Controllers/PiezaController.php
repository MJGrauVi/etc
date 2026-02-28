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
    public function store(StorePiezaRequest $request)
    {
        return auth()->user()->piezas()->create($request->all());
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
       /* $this->authorize('update', $pieza);*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pieza $pieza)
    {
        $this->authorize('delete', $pieza);
    }
}
