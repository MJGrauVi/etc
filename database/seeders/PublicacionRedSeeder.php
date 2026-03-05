<?php

namespace Database\Seeders;

use App\Models\Publicacion;
use App\Models\Red;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PublicacionRedSeeder extends Seeder
{
    public function run(): void
    {
        $publicaciones = Publicacion::all();
        $reds = Red::all();

        foreach ($publicaciones as $publicacion) {

            // Seleccionar entre 1 y 4 redes aleatorias.
            $redesSeleccionadas = $reds->random(rand(1, min(4, $reds->count())));

            foreach ($redesSeleccionadas as $red) {
                DB::table('publicacion_red')->insert([
                    'publicacion_id'   => $publicacion->id,
                    'red_id'           => $red->id,
                    'fecha_vencimiento'=> Carbon::now()->addMonths(3),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}

