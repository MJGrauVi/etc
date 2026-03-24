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
        //Usuarios aleatorios para rellenar la BD.
        $users = User::factory(5)->create()->each(function ($user) {
            $user->assignRole('Usuario');
        });

        // Crea 20 piezas asignadas a usuarios aleatorios(sin imagen).
        Pieza::factory(10)->create([
            'user_id' => $users->random()->id
        ]);
        //Creación de usuarios fijos.
        $admin = User::create([
            'nombre' => 'Admin',
            "email" => 'admin@admin.com',
            "password" => Hash::make('123456'),
        ])->assignRole('Administrador');
        $logoEtc = 'seeders/images/logos/logoEtc.svg';

        if (File::exists(database_path($logoEtc))) {
            $pathFinal = 'logos/logo_etc.svg';
            Storage::disk('public')->put($pathFinal, File::get(database_path($logoEtc)));

            $admin->perfil()->updateOrCreate([
                'logo' => $pathFinal,
                'web' => 'http://www.etc.com',
                'redes_sociales' => [
                    'facebook' => 'https://facebook.com/etcApps'
                ],
            ]);
        }

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


        /*****************Usuarios con perfil y logo******************************/
        $userTitufa = User::create([
            'nombre'=>'Titufas',
            'email' => 'titufas@gmail.com',
            'password' => '123456'])->assignRole('Usuario');
        // Ruta en la carpeta de seeders (viaja con el código).
        $fotoLogo = 'seeders/images/logos/logoTitufa11.png';

        if (File::exists(database_path($fotoLogo))) {
            $pathDestino = 'logos/logo_titufa_seed.png';
            Storage::disk('public')->put($pathDestino, File::get(database_path($fotoLogo)));

            // Actualizamos el perfil que creó el Observer.
            // SEPARAMOS EL CONTENIDO:
// El primer array [] es para BUSCAR (solo el ID).
// El segundo array [] es para LOS DATOS que quieres insertar o actualizar.

            $userTitufa->perfil()->updateOrCreate(
                ['user_id' => $userTitufa->id], // BUSCAMOS SOLO POR EL ID (Esto no da error)
                [                               // AQUÍ VAN TODOS LOS DATOS A GUARDAR
                    'tipo_documento' => 'nif',
                    'documento'      => '12345678T',
                    'movil'          => '606999555',
                    'logo'           => $pathDestino,
                    'descripcion'    => 'Diseño y elaboro fofuchas totalmente personalizadas...',
                    'web'            => 'http://www.titufasFofuchas.com',
                    'redes_sociales' => [
                        'facebook' => 'https://facebook.com/titufasFofuchas'
                    ],
                ]
            );
        }
        /**********************************************************************************/

        //Definir piezas reales asignadas a imagenes físicas.
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
            [
                'nombre' => 'Fofucha Abanderada Infantil',
                'descripcion' => 'Focucha realizada en gomaeva y otros abalorios imitando la imagen facilitada.',
                'imagen' => 'abanderadaInfantil.jpeg',
                'user' => $userTitufa
            ]
        ];
        //Asignar las piezas a sus medias automaticamente.
        foreach ($todasPiezas as $pieza) {

            // 1. Crear la pieza
            $nuevaPieza = Pieza::create([
                'nombre' => $pieza['nombre'],
                'descripcion' => $pieza['descripcion'],
                'user_id' => $pieza['user']->id,
            ]);

            // 2. Copiar la imagen desde database/seeders/images/ a storage/app/public/imagenes/
            $origen = database_path('seeders/images/fotos/' . $pieza['imagen']);
            $destino = 'imagenes/' . $pieza['imagen'];

            if (File::exists($origen)) {
                Storage::disk('public')->put($destino, File::get($origen));
            } else {
                // Para depuración si alguna imagen falta
                dump("Imagen no encontrada en: " . $origen);
            }

            // 3. Crear la media asociada
            Media::create([
                'pieza_id' => $nuevaPieza->id,
                'nombre_original' => $pieza['imagen'],
                'tipo' => 'imagen',
                'path' => $destino, // ESTE path es el que GeminiService usa
                'es_portada' => true,
            ]);
        }

    }
}
