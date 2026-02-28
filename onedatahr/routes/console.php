<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Task Scheduler: Sinkronisasi API Kompetensi Wadja Institute (Setiap hari pukul 01:00 pagi)
Schedule::command('wadja:sync-competency')->dailyAt('01:00');
