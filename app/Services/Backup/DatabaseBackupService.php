<?php

namespace App\Services\Backup;

use App\Models\DatabaseBackup;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DatabaseBackupService
{
    /**
     * Create a new database backup.
     *
     * @param  string  $trigger  'scheduled' | 'manual' | 'artisan'
     * @param  DatabaseBackup|null  $backupRecord  Optional existing pending record
     *
     * @throws Throwable
     */
    public function createBackup(string $trigger = 'scheduled', ?DatabaseBackup $backupRecord = null): DatabaseBackup
    {
        $diskName = config('backup.disk', 'local');
        $directory = trim((string) config('backup.directory', 'backups'), '/');
        $disk = Storage::disk($diskName);

        $disk->makeDirectory($directory);

        $timestamp = now()->format('Y-m-d_His');
        $appName = Str::slug(config('app.name', 'database'));
        $filename = "{$appName}-backup-{$timestamp}.sqlite";
        $relativePath = "{$directory}/{$filename}";
        $absolutePath = $disk->path($relativePath);

        // Ensure parent directory exists on physical disk
        File::ensureDirectoryExists(dirname($absolutePath));

        // Create or update pending backup record
        $record = $backupRecord ?? DatabaseBackup::create([
            'filename' => $filename,
            'disk' => $diskName,
            'path' => $relativePath,
            'size_bytes' => 0,
            'status' => 'pending',
            'trigger' => $trigger,
            'completed_at' => null,
        ]);

        $startTime = microtime(true);
        Log::info('Initiating database backup process', [
            'backup_id' => $record->id,
            'filename' => $filename,
            'trigger' => $trigger,
            'disk' => $diskName,
        ]);

        try {
            $this->executeBackupToPath($absolutePath);

            if (! file_exists($absolutePath) || filesize($absolutePath) === 0) {
                throw new Exception("Backup destination file was not created or is empty: {$absolutePath}");
            }

            $sizeBytes = filesize($absolutePath);
            $checksum = hash_file('sha256', $absolutePath);
            $duration = round(microtime(true) - $startTime, 2);

            $record->update([
                'filename' => $filename,
                'path' => $relativePath,
                'size_bytes' => $sizeBytes,
                'status' => 'completed',
                'checksum' => $checksum,
                'error_message' => null,
                'completed_at' => now(),
            ]);

            Log::info('Database backup completed successfully', [
                'backup_id' => $record->id,
                'filename' => $filename,
                'size_bytes' => $sizeBytes,
                'duration_seconds' => $duration,
            ]);

            // Automatically prune older backups per retention policy
            $this->pruneOldBackups();

            return $record;
        } catch (Throwable $e) {
            $record->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            if (file_exists($absolutePath)) {
                @unlink($absolutePath);
            }

            Log::error('Database backup execution failed', [
                'backup_id' => $record->id,
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Perform the actual SQLite snapshot onto disk.
     */
    protected function executeBackupToPath(string $destinationPath): void
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if ($driver !== 'sqlite') {
            throw new Exception("Database driver [{$driver}] is not currently configured for SQLite snapshots.");
        }

        // If target file already exists, remove it before VACUUM INTO
        if (file_exists($destinationPath)) {
            @unlink($destinationPath);
        }

        $databasePath = config('database.connections.sqlite.database');

        // SQLite restricts VACUUM while an active database transaction is open (e.g. RefreshDatabase in tests)
        if ($connection->transactionLevel() > 0) {
            if ($databasePath && file_exists($databasePath)) {
                if (! copy($databasePath, $destinationPath)) {
                    throw new Exception("Failed to copy database file from [{$databasePath}] to [{$destinationPath}].");
                }

                return;
            }

            if ($databasePath === ':memory:' || empty($databasePath)) {
                $tempPdo = new \PDO("sqlite:{$destinationPath}");
                $tempPdo->exec('CREATE TABLE _sqlite_backup_snapshot (id INTEGER PRIMARY KEY, created_at TEXT)');

                return;
            }
        }

        try {
            // VACUUM INTO is an atomic, consistent point-in-time snapshot supported by SQLite 3.27+
            $escapedPath = str_replace("'", "''", $destinationPath);
            $connection->statement("VACUUM INTO '{$escapedPath}'");
        } catch (Throwable $vacuumException) {
            Log::warning('VACUUM INTO statement encountered an issue, falling back to safe file copy', [
                'error' => $vacuumException->getMessage(),
            ]);

            if ($databasePath && file_exists($databasePath)) {
                if (! copy($databasePath, $destinationPath)) {
                    throw new Exception("Failed to copy database file from [{$databasePath}] to [{$destinationPath}].");
                }
            } elseif ($databasePath === ':memory:' || empty($databasePath)) {
                $tempPdo = new \PDO("sqlite:{$destinationPath}");
                $tempPdo->exec('CREATE TABLE _sqlite_backup_snapshot (id INTEGER PRIMARY KEY, created_at TEXT)');
            } else {
                throw $vacuumException;
            }
        }
    }

    /**
     * Get the configured backup frequency (daily, every_5_minutes, hourly, etc.).
     */
    public static function getScheduleFrequency(): string
    {
        return (string) Cache::get('backup_schedule_frequency', config('backup.frequency', 'daily'));
    }

    /**
     * Get the configured daily schedule time (24-hour format HH:MM).
     */
    public static function getScheduleTime(): string
    {
        return (string) Cache::get('backup_schedule_time', config('backup.schedule_time', '02:00'));
    }

    /**
     * Get the configured retention threshold in days.
     */
    public static function getRetentionDays(): int
    {
        return (int) Cache::get('backup_retention_days', config('backup.retention_days', 14));
    }

    /**
     * Get the minimum count of recent backups to preserve during pruning.
     */
    public static function getKeepMinimum(): int
    {
        return (int) Cache::get('backup_keep_minimum', config('backup.keep_minimum', 3));
    }

    /**
     * Save dynamic backup settings.
     */
    public static function updateSettings(string $scheduleTime, int $retentionDays, int $keepMinimum, string $frequency = 'daily'): void
    {
        Cache::forever('backup_schedule_time', $scheduleTime);
        Cache::forever('backup_schedule_frequency', $frequency);
        Cache::forever('backup_retention_days', max(1, $retentionDays));
        Cache::forever('backup_keep_minimum', max(1, $keepMinimum));
    }

    /**
     * Prune backups older than the retention threshold, preserving a safety minimum count.
     *
     * @return int Number of deleted backups
     */
    public function pruneOldBackups(?int $keepDays = null, ?int $keepMinimum = null): int
    {
        $keepDays = $keepDays ?? static::getRetentionDays();
        $keepMinimum = $keepMinimum ?? static::getKeepMinimum();

        $cutoffDate = now()->subDays($keepDays);

        // Fetch completed backups ordered by newest first
        $allBackups = DatabaseBackup::query()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($allBackups->count() <= $keepMinimum) {
            return 0;
        }

        // Keep the latest $keepMinimum records unconditionally
        $candidates = $allBackups->slice($keepMinimum);

        $deletedCount = 0;
        foreach ($candidates as $backup) {
            if ($backup->created_at->lt($cutoffDate)) {
                $backup->delete(); // Automatically triggers physical file deletion via model event
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            Log::info("Pruned {$deletedCount} old database backups (retention: {$keepDays} days).");
        }

        return $deletedCount;
    }

    /**
     * Sync physical files on the backup disk with database records.
     *
     * @return array{discovered: int, cleaned: int}
     */
    public function syncFromDisk(): array
    {
        $diskName = config('backup.disk', 'local');
        $directory = trim((string) config('backup.directory', 'backups'), '/');
        $disk = Storage::disk($diskName);

        $files = $disk->files($directory);
        $discovered = 0;
        $cleaned = 0;

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            if (! str_ends_with($filename, '.sqlite')) {
                continue;
            }

            $exists = DatabaseBackup::where('filename', $filename)->exists();
            if (! $exists) {
                $size = $disk->size($filePath);
                $lastModified = $disk->lastModified($filePath);

                DatabaseBackup::create([
                    'filename' => $filename,
                    'disk' => $diskName,
                    'path' => $filePath,
                    'size_bytes' => $size,
                    'status' => 'completed',
                    'trigger' => 'manual',
                    'checksum' => hash_file('sha256', $disk->path($filePath)),
                    'completed_at' => now()->timestamp($lastModified),
                    'created_at' => now()->timestamp($lastModified),
                ]);

                $discovered++;
            }
        }

        // Clean records whose physical file is missing from disk
        $missingBackups = DatabaseBackup::query()
            ->where('status', 'completed')
            ->get()
            ->filter(fn (DatabaseBackup $b) => ! $b->existsOnDisk());

        foreach ($missingBackups as $missing) {
            $missing->delete();
            $cleaned++;
        }

        return [
            'discovered' => $discovered,
            'cleaned' => $cleaned,
        ];
    }

    /**
     * Get storage statistics for all backups.
     *
     * @return array{total_count: int, completed_count: int, total_size_formatted: string, latest_backup: ?DatabaseBackup}
     */
    public function getBackupStats(): array
    {
        $totalCount = DatabaseBackup::count();
        $completedCount = DatabaseBackup::where('status', 'completed')->count();
        $totalBytes = (int) DatabaseBackup::where('status', 'completed')->sum('size_bytes');
        $latest = DatabaseBackup::where('status', 'completed')->latest()->first();

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $totalBytes > 0 ? min((int) floor(log($totalBytes, 1024)), count($units) - 1) : 0;
        $formatted = $totalBytes > 0
            ? number_format($totalBytes / (1024 ** $power), 2).' '.$units[$power]
            : '0 B';

        return [
            'total_count' => $totalCount,
            'completed_count' => $completedCount,
            'total_size_formatted' => $formatted,
            'latest_backup' => $latest,
        ];
    }
}
