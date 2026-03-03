<?php

namespace Database\Factories;

use App\Models\Pieza;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publicacion>
 */
class PublicacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    //
    protected $model = Publicacion::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pieza_id' => Pieza::factory(),
            'nombre' => $this->faker->sentence(),
            'descripcion' => $this->faker->paragraph(4),
            'estado' => 'borrador',
            'publicado_en' => null,
            ];
    }
}
