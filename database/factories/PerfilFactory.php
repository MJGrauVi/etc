<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perfil>
 */
class PerfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_documento' => $this->faker->randomElement(['dni', 'cif', 'nie']),
            'documento' => $this->faker->bothify('########?'),
            'movil' => $this->faker->phoneNumber(),
            'logo' => null,
            'descripcion' => $this->faker->sentence(10),
            'web' => $this->faker->url(),
            'redes_sociales' => ['instagram' => $this->faker->url(),'facebook' => $this->faker->url(),
            ],
        ];
    }
}
