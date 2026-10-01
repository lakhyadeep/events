<?php

namespace App\Filament\Resources\Winners\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WinnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Winner Declaration')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('award_id')
                            ->relationship('award', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('participant_id')
                            ->relationship('participant', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('rank_order')
                            ->label('Award Rank / Position')
                            ->options([
                                '1st' => '1st Prize (Champion / Gold)',
                                '2nd' => '2nd Prize (Runner Up / Silver)',
                                '3rd' => '3rd Prize (Bronze)',
                                'Special Mention' => 'Special Jury Mention',
                                'People Choice' => 'People Choice Award',
                            ])
                            ->required(),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft (Unpublished)',
                                'published' => 'Published on Podium',
                            ])
                            ->default('published')
                            ->required(),
                    ]),
            ]);
    }
}
