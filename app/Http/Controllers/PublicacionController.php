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
     * Listado de publicaciones (Admin ve todas, Usuario las suyas).
     */
    public function index() {
        $user = auth()->user();
        // Autorizar acceso general
        $this->authorize('viewAny', Publicacion::class);

        // Ajuste: Cargamos pieza.medias porque media no cuelga de publicacion.
        $query = Publicacion::with(['pieza.medias', 'reds']);

        // Si es admin puede ver todas.
        if (!$user->hasRole('Administrador')) {
            $query->whereHas('pieza', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        return response()->json($query->get());
    }

    /**
     * Crear una publicacion manual.
     */
    public function store(StorePublicacionRequest $request)
    {
        $data = $request->validated();
        $pieza = Pieza::with('medias')->findOrFail($data['pieza_id']);

        // Autorización mediante Policy.
        $this->authorize('create', [Publicacion::class, $pieza]);

        $tituloFinal = $data['titulo'] ?? null;
        $contenidoFinal = $data['contenido'] ?? null;
        $hashtagsFinal = $data['hashtags'] ?? '';

        // Formateo automático de Hashtags (Asegura el símbolo #).
        if (!empty($hashtagsFinal)) {
            $hashtagsFinal = collect(explode(' ', str_replace('#', '', $hashtagsFinal)))
                ->filter()
                ->map(fn($tag) => "#" . trim($tag))
                ->implode(' ');
        }

        // Persistencia en Base de Datos.
        $publicacion = Publicacion::create([
            'user_id'   => auth()->id(),
            'pieza_id'  => $pieza->id,
            'titulo'    => trim(str_replace(['*', '#'], '', $tituloFinal ?? "Publicación de {$pieza->nombre}")),
            'contenido' => $contenidoFinal === null
                ? null
                : trim(str_replace(['**'], '', $contenidoFinal)),
            'hashtags'  => $hashtagsFinal,
            'estado'    => 'borrador'
        ]);

        // Sincronización con Redes Sociales (Vencimiento a 3 meses).
        if (!empty($data['reds'])) {
            $pivotData = [];
            foreach ($data['reds'] as $redId) {
                $pivotData[$redId] = ['fecha_vencimiento' => now()->addMonths(3)];
            }
            $publicacion->reds()->sync($pivotData);
        }

        return response()->json([
            'message' => 'Publicación creada correctamente',
            'data' => $publicacion->load('pieza.medias', 'reds')
        ], 201);
    }
    /**
     * Ver una publicación específica.
     */
    public function show(Publicacion $publicacion)
    {
        $this->authorize('view', $publicacion);

        $publicacion->load('pieza.medias','reds');

        return response()->json($publicacion);
    }


    /**
     * Actualizar publicación.
     */
    public function update(UpdatePublicacionRequest $request, Publicacion $publicacion)
    {
        // Carga la relación antes de la policy.
        $publicacion->loadMissing('pieza');

        //LLama a PublicaciónPolicy-update.
        $this->authorize('update', $publicacion);
        //Validación.
        $data = $request->validated();
        //Actualiza compos.
        $publicacion->update([
          /*  'titulo'    => $data['titulo'] ?? $publicacion->titulo,*/
            'titulo' => array_key_exists('titulo', $data)
                ? $data['titulo']
                : $publicacion->titulo,
            /*'contenido' => $data['contenido'] ?? $publicacion->contenido,*/
            'contenido' => array_key_exists('contenido', $data)
                ? $data['contenido']
                : $publicacion->contenido,
            'estado'    => $data['estado'] ?? $publicacion->estado,
           /* 'hashtags'  => $data['hashtags'] ?? $publicacion->hashtags,*/
            'hashtags' => array_key_exists('hashtags', $data)
                ? $data['hashtags']
                : $publicacion->hashtags,
        ]);
        //Actualizar relación con redes sociales.
        if (isset($data['reds'])) {
            $publicacion->reds()->sync($data['reds']);
        }

        //Cargar relaciones necesarias para el frontend.
        $publicacion->load('pieza.medias', 'reds');

        //Cargamos 'pieza.media' para que React reciba la url de la imagen.
        return response()->json([
            'message' => 'Publicación actualizada',
            'data' => $publicacion
        ]);
    }
    /**
     * Eliminar publicación.
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
        $desde = now()->startOfDay();
        $hasta = now()->addDays(7)->endOfDay();

        // Buscamos publicaciones del usuario con redes que vencen en los próximos 7 días.
        // fecha_vencimiento vive en la tabla pivote publicacion_red, por eso usamos wherePivot.
        $alertas = Publicacion::with([
            'pieza',
            'reds' => function ($query) use ($desde, $hasta) {
                $query->wherePivot('fecha_vencimiento', '>=', $desde)
                    ->wherePivot('fecha_vencimiento', '<=', $hasta);
            }
        ])
            ->whereHas('pieza', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->whereHas('reds', function ($query) use ($desde, $hasta) {
                $query->wherePivot('fecha_vencimiento', '>=', $desde)
                    ->wherePivot('fecha_vencimiento', '<=', $hasta);
            })
            ->get();

        return response()->json([
            "error"=>false,
            "mensaje"=>"Tiene " . $alertas->count() . " publicaciones próximas a vencer.",
            "data"=>$alertas
        ]);
    }
    /**
     * Cambiar estado a publicada.
     */
    public function publicar(Publicacion $publicacion)
    {
        $pieza = $publicacion->pieza()->first();
        $this->authorize('update', $publicacion);

        $publicacion->update(['estado' => 'publicado']);

        return response()->json($publicacion);
    }

    public function generarContenido(Request $request)
    {
        $request->validate([
            'pieza_id' => 'required|exists:piezas,id',
            'estilo'   => 'nullable|string',
        ]);

        $pieza = Pieza::with('medias')->findOrFail($request->pieza_id);
        $primeraImagen = $pieza->medias->first();

        if (!$primeraImagen) {
            return response()->json([
                'error' => 'La pieza no tiene ninguna imagen asociada.'
            ], 422);
        }

        $imagePath = storage_path('app/public/' . $primeraImagen->path);

        if (!file_exists($imagePath)) {
            return response()->json([
                'error'          => 'Imagen no encontrada',
                'ruta_intentada' => $imagePath
            ], 404);
        }

        try {
            $prompt = "Actúa como un experto en Marketing Digital para artesanos.
        Analiza esta imagen de una pieza llamada '{$pieza->nombre}' y genera:
        1. Un título llamativo (máximo 10 palabras).
        2. Una descripción emocional y profesional para Instagram (menciona texturas y materiales).
        3. Una lista de 5 hashtags relevantes.

        IMPORTANTE: Devuelve la respuesta con este formato:
        Título: [Aquí el título]
        Contenido: [Aquí la descripción]
        Hashtags: [Aquí los hashtags]";

            $contenido = $this->gemini->generateCaption($imagePath, $prompt);

            if (
                str_starts_with($contenido, "ERROR") ||
                str_starts_with($contenido, "DETALLE_GOOGLE") ||
                str_starts_with($contenido, "EXCEPCION_CONEXION")
            ) {
                // 👇 Detecta si es error de cuota específicamente
                $esCuota = str_contains($contenido, '429') ||
                    str_contains($contenido, 'RESOURCE_EXHAUSTED') ||
                    str_contains($contenido, 'quota');

                if ($esCuota) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'Has alcanzado el límite diario de peticiones de IA. Inténtalo mañana o actualiza el plan en Google AI Studio.',
                    ], 429); // 👈 código 429 específico para cuota
                }
                return response()->json([
                    'success'    => false,
                    'error_ia'   => $contenido,
                ], 500);
            }

            // Extraer campos de la respuesta de Gemini
            $lineas      = explode("\n", $contenido);
            $tituloIA    = '';
            $contenidoIA = '';
            $hashtagsIA  = '';

            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if (str_starts_with($linea, "Título: ")) {
                    $tituloIA = trim(substr($linea, 8));
                } elseif (str_starts_with($linea, "Contenido: ")) {
                    $contenidoIA = trim(substr($linea, 11));
                } elseif (str_starts_with($linea, "Hashtags: ")) {
                    $hashtagsIA = trim(substr($linea, 10));
                }
            }

            // Normalizar hashtags
            $hashtagsIA = collect(explode(' ', str_replace('#', '', $hashtagsIA)))
                ->filter()
                ->map(fn($tag) => '#' . trim($tag))
                ->implode(' ');

            // Limpiar título
            $tituloIA = trim(str_replace(['*', '#', ':'], '', $tituloIA));

            $publicacion = new Publicacion();
            $publicacion->user_id   = auth()->id();
            $publicacion->pieza_id  = $pieza->id;
            $publicacion->titulo    = $tituloIA;
            $publicacion->contenido = $contenidoIA;
            $publicacion->estado    = 'borrador';
            $publicacion->hashtags  = $hashtagsIA;
            $publicacion->save();

            $publicacion->refresh();

            Log::info("Publicación generada ID {$publicacion->id} para pieza {$pieza->nombre}");

            return response()->json([
                'success' => true,
                'message' => 'Publicación generada y guardada como borrador',
                'data'    => $publicacion->load('pieza.medias') // ← singular
            ], 201);

        } catch (\Exception $e) {
            Log::error("IA Error: " . $e->getMessage());
            return response()->json([
                'error' => 'Excepción: ' . $e->getMessage()
            ], 500);
        }
    }

}
