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
use Illuminate\Support\Facades\Gate;
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
     * Usar esta opción con Laravel 11.
     */
/*    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Pieza::class, PiezaPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Publicacion::class, PublicacionPolicy::class);
    }*/
}
