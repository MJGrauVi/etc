<?php

use App\Http\Controllers\PiezaController;
use App\Http\Controllers\PublicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

//Route::get('/test', fn() => 'ESTE ES MI LARAVEL');
//Devuelve el usuario autenticado se se envia con el token válido.
/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

/*Route::post('/media', function (Request $request) {
    return [
        'content_type' => $request->header('Content-Type'),
        'has_file' => $request->hasFile('file'),
        'all' => $request->all(), ];
});*/

Route::middleware('auth:sanctum')->group(function () {
    //Rutas REST para Pieza.
    //Cualquier peticion necesita token Bearer.
    Route::apiResource('piezas', PiezaController::class);
    Route::apiResource('medias', MediaController::class);

    //Guardar en bbdd las imagenes de las piezas.
    Route::post('/medias', [MediaController::class, 'store']);
    //Muestra las url de las imagenes.
    Route::get('imagenes', [MediaController::class, 'index']);
    //Muestra las url de las imagenes en storage.
   // Route::middleware('auth:sanctum')->get('imagenes/storage', [MediaController::class, 'listarImagenesStorage']);
    Route::post('piezas/{pieza}/publicacions', [PublicacionController::class, 'store']);
    Route::patch('publicacions/{publicacion}/publicar', [PublicacionController::class, 'publicar']);
    Route::delete('publicacions/{publicacion}', [PublicacionController::class, 'destroy']);
});
