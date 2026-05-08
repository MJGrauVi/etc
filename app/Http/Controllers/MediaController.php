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
            'imagen'   => 'required|file|image|max:5120', // max 5MB
            'es_portada' => 'nullable|boolean',
        ]);

        $pieza = Pieza::findOrFail($request->pieza_id);

        // Comprobamos que la pieza pertenece al usuario autenticado.
        if ($pieza->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'No dispone de autorización para modificar esta pieza.'
            ], 403);
        }

        $file = $request->file('imagen');

        // Guardamos el archivo en storage/app/public/imagenes
        $path = $file->store('imagenes', 'public');

        // Creamos el registro en la tabla medias
        $media = $pieza->medias()->create([
            'nombre_original' => $file->getClientOriginalName(),
            'path'            => $path,
            'tipo'            => 'imagen',
            'es_portada'      => $request->boolean('es_portada', false),
        ]);

        return response()->json([
            'error' => false,
            'data'  => $media
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }

    public function archivo(Media $media)
    {
        $media->loadMissing('pieza');

        if (!auth()->user()->hasRole('Administrador') && $media->pieza->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'No dispone de autorizacion para ver esta imagen.'
            ], 403);
        }

        if (!Storage::disk('public')->exists($media->path)) {
            return response()->json([
                'message' => 'Imagen no encontrada.'
            ], 404);
        }

        return Storage::disk('public')->response($media->path);
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
    public function update(Request $request, Media $media)
    {
        // Comprobamos que la pieza pertenece al usuario autenticado.
        if ($media->pieza->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'No dispone de autorización.'
            ], 403);
        }

        // Si marcamos esta como portada, quitamos la portada anterior
        if ($request->boolean('es_portada')) {
            $media->pieza->medias()->update(['es_portada' => false]);
        }

        $media->update($request->only('es_portada'));

        return response()->json([
            'error' => false,
            'data'  => $media
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        $media->load('pieza'); // Ver que la pieza está cargada
        $this->authorize('delete', $media);

        Storage::disk('public')->delete($media->path);

        $media->delete();

        return response()->json([
            'message' => 'Media eliminada correctamente'
        ]);
    }
}
