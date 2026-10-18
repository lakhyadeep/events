<?php

namespace App\Filament\Resources\DatabaseBackups;

use App\Filament\Resources\DatabaseBackups\Pages\ManageDatabaseBackups;
use App\Models\DatabaseBackup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class DatabaseBackupResource extends Resource
{
    protected static ?string $model = DatabaseBackup::class;

    protected static UnitEnum|string|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Database Backups';

    protected static ?string $modelLabel = 'Database Backup';

    protected static ?string $pluralModelLabel = 'Database Backups';

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('filename')
                    ->label('Backup File')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-circle-stack')
                    ->copyable()
                    ->copyMessage('Backup filename copied'),

                TextColumn::make('formatted_size')
                    ->label('Size')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('size_bytes', $direction);
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'completed' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-arrow-path',
                        'failed' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('trigger')
                    ->label('Trigger Source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'manual' => 'primary',
                        'artisan' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone('Asia/Kolkata')
                    ->dateTime('M d, Y h:i:s A')
                    ->description(fn (DatabaseBackup $record): ?string => $record->created_at?->diffForHumans())
                    ->sortable(),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(30)
                    ->tooltip(fn (DatabaseBackup $record): ?string => $record->error_message)
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (DatabaseBackup $record): bool => $record->status === 'completed' && $record->existsOnDisk())
                    ->action(function (DatabaseBackup $record): ?BinaryFileResponse {
                        if (! $record->existsOnDisk()) {
                            return null;
                        }

                        return response()->download($record->getAbsolutePath(), $record->filename);
                    }),

                DeleteAction::make()
                    ->modalHeading('Delete Backup File')
                    ->modalDescription('Are you sure you want to permanently delete this database backup snapshot from disk?'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDatabaseBackups::route('/'),
        ];
    }
}
