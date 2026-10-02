<?php

namespace App\Filament\Resources\Localities\Pages;

use App\Filament\Resources\Localities\LocalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListLocalities extends ListRecords
{
    protected static string $resource = LocalityResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
