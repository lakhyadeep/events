<?php

namespace App\Filament\Resources\Zones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Zone Details')
                    ->description('Define regional zone for participant segmentation and geographic sorting.')
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 4])
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Zone Name')
                            ->placeholder('e.g. Central Dibrugarh')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->label('URL / Code Slug')
                            ->placeholder('e.g. central-dibrugarh')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191),
                        TextInput::make('sort_order')
                            ->label('Display Sort Order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active Zone')
                            ->helperText('Visible in frontend filters and participant registration')
                            ->default(true),
                    ]),
            ]);
    }
}
