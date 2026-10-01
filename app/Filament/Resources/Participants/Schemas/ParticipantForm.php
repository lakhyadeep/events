<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Candidate Profile & Location')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Official Name (Club / Individual)')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('display_name')
                            ->label('Public Display Name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('mobile_number')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Select::make('zone')
                            ->options([
                                'North Kolkata' => 'North Kolkata',
                                'South Kolkata' => 'South Kolkata',
                                'Central Kolkata' => 'Central Kolkata',
                                'East Kolkata' => 'East Kolkata',
                                'Howrah' => 'Howrah',
                                'Salt Lake & New Town' => 'Salt Lake & New Town',
                                'Other' => 'Other',
                            ])
                            ->searchable()
                            ->required(),
                        TextInput::make('locality')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('landmark')
                            ->maxLength(191),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        Select::make('registration_status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('approved')
                            ->required(),
                        Toggle::make('is_shortlisted')
                            ->label('Shortlisted Candidate (Featured on Showcase)')
                            ->default(false),
                        Textarea::make('address')
                            ->columnSpanFull()
                            ->rows(2),
                        Textarea::make('short_introduction')
                            ->label('Short Introduction / Bio')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Key Contact Persons')
                    ->columns(2)
                    ->schema([
                        TextInput::make('key_contact_1_name')
                            ->label('Primary Contact Name')
                            ->maxLength(191),
                        TextInput::make('key_contact_1_phone')
                            ->label('Primary Contact Phone')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('key_contact_2_name')
                            ->label('Secondary Contact Name')
                            ->maxLength(191),
                        TextInput::make('key_contact_2_phone')
                            ->label('Secondary Contact Phone')
                            ->tel()
                            ->maxLength(20),
                    ]),

                Section::make('Media & Photo Gallery')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('primary_display_image')
                            ->label('Primary Showcase Photo')
                            ->image()
                            ->directory('participants/primary')
                            ->required(),
                        FileUpload::make('image_1')
                            ->label('Gallery Photo 1')
                            ->image()
                            ->directory('participants/gallery'),
                        FileUpload::make('image_2')
                            ->label('Gallery Photo 2')
                            ->image()
                            ->directory('participants/gallery'),
                        FileUpload::make('image_3')
                            ->label('Gallery Photo 3')
                            ->image()
                            ->directory('participants/gallery'),
                    ]),

                Section::make('Cultural & Durga Puja Contest Attributes')
                    ->description('Specialized fields for Durga Puja / Cultural Festival Contests')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_puja_contest')
                            ->label('This is a Puja / Cultural Festival Entry')
                            ->default(true),
                        TextInput::make('first_year_of_puja')
                            ->label('First Year of Puja (Est.)')
                            ->numeric()
                            ->placeholder('e.g. 1948'),
                        TextInput::make('puja_theme')
                            ->label('Puja Theme')
                            ->maxLength(191),
                        TextInput::make('idol_artist')
                            ->label('Idol Artist (Pratima Shilpi)')
                            ->maxLength(191),
                        TextInput::make('theme_artist')
                            ->label('Theme Artist / Concept Designer')
                            ->maxLength(191),
                        TextInput::make('light_designer')
                            ->label('Light Designer (Aalokshojja)')
                            ->maxLength(191),
                        TextInput::make('sound_designer')
                            ->label('Sound Designer / Background Music')
                            ->maxLength(191),
                        FileUpload::make('concept_note_image')
                            ->label('Concept Note / Theme Art (Document or Image)')
                            ->image()
                            ->directory('participants/concept_notes'),
                    ]),
            ]);
    }
}
