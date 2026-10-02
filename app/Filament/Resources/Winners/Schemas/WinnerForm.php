<?php

namespace App\Filament\Resources\Winners\Schemas;

use App\Enums\WinnerRank;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WinnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Winning Candidate & Category')
                        ->description('Select the victorious club or participant and the corresponding award category.')
                        ->schema([
                            Select::make('participant_id')
                                ->label('Winning Candidate / Club')
                                ->relationship('participant', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('award_id')
                                ->label('Award Trophy / Category')
                                ->relationship('award', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Podium Placement & Status')
                        ->description('Designate ranking rank, award edition, and publication status.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Festival Event')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('rank_order')
                                ->label('Award Rank / Position')
                                ->options(WinnerRank::options())
                                ->required(),
                            TextInput::make('year')
                                ->label('Award Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            Select::make('status')
                                ->label('Podium Publication Status')
                                ->options([
                                    'draft' => 'Draft (Embargoed / Sealed Envelope)',
                                    'published' => 'Published (Live on Public Podium)',
                                ])
                                ->default('published')
                                ->required(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
