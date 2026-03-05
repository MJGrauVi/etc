<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicacionRedRequest;
use App\Http\Requests\UpdatePublicacionRedRequest;
use App\Models\Publicacion;
use App\Models\Red;

class PublicacionRedController extends Controller
{
    // Añadir redes a una publicación (attach)
    public function attach(StorePublicacionRedRequest $request, Publicacion $publicacion)
    {
        // Policy: solo admin o dueño de la pieza
        $this->authorize('update', $publicacion);

        $publicacion->reds()->syncWithoutDetaching($request->redes);

        return response()->json([
            'message' => 'Redes asociadas correctamente',
            'data' => $publicacion->reds
        ]);
    }

    // Actualizar datos del pivote (fecha_vencimiento)
    public function update(UpdatePublicacionRedRequest $request, Publicacion $publicacion, Red $red)
    {
        $this->authorize('update', $publicacion);

        $publicacion->reds()->updateExistingPivot($red->id, [
            'fecha_vencimiento' => $request->fecha_vencimiento
        ]);

        return response()->json([
            'message' => 'Datos de la red actualizados correctamente'
        ]);
    }

    // Quitar una red de una publicación (detach)
    public function detach(Publicacion $publicacion, Red $red)
    {
        $this->authorize('update', $publicacion);

        $publicacion->reds()->detach($red->id);

        return response()->json([
            'message' => 'Red eliminada de la publicación'
        ]);
    }
}
