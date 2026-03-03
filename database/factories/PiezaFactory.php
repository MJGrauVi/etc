<?php

namespace Database\Factories;

use App\Models\Pieza;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pieza>
 */
class PiezaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    Protected $model = Pieza::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nombre' => $this->faker->name(),
            'descripcion' => $this->faker->paragraph(3),
            /*'categoria' =>$this->faker->randomElement(['general', 'carpintero', 'escultor', 'herrero']),*/
            'categoria' => $this->faker->word(),
            'precio' => $this->faker->randomFloat(2, 10, 100),
            ];
    }
}
