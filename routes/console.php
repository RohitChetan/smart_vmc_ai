<?php

use App\Services\SlaMonitorService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(SlaMonitorService::class)->monitor();
})
    ->name('smart-vadodara-sla-monitor')
    ->everyMinute()
    ->withoutOverlapping();