<?php

namespace App\Filament\Resources\Localities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LocalityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Locality Details')
                    ->description('Define locality / neighborhood master data mapped to a geographic zone.')
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Select::make('zone_id')
                            ->label('Geographic Zone')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Locality Name')
                            ->placeholder('e.g. Chowkidinghee')
                            ->required()
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->label('URL / Code Slug')
                            ->placeholder('e.g. chowkidinghee')
                            ->maxLength(191),
                        TextInput::make('sort_order')
                            ->label('Display Sort Order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active Locality')
                            ->helperText('Available for candidate selection')
                            ->default(true),
                    ]),
            ]);
    }
}
