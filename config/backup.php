<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Backup Disk & Storage Directory
    |--------------------------------------------------------------------------
    |
    | Backups are stored on the local private storage disk inside
    | storage/app/private/backups, securing the file from public web access.
    |
    */
    'disk' => env('BACKUP_DISK', 'local'),
    'directory' => env('BACKUP_DIRECTORY', 'backups'),

    /*
    |--------------------------------------------------------------------------
    | Retention Policy (in Days)
    |--------------------------------------------------------------------------
    |
    | Number of days to retain backups before automatic pruning cleans them up.
    | A minimum count is preserved to avoid ever deleting all copies.
    |
    */
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 14),
    'keep_minimum' => (int) env('BACKUP_KEEP_MINIMUM', 3),

    /*
    |--------------------------------------------------------------------------
    | Daily Scheduler Time, Frequency & Process Mode
    |--------------------------------------------------------------------------
    |
    | Frequency can be: daily, every_5_minutes, every_15_minutes, hourly, etc.
    | Time of day (24-hour format) when daily automated backups run.
    | On shared hosts, run_in_background is disabled by default for reliability.
    |
    */
    'frequency' => env('BACKUP_FREQUENCY', 'daily'),
    'schedule_time' => env('BACKUP_SCHEDULE_TIME', '02:00'),
    'run_in_background' => (bool) env('BACKUP_RUN_IN_BACKGROUND', false),
];
