<?php

namespace App\Filament\Resources\VideoShorts\Schemas;

use App\Enums\VideoPlatform;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoShortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Video Story & Links')
                        ->description('Video title, direct URL, optional video identifier, and subtitle synopsis.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Video Story Title')
                                ->placeholder('e.g. Sculpting Immortality with Sanatan Dinda')
                                ->columnSpanFull()
                                ->required()
                                ->maxLength(191),
                            TextInput::make('video_url')
                                ->label('Direct Video URL')
                                ->placeholder('https://www.youtube.com/shorts/dQw4w9WgXcQ')
                                ->url()
                                ->required()
                                ->maxLength(191),
                            TextInput::make('video_id')
                                ->label('External Video ID (Auto-extracted if blank)')
                                ->placeholder('e.g. dQw4w9WgXcQ')
                                ->maxLength(191),
                            Textarea::make('caption')
                                ->label('Short Reel Synopsis / Subtitle')
                                ->placeholder('A masterclass behind the scenes at the artisan workshop...')
                                ->columnSpanFull()
                                ->rows(3),
                        ]),

                    Section::make('Visual Preview & Poster')
                        ->description('Custom 9:16 vertical cover poster displayed over the media tile.')
                        ->schema([
                            FileUpload::make('thumbnail_image')
                                ->label('Custom 9:16 Cover Poster (Optional)')
                                ->helperText('If not provided, the platform default thumbnail will be displayed')
                                ->image()
                                ->maxSize(3072)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('shorts/thumbnails'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Event & Platform Settings')
                        ->description('Target event, video host platform, year, and feed visibility.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Target Event')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('platform')
                                ->label('Hosting Platform')
                                ->options(VideoPlatform::options())
                                ->default(VideoPlatform::YouTubeShorts->value)
                                ->required(),
                            TextInput::make('year')
                                ->label('Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            TextInput::make('sort_order')
                                ->label('Sort Priority')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Publish to Shorts Carousel')
                                ->helperText('Visible in the 9:16 vertical video stories section')
                                ->default(true),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
