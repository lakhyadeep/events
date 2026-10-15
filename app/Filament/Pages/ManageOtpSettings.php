<?php

namespace App\Filament\Pages;

use App\Models\OtpSetting;
use App\Services\Otp\OtpService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageOtpSettings extends Page
{
    protected static UnitEnum|string|null $navigationGroup = 'Masters & Settings';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationLabel = 'Mobile & OTP Settings';

    protected static ?string $title = 'Mobile OTP Gateway & Policy Configuration';

    /**
     * Form state data.
     *
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $setting = OtpSetting::current();
        $this->form->fill($setting->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Policy & Security Limits')
                    ->description('Configure verification rules, expiration, spam-prevention cooldown, and code formatting.')
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->schema([
                        Select::make('active_driver')
                            ->label('Active SMS / OTP Gateway')
                            ->options([
                                'log' => 'Log SMS Driver (Local & Staging / Testing)',
                                'fast2sms' => 'Fast2SMS Gateway (Production SMS / India)',
                            ])
                            ->default('log')
                            ->helperText('Select the active gateway. Production SMS will use Fast2SMS once configured.')
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('otp_length')
                            ->label('OTP Digit Length')
                            ->options([
                                4 => '4 Digits (Standard Default)',
                                6 => '6 Digits (Extended)',
                                8 => '8 Digits (High Security)',
                            ])
                            ->default(4)
                            ->required(),

                        TextInput::make('expiry_minutes')
                            ->label('Code Expiry Duration')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->suffix('minutes')
                            ->required(),

                        TextInput::make('resend_cooldown_seconds')
                            ->label('Resend Cooldown Period')
                            ->numeric()
                            ->minValue(10)
                            ->maxValue(300)
                            ->suffix('seconds')
                            ->helperText('Minimum wait time between requests for the same mobile number')
                            ->required(),

                        TextInput::make('max_attempts')
                            ->label('Max Verification Attempts')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('Brute-force lockout: invalidates OTP when attempts exceed this threshold')
                            ->required(),

                        Textarea::make('message_template')
                            ->label('SMS Body Template')
                            ->columnSpanFull()
                            ->rows(2)
                            ->helperText('Dynamic variables: :otp (the generated code) and :minutes (expiry duration).')
                            ->placeholder('Your verification code is :otp. Valid for :minutes minutes.'),
                    ]),

                Section::make('Sandbox & Quality Assurance (Testing Overrides)')
                    ->description('Optional master bypass code for automated QA or app review testing without sending live SMS.')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->collapsible()
                    ->schema([
                        TextInput::make('master_otp')
                            ->label('Master Test OTP Code')
                            ->placeholder('e.g. 123456')
                            ->helperText('Static code that will always verify successfully for designated test numbers.'),

                        TextInput::make('test_phone_numbers')
                            ->label('Designated Test Mobile Numbers')
                            ->placeholder('e.g. 9876543210, 9999999999')
                            ->helperText('Comma-separated list of test mobile numbers that will accept the Master OTP code.'),
                    ]),

                Section::make('Fast2SMS Gateway Configuration')
                    ->description('Configure Fast2SMS API key and routing for quick Indian SMS and OTP delivery.')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (Get $get): bool => $get('active_driver') === 'fast2sms')
                    ->schema([
                        TextInput::make('fast2sms_settings.api_key')
                            ->label('API Authorization Key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('active_driver') === 'fast2sms')
                            ->columnSpanFull(),

                        Select::make('fast2sms_settings.route')
                            ->label('Route Type')
                            ->options([
                                'otp' => 'OTP Route (Quick Instant OTP)',
                                'dlt' => 'DLT Approved Route (Transactional)',
                                'q' => 'Quick SMS Route',
                            ])
                            ->default('otp'),

                        TextInput::make('fast2sms_settings.sender_id')
                            ->label('Sender ID (DLT only)')
                            ->placeholder('DIBEVT')
                            ->maxLength(6),

                        TextInput::make('fast2sms_settings.entity_id')
                            ->label('Entity / Template ID (DLT only)')
                            ->placeholder('e.g. 1207161...'),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save OTP Configuration')
                ->icon('heroicon-o-check')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestOtp')
                ->label('Send Test OTP')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->modalHeading('Test Gateway Connectivity')
                ->modalDescription('Dispatch a live verification OTP using the currently active SMS gateway to test credentials and message delivery.')
                ->form([
                    TextInput::make('test_phone')
                        ->label('Recipient Mobile Number')
                        ->placeholder('e.g. 9876543210')
                        ->required()
                        ->tel(),
                ])
                ->action(function (array $data, OtpService $otpService): void {
                    $phone = $data['test_phone'] ?? '';
                    $result = $otpService->sendOtp($phone, 'admin-test');

                    if ($result->successful) {
                        Notification::make()
                            ->title('Test OTP Dispatched Successfully!')
                            ->body("Active Gateway: [{$result->gateway}]. Verification code has been sent to {$phone}.")
                            ->success()
                            ->duration(6000)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Test OTP Dispatch Failed')
                            ->body($result->message)
                            ->danger()
                            ->duration(10000)
                            ->send();
                    }
                }),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = OtpSetting::current();
        $setting->update($data);

        OtpSetting::clearCache();

        Notification::make()
            ->title('OTP Settings Saved')
            ->body('Configuration and provider credentials have been updated successfully.')
            ->success()
            ->send();
    }
}
