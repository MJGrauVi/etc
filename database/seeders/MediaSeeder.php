<?php
namespace Database\Seeders;
use App\Models\Pieza;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class MediaSeeder extends Seeder {
/**
* Run the database seeds.
*/
    public function run(): void
    {
        /**********************************************/
        //Obtener imágenes del directorio.
        /*        $imagenes = Storage::files('public/imagenes');

                if(empty($imagenes)){
                    $this->command->error("No se encontraron imagenes en storage/app/public/imagenes.");
                    return;
                }
                //Obtener todas las piezas.
                $piezas = Pieza::all();

                foreach ($piezas as $pieza => $pieza) {
                   //Seleccionamos una imagen de la lista.
                        //Extrae el nombre del archivo.
                        $imgPath = $imagenes[array_rand($imagenes)];
                        $nombreImg = basename($imgPath);
                        Media::create([
                            'pieza_id' => $pieza->id,
                            'nombre_original' => $nombreImg,
                            'tipo' => 'imagen',
                            //Guardamos el path relativo al disco public.
                            'path' => 'imagenes/' . $nombreImg,
                            //Marcamos la primera como portada.
                            'es_portada' => true,
                            ]);
                    }
                $this->command->info("Se han asignado imágenes reales a las " . $piezas->count() . " piezas.");
                }*/
        /**********************************************/

        // Obtener todas las piezas existentes
        /* $piezas = Pieza::all();

         foreach ($piezas as $pieza) {*/
        // Número aleatorio de medias por pieza (entre 1 y 5)
        /*      $cantidad = rand(1, 5);
              for ($i = 0; $i < $cantidad; $i++) {
                  Media::create([
                  'pieza_id' => $pieza->id,
                  'nombre_original' => 'imagen_' . Str::random(5) . '.jpg',
                  'tipo' => 'imagen',
                  'path' => 'https://picsum.photos/seed/' . Str::random(10) . '/600/400',
                  'orden' => $i,
                  'es_portada' => false,
                  ]);
              }
          }*/
    }
}
