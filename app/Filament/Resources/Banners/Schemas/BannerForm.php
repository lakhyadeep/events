<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Slide Content & Call to Action')
                        ->description('Specify slide heading and optional redirect destination.')
                        ->schema([
                            TextInput::make('title')
                                ->label('Slide Headline')
                                ->placeholder('e.g. The Grand Celebration of Art & Devotion')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('cta_link')
                                ->label('Call To Action (CTA) Link')
                                ->placeholder('e.g. #participants or https://...')
                                ->helperText('Relative anchor (e.g. #vote) or full web URL')
                                ->maxLength(191),
                        ]),

                    Section::make('Responsive Media Assets')
                        ->description('Upload high-resolution media optimized for desktop widescreen (1920x600) and mobile viewport (768x960).')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('image_desktop')
                                ->label('Desktop Hero Banner (1920×600 px)')
                                ->disk('public')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('banners/desktop')
                                ->required(),
                            FileUpload::make('image_mobile')
                                ->label('Mobile Portrait Banner (768×960 px)')
                                ->disk('public')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('banners/mobile')
                                ->required(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Target Event & Display Settings')
                        ->description('Assign the banner to an active event, specify carousel rotation order and visibility.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Target Event / Contest')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('year')
                                ->label('Event Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            TextInput::make('sort_order')
                                ->label('Carousel Sort Order')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Display on Carousel')
                                ->helperText('Visible in above-the-fold rotation')
                                ->default(true),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
