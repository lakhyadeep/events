<?php

namespace App\Filament\Resources\Winners;

use App\Filament\Resources\Winners\Pages\CreateWinner;
use App\Filament\Resources\Winners\Pages\EditWinner;
use App\Filament\Resources\Winners\Pages\ListWinners;
use App\Filament\Resources\Winners\Schemas\WinnerForm;
use App\Filament\Resources\Winners\Tables\WinnersTable;
use App\Models\Winner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WinnerResource extends Resource
{
    protected static ?string $model = Winner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WinnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WinnersTable::configure($table);
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
            'index' => ListWinners::route('/'),
            'create' => CreateWinner::route('/create'),
            'edit' => EditWinner::route('/{record}/edit'),
        ];
    }
}
