<?php

use App\Http\Controllers\PiezaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\PublicacionRedController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

//Route::get('/test', fn() => 'ESTE ES MI LARAVEL');

/*Route::post('/media', function (Request $request) {
    return [
        'content_type' => $request->header('Content-Type'),
        'has_file' => $request->hasFile('file'),
        'all' => $request->all(), ];
});*/

//Pruebas en postman incluir /api/.....
Route::post('/register', [UserController::class, 'store']);//ok
//Para login: http://localhost/api/user/login (añadir Bearer Token y Content-Type)
Route::post('/login', [UserController::class, 'verify']);//ok

/************* Dentro del grupo las rutas que estan protegidas con middleware.*******************/
//Cualquier peticion necesita token Bearer.
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index']);//ok admin + token.
    Route::get('/user/{user}', [UserController::class, 'show']);//ok admin, y user + token.
    Route::put('/user/{user}', [UserController::class, 'update']);//ok admin y user + token.
    Route::delete('/users/{user}', [UserController::class, 'destroy']);//ok admin y authUser/token.
    Route::post('/user/logout', [UserController::class, 'logout']);//ok con token.

    //Rutas REST para Pieza.

    Route::get('/piezas', [PiezaController::class, 'index']);//Administrador ve todas, usuario las suyas.
    Route::post('/pieza', [PiezaController::class, 'store']);//ok
    Route::get('/pieza/{pieza}', [PiezaController::class, 'show']);//Admin de cualquier usuario, usuario las suyas.
    Route::put('/pieza/{pieza}', [PiezaController::class, 'update']);//ok
    Route::delete('/pieza/{pieza}', [PiezaController::class, 'destroy']);//ok

    //Guardar en bbdd las rutas de las imagenes de las piezas.
    Route::get('/medias', [PiezaController::class, 'index']);
    Route::post('/media', [PiezaController::class, 'store']);
    Route::get('/media/{pieza}', [PiezaController::class, 'show']);
    Route::put('/media/{media}', [PiezaController::class, 'update']);
    Route::delete('/media/{media}', [PiezaController::class, 'destroy']);

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



    //Route::apiResource('piezas', PiezaController::class);
    //Route::apiResource('medias', MediaController::class);

});
