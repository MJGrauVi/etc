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
       // $pieza = Pieza::first(); // Asignamos a la primera pieza creada en PiezaSeeder
       // $user = User::where('email', 'titufas@gmail.com')->first();
   /*     $pieza = Pieza::first();
        $fotoFuente = 'seeders/images/fotos/abanderadaInfantil.jpeg';

        if (File::exists(database_path($fotoFuente))) {
            //El destino donde la API busca la imagen lara leerla.(creamos la carpeta imagenes dentro de public).
            $pathDestino = 'imagenes/abanderadaInfantil.jpeg';
            Storage::disk('public')->put($pathDestino, File::get(database_path($fotoFuente)));

            // Creamos el registro en la tabla medias
            Media::create([
                'pieza_id' => $pieza->id,
                'nombre_original' => $nombreArchivo,
                'tipo' => 'imagen',
                'path' => 'imagenes/' . $nombreArchivo,
                'es_portada' => false,
            ]);
        }*/
    }
}
