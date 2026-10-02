<?php

namespace App\Filament\Resources\TimelineItems\Schemas;

use App\Enums\TimelineStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimelineItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Milestone Stage & Activities')
                        ->description('Define stage name, public date label, and editorial briefing of festival milestones.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('title')
                                ->label('Milestone Stage Title')
                                ->placeholder('e.g. Nominations Open, Judging Phase, Award Gala')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('milestone_date')
                                ->label('Scheduled Date / Period Label')
                                ->placeholder('e.g. 15 Aug - 15 Sep 2026')
                                ->helperText('Formatted textual representation shown on timeline pin')
                                ->required()
                                ->maxLength(191),
                            Textarea::make('description')
                                ->label('Milestone Description / Key Activities')
                                ->placeholder('Online submission of concept notes, themes, and club registration...')
                                ->columnSpanFull()
                                ->rows(4),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Event & Roadmap Progression')
                        ->description('Link milestone to festival edition and configure progress indicator.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Target Event')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('status')
                                ->label('Progress Status')
                                ->options(TimelineStatus::options())
                                ->default(TimelineStatus::Upcoming->value)
                                ->required(),
                            TextInput::make('sort_order')
                                ->label('Sequential Sort Order')
                                ->numeric()
                                ->default(0),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
