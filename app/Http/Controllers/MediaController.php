<?php

namespace App\Http\Controllers;


use App\Models\Media;
use App\Models\Pieza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
/*use App\Http\Requests\UpdateMediaRequest;*/

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /* Equivalente a:
    SELECT * FROM medias WHERE piezas.id = medias.pieza_id
    AND piezas.user_id = ?);
      */
    /* Alternativa más legible:
        $user = auth()->user();
        $medias = $user->piezas()
            ->with('medias')
            ->get()
            ->pluck('medias')
            ->flatten();
        return response()->json($medias);
     * */
    public function index(){
        $user = Auth::user();
        $medias = Media::whereHas('pieza', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json($medias);
    }
    public function listarImagenesStorage()
    {
        if (!Storage::disk('public')->exists('imagenes')) {
            return response()->json([
                'total' => 0,
                'imagenes' => []
            ]);
        }
        // Obtener todos los archivos dentro de la carpeta imagenes.
        $files = Storage::disk('public')->files('imagenes');

        // Convertir rutas internas en URLs públicas.
        $urls = collect($files)->map(function ($file) {
            return asset('storage/' . $file);
        });

        return response()->json([
            'total' => count($urls),
            'imagenes' => $urls
        ]);
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
            'pieza_id' => 'required|exists:piezas,id',
            'tipo' => 'required|in:image,video',
            'path'=> 'required|string',
            'order' => 'nullable|integer',
            'es_portada' => 'nullable|boolean',
        ]);

        $pieza = Pieza::findOrFail($request->pieza_id);

        //Comprobamos que la pieza pertenece al usuario autenticado.
        if($pieza->user_id !== auth()->id()){
            return response()->json([
                'mensaje' => 'No dispone de autorización para guardar este elemento en su BBDD.'
            ],403);
        }

        //Creamos el registro en la BBDD.
        $media = $pieza->medias()->create([
            'tipo'=> $request->tipo,
            'path'=> $request->path,
            'order'=> $request->order ?? 0,
            'es_portada'=> $request->es_portada ?? false
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
        $this->authorize('delete', $media);

        Storage::disk('public')->delete($media->path);

        $media->delete();

        return response()->json([
            'message' => 'Media eliminada correctamente'
        ]);
    }
}
