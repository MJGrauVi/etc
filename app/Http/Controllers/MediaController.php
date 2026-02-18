<?php

namespace App\Http\Controllers;


use App\Models\Media;
use Illuminate\Http\Request;
/*use App\Http\Requests\UpdateMediaRequest;*/

class MediaController extends Controller
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
    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov|max:20480',
            'pieza_id' => 'required|exists:piezas,id',
            'order' => 'nullable|integer',
            'es_portada' => 'nullable|boolean',
        ]);

        //Recogemos el archivo.
        $file = $request->file('file');

        //Guarda el archivo en storage/app/public/media/{pieza_id}
        $path = $file->store('media/' . $request->pieza_id, 'public');

        //Creamos el registro en la BBDD.
        $media = Media::create([
            'pieza_id' => $request->pieza_id,
            'tipo' => str_contains($file->getClientMimeType(), 'image')? 'image': 'video',
            'path' => $path,
            'order' => $request->order ?? 0,
            'es_portada' => $request->es_portada ?? false,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'nombre_original' => $file->getClientOriginalName(),
        ]);
        //Devolvemos el JSON al frontend.
        return response()->json($media,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        //
    }
}
