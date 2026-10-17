<?php

namespace Tests\Feature;

use App\Filament\Resources\DatabaseBackups\Pages\ManageDatabaseBackups;
use App\Jobs\BackupDatabaseJob;
use App\Models\DatabaseBackup;
use App\Models\User;
use App\Services\Backup\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_service_can_create_database_backup(): void
    {
        $service = app(DatabaseBackupService::class);

        $backup = $service->createBackup('artisan');

        $this->assertInstanceOf(DatabaseBackup::class, $backup);
        $this->assertEquals('completed', $backup->status);
        $this->assertEquals('artisan', $backup->trigger);
        $this->assertGreaterThan(0, $backup->size_bytes);
        $this->assertNotEmpty($backup->checksum);
        $this->assertTrue($backup->existsOnDisk());
        $this->assertDatabaseHas('database_backups', [
            'id' => $backup->id,
            'status' => 'completed',
        ]);
    }

    public function test_pruning_removes_expired_backups_while_preserving_minimum_count(): void
    {
        $service = app(DatabaseBackupService::class);

        // Create 5 fake backups with varying ages
        $disk = Storage::disk('local');
        for ($i = 1; $i <= 5; $i++) {
            $filename = "test-backup-{$i}.sqlite";
            $path = "backups/{$filename}";
            $disk->put($path, "database content {$i}");

            DatabaseBackup::create([
                'filename' => $filename,
                'disk' => 'local',
                'path' => $path,
                'size_bytes' => strlen("database content {$i}"),
                'status' => 'completed',
                'trigger' => 'scheduled',
                'checksum' => hash('sha256', "database content {$i}"),
                'completed_at' => now()->subDays($i * 5),
                'created_at' => now()->subDays($i * 5),
            ]);
        }

        $this->assertEquals(5, DatabaseBackup::count());

        // Prune older than 10 days, keeping minimum 2
        $pruned = $service->pruneOldBackups(keepDays: 10, keepMinimum: 2);

        $this->assertEquals(3, $pruned);
        $this->assertEquals(2, DatabaseBackup::count());
    }

    public function test_sync_from_disk_discovers_untracked_files_and_cleans_missing_records(): void
    {
        $service = app(DatabaseBackupService::class);
        $disk = Storage::disk('local');

        // Put an untracked backup file onto disk
        $disk->put('backups/untracked-database.sqlite', 'sqlite data payload');

        // Create a DB record whose file does NOT exist on disk
        $missing = DatabaseBackup::create([
            'filename' => 'missing-file.sqlite',
            'disk' => 'local',
            'path' => 'backups/missing-file.sqlite',
            'size_bytes' => 100,
            'status' => 'completed',
            'trigger' => 'manual',
            'completed_at' => now(),
        ]);

        $results = $service->syncFromDisk();

        $this->assertEquals(1, $results['discovered']);
        $this->assertEquals(1, $results['cleaned']);

        $this->assertDatabaseHas('database_backups', [
            'filename' => 'untracked-database.sqlite',
        ]);
        $this->assertDatabaseMissing('database_backups', [
            'id' => $missing->id,
        ]);
    }

    public function test_model_deletion_removes_physical_file_from_disk(): void
    {
        $disk = Storage::disk('local');
        $path = 'backups/removable.sqlite';
        $disk->put($path, 'dummy database bytes');

        $backup = DatabaseBackup::create([
            'filename' => 'removable.sqlite',
            'disk' => 'local',
            'path' => $path,
            'size_bytes' => 20,
            'status' => 'completed',
            'trigger' => 'manual',
            'completed_at' => now(),
        ]);

        $this->assertTrue($disk->exists($path));

        $backup->delete();

        $this->assertFalse($disk->exists($path));
        $this->assertDatabaseMissing('database_backups', ['id' => $backup->id]);
    }

    public function test_backup_database_job_executes_successfully(): void
    {
        $job = new BackupDatabaseJob('scheduled');
        $job->handle(app(DatabaseBackupService::class));

        $this->assertDatabaseHas('database_backups', [
            'trigger' => 'scheduled',
            'status' => 'completed',
        ]);
    }

    public function test_backup_database_artisan_command_runs_synchronously(): void
    {
        $exitCode = Artisan::call('db:backup');

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('database_backups', [
            'trigger' => 'artisan',
            'status' => 'completed',
        ]);
    }

    public function test_backup_database_artisan_command_queues_job_when_requested(): void
    {
        Queue::fake();

        $exitCode = Artisan::call('db:backup', ['--queue' => true]);

        $this->assertEquals(0, $exitCode);
        Queue::assertPushed(BackupDatabaseJob::class, function ($job) {
            return $job->trigger === 'artisan';
        });
    }

    public function test_admin_user_can_access_filament_database_backups_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/database-backups');

        $response->assertStatus(200);
        $response->assertSee('Database Backups');
    }

    public function test_filament_action_triggers_background_backup(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageDatabaseBackups::class)
            ->callAction('create_backup', [
                'execution_mode' => 'background',
            ])
            ->assertNotified();

        Queue::assertPushed(BackupDatabaseJob::class);
        $this->assertDatabaseHas('database_backups', [
            'status' => 'pending',
            'trigger' => 'manual',
        ]);
    }

    public function test_filament_action_can_download_completed_backup(): void
    {
        $user = User::factory()->create();
        $service = app(DatabaseBackupService::class);
        $backup = $service->createBackup('manual');

        $component = Livewire::actingAs($user)
            ->test(ManageDatabaseBackups::class);

        $response = $component->callTableAction('download', $backup);

        $response->assertFileDownloaded($backup->filename);
    }

    public function test_dynamic_backup_settings_can_be_configured_and_retrieved(): void
    {
        DatabaseBackupService::updateSettings('04:45', 30, 5, 'every_5_minutes');

        $this->assertEquals('04:45', DatabaseBackupService::getScheduleTime());
        $this->assertEquals('every_5_minutes', DatabaseBackupService::getScheduleFrequency());
        $this->assertEquals(30, DatabaseBackupService::getRetentionDays());
        $this->assertEquals(5, DatabaseBackupService::getKeepMinimum());
    }

    public function test_filament_action_can_update_backup_schedule_settings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageDatabaseBackups::class)
            ->callAction('configure_schedule', [
                'frequency' => 'daily',
                'schedule_time' => '03:15',
                'retention_days' => 21,
                'keep_minimum' => 4,
            ])
            ->assertNotified()
            ->assertHasNoActionErrors();

        $this->assertEquals('daily', DatabaseBackupService::getScheduleFrequency());
        $this->assertEquals('03:15', DatabaseBackupService::getScheduleTime());
        $this->assertEquals(21, DatabaseBackupService::getRetentionDays());
        $this->assertEquals(4, DatabaseBackupService::getKeepMinimum());
    }
}
