<?php

use App\Services\Backup\DatabaseBackupService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Automated Daily Database Backup
|--------------------------------------------------------------------------
|
| Executes the database backup at the configured schedule time (default 02:00 AM),
| ensuring no lock overlap while automatically pruning expired snapshots.
| On shared hosting where proc_open may be restricted, runs safely within the cron run.
|
*/
$backupTime = DatabaseBackupService::getScheduleTime();
$backupSchedule = Schedule::command('db:backup')
    ->dailyAt($backupTime)
    ->withoutOverlapping(60);

if ((bool) config('backup.run_in_background', false) && function_exists('proc_open')) {
    $backupSchedule->runInBackground();
}

/*
|--------------------------------------------------------------------------
| Shared Hosting Background Queue Worker
|--------------------------------------------------------------------------
|
| On shared hosting where supervisor daemons are not available, process
| queued background jobs every minute until the queue is drained.
|
*/
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping(2);
