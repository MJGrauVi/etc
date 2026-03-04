<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fichero = fopen(Storage_path('app/private/media.txt'), "r");
        while(($datos = fgetcsv($fichero))!=null){
            Media::create([
                'nombre_original' => $datos[0],
                'path' => $datos[1],
            ]);
        }

    }
}
