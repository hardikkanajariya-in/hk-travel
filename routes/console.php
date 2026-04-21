<?php

use App\Console\Commands\PruneAuditLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune the audit log nightly using the configured retention window.
Schedule::command(PruneAuditLog::class, ['--days' => (int) config('hk.audit.retention_days', 180)])
    ->dailyAt('03:15')
    ->onOneServer();
