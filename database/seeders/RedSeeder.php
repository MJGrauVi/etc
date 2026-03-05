<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Red;

class RedSeeder extends Seeder
{
    public function run(): void
    {
        $redes = [
            [
                'nombre' => 'Twitter',
                'url_base' => 'https://twitter.com/'
            ],
            [
                'nombre' => 'Instagram',
                'url_base' => 'https://instagram.com/'
            ],
            [
                'nombre' => 'Facebook',
                'url_base' => 'https://facebook.com/'
            ],
            [
                'nombre' => 'LinkedIn',
                'url_base' => 'https://linkedin.com/'
            ],
            [
                'nombre' => 'TikTok',
                'url_base' => 'https://tiktok.com/'
            ]
        ];

        foreach ($redes as $red) {
            Red::create($red);
        }
    }
}
