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
$backupFrequency = DatabaseBackupService::getScheduleFrequency();

$backupSchedule = Schedule::command('db:backup')
    ->withoutOverlapping(60);

match ($backupFrequency) {
    'every_5_minutes' => $backupSchedule->everyFiveMinutes(),
    'every_10_minutes' => $backupSchedule->everyTenMinutes(),
    'every_15_minutes' => $backupSchedule->everyFifteenMinutes(),
    'every_30_minutes' => $backupSchedule->everyThirtyMinutes(),
    'hourly' => $backupSchedule->hourly(),
    'every_6_hours' => $backupSchedule->everySixHours(),
    'twice_daily' => $backupSchedule->twiceDaily(2, 14),
    'weekly' => $backupSchedule->weeklyOn(0, $backupTime),
    default => $backupSchedule->dailyAt($backupTime),
};

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

/*
|--------------------------------------------------------------------------
| Automated Analytics Log Pruning
|--------------------------------------------------------------------------
|
| Retains 90 days of page view and user activity tracking records to keep
| the database lean and performant.
|
*/
Schedule::command('analytics:prune --days=90')
    ->dailyAt('03:30')
    ->withoutOverlapping(30);
