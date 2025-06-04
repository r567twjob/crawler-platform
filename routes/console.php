<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Schedule::command('inspire')->everyMinute()->purpose('Run the scheduled tasks');
// Schedule::call(function () {
//     Log::info("This is a scheduled task running every minute.");
// })->everyMinute();

// Schedule::command('inspire')
//     ->everyMinute();

Schedule::command('app:process-job')->everyMinute();
Schedule::command('app:daily-clear-cache')->everyTwoHours();
