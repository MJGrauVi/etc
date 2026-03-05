<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\Pieza;
use App\Models\Publicacion;
use App\Models\User;
use App\Policies\MediaPolicy;
use App\Policies\PiezaPolicy;
use App\Policies\PublicacionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //Registrar la policy para enlazar con el sistema.
    protected $policies = [
        User::class => UserPolicy::class,
        Pieza::class => PiezaPolicy::class,
        Media::class => MediaPolicy::class,
        Publicacion::class => PublicacionPolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
