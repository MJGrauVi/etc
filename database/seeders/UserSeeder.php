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
        // ── Administradores ──────────────────────────────────────────
        $admin = User::create([
            'nombre'            => 'Admin',
            'email'             => 'admin@admin.com',
            'password'          => Hash::make('123456'),
            'email_verified_at' => now(),
        ])->assignRole('Administrador');

        // ── Administrador Etc-Apps ──────────────────────────────────────────
        $admon = User::create([
            'nombre'            => 'Admon-Etc',
            'email'             => 'etc-apps@proton.me',
            'password'          => Hash::make('123456'),
            'email_verified_at' => now(),
        ])->assignRole('Administrador');

        $logoEtc = 'seeders/images/logos/logoEtc.svg';

        if (File::exists(database_path($logoEtc))) {
            $pathFinal = 'logos/logo_etc.svg';
            Storage::disk('public')->put($pathFinal, File::get(database_path($logoEtc)));

            //CAMBIO 1: updateOrCreate con condición de búsqueda separada
            $admon->perfil()->updateOrCreate(
                ['user_id' => $admon->id],  // condición de búsqueda
                [                           // datos a guardar
                    'logo' => $pathFinal,
                    'descripcion' => 'ETC Apps desarrolla soluciones web orientadas a pequeños negocios, artesanos y profesionales que necesitan mejorar su presencia digital de forma sencilla. La plataforma ayuda a gestionar piezas, generar contenido con inteligencia artificial y preparar publicaciones para redes sociales desde un único entorno.',
                    'web'  => 'http://www.etc-apps.com',
                    'redes_sociales' => [
                        'facebook' => 'https://facebook.com/etcApps'
                    ],
                ]
            );
        }

        // ── Usuarios fijos ───────────────────────────────────────────
        $userNormal = User::create([
            'nombre'            => 'Usuario',
            'email'             => 'usuario@usuario.com',
            'password'          => Hash::make('123456'),
            'email_verified_at' => now(),
        ])->assignRole('Usuario');

        $invitado = User::create([
            'nombre'            => 'Invitado',
            'email'             => 'invitado@invitado.com',
            'password'          => Hash::make('123456'),
            'email_verified_at' => now(),
        ])->assignRole('Invitado'); // Este rol debe existir en RoleSeeder


        // ── Titufas ──────────────────────────────────────────────────
        // CAMBIO 2: password hasheada con Hash::make
        $userTitufa = User::create([
            'nombre'            => 'Titufas',
            'email'             => 'titufas@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('123456'),
        ])->assignRole('Usuario');

        $fotoLogo = 'seeders/images/logos/logoTitufa11.png';

        if (File::exists(database_path($fotoLogo))) {
            $pathDestino = 'logos/logo_titufa_seed.png';
            Storage::disk('public')->put($pathDestino, File::get(database_path($fotoLogo)));

            $userTitufa->perfil()->updateOrCreate(
                ['user_id' => $userTitufa->id],
                [
                    'tipo_documento' => 'nif',
                    'documento'      => '12345678T',
                    'movil'          => '606000779',
                    'logo'           => $pathDestino,
                    'descripcion'    => 'En titufas fofuchas creamos muñecos personalizados con los rasgos, deporte, profesión o cualquier rasgo que identifique a la persona que quieres agasajar, solo tienes que enviarnos unas imagenes y tendrás tu muñeco personalizado. ',
                    'web'            => 'http://www.titufasFofuchas.com',
                    'redes_sociales' => [
                        'facebook' => 'https://facebook.com/titufasFofuchas'
                    ],
                ]
            );
        }

        // ── Piezas con imágenes reales ───────────────────────────────
        $todasPiezas = [
            [
                'nombre'      => 'Escalera Artesanal de Color',
                'descripcion' => 'Una pieza única pintada a mano y tronco lateral.',
                'imagen'      => 'escaleraColor.jpeg',
                'user'        => $invitado
            ],
            [
                'nombre'      => 'Rinconera tronco',
                'descripcion' => 'Sofa rinconera tallado en el tronco de un arbol.',
                'imagen'      => 'rinconTronco.jpeg',
                'user'        => $invitado
            ],
            [
                'nombre'      => 'Lavabo madera con flores',
                'descripcion' => 'Lavabo rústico con acabados florales incrustados en la madera.',
                'imagen'      => 'lavaboColor.jpeg',
                'user'        => $admin
            ],
            [
                'nombre'      => 'Mesa motivos Mariposa',
                'descripcion' => 'Mesa rustica con sillas a juego, motivos coloridos y diseño mariposas.',
                'imagen'      => 'mesaMariposa.jpeg',
                'user'        => $admin
            ],
            [
                'nombre'      => 'Peldaños desiguales en madera.',
                'descripcion' => 'Peldaños de escalera de madera con incrustaciones en nogal acabado exposi brillo.',
                'imagen'      => 'peldanosEscalera.jpeg',
                'user'        => $admin
            ],
            [
                'nombre'      => 'Ducha en tronco arbol',
                'descripcion' => 'Creación de ducha tallada en tronco centenario acabada con mampara curva transparente.',
                'imagen'      => 'duchaMadera.jpeg',
                'user'        => $userNormal
            ],
            [
                'nombre'      => 'Lámpara de Suelo funcional',
                'descripcion' => 'Iluminación cálida para salones con mesita y estante.',
                'imagen'      => 'lamparaSuelo.jpeg',
                'user'        => $userNormal
            ],
            [
                'nombre'      => 'Fofucha Abanderada Infantil',
                'descripcion' => 'Focucha realizada en gomaeva y otros abalorios imitando la imagen facilitada.',
                'imagen'      => 'abanderadaInfantil.jpeg',
                'user'        => $userTitufa
            ],
            [
                'nombre'      => 'Focucha Gitanilla',
                'descripcion' => 'Focucha realizada en gomaeva de la comparsa contrabandista.',
                'imagen'      => 'gitanilla1.jpeg',
                'imagenes'    => ['gitanilla1.jpeg', 'gitanillaLateral.jpeg'],
                'user'        => $userTitufa
            ],
            [
                'nombre'      => 'Fofucho Minion',
                'descripcion' => 'Muñeco realizado en gomaeva..',
                'imagen'      => 'miniMarron.jpeg',
                'user'        => $userTitufa
            ]
        ];

        foreach ($todasPiezas as $pieza) {
            $nuevaPieza = Pieza::create([
                'nombre'      => $pieza['nombre'],
                'descripcion' => $pieza['descripcion'],
                'user_id'     => $pieza['user']->id,
            ]);

            $imagenes = $pieza['imagenes'] ?? [$pieza['imagen']];

            foreach ($imagenes as $indice => $imagen) {
                $origen  = database_path('seeders/images/fotos/' . $imagen);
                $destino = 'imagenes/' . $imagen;

                if (File::exists($origen)) {
                    Storage::disk('public')->put($destino, File::get($origen));
                } else {
                    $this->command?->warn("Imagen no encontrada en: " . $origen);
                }

                Media::create([
                    'pieza_id'       => $nuevaPieza->id,
                    'nombre_original' => $imagen,
                    'tipo'           => 'imagen',
                    'path'           => $destino,
                    'es_portada'     => $indice === 0,
                ]);
            }
        }
    }
}
