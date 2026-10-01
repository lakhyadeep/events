<?php

namespace App\Filament\Resources\TimelineItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimelineItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Milestone Details')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label('Milestone Title')
                            ->placeholder('e.g. Nominations Open, Judging Phase, Award Gala')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('milestone_date')
                            ->label('Date or Period Label')
                            ->placeholder('e.g. 15 Sep - 25 Sep 2026')
                            ->required()
                            ->maxLength(191),
                        Select::make('status')
                            ->options([
                                'completed' => 'Completed',
                                'ongoing' => 'Ongoing / Active Phase',
                                'upcoming' => 'Upcoming Phase',
                            ])
                            ->default('upcoming')
                            ->required(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ]);
    }
}
