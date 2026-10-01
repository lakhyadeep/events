<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Banner Information')
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
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        TextInput::make('cta_link')
                            ->label('CTA Destination Link')
                            ->url()
                            ->maxLength(191),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active on Carousel')
                            ->default(true),
                    ]),

                Section::make('Responsive Media Assets')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_desktop')
                            ->label('Desktop Banner Image (1920x600 recommended)')
                            ->image()
                            ->directory('banners/desktop')
                            ->required(),
                        FileUpload::make('image_mobile')
                            ->label('Mobile Banner Image (768x960 recommended)')
                            ->image()
                            ->directory('banners/mobile')
                            ->required(),
                    ]),
            ]);
    }
}
