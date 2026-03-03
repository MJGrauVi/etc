<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Pieza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    Protected $model = Media::class;

    public function definition(): array
    {
        return [
            'pieza_id' => Pieza::factory(),
            'tipo' => $this->faker->randomElements(['imagen', 'video']),
            'path' => $this->faker->imageUrl(),
            'order' => $this->faker->randomDigitNotZero(),
            'es_portada' => $this->faker->boolean(),

            ];

    }
}
