<?php

namespace App\Filament\Resources\Zones\Pages;

use App\Filament\Resources\Zones\ZoneResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateZone extends CreateRecord
{
    protected static string $resource = ZoneResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
