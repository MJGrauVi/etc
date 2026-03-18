<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicacionRequest;
use App\Http\Requests\UpdatePublicacionRequest;
use App\Services\OllamaService;
use App\Models\Pieza;
use App\Models\Publicacion;
use Illuminate\Http\Request;


class PublicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $user = auth()->user();
        // Autorizar acceso general
        $this->authorize('viewAny', Publicacion::class);

        // Ajuste: Cargamos pieza.media porque media no cuelga de publicacion
        $query = Publicacion::with(['piezas.medias', 'reds']);

        // Si es admin → ver todas
        if (!$user->hasRole('Administrador')) {
            $query->whereHas('piezas', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        return response()->json($query->get());
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
            'titulo' => $data['titulo'] ?? "Publicación de {$pieza->nombre}",
            'contenido' => $data['contenido'] ?? null,//Contenido generado/editado.
            'pieza_id' => $pieza->id,
            'user_id' => auth()->id()
            ]);
        // 4. Sincronizar redes sociales seleccionadas. (Relación N:N con redes.)
        if (!empty($data['reds'])) {
            $publicacion->reds()->sync($data['reds']);

        } return response()->json([
            'message' => 'Publicación lista para redes sociales.',
            'data' => $publicacion->load('piezas.medias', 'reds')
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
            'contenido' => $data['contenido'] ?? $publicacion->contenido,
            'estado' => $data['estado'] ?? $publicacion->estado,
        ]);

        if(isset($data['reds'])){
            $publicacion->reds()->sync($data['reds']);
        }
        //Cargamos 'pieza.media' para que React reciba la url de la imagen.
        return response()->json([
            'message' => 'Publicación actualizada',
            'data' => $publicacion->load('piezas.medias','reds')
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
    public function generarContenido(Request $request, OllamaService $ollama)
    {
        //Validamos que la pieza existe.
        $request->validate([
            'pieza_id' => 'required|exists:piezas,id',
            'estilo' => 'nullable|string',//Ej: "vintage", "minimalista",
        ]);

        //Buscamos la pieza y su imagen.
        $pieza = Pieza::with('medias')->findOrFail($request->pieza_id);

        // Recuperamos el estilo del request o usamos uno por defecto
        $estilo = $request->input('estilo', 'profesional');

        //Promp adaptado al negocio.
        $prompt = "Respondo solo con el texto de la publicación, sin comentarios adicionales del tipo 'Aquí tienes tu texto'. Actúa como un experto en marketing digital.
               Analiza la imagen de esta pieza: '{$pieza->nombre}'.
               Genera el contenido de una publicación para redes sociales.
               USA UN ESTILO DE REDACCIÓN: {$estilo}.";
               /*El texto debe ser para la columna 'contenido' de la base de datos."*/

        //Como 'medias es HasMany o BelongsToMany (varias) tomamos la primera imagen disponible.
        $primeraImagen = $pieza->medias->where('tipo', 'imagen')->first();
        if(!$primeraImagen){
            return response()->json(['error'=>'La pieza no tiene ninguna imagen asociada.'],404);
        }


        //La Pieza tiene relacion con la imagen y guarda el path local(storage/app/public).
        $imagePath = storage_path('app/public/' . $primeraImagen->path);

        if(!file_exists($imagePath)){
            return response()->json(['error'=> 'La imagen de la pieza no existe en: '. $imagePath],404);
        }

        //Llama a ollama (Contenedor local).
        try{
            $contenido = $ollama->generateCaption($imagePath, $prompt);
            return response()->json([
                'titulo' => 'Descubre mi última creación: '. $pieza->nombre,
                'contenido' => $contenido,
                'pieza' => $pieza
            ]);
        }catch(\Exception $e){
            return response()->json(['error'=>'Error en el motor de la IA local' . $e->getMessage()], 500);
        }
    }

}
