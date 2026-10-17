<?php

namespace App\Filament\Resources\DatabaseBackups\Pages;

use App\Filament\Resources\DatabaseBackups\DatabaseBackupResource;
use App\Jobs\BackupDatabaseJob;
use App\Models\DatabaseBackup;
use App\Services\Backup\DatabaseBackupService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class ManageDatabaseBackups extends ManageRecords
{
    protected static string $resource = DatabaseBackupResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        $frequency = DatabaseBackupService::getScheduleFrequency();
        $time = DatabaseBackupService::getScheduleTime();
        $retention = DatabaseBackupService::getRetentionDays();

        $freqLabel = match ($frequency) {
            'every_5_minutes' => 'Every 5 Minutes',
            'every_10_minutes' => 'Every 10 Minutes',
            'every_15_minutes' => 'Every 15 Minutes',
            'every_30_minutes' => 'Every 30 Minutes',
            'hourly' => 'Hourly',
            'every_6_hours' => 'Every 6 Hours',
            'twice_daily' => 'Twice Daily (02:00 & 14:00)',
            'weekly' => "Weekly (Sunday at {$time})",
            default => "Daily at {$time} (24h time)",
        };

        return "Automated backups frequency: {$freqLabel}. Snapshots are stored securely in storage/app/private/backups and retained for {$retention} days.";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('Backup Database Now')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalHeading('Create Database Backup Snapshot')
                ->modalDescription('Generate an atomic point-in-time snapshot of the SQLite database. Choose whether to queue the task as a background process or execute it immediately.')
                ->form([
                    Select::make('execution_mode')
                        ->label('Execution Mode')
                        ->options([
                            'background' => 'Background Process (Recommended - Non-blocking Queue Worker)',
                            'immediate' => 'Immediate Execution (Synchronous)',
                        ])
                        ->default('background')
                        ->required()
                        ->helperText('Background mode runs via the Laravel queue worker without keeping your browser waiting.'),
                ])
                ->action(function (array $data, DatabaseBackupService $service): void {
                    $mode = $data['execution_mode'] ?? 'background';

                    if ($mode === 'background') {
                        $record = DatabaseBackup::create([
                            'filename' => 'backup-in-progress-'.now()->format('His').'.sqlite',
                            'disk' => config('backup.disk', 'local'),
                            'path' => 'backups/pending',
                            'size_bytes' => 0,
                            'status' => 'pending',
                            'trigger' => 'manual',
                        ]);

                        BackupDatabaseJob::dispatch('manual', $record->id);

                        Notification::make()
                            ->title('Background Backup Task Dispatched')
                            ->body('Database backup has been queued. The status table will update automatically once completed.')
                            ->info()
                            ->send();
                    } else {
                        try {
                            $backup = $service->createBackup('manual');

                            Notification::make()
                                ->title('Database Backup Created')
                                ->body("Snapshot {$backup->filename} ({$backup->formatted_size}) successfully generated.")
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Backup Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }
                }),

            Action::make('configure_schedule')
                ->label('Schedule & Retention')
                ->icon('heroicon-o-clock')
                ->color('secondary')
                ->modalHeading('Configure Backup Schedule & Retention')
                ->modalDescription('Customize how frequently automated backups execute and how long they are retained. Changes apply immediately to your cron tasks.')
                ->fillForm(fn () => [
                    'frequency' => DatabaseBackupService::getScheduleFrequency(),
                    'schedule_time' => DatabaseBackupService::getScheduleTime(),
                    'retention_days' => DatabaseBackupService::getRetentionDays(),
                    'keep_minimum' => DatabaseBackupService::getKeepMinimum(),
                ])
                ->form([
                    Select::make('frequency')
                        ->label('Backup Frequency')
                        ->options([
                            'every_5_minutes' => 'Every 5 Minutes (Frequent Backup / Active Testing)',
                            'every_15_minutes' => 'Every 15 Minutes',
                            'every_30_minutes' => 'Every 30 Minutes',
                            'hourly' => 'Hourly',
                            'every_6_hours' => 'Every 6 Hours',
                            'twice_daily' => 'Twice Daily (02:00 & 14:00)',
                            'daily' => 'Daily (at specified time)',
                            'weekly' => 'Weekly (Sundays)',
                        ])
                        ->default('daily')
                        ->required()
                        ->live(),

                    TextInput::make('schedule_time')
                        ->label('Daily / Weekly Execution Time (24h format HH:MM)')
                        ->placeholder('e.g. 02:00 or 03:30')
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('frequency') ?? 'daily', ['daily', 'weekly']))
                        ->regex('/^(?:[01]\d|2[0-3]):[0-5]\d$/')
                        ->helperText('Specify the 24-hour time (HH:MM) when daily or weekly backups run.'),

                    TextInput::make('retention_days')
                        ->label('Retention Period (Days)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(365)
                        ->required()
                        ->helperText('Snapshots older than this number of days will be automatically deleted.'),

                    TextInput::make('keep_minimum')
                        ->label('Minimum Snapshots to Keep')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->required()
                        ->helperText('Safety threshold: prune will never delete below this number of recent backups.'),
                ])
                ->action(function (array $data): void {
                    DatabaseBackupService::updateSettings(
                        scheduleTime: $data['schedule_time'] ?? DatabaseBackupService::getScheduleTime(),
                        retentionDays: (int) $data['retention_days'],
                        keepMinimum: (int) $data['keep_minimum'],
                        frequency: $data['frequency'] ?? 'daily',
                    );

                    Notification::make()
                        ->title('Backup Schedule Settings Saved')
                        ->body("Automated backup set to: {$data['frequency']}. Retention: {$data['retention_days']} days.")
                        ->success()
                        ->send();
                }),

            Action::make('sync_storage')
                ->label('Sync with Storage')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (DatabaseBackupService $service): void {
                    $results = $service->syncFromDisk();

                    Notification::make()
                        ->title('Storage Synced')
                        ->body("Discovered {$results['discovered']} new files, cleaned {$results['cleaned']} missing records.")
                        ->info()
                        ->send();
                }),
        ];
    }
}
