<?php

namespace App\Filament\Resources\Votes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Vote Details')
                        ->description('Contestant and category selected by the voter.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Festival Event')
                                ->relationship('event', 'display_name')
                                ->disabled(),
                            Select::make('participant_id')
                                ->label('Puja Committee / Candidate')
                                ->relationship('participant', 'display_name')
                                ->disabled(),
                            Select::make('award_id')
                                ->label('Award Category')
                                ->relationship('award', 'display_name')
                                ->placeholder('Overall / General Event Vote')
                                ->disabled(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Voter Verification & Audit')
                        ->description('Audit trail recorded during OTP verification.')
                        ->schema([
                            TextInput::make('mobile_number')
                                ->label('Verified Mobile Number')
                                ->disabled(),
                            TextInput::make('ip_address')
                                ->label('Voter IP Address')
                                ->disabled(),
                            Textarea::make('user_agent')
                                ->label('Browser / Device')
                                ->rows(3)
                                ->disabled(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
