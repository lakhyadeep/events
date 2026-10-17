<?php

namespace App\Console\Commands;

use App\Jobs\BackupDatabaseJob;
use App\Services\Backup\DatabaseBackupService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('db:backup {--queue : Dispatch as an asynchronous background queue job} {--prune= : Retention days override for pruning old backups}')]
#[Description('Create an atomic snapshot backup of the SQLite database and prune old archives')]
class BackupDatabaseCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $shouldQueue = (bool) $this->option('queue');

        if ($shouldQueue) {
            BackupDatabaseJob::dispatch('artisan');
            $this->info('Database backup job has been queued in the background.');

            return self::SUCCESS;
        }

        $this->info('Starting database backup process...');

        try {
            $backup = $backupService->createBackup('artisan');

            $pruneDays = $this->option('prune');
            if ($pruneDays !== null) {
                $prunedCount = $backupService->pruneOldBackups((int) $pruneDays);
                if ($prunedCount > 0) {
                    $this->line("Pruned {$prunedCount} backups older than {$pruneDays} days.");
                }
            }

            $this->newLine();
            $this->info('Database backup completed successfully!');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Filename', $backup->filename],
                    ['Size', $backup->formatted_size],
                    ['Path', $backup->path],
                    ['Absolute Path', $backup->getAbsolutePath()],
                    ['SHA256 Checksum', substr($backup->checksum ?? 'N/A', 0, 16).'...'],
                    ['Completed At', $backup->completed_at?->toDateTimeString() ?? 'Now'],
                ]
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Database backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
