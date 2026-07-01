<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands - The Odds API
|--------------------------------------------------------------------------
|
| Sync odds every 5 minutes, live scores every minute.
| Run the scheduler via cron: * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
|
*/

Schedule::command('odds:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/odds-sync.log'));

Schedule::command('odds:live-scores')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/live-scores.log'));
