<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Usuario']);
        Role::create(['name' => 'Invitado']);

        $admin = Role::findByName( 'Administrador');
        $admin->givePermissionTo(Permission::all());

        $usuario = Role::findByName('Usuario');
        $usuario->givePermissionTo([
            'ver pieza',
            'crear pieza',
            'editar pieza',
            'borrar pieza',
            'ver media',
            'crear media',
            'editar media',
            'borrar media',
            'ver publicacion',
            'crear publicacion',
            'editar publicacion',
            'borrar publicacion',

            ]);
        $invitado = Role::findByName('Invitado');
        $invitado->givePermissionTo([
            'ver pieza',
            'ver publicacion',
            'ver media',
            ]);
    }
}
