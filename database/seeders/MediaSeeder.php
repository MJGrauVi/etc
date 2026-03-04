<?php
namespace Database\Seeders;
use App\Models\Pieza;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class MediaSeeder extends Seeder {
/**
* Run the database seeds.
*/
public function run(): void {
// Obtener todas las piezas existentes
$piezas = Pieza::all();

foreach ($piezas as $pieza) {
// Número aleatorio de medias por pieza (entre 1 y 5)
$cantidad = rand(1, 5);
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
}
}
}
