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
                                'log' => 'Log Simulator (Local & Staging)',
                                'twilio' => 'Twilio SMS (Global)',
                                'msg91' => 'MSG91 (India DLT Flow)',
                                'fast2sms' => 'Fast2SMS (India Quick SMS)',
                                'custom_http' => 'Custom HTTP / Webhook Gateway (Generic Any Provider)',
                            ])
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Select::make('otp_length')
                            ->label('OTP Digit Length')
                            ->options([
                                4 => '4 Digits (Quick)',
                                6 => '6 Digits (Recommended Standard)',
                                8 => '8 Digits (High Security)',
                            ])
                            ->default(6)
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

                Section::make('Twilio Gateway Configuration')
                    ->description('Configure Twilio API credentials for international SMS dispatch.')
                    ->columns(['default' => 1, 'sm' => 3])
                    ->visible(fn (Get $get): bool => $get('active_driver') === 'twilio')
                    ->schema([
                        TextInput::make('twilio_settings.sid')
                            ->label('Account SID')
                            ->placeholder('ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->required(fn (Get $get): bool => $get('active_driver') === 'twilio'),

                        TextInput::make('twilio_settings.token')
                            ->label('Auth Token')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('active_driver') === 'twilio'),

                        TextInput::make('twilio_settings.from')
                            ->label('From Phone Number / Sender ID')
                            ->placeholder('+1234567890')
                            ->required(fn (Get $get): bool => $get('active_driver') === 'twilio'),
                    ]),

                Section::make('MSG91 Gateway Configuration')
                    ->description('Configure MSG91 API credentials and DLT-approved template IDs for Indian SMS.')
                    ->columns(['default' => 1, 'sm' => 3])
                    ->visible(fn (Get $get): bool => $get('active_driver') === 'msg91')
                    ->schema([
                        TextInput::make('msg91_settings.auth_key')
                            ->label('Auth Key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('active_driver') === 'msg91'),

                        TextInput::make('msg91_settings.sender_id')
                            ->label('Sender ID (6 Chars)')
                            ->placeholder('DIBEVT')
                            ->maxLength(6),

                        TextInput::make('msg91_settings.template_id')
                            ->label('DLT Template / Flow ID')
                            ->placeholder('64f8xxxxxxxxxxxxxxxx')
                            ->required(fn (Get $get): bool => $get('active_driver') === 'msg91'),
                    ]),

                Section::make('Fast2SMS Gateway Configuration')
                    ->description('Configure Fast2SMS API key and routing for quick Indian SMS.')
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
                                'otp' => 'OTP Route (Quick Instant SMS)',
                                'dlt' => 'DLT Approved Route',
                            ])
                            ->default('otp'),

                        TextInput::make('fast2sms_settings.sender_id')
                            ->label('Sender ID (DLT only)')
                            ->placeholder('DIBEVT'),

                        TextInput::make('fast2sms_settings.entity_id')
                            ->label('Entity / Message ID (DLT only)'),
                    ]),

                Section::make('Custom HTTP / Webhook Gateway')
                    ->description('Integrate ANY third-party SMS provider dynamically using HTTP webhooks or custom REST APIs.')
                    ->columns(1)
                    ->visible(fn (Get $get): bool => $get('active_driver') === 'custom_http')
                    ->schema([
                        TextInput::make('custom_http_settings.url')
                            ->label('Endpoint URL')
                            ->placeholder('https://api.smsvendor.com/v1/send?to={phone}&text={url_encoded_message}')
                            ->helperText('Available tokens: {phone}, {raw_phone}, {otp}, {message}, {url_encoded_message}')
                            ->required(fn (Get $get): bool => $get('active_driver') === 'custom_http'),

                        Select::make('custom_http_settings.method')
                            ->label('HTTP Request Method')
                            ->options([
                                'POST' => 'HTTP POST',
                                'GET' => 'HTTP GET',
                            ])
                            ->default('POST')
                            ->required(),

                        Textarea::make('custom_http_settings.headers')
                            ->label('Custom HTTP Headers (JSON format)')
                            ->placeholder('{"Authorization": "Bearer YOUR_API_TOKEN", "Content-Type": "application/json"}')
                            ->rows(2),

                        Textarea::make('custom_http_settings.payload')
                            ->label('Request Body / Payload Template')
                            ->placeholder('{"mobile": "{phone}", "code": "{otp}", "message": "{message}"}')
                            ->helperText('JSON or form payload template with dynamic tokens replaced at send-time.')
                            ->rows(3),

                        TextInput::make('custom_http_settings.success_keyword')
                            ->label('Success Keyword / Regex Matcher (Optional)')
                            ->placeholder('e.g. "status":"success" or "200"')
                            ->helperText('Optional keyword required in response body to consider dispatch successful. If left blank, any HTTP 2xx status is accepted.'),
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
