<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicacionRequest;
use App\Http\Requests\UpdatePublicacionRequest;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;
use App\Models\Pieza;
use App\Models\Publicacion;
use Illuminate\Http\Request;


class PublicacionController extends Controller
{

    //Definimos la variable protegida.
    protected $gemini;

    //Int¡yectamos el servicio aquí.
    public function __construct(GeminiService $gemini){
        $this->gemini = $gemini;
    }
    /**
     * Mostrar un listado del recurso.
     */
    public function index() {
        $user = auth()->user();
        // Autorizar acceso general
        $this->authorize('viewAny', Publicacion::class);

        // Ajuste: Cargamos piezas.medias porque media no cuelga de publicacion
        $query = Publicacion::with(['piezas.medias', 'reds']);

        // Si es admin puede ver todas.
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
    public function store_generate(StorePublicacionRequest $request) {
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

    public function store(StorePublicacionRequest $request)
    {
        // 1. Datos ya validados por tu FormRequest
        $data = $request->validated();

        // 2. Obtener la pieza con sus medias (Eager Loading para eficiencia)
        $pieza = Pieza::with('medias')->findOrFail($data['pieza_id']);

        // 3. Autorizar (Se mantiene tu lógica)
        $this->authorize('create', [Publicacion::class, $pieza]);

        // 4. Lógica de Contenido: Si el usuario no envía contenido, usamos la IA
        $contenidoFinal = $data['contenido'] ?? null;

        if (empty($contenidoFinal)) {
            $primeraImagen = $pieza->medias->first();

            if (!$primeraImagen) {
                return response()->json(['error' => 'La pieza no tiene imágenes para analizar.'], 422);
            }

            // Construimos la ruta y llamamos al servicio inyectado ($this->gemini)
            $imagePath = storage_path('app/public/' . $primeraImagen->path);

            if (file_exists($imagePath)) {
                $prompt = "Analiza esta pieza llamada '{$pieza->nombre}' y genera un título y descripción.";
                $contenidoFinal = $this->gemini->generateCaption($imagePath, $prompt);
            }
        }

        // 5. Crear la publicación (Limpio y estándar)
        $publicacion = Publicacion::create([
            'titulo' => $data['titulo'] ?? "Sug: {$pieza->nombre}",
            'contenido' => $contenidoFinal,
            'pieza_id' => $pieza->id,
            'user_id' => auth()->id(),
            'estado' => 'borrador'
        ]);

        // 6. Relaciones N:N
        if (!empty($data['reds'])) {
            $redesConDatos = [];

            foreach ($data['reds'] as $redId) {
                $redesConDatos[$redId] = [
                    // Guardamos la fecha de alerta: hoy + 3 meses.
                    'fecha_vencimiento' => now()->addMonths(3)
                ];
            }
            // sync() se encarga de todo:
            // 1. Pone la fecha de hoy en 'created_at' (Publicación)
            // 2. Pone la fecha de hoy + 3 meses en 'fecha_vencimiento' (Alerta)
            $publicacion->reds()->sync($redesConDatos);
        }
        return response()->json([
            'message' => 'Publicación creada exitosamente.',
            'data' => $publicacion->load('piezas.medias', 'reds')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicacion $publicacion)
    {
        $this->authorize('view', $publicacion);

        $publicacion->load('piezas.medias','reds');

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

    Public function alertaVencimiento(){
        //Buscamos en la tabla pivor las relaciones que vencen en 7 días.
        $alertas = auth()->user()->piezas()-with(['publicaciones.reds', function($query){
            $query->wherePivot('fecha_vencimiento', '<=', now()->addDays(7));

    }])->get()->pluck('publicacions')-flatten()->filter(function($p){
        return $p->reds->isNotEmpty();
            });
        return response()->json([
            "error"=>false,
            "mensajes"=>"Tiene " . $alertas->count() . "publicaciones próximas a vencer.",
            "data"=>$alertas
        ]);
    }
    public function publicar(Publicacion $publicacion)
    {
        $pieza = $publicacion->piezas()->first();
        // Seguridad ¿Es su pieza?
        $this->authorize('generate', [Publicacion::class, $pieza]);
        $this->authorize('update', $publicacion);

        $publicacion->publicar();

        return response()->json($publicacion);
    }

    public function generarContenido(Request $request)
    {
        $request->validate([
            'pieza_id' => 'required|exists:piezas,id',
            'estilo' => 'nullable|string',
        ]);

        $pieza = Pieza::with('medias')->findOrFail($request->pieza_id);
        $primeraImagen = $pieza->medias->first();

        if(!$primeraImagen){
            return response()->json(['error'=>'La pieza no tiene ninguna imagen asociada.'],422);
        }

        $imagePath = storage_path('app/public/' . $primeraImagen->path);

        if(!$imagePath || !file_exists($imagePath)){
            return response()->json([
                'error'=> 'Imagen no encontrada',
                'ruta_intentada' => $imagePath
            ],404);
        }

        try{
            $estilo = $request->estilo ?? 'profesional y creativo';
            $prompt = "Actúa como un experto en Marketing Digital para artesanos.
            Analiza esta imagen de una pieza llamada '{$pieza->nombre}' y genera:
            1. Un título llamativo (máximo 10 palabras).
            2. Una descripción emocional y profesional para Instagram (menciona texturas y materiales).
            3. Una lista de 5 hashtags relevantes.

            IMPORTANTE: Devuelve la respuesta con este formato:
            Título: [Aquí el título]
            Contenido: [Aquí la descripción]
            Hashtags: [Aquí los hashtags]";

            // Llamada al servicio (usando tus variables)
            $contenido = $this->gemini->generateCaption($imagePath, $prompt);

            // --- ESTE ES EL CAMBIO CLAVE PARA EL TEST ---
            // Si el contenido trae un mensaje de error, cortamos aquí para verlo en Postman
            if (!is_string($contenido) || preg_match('/error|detalle|excepcion|not_found|invalid/i', $contenido)) {
                return response()->json([
                    'success' => false,
                    'error_ia' => $contenido, // Aquí verás el error real de Google
                    'debug_path' => $imagePath
                ], 500);
            }

            $publicacion = new Publicacion();
            $publicacion->user_id = auth()->id() ?? 1; // Fallback al ID 1 para el test de Postman
            $publicacion->pieza_id = $pieza->id;
            $publicacion->titulo = 'Sugerencia IA: ' . $pieza->nombre;
            $publicacion->contenido = $contenido;
            $publicacion->estado = 'borrador';
            $publicacion->save();

            $publicacion->refresh();

            Log::info("Contenido guardado en ID " . $publicacion->id . ": " . $publicacion->contenido);

            return response()->json([
                'success' => true,
                'message' => 'Publicación generada y guardada como borrador',
                'data'    => $publicacion->load('piezas.medias')
            ], 201);

        } catch(\Exception $e) {
            Log::error("IA Error: " . $e->getMessage());
            return response()->json(['error'=> 'Excepción: ' . $e->getMessage()], 500);
        }
    }

}
