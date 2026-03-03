<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pieza;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(10)->create()->each(function ($user) {
            $user->assignRole('Usuario');
        });

        // Crea 20 piezas asignadas a usuarios aleatorios
        Pieza::factory(20)->create([
            'user_id' => $users->random()->id
        ]);

        User::create([
            'nombre'=>'Administrador',
            "email"=>'administracion@administracion.com',
            "password"=>Hash::make('123456'),
        ])->assignRole('Administrador');

        User::create([
            'nombre'=>'Usuario',
            'email'=>'usuario@usuario.com',
            "password"=>Hash::make('123456'),
        ])->assignRole('Usuario');

        User::create([
            'nombre'=>'Invitado',
            "email"=>'invitado@invitado.com',
            "password"=>Hash::make('123456'),
        ])->assignRole('Invitado');
    }
}
