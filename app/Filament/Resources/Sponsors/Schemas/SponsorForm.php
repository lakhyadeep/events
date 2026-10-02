<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use App\Enums\SponsorType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Brand & Corporate Identity')
                        ->description('Corporate company name, consumer-facing brand label, and partnership narrative.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('title')
                                ->label('Internal Corporate Name')
                                ->placeholder('e.g. Fortune Oil & Foods Ltd.')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('display_name')
                                ->label('Public Display Brand Name')
                                ->placeholder('e.g. Fortune')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('sponsor_tag')
                                ->label('Promotional Tagline / Badge')
                                ->placeholder('e.g. Title Presenting Partner or Associate Sponsor 1')
                                ->maxLength(191),
                            TextInput::make('landing_url')
                                ->label('Destination Landing URL')
                                ->placeholder('https://brand.com/campaign')
                                ->url()
                                ->maxLength(191),
                            Textarea::make('description')
                                ->label('Partnership Narrative / Notes')
                                ->placeholder('Brief description of corporate sponsorship details.')
                                ->columnSpanFull()
                                ->rows(3),
                        ]),

                    Section::make('Branding & Logo Assets')
                        ->description('Upload high-contrast transparent logos for top masthead and sponsor ribbons.')
                        ->schema([
                            FileUpload::make('logo')
                                ->label('Sponsor Logo (Transparent PNG/SVG/WebP)')
                                ->helperText('Recommended: horizontal logo format on transparent background')
                                ->image()
                                ->maxSize(3072)
                                ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp', 'image/jpeg'])
                                ->directory('sponsors/logos')
                                ->required(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Tier & Masthead Placement')
                        ->description('Categorize sponsor tier, slot priority (1 to 5), and festival edition.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Associated Event')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('sponsor_type')
                                ->label('Sponsorship Tier')
                                ->options(SponsorType::options())
                                ->default(SponsorType::AssociateSponsor->value)
                                ->required(),
                            Select::make('slot_order')
                                ->label('Masthead Slot Position (1 to 5)')
                                ->options([
                                    1 => 'Slot 1 (Highest Priority)',
                                    2 => 'Slot 2',
                                    3 => 'Slot 3',
                                    4 => 'Slot 4',
                                    5 => 'Slot 5',
                                ])
                                ->default(1)
                                ->required(),
                            TextInput::make('year')
                                ->label('Festival Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            Toggle::make('is_active')
                                ->label('Publish on Masthead')
                                ->helperText('Visible in top masthead header')
                                ->default(true),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
