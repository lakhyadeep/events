<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('display_name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                        TextInput::make('tagline')
                            ->columnSpanFull()
                            ->maxLength(191),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),

                Section::make('Feature & Phase Toggles')
                    ->columns(4)
                    ->schema([
                        Toggle::make('is_registration_active')
                            ->label('Registration Active'),
                        Toggle::make('is_voting_active')
                            ->label('Voting Active'),
                        Toggle::make('is_awards_active')
                            ->label('Awards Active'),
                        Toggle::make('is_timeline_active')
                            ->label('Timeline Active')
                            ->default(true),
                    ]),

                Section::make('Event Content & Editorial')
                    ->schema([
                        RichEditor::make('about_text')
                            ->label('About the Event'),
                        RichEditor::make('criteria_text')
                            ->label('Selection Criteria'),
                        RichEditor::make('terms_and_conditions')
                            ->label('Terms and Conditions'),
                        DateTimePicker::make('countdown_datetime')
                            ->label('Countdown Date & Time'),
                        Textarea::make('closure_message')
                            ->label('Closure Message (Displayed after countdown/voting ends)')
                            ->rows(2),
                    ]),

                Section::make('SEO & Social Sharing')
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(191),
                        FileUpload::make('og_image')
                            ->label('Open Graph (OG) Image')
                            ->image()
                            ->directory('events/og'),
                        Textarea::make('meta_description')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),

                Section::make('Contact & External Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(191),
                        TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(191),
                        TextInput::make('external_link')
                            ->url()
                            ->maxLength(191),
                    ]),
            ]);
    }
}
