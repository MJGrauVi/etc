<?php

namespace Database\Factories;

use App\Models\Red;
use Illuminate\Database\Eloquent\Factories\Factory;

class RedFactory extends Factory
{
    public function definition(): array{
        /*$redes = [
            ['nombre' => 'Twitter', 'url_base' => 'https://twitter.com/'],
            ['nombre' => 'Instagram', 'url_base' => 'https://instagram.com/'],
            ['nombre' => 'Facebook', 'url_base' => 'https://facebook.com/'],
            ['nombre' => 'LinkedIn', 'url_base' => 'https://linkedin.com/'],
            ['nombre' => 'TikTok', 'url_base' => 'https://tiktok.com/'],
        ];

        $red = $this->faker->randomElement($redes);

        return [
            'nombre' => $red['nombre'],
            'url_base' => $red['url_base'],
        ];*/

        return Red::factory()->count(5)->create();
    }
}
