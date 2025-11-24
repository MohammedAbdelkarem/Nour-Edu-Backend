<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


//User
Schedule::command('app:ban-remove')
    ->everyMinute()
    ->runInBackground()
    ->withoutOverlapping();
Schedule::command('app:delete-unverified')
    ->everySixHours()
    ->runInBackground()
    ->withoutOverlapping();

//Others
Schedule::command('app:delete-unverified')
    ->everySixHours()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('banner:remove')
    ->everyTwoMinutes()
    ->runInBackground()
    ->withoutOverlapping();


Schedule::command('app:delete-otp')
    ->hourly()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:delete-tokens')
    ->hourly()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:payment-remover')
    ->everyTwoHours()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:payment-remover')
    ->everyTwoHours()
    ->runInBackground()
    ->withoutOverlapping();

//e-learning




