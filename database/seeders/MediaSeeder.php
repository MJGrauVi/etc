<?php
namespace Database\Seeders;
use App\Models\Pieza;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class MediaSeeder extends Seeder {
/**
* Run the database seeds.
*/
    public function run(): void
    {
        $pieza = Pieza::first(); // Asignamos a la primera pieza creada en PiezaSeeder
        $pieza1 = Pieza::first();
        $fotoPieza = 'seeders/images/fotos/abanderadaInfantil.jpg';

        if (File::exists(database_path($fotoPieza))) {
            $pathDestino = 'piezas/abanderada_seed.jpg';
            Storage::disk('public')->put($pathDestino, File::get(database_path($fotoPieza)));

            // Creamos el registro en la tabla medias
            $pieza1->medias()->create([
                'url' => $pathDestino,
                'tipo' => 'image'
            ]);
        }
    }
}
