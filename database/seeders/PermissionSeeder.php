<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name'=>'ver pieza']);
        Permission::create(['name'=>'editar pieza']);
        Permission::create(['name'=>'borrar pieza']);
        Permission::create(['name'=>'crear pieza']);


        Permission::create(['name'=>'ver perfil']);
        Permission::create(['name'=>'editar perfil']);
        Permission::create(['name'=>'borrar perfil']);
        Permission::create(['name'=>'crear perfil']);

        Permission::create(['name'=>'ver media']);
        Permission::create(['name'=>'editar media']);
        Permission::create(['name'=>'borrar media']);
        Permission::create(['name'=>'crear media']);

        Permission::create(['name'=>'ver user']);
        Permission::create(['name'=>'editar user']);
        Permission::create(['name'=>'borrar user']);
        Permission::create(['name'=>'crear user']);

        Permission::create(['name'=>'ver publicacion']);
        Permission::create(['name'=>'editar publicacion']);
        Permission::create(['name'=>'borrar publicacion']);
        Permission::create(['name'=>'crear publicacion']);

        Permission::create(['name'=>'ver red']);
        Permission::create(['name'=>'editar red']);
        Permission::create(['name'=>'borrar red']);
        Permission::create(['name'=>'crear red']);
    }
}
