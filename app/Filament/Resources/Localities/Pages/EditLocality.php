<?php

namespace App\Filament\Resources\Localities\Pages;

use App\Filament\Resources\Localities\LocalityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditLocality extends EditRecord
{
    protected static string $resource = LocalityResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
