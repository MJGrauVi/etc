<?php

use App\Http\Controllers\PiezaController;
use App\Http\Controllers\PublicacionController;
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
Route::post('/user', [UserController::class, 'store']);
Route::post('/user/login', [UserController::class, 'verify']);

/************* Dentro del grupo las rutas que estan protegidas con middleware.*******************/
Route::middleware('auth:sanctum')->group(function () {

Route::get('/user/logout', [UserController::class, 'logout']);

    //Rutas REST para Pieza.
    //Cualquier peticion necesita token Bearer.
    Route::apiResource('piezas', PiezaController::class);
    Route::apiResource('medias', MediaController::class);

    //Guardar en bbdd las rutas de las imagenes de las piezas.
    Route::post('/medias', [MediaController::class, 'store']);
    //Muestra las url de las imagenes.
    Route::get('imagenes', [MediaController::class, 'index']);
    //Muestra las url de las imagenes en storage.
   // Route::middleware('auth:sanctum')->get('imagenes/storage', [MediaController::class, 'listarImagenesStorage']);


    Route::post('piezas/{pieza}/publicacions', [PublicacionController::class, 'store']);
    Route::patch('publicacions/{publicacion}/publicar', [PublicacionController::class, 'publicar']);
    Route::delete('publicacions/{publicacion}', [PublicacionController::class, 'destroy']);
});
