<?php

namespace App\Providers;

use App\Faker\EtcFakerProvider;
use Faker\Factory;
use Generator;
use Illuminate\Support\ServiceProvider;

class EtcFakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create(env('APP_FAKER_LOCALE'));
            $faker->addProvider(new EtcFakerProvider($faker));
            return $faker;
        });
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
