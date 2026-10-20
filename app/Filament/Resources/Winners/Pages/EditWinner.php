<?php

namespace App\Filament\Resources\Winners\Pages;

use App\Filament\Resources\Winners\WinnerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWinner extends EditRecord
{
    protected static string $resource = WinnerResource::class;

    protected function getHeaderActions(): array
    {
        $event = $this->record?->event;

        return [
            Action::make('previewHomepage')
                ->label('Preview on Homepage')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->url(fn () => $event ? route('events.preview-awards', ['event' => $event->slug]) : url('/preview-awards'))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
