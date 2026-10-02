<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('General Information')
                        ->description('Core event identity, slug routing, annual edition, and public display title.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Internal Event Name')
                                ->placeholder('e.g. Dib 24x7 Sharod Samman 2026')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('display_name')
                                ->label('Public Display Name')
                                ->placeholder('e.g. Dib 24x7 Sharod Samman 2026')
                                ->required()
                                ->maxLength(191)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                    'slug',
                                    Str::slug($state ?? ''),
                                )),
                            TextInput::make('slug')
                                ->label('URL Slug')
                                ->placeholder('e.g. sharod-samman-2026')
                                ->helperText('Defines public URL: /events/{slug}')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(191),
                            TextInput::make('tagline')
                                ->label('Festival Tagline / Motto')
                                ->columnSpanFull()
                                ->placeholder("e.g. Bengal's Most Prestigious Festival & Puja Excellence Awards")
                                ->maxLength(191),
                        ]),

                    Section::make('Event Content & Editorial')
                        ->description('Rich text narratives, judging criteria, and terms and conditions.')
                        ->collapsible()
                        ->schema([
                            RichEditor::make('about_text')
                                ->label('About the Event'),
                            RichEditor::make('criteria_text')
                                ->label('Selection Criteria & Guidelines'),
                            RichEditor::make('terms_and_conditions')
                                ->label('Terms and Conditions'),
                        ]),

                    Section::make('SEO & Social Sharing')
                        ->description('Metadata, title tags, and OpenGraph social card previews for Facebook, WhatsApp, and Twitter sharing.')
                        ->columns(2)
                        ->collapsible()
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title')
                                ->placeholder('Dib 24x7 Sharod Samman 2026 | Grand Festival Microsite')
                                ->maxLength(191),
                            FileUpload::make('og_image')
                                ->label('Open Graph (OG) Share Image')
                                ->image()
                                ->maxSize(3072)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->directory('events/og'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->placeholder('Explore the finest pandals and cast your vote.')
                                ->columnSpanFull()
                                ->rows(2),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Lifecycle & Edition')
                        ->description('Publishing status and festival year.')
                        ->schema([
                            Select::make('status')
                                ->label('Lifecycle Status')
                                ->options(EventStatus::options())
                                ->default(EventStatus::Draft->value)
                                ->required(),
                            TextInput::make('year')
                                ->label('Festival Year')
                                ->required()
                                ->numeric()
                                ->default(now()->year),
                        ]),

                    Section::make('Interactive Features')
                        ->description('Enable or disable live interactivity, registration, voting, and timelines.')
                        ->schema([
                            Toggle::make('is_registration_active')
                                ->label('Registration Active')
                                ->helperText('Allow candidate signups'),
                            Toggle::make('is_voting_active')
                                ->label('Voting Active')
                                ->helperText('Show Vote Now button'),
                            Toggle::make('is_awards_active')
                                ->label('Awards Active')
                                ->helperText('Reveal winners podium'),
                            Toggle::make('is_timeline_active')
                                ->label('Timeline Active')
                                ->helperText('Show roadmap section')
                                ->default(true),
                        ]),

                    Section::make('Countdown & Deadlines')
                        ->description('Countdown clock target and post-event closure alerts.')
                        ->collapsible()
                        ->schema([
                            DateTimePicker::make('countdown_datetime')
                                ->label('Countdown Target Date & Time')
                                ->helperText('When countdown expires, the closure notice replaces the timer.'),
                            Textarea::make('closure_message')
                                ->label('Closure Notice')
                                ->placeholder('Voting and nominations have officially concluded.')
                                ->rows(2),
                        ]),

                    Section::make('Contact & Inquiry Information')
                        ->description('Public contact channels and external portal.')
                        ->collapsible()
                        ->schema([
                            TextInput::make('contact_email')
                                ->label('Contact Email')
                                ->placeholder('events@dib24x7.com')
                                ->email()
                                ->maxLength(191),
                            TextInput::make('contact_phone')
                                ->label('Contact Phone / Hotline')
                                ->placeholder('+91 98300 12345')
                                ->tel()
                                ->maxLength(191),
                            TextInput::make('external_link')
                                ->label('Official External Portal')
                                ->placeholder('https://dib24x7.com')
                                ->url()
                                ->maxLength(191),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
