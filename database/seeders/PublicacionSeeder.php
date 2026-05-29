<?php

namespace Database\Seeders;

use App\Models\Pieza;
use App\Models\Publicacion;
use Illuminate\Database\Seeder;

class PublicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publicaciones = [
            [
                'pieza' => 'Fofucha Abanderada Infantil',
                'titulo' => 'Fofucha Abanderada Infantil',
                'contenido' => 'Figura personalizada realizada en goma eva, cuidando los detalles del traje, los colores y los accesorios para crear una pieza única.',
                'hashtags' => '#fofuchas #hechoamano #gomaeva #artesania',
                'estado' => 'borrador',
            ],
            [
                'pieza' => 'Focucha Gitanilla',
                'titulo' => 'Fofucha Gitanilla Artesanal',
                'contenido' => 'Fofucha inspirada en traje de gitanilla, elaborada artesanalmente y pensada como detalle personalizado para celebraciones o recuerdos especiales.',
                'hashtags' => '#fofucha #artesania #personalizado #gitanilla',
                'estado' => 'pendiente',
            ],
            [
                'pieza' => 'Fofucho Minion',
                'titulo' => 'Fofucho Minion Personalizado',
                'contenido' => 'Muñeco decorativo realizado en goma eva, ideal para regalar o decorar espacios infantiles con una pieza diferente y hecha a mano.',
                'hashtags' => '#fofucho #minion #gomaeva #regalospersonalizados',
                'estado' => 'publicado',
            ],
        ];

        foreach ($publicaciones as $datos) {
            $pieza = Pieza::where('nombre', $datos['pieza'])->first();

            if (! $pieza) {
                continue;
            }

            Publicacion::updateOrCreate(
                [
                    'pieza_id' => $pieza->id,
                    'titulo' => $datos['titulo'],
                ],
                [
                    'user_id' => $pieza->user_id,
                    'contenido' => $datos['contenido'],
                    'hashtags' => $datos['hashtags'],
                    'estado' => $datos['estado'],
                ]
            );
        }
    }
}
