<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule overdue deployment check
// Runs every hour to check for overdue deployments
Schedule::command('deployment:check-overdue')
    ->hourly()
    ->withoutOverlapping()
    ->description('Check for overdue deployments and send notifications');

// You can also run it more frequently during business hours
// Schedule::command('deployment:check-overdue')
//     ->everyThirtyMinutes()
//     ->between('8:00', '20:00')
//     ->weekdays();
