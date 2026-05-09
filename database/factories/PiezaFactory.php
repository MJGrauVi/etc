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
            'nombre' => $this->faker->randomElement([
                'Lampara artesanal de madera',
                'Mesa auxiliar decorada',
                'Figura personalizada en goma eva',
                'Cuadro decorativo floral',
                'Caja de madera pintada a mano',
                'Centro de mesa artesanal',
                'Marco decorativo personalizado',
                'Estanteria rustica',
                'Macetero decorativo',
                'Bandeja artesanal',
            ]),
            'descripcion' => $this->faker->paragraph(3),
            /*'categoria' =>$this->faker->randomElement(['general', 'carpintero', 'escultor', 'herrero']),*/
            'categoria' => $this->faker->randomElement([
                'Decoracion',
                'Madera',
                'Goma eva',
                'Hogar',
                'Personalizados',
            ]),
            'precio' => $this->faker->randomFloat(2, 10, 100),
            ];
    }
}
