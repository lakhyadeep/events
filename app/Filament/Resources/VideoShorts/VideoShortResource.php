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
use Filament\Tables\Table;
use UnitEnum;

class VideoShortResource extends Resource
{
    protected static ?string $model = VideoShort::class;

    protected static UnitEnum|string|null $navigationGroup = 'Commercial & Media';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

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
