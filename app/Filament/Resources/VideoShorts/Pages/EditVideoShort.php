<?php

namespace App\Filament\Resources\VideoShorts\Pages;

use App\Filament\Resources\VideoShorts\VideoShortResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVideoShort extends EditRecord
{
    protected static string $resource = VideoShortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
