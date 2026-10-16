<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\ParticipantResource;
use App\Models\Event;
use App\Services\ParticipantCsvSyncService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('syncCsv')
                ->label('Sync from CSV')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->modalHeading('Sync Puja Committees from CSV')
                ->modalDescription('Upload an updated CSV file to sync committees non-destructively. Leave empty to use the server\'s built-in all-puja.csv.')
                ->form([
                    FileUpload::make('csv_file')
                        ->label('Upload New CSV File (Optional)')
                        ->disk('local')
                        ->directory('temp-csv-sync')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/csv'])
                        ->helperText('Upload any updated all-puja.csv file from your computer.'),
                ])
                ->action(function (array $data, ParticipantCsvSyncService $syncService) {
                    $event = Event::where('status', 'active')->latest('year')->first() ?? Event::latest('year')->first();

                    if (! $event) {
                        Notification::make()
                            ->title('No active event found')
                            ->danger()
                            ->send();

                        return;
                    }

                    $csvPath = null;
                    if (! empty($data['csv_file'])) {
                        $csvPath = Storage::disk('local')->path($data['csv_file']);
                    } else {
                        $csvPath = $syncService->resolveDefaultCsvPath();
                    }

                    if (! $csvPath || ! file_exists($csvPath)) {
                        Notification::make()
                            ->title('CSV file not found')
                            ->body('Please upload a CSV file or ensure all-puja.csv exists on the server.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $result = $syncService->syncFromCsv($event, $csvPath);

                    Notification::make()
                        ->title('Sync Complete')
                        ->body($result['message'])
                        ->success()
                        ->send();
                }),
        ];
    }
}
