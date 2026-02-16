<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        //Ejecuta seeders personalizados.
        $this->call([
            TemplateSeeder::class,
        ]);
        //Crea usuario de prueba.
        User::factory()->create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
