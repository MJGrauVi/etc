<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

//Route::get('/test', fn() => 'ESTE ES MI LARAVEL');
//Devuelve el usuario autenticado se se envia con el token válido.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/media', function (Request $request) {
    return [
        'content_type' => $request->header('Content-Type'),
        'has_file' => $request->hasFile('file'),
        'all' => $request->all(), ];
});

/*Route::middleware('auth:sanctum')->group(function () {*/
    //Rutas REST para Media.
 //  Route::apiResource('media', MediaController::class);
/*});*/
