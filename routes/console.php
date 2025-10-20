<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule auto-assignment to run every 5 minutes
Schedule::command('requests:auto-assign')->everyFiveMinutes()
    ->name('Auto-assign pending requests')
    ->description('Automatically assign pending help requests when admin is inactive');
