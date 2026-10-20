<?php

namespace App\Filament\Resources\Winners\Pages;

use App\Filament\Resources\Winners\WinnerResource;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWinners extends ListRecords
{
    protected static string $resource = WinnerResource::class;

    protected function getHeaderActions(): array
    {
        $activeEvent = Event::where('status', 'active')->latest('year')->first()
            ?? Event::latest('year')->latest('id')->first();

        return [
            Action::make('previewHomepage')
                ->label('Preview Awards on Homepage')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->url(fn () => $activeEvent ? route('events.preview-awards', ['event' => $activeEvent->slug]) : url('/preview-awards'))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
