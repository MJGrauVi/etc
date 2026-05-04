<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\PublicacionRedController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Services\GeminiService;
/************************************************************/
// routes/web.php
Route::get('/test-key', function () {
    return "La clave configurada es: " . config('services.gemini.key');
});

/*************************************************************/
//Route::get('/test', fn() => 'ESTE ES MI LARAVEL');

Route::get('/test-gemini', function () {
    $service = new GeminiService();

    $imagePath = storage_path("app/public/imagenes/peldanosEscalera.jpeg");

    $prompt = "Describe esta imagen y genera título y hashtags.";

    $resultado = $service->generateCaption($imagePath, $prompt);

    dd($resultado);
});

//Pruebas en postman incluir /api/.....
Route::post('/register', [UserController::class, 'store']);//ok
// Añade esto cerca de Route::post('/register', ...)
Route::get('/check-email', function (Request $request) {
    if (!$request->has('email')) {
        return response()->json(['exists' => false]);
    }
    $exists = User::where('email', $request->query('email'))->exists();
    return response()->json(['exists' => $exists]);
});


//Para login: http://localhost/api/user/login (añadir Bearer Token y Content-Type)
Route::post('/login', [UserController::class, 'verify']);//ok

/********************************************************************************/
//Añadimos verificacion de email.
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\URL;

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    // 1. Validar firma del enlace
    if (! URL::hasValidSignature($request)) {
        return response()->json(['message' => 'El enlace no es válido o ha expirado.'], 401);
    }

    // 2. Buscar usuario
    $user = User::find($id);

    if (! $user) {
        return response()->json(['message' => 'Usuario no encontrado.'], 404);
    }

    // 3. Validar hash
    if (! hash_equals($hash, sha1($user->email))) {
        return response()->json(['message' => 'Hash inválido.'], 403);
    }

    // 4. Verificar email si no lo está
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'El email ya estaba verificado.']);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email verificado correctamente.']);
})->middleware('signed')->name('verification.verify');

/************* Dentro del grupo las rutas que estan protegidas con middleware.*******************/
//Cualquier peticion necesita token Bearer.
Route::middleware('auth:sanctum', 'verified')->group(function () {

    Route::get('/users', [UserController::class, 'index']);//ok admin + token.
    Route::put('/user/settings', [UserController::class, 'updateSettings']);
    Route::get('/user/{user}', [UserController::class, 'show']);//ok admin, y user + token.
    Route::put('/user/{user}', [UserController::class, 'update']);//ok admin y user + token.
    Route::delete('/user/{user}', [UserController::class, 'destroy']);//ok admin y authUser/token.
    Route::post('/user/logout', [UserController::class, 'logout']);//ok con token.

    //Ruta para sesion.
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load('roles');
        return response()->json([
            'error' => false,
            'data'  => [
                ...$user->toArray(),
                'rol' => $user->roles->pluck('name')->first()
            ]
        ]);
    });

    //Rutas REST para Pieza.

    Route::get('/piezas', [PiezaController::class, 'index']);//Administrador ve todas, usuario las suyas.
    Route::post('/pieza', [PiezaController::class, 'store']);//ok
    Route::get('/pieza/{pieza}', [PiezaController::class, 'show']);//Admin de cualquier usuario, usuario las suyas.
    Route::put('/pieza/{pieza}', [PiezaController::class, 'update']);//ok
    Route::delete('/pieza/{pieza}', [PiezaController::class, 'destroy']);//ok

    //Guardar en bbdd las rutas de las imagenes de las piezas.
    Route::get('/medias', [MediaController::class, 'index']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/media/{pieza}', [MediaController::class, 'show']);
    Route::put('/media/{media}', [MediaController::class, 'update']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);

    //Ruta para generar contenido con OpenAI.
    Route::post('/publicacion/generar', [PublicacionController::class, 'generarContenido']);
    //Rutas para las publicaciones.
    Route::get('/publicaciones', [PublicacionController::class, 'index']);
    Route::post('/publicacion', [PublicacionController::class, 'store']);
    Route::get('/publicacion/{publicacion}', [PublicacionController::class, 'show']);
    Route::put('/publicacion/{publicacion}', [PublicacionController::class, 'update']);
    Route::delete('publicacion/{publicacion}', [PublicacionController::class, 'destroy']);

    // Redes asociadas a una publicación (N:N)
    Route::post('/publicacion/{publicacion}/reds', [PublicacionRedController::class, 'attach']);
    Route::put('/publicacion/{publicacion}/reds/{red}', [PublicacionRedController::class, 'update']);
    Route::delete('/publicacion/{publicacion}/reds/{red}', [PublicacionRedController::class, 'detach']);


    Route::post('piezas/{pieza}/publicacions', [PublicacionController::class, 'store']);
    Route::patch('publicacions/{publicacion}/publicar', [PublicacionController::class, 'publicar']);
    //Muestra las url de las imagenes.
    Route::get('imagenes', [MediaController::class, 'index']);
    //Muestra las url de las imagenes en storage.
   // Route::middleware('auth:sanctum')->get('imagenes/storage', [MediaController::class, 'listarImagenesStorage']);

    /* Obtener los datos del perfil.***********************************************************/
    Route::get('/perfil', [PerfilController::class, 'show']);

    // Actualizar datos de texto (NIF, móvil, web, etc.).
    Route::put('/perfil', [PerfilController::class, 'update']);

    // Subir o actualizar solo el logo (POST porque es un archivo).
    Route::post('/perfil/logo', [PerfilController::class, 'uploadLogo']);

    /*Sistema de notificaciones para el vencimiento de las publicaciones.***********************/
    // 1. Obtener todas las notificaciones (leídas y no leídas).
    Route::get('/notificaciones', [NotificacionController::class, 'index']);

    // 2. Obtener SOLO las no leídas (ideal para el contador de la campana).
    Route::get('/notificaciones/unread', [NotificacionController::class, 'unread']);

    // 3. Marcar una notificación específica como leída.
    Route::post('/notificaciones/{id}/read', [NotificacionController::class, 'markAsRead']);

    // 4. Marcar TODAS como leídas (el típico botón "Limpiar todo").
    Route::post('/notificaciones/read-all', [NotificacionController::class, 'markAllAsRead']);

    //Rutas para el panel de administración.
    Route::get('/admin/usuarios', [AdminController::class, 'index']);
    Route::put('/admin/usuarios/{user}/rol', [AdminController::class, 'cambiarRol']);
});
