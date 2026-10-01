<?php

namespace App\Filament\Resources\TimelineItems\Pages;

use App\Filament\Resources\TimelineItems\TimelineItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimelineItems extends ListRecords
{
    protected static string $resource = TimelineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
