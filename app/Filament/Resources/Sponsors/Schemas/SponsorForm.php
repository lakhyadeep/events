<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sponsor Profile')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('display_name')
                            ->required()
                            ->maxLength(191),
                        Select::make('sponsor_type')
                            ->options([
                                'presenting_partner' => 'Presenting Partner (Masthead Title)',
                                'associate_sponsor' => 'Associate Sponsor (Masthead Slots 1-5)',
                                'powered_by' => 'Powered By',
                                'co_sponsor' => 'Co-Sponsor',
                                'partner' => 'Partner',
                            ])
                            ->default('associate_sponsor')
                            ->required(),
                        TextInput::make('sponsor_tag')
                            ->label('Sponsor Tagline / Label (e.g. Presenting Partner, Official Beverage Partner)')
                            ->maxLength(191),
                        Select::make('slot_order')
                            ->label('Masthead Slot Order (1 to 5 for Associate Sponsors)')
                            ->options([
                                1 => 'Slot 1',
                                2 => 'Slot 2',
                                3 => 'Slot 3',
                                4 => 'Slot 4',
                                5 => 'Slot 5',
                            ])
                            ->default(1)
                            ->required(),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        Toggle::make('is_active')
                            ->label('Active on Masthead')
                            ->default(true),
                    ]),

                Section::make('Branding & Landing Redirect')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Sponsor Logo (PNG/SVG/WebP with transparent background)')
                            ->image()
                            ->directory('sponsors/logos')
                            ->required(),
                        TextInput::make('landing_url')
                            ->label('Sponsor Website / Landing URL')
                            ->url()
                            ->maxLength(191),
                        Textarea::make('description')
                            ->label('Sponsor Description')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
