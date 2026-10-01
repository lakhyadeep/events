<?php

namespace App\Filament\Resources\VideoShorts\Pages;

use App\Filament\Resources\VideoShorts\VideoShortResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoShorts extends ListRecords
{
    protected static string $resource = VideoShortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
