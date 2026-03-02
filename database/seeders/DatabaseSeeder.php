<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            PublicacionSeeder::class,
            MediaSeeder::class,
            PiezaSeeder::class,

            RedSeeder::class,

        ]);
    }
}
