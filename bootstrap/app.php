<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Habilitamos el middleware de CORS para que use tu config/cors.php.
        //HandleCors lee las reglas de config/cors.php en allowed_origins =>['*'] y decide si pasa React.
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // 2. Importante para SPAs (React): permite que Laravel reconozca
        // las cookies de sesión y el estado de autenticación (Sanctum).
        //statefullApi guarda quién es el usuario que está navegando desde el frontend.
        $middleware->statefulApi();

        // 3. Opcional: Si tienes problemas con el token CSRF en las pruebas
        // iniciales de la API, puedes excluir tus rutas de API aquí:
        //Con esto evitamos errores en produccion.
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
