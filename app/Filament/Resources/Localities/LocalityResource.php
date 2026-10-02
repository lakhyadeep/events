<?php

namespace App\Filament\Resources\Localities;

use App\Filament\Resources\Localities\Pages\CreateLocality;
use App\Filament\Resources\Localities\Pages\EditLocality;
use App\Filament\Resources\Localities\Pages\ListLocalities;
use App\Filament\Resources\Localities\Schemas\LocalityForm;
use App\Filament\Resources\Localities\Tables\LocalitiesTable;
use App\Models\Locality;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LocalityResource extends Resource
{
    protected static ?string $model = Locality::class;

    protected static UnitEnum|string|null $navigationGroup = 'Masters & Settings';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Schema $schema): Schema
    {
        return LocalityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocalitiesTable::configure($table);
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
            'index' => ListLocalities::route('/'),
            'create' => CreateLocality::route('/create'),
            'edit' => EditLocality::route('/{record}/edit'),
        ];
    }
}
