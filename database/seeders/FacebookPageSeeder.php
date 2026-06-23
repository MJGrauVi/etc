<?php

namespace Database\Seeders;

use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacebookPageSeeder extends Seeder
{
    public function run(): void
    {
        $pageId = config('services.facebook.page_id');
        $accessToken = config('services.facebook.page_access_token');

        if (empty($pageId) || empty($accessToken)) {
            $this->command?->warn('No se ha creado pagina Facebook para Titufas: faltan credenciales demo en .env.');
            return;
        }

        $titufas = User::where('email', 'titufas@gmail.com')->first();

        if (!$titufas) {
            $this->command?->warn('No se ha creado pagina Facebook para Titufas: usuario no encontrado.');
            return;
        }

        FacebookPage::updateOrCreate(
            [
                'user_id' => $titufas->id,
                'facebook_page_id' => $pageId,
            ],
            [
                'name' => config('services.facebook.demo_page_name', 'Titufas Fofuchas'),
                'access_token' => $accessToken,
                'is_default' => true,
                'connected_at' => now(),
            ]
        );
    }
}
