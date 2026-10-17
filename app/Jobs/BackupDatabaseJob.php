<?php

namespace App\Jobs;

use App\Models\DatabaseBackup;
use App\Services\Backup\DatabaseBackupService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class BackupDatabaseJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 180;

    /**
     * The number of seconds after which the job's unique lock expires.
     */
    public int $uniqueFor = 300;

    /**
     * The backoff delays in seconds between retries.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 30];

    /**
     * Create a new job instance.
     *
     * @param  string  $trigger  'scheduled' | 'manual' | 'artisan'
     * @param  int|null  $backupId  Pre-created pending backup record ID
     */
    public function __construct(
        public string $trigger = 'scheduled',
        public ?int $backupId = null
    ) {}

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'database-backup-singleton';
    }

    /**
     * Execute the job.
     */
    public function handle(DatabaseBackupService $backupService): void
    {
        $record = $this->backupId ? DatabaseBackup::find($this->backupId) : null;

        Log::info('Background database backup job started processing', [
            'trigger' => $this->trigger,
            'backup_id' => $this->backupId,
        ]);

        $backupService->createBackup($this->trigger, $record);
    }

    /**
     * Handle a terminal failure of the job.
     */
    public function failed(?Throwable $exception): void
    {
        if ($this->backupId) {
            $record = DatabaseBackup::find($this->backupId);
            $record?->update([
                'status' => 'failed',
                'error_message' => $exception?->getMessage() ?? 'Job failed unexpectedly',
                'completed_at' => now(),
            ]);
        }

        Log::error('Background database backup job failed fatally', [
            'backup_id' => $this->backupId,
            'trigger' => $this->trigger,
            'error' => $exception?->getMessage(),
        ]);
    }
}
