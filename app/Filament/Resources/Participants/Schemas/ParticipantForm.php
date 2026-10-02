<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Enums\RegistrationStatus;
use App\Enums\Zone;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Club & Candidate Profile')
                        ->description('Official club identity, public display name, heritage background, and physical premises.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Official Name (Club / Organization)')
                                ->placeholder('e.g. Ballygunge Cultural Association')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('display_name')
                                ->label('Public Display Name')
                                ->placeholder('e.g. Ballygunge Cultural')
                                ->required()
                                ->maxLength(191),
                            Textarea::make('address')
                                ->label('Full Physical Address')
                                ->columnSpanFull()
                                ->rows(2),
                            Textarea::make('short_introduction')
                                ->label('Short Introduction / Heritage Summary')
                                ->placeholder('Celebrated for traditional elegance and architectural design since 1951...')
                                ->columnSpanFull()
                                ->rows(3),
                        ]),

                    Section::make('Media & Showcase Gallery')
                        ->description('High-definition photography of the pandal, illumination, and idol for the interactive card gallery and modal inspection.')
                        ->columns(3)
                        ->collapsible()
                        ->schema([
                            FileUpload::make('primary_display_image')
                                ->label('Primary Showcase Photo (Featured Cover)')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('participants/primary')
                                ->columnSpanFull()
                                ->required(),
                            FileUpload::make('image_1')
                                ->label('Gallery Photo 1 (Pandal Exterior / Theme)')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('participants/gallery'),
                            FileUpload::make('image_2')
                                ->label('Gallery Photo 2 (Idol / Sculpting Detail)')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('participants/gallery'),
                            FileUpload::make('image_3')
                                ->label('Gallery Photo 3 (Lighting / Atmosphere)')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('participants/gallery'),
                        ]),

                    Section::make('Cultural & Durga Puja Contest Attributes')
                        ->description('Specialized fields for Durga Puja festival entries: theme philosophy, sculptors, illumination wizards, and concept art.')
                        ->columns(2)
                        ->collapsible()
                        ->schema([
                            Toggle::make('is_puja_contest')
                                ->label('Festival Entry (Enables Puja Attributes)')
                                ->columnSpanFull()
                                ->default(true),
                            TextInput::make('first_year_of_puja')
                                ->label('Establishment Year (First Year of Puja)')
                                ->numeric()
                                ->placeholder('e.g. 1951'),
                            TextInput::make('puja_theme')
                                ->label('Puja Theme / Title')
                                ->placeholder('e.g. Echoes of Terracotta & Clay')
                                ->maxLength(191),
                            TextInput::make('idol_artist')
                                ->label('Idol Artist (Pratima Shilpi)')
                                ->placeholder('e.g. Sanatan Dinda')
                                ->maxLength(191),
                            TextInput::make('theme_artist')
                                ->label('Theme Designer / Concept Architect')
                                ->placeholder('e.g. Anirban Das')
                                ->maxLength(191),
                            TextInput::make('light_designer')
                                ->label('Light Designer (Aalokshojja Specialist)')
                                ->placeholder('e.g. Chandannagar Electric Studio')
                                ->maxLength(191),
                            TextInput::make('sound_designer')
                                ->label('Sound Designer / Ambient Music')
                                ->placeholder('e.g. Bickram Ghosh Studio')
                                ->maxLength(191),
                            FileUpload::make('concept_note_image')
                                ->label('Concept Note / Architectural Blueprint')
                                ->image()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('participants/concept_notes')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Event & Review Status')
                        ->description('Target event edition, review approval, and showcase highlights.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Target Event / Contest')
                                ->relationship('event', 'display_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('year')
                                ->label('Registration Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                            Select::make('registration_status')
                                ->label('Registration Status')
                                ->options(RegistrationStatus::options())
                                ->default(RegistrationStatus::Approved->value)
                                ->required(),
                            Toggle::make('is_shortlisted')
                                ->label('Featured Contender (Shortlisted)')
                                ->helperText('Featured prominently in top showcase cards')
                                ->default(false),
                        ]),

                    Section::make('Zone & Geographic Location')
                        ->description('Zonal grouping and neighborhood landmarks.')
                        ->schema([
                            Select::make('zone')
                                ->label('Geographic Zone')
                                ->options(Zone::options())
                                ->searchable()
                                ->required(),
                            TextInput::make('locality')
                                ->label('Locality / Neighborhood')
                                ->placeholder('e.g. Ballygunge Place')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('landmark')
                                ->label('Prominent Landmark')
                                ->placeholder('e.g. Near Lake Market')
                                ->maxLength(191),
                            TextInput::make('mobile_number')
                                ->label('Official Helpline Mobile')
                                ->placeholder('e.g. 9830011223')
                                ->tel()
                                ->required()
                                ->maxLength(20),
                        ]),

                    Section::make('Key Contacts & Office Bearers')
                        ->description('Emergency, administrative, and liaison contacts.')
                        ->collapsible()
                        ->schema([
                            TextInput::make('key_contact_1_name')
                                ->label('Primary Contact / President Name')
                                ->placeholder('e.g. Dr. Subir Sen')
                                ->maxLength(191),
                            TextInput::make('key_contact_1_phone')
                                ->label('Primary Contact Phone')
                                ->placeholder('e.g. 9830011223')
                                ->tel()
                                ->maxLength(20),
                            TextInput::make('key_contact_2_name')
                                ->label('Secondary Contact / Secretary Name')
                                ->placeholder('e.g. Aniruddha Das')
                                ->maxLength(191),
                            TextInput::make('key_contact_2_phone')
                                ->label('Secondary Contact Phone')
                                ->placeholder('e.g. 9831122334')
                                ->tel()
                                ->maxLength(20),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
