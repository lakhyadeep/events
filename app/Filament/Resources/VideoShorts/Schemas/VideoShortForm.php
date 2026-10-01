<?php

namespace App\Filament\Resources\VideoShorts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoShortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Short & Reel Metadata')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Video Title')
                            ->required()
                            ->maxLength(191),
                        Select::make('platform')
                            ->label('Video Platform')
                            ->options([
                                'youtube_shorts' => 'YouTube Shorts (Vertical 9:16)',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                                'instagram' => 'Instagram Reel',
                            ])
                            ->default('youtube_shorts')
                            ->required(),
                        TextInput::make('video_url')
                            ->label('Video / Reel URL')
                            ->placeholder('e.g. https://www.youtube.com/shorts/XXXXXXXXXXX')
                            ->url()
                            ->required()
                            ->maxLength(191),
                        TextInput::make('video_id')
                            ->label('Video ID (Optional - auto-extracted if empty)')
                            ->placeholder('e.g. XXXXXXXXXXX')
                            ->maxLength(191),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active on Shorts Carousel')
                            ->default(true),
                        FileUpload::make('thumbnail_image')
                            ->label('Custom Thumbnail Image (Optional)')
                            ->image()
                            ->directory('shorts/thumbnails'),
                        Textarea::make('caption')
                            ->label('Video Caption / Description')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ]);
    }
}
