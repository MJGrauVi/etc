<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


//Esto le dice a Laravel: "Cada mañana a las 8:00 ejecuta mi comando de alertas."
Schedule::command('app:revisar-vencimientos')->dailyAt('08:00');

