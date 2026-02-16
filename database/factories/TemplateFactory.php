<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{

    public function definition(): array
    {
        return [ 'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'preview_path' => null,// o un fake path si quieres
            'config_json' => [
                'color' => $this->faker->safeColorName(),
                'fontSize' => $this->faker->numberBetween(12, 32),
                'layout' => $this->faker->randomElement(['grid', 'list', 'carousel']),
                ],
            'active' => $this->faker->boolean(80), ];
    }
}
