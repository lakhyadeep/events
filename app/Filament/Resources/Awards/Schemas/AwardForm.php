<?php

namespace App\Filament\Resources\Awards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AwardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Award Identity & Evaluation Criteria')
                        ->description('Internal reference, public exhibition title, category grouping, and prize purse package.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Internal Reference Name')
                                ->placeholder('e.g. Best Puja Overall (Serar Sera)')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('display_name')
                                ->label('Public Display Title')
                                ->placeholder('e.g. Best Puja Overall (সেরার সেরা)')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('category')
                                ->label('Category Group')
                                ->placeholder('e.g. Grand Championship, Art & Sculpture, Lighting')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('prize_money_or_award')
                                ->label('Prize Package / Trophy')
                                ->placeholder('e.g. ₹2,50,000 Cash + Gold Trophy')
                                ->maxLength(191),
                            Textarea::make('description')
                                ->label('Category Description & Evaluation Guidelines')
                                ->placeholder('Explain the thematic, sculpturing, illumination, or civic parameters required to win.')
                                ->columnSpanFull()
                                ->rows(4),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Event & Visibility')
                        ->description('Link award to festival edition and configure display priority.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Target Event')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('year')
                                ->label('Award Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            TextInput::make('sort_order')
                                ->label('Display Order Index')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Active Award Category')
                                ->helperText('Visible in public awards matrix')
                                ->default(true),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
