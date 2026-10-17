<?php

namespace App\Models;

use Database\Factories\DatabaseBackupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Model
{
    /** @use HasFactory<DatabaseBackupFactory> */
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk',
        'path',
        'size_bytes',
        'status',
        'trigger',
        'checksum',
        'error_message',
        'completed_at',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Delete the associated physical backup file upon model deletion.
     */
    protected static function booted(): void
    {
        static::deleted(function (DatabaseBackup $backup): void {
            $backup->deleteFile();
        });
    }

    /**
     * Formatted human-readable file size (e.g., 2.34 MB).
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_bytes;

        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $size = $bytes / (1024 ** $power);

        return number_format($size, $power > 0 ? 2 : 0).' '.$units[$power];
    }

    /**
     * Determine if the physical backup file exists on the designated disk.
     */
    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * Get the absolute path to the backup file on the host filesystem.
     */
    public function getAbsolutePath(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    /**
     * Remove the physical backup file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->existsOnDisk()) {
            return Storage::disk($this->disk)->delete($this->path);
        }

        return false;
    }
}
