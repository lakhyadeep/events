<?php

namespace App\Filament\Resources\Localities\Pages;

use App\Filament\Resources\Localities\LocalityResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateLocality extends CreateRecord
{
    protected static string $resource = LocalityResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
