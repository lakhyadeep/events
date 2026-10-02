<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('User Account & Security Credentials')
                    ->description('Manage administrator and team member identities, authentication credentials, and verification status.')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('e.g. John Doe')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('e.g. user@dib24x7.com')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->maxLength(191)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (string $operation): string => $operation === 'edit' ? 'Leave blank to keep existing password unchanged.' : 'Enter a strong secret password.'),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verification Timestamp')
                            ->helperText('Specifies when email address was verified')
                            ->default(now()),
                    ]),
            ]);
    }
}
