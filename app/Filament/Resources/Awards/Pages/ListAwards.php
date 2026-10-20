<?php

namespace App\Filament\Resources\Awards\Pages;

use App\Filament\Resources\Awards\AwardResource;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwards extends ListRecords
{
    protected static string $resource = AwardResource::class;

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
