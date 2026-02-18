<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fichero = fopen(storage_path('app/public/media.txt'), 'r');
        while(($datos=fgetmedia($fichero)!=null)){
            Media::create([
                "nombre" => $datos[0],
                "url" => $datos[1],
                "tipo" => $datos[2],

            ]);
        }
    }
}
