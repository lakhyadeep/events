<?php

namespace App\Filament\Resources\TimelineItems\Pages;

use App\Filament\Resources\TimelineItems\TimelineItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTimelineItem extends EditRecord
{
    protected static string $resource = TimelineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
