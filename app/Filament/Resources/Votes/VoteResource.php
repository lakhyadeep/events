<?php

namespace App\Filament\Resources\Votes;

use App\Filament\Resources\Votes\Pages\ListVotes;
use App\Filament\Resources\Votes\Schemas\VoteForm;
use App\Filament\Resources\Votes\Tables\VotesTable;
use App\Models\Vote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static UnitEnum|string|null $navigationGroup = 'Contenders & Judging';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Votes';

    protected static ?string $modelLabel = 'Vote';

    protected static ?string $pluralModelLabel = 'Votes';

    public static function form(Schema $schema): Schema
    {
        return VoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VotesTable::configure($table);
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
            'index' => ListVotes::route('/'),
        ];
    }
}
