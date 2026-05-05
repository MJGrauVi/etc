<?php

namespace App\Console\Commands;

use App\Models\Publicacion;
use App\Notifications\PublicacionVencida;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevisarVencimientos extends Command
{
    /**
     * Nombre con el que llamaremos al comando: php artisan app:revisar-vencimientos.
     *
     * @var string
     */
    protected $signature = 'app:revisar-vencimientos';
    protected $description = 'Busca publicaciones que han vencido y avisa a los usuarios';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Buscamos en la tabla pivote los registros que vencen hoy o antes
        $vencidos = DB::table('publicacion_red')
            ->whereDate('fecha_vencimiento', '<=', now())
            ->get();

        if ($vencidos->isEmpty()) {
            $this->info('No hay publicaciones vencidas hoy.');
            return;
        }

        foreach ($vencidos as $registro) {
            // 2. Buscamos la publicación y su dueño (User)
            $publicacion = Publicacion::with('user', 'pieza')->find($registro->publicacion_id);

            if ($publicacion && $publicacion->user) {
                // 3. ¡ENVIAMOS LA NOTIFICACIÓN!
                // Esto inserta una fila en la tabla 'notifications'
                $publicacion->user->notify(new PublicacionVencida($publicacion));

                $this->info("Notificación enviada a {$publicacion->user->email} por la pieza: {$publicacion->titulo}");
            }
        }

        $this->info('Proceso de revisión final con exito.');
    }
}
