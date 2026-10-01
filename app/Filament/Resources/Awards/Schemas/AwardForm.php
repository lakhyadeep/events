<?php

namespace App\Filament\Resources\Awards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AwardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Award Details')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Internal Name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('display_name')
                            ->label('Public Display Name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('category')
                            ->label('Category (e.g. Best Idol, Best Lighting, Best Theme, Public Choice)')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('prize_money_or_award')
                            ->label('Prize Money / Trophy Specification')
                            ->placeholder('e.g. ₹1,00,000 Cash + Trophy')
                            ->maxLength(191),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active Award')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Award Description & Criteria')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
