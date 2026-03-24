<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Models\Pieza;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(5)->create()->each(function ($user) {
            $user->assignRole('Usuario');
        });

        // Crea 20 piezas asignadas a usuarios aleatorios
        Pieza::factory(20)->create([
            'user_id' => $users->random()->id
        ]);

        $admin = User::create([
            'nombre' => 'Admin',
            "email" => 'admin@admin.com',
            "password" => Hash::make('123456'),
        ])->assignRole('Administrador');

        $userNormal = User::create([
            'nombre' => 'Usuario',
            'email' => 'usuario@usuario.com',
            "password" => Hash::make('123456'),
        ])->assignRole('Usuario');

        $invitado = User::create([
            'nombre' => 'Invitado',
            "email" => 'invitado@invitado.com',
            "password" => Hash::make('123456'),
        ])->assignRole('Invitado');
        $creador = User::create([
            'nombre' => 'Creador',
            "email" => 'creador@creador.com',
            "password" => Hash::make('123456'),
        ])->assignRole('Usuario');

        //Definir piezas reales asignadas a imagenes.
        $todasPiezas = [
            [
                'nombre' => 'Escalera Artesanal de Color',
                'descripcion' => 'Una pieza única pintada a mano y tronco lateral.',
                'imagen' => 'escaleraColor.jpeg',
                'user' => $creador // Asignada al Creador
            ],
            [
                'nombre' => 'Lavabo madera con flores',
                'descripcion' => 'Lavabo rústico con acabados florales incrustados en la madera.',
                'imagen' => 'lavaboColor.jpeg',
                'user' => $creador
            ],
            [
                'nombre' => 'Mesa motivos Mariposa',
                'descripcion' => 'Mesa rustica con sillas a juego, motivos coloridos y diseño mariposas.',
                'imagen' => 'mesaMariposa.jpeg',
                'user' => $userNormal // Asignada al Usuario
            ],
            [
                'nombre' => 'Ducha en tronco arbol',
                'descripcion' => 'Creación de ducha tallada en tronco centenario acabada con mampara curva transparente.',
                'imagen' => 'duchaMadera.jpeg',
                'user' => $userNormal
            ],
            [
                'nombre' => 'Lámpara de Suelo funcional',
                'descripcion' => 'Iluminación cálida para salones con mesita y estante.',
                'imagen' => 'lamparaSuelo.jpeg',
                'user' => $invitado
            ],
            [
                'nombre' => 'Peldaños desiguales en madera.',
                'descripcion' => 'Peldaños de escalera de madera con incrustaciones en nogal acabado exposi brillo.',
                'imagen' => 'peldanosEscalera.jpeg',
                'user' => $userNormal
            ],
        ];
        //Asignar las piezas a sus medias automaticamente.
        foreach ($todasPiezas as $pieza) {
            $nuevaPieza = Pieza::create([
                'nombre' => $pieza['nombre'],
                'descripcion' => $pieza['descripcion'],
                'user_id' => $pieza['user']->id,
            ]);
            //Creamos la media para que esten vinculados.
            Media::create([
                'pieza_id' => $nuevaPieza->id,
                'nombre_original' => $pieza['imagen'],
                'tipo' => 'imagen',
                'path' => 'imagenes/' . $pieza['imagen'],
                'es_portada' => true,
            ]);
        }
        /***********************************************/
        $user = User::factory()->create(['email' => 'titufas@gmail.com']);

        // Ruta en la carpeta de seeders
        $fotoLogo = 'seeders/images/logos/logoTitufa11.png';

        if (File::exists(database_path($fotoLogo))) {
            $pathDestino = 'logos/logo_seed.png';
            Storage::disk('public')->put($pathDestino, File::get(database_path($fotoLogo)));

            // Actualizamos el perfil que creó el Observer
            $user->perfil->update(['logo' => $pathDestino]);
        }
    }
}
