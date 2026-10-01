<?php

namespace App\Filament\Resources\VideoShorts;

use App\Filament\Resources\VideoShorts\Pages\CreateVideoShort;
use App\Filament\Resources\VideoShorts\Pages\EditVideoShort;
use App\Filament\Resources\VideoShorts\Pages\ListVideoShorts;
use App\Filament\Resources\VideoShorts\Schemas\VideoShortForm;
use App\Filament\Resources\VideoShorts\Tables\VideoShortsTable;
use App\Models\VideoShort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VideoShortResource extends Resource
{
    protected static ?string $model = VideoShort::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return VideoShortForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoShortsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVideoShorts::route('/'),
            'create' => CreateVideoShort::route('/create'),
            'edit' => EditVideoShort::route('/{record}/edit'),
        ];
    }
}
