<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageOtpSettings;
use App\Filament\Widgets\SmsBalanceAlertWidget;
use App\Filament\Widgets\SmsUsageOverviewWidget;
use App\Models\OtpSetting;
use App\Models\User;
use App\Services\Otp\Drivers\LogSmsDriver;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class OtpManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        LogSmsDriver::clearSentMessages();
        OtpSetting::clearCache();
    }

    public function test_admin_user_can_access_otp_settings_page(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/admin/manage-otp-settings');

        $response->assertStatus(200);
        $response->assertSee('Mobile OTP Gateway & Policy Configuration');
    }

    public function test_admin_can_save_otp_settings(): void
    {
        $user = User::query()->first();

        $this->actingAs($user);

        Livewire::test(ManageOtpSettings::class)
            ->fillForm([
                'active_driver' => 'log',
                'otp_length' => 4,
                'expiry_minutes' => 10,
                'resend_cooldown_seconds' => 90,
                'max_attempts' => 3,
                'message_template' => 'Your code is :otp. Valid for :minutes min.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        OtpSetting::clearCache();
        $updated = OtpSetting::current();

        $this->assertSame('log', $updated->active_driver);
        $this->assertSame(4, $updated->otp_length);
        $this->assertSame(10, $updated->expiry_minutes);
        $this->assertSame(90, $updated->resend_cooldown_seconds);
        $this->assertSame(3, $updated->max_attempts);
        $this->assertSame('Your code is :otp. Valid for :minutes min.', $updated->message_template);
    }

    public function test_admin_can_save_fast2sms_settings(): void
    {
        $user = User::query()->first();

        $this->actingAs($user);

        Livewire::test(ManageOtpSettings::class)
            ->fillForm([
                'active_driver' => 'fast2sms',
                'otp_length' => 6,
                'expiry_minutes' => 5,
                'resend_cooldown_seconds' => 60,
                'max_attempts' => 5,
                'fast2sms_settings' => [
                    'api_key' => 'F2S_ADMIN_TEST_KEY',
                    'route' => 'otp',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        OtpSetting::clearCache();
        $updated = OtpSetting::current();

        $this->assertSame('fast2sms', $updated->active_driver);
        $this->assertSame(6, $updated->otp_length);
        $this->assertSame('F2S_ADMIN_TEST_KEY', $updated->fast2sms_settings['api_key']);
        $this->assertSame('otp', $updated->fast2sms_settings['route']);
    }

    public function test_admin_can_trigger_send_test_otp_action(): void
    {
        $user = User::query()->first();

        $this->actingAs($user);

        Livewire::test(ManageOtpSettings::class)
            ->callAction('sendTestOtp', [
                'test_phone' => '9876543210',
            ])
            ->assertHasNoActionErrors();

        $this->assertCount(1, LogSmsDriver::$sentMessages);
        $this->assertSame('9876543210', LogSmsDriver::$sentMessages[0]['phone']);
        $this->assertSame('admin-test', LogSmsDriver::$sentMessages[0]['options']['purpose']);
    }

    public function test_admin_can_save_fast2sms_low_balance_threshold(): void
    {
        $user = User::query()->first();

        $this->actingAs($user);

        Livewire::test(ManageOtpSettings::class)
            ->fillForm([
                'active_driver' => 'fast2sms',
                'otp_length' => 4,
                'expiry_minutes' => 5,
                'resend_cooldown_seconds' => 60,
                'max_attempts' => 5,
                'fast2sms_settings' => [
                    'api_key' => 'F2S_THRESHOLD_KEY',
                    'route' => 'otp',
                    'low_balance_threshold' => 150,
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        OtpSetting::clearCache();
        $updated = OtpSetting::current();

        $this->assertSame(150.0, $updated->getLowBalanceThreshold());
    }

    public function test_admin_can_trigger_check_sms_balance_action(): void
    {
        Http::fake([
            'www.fast2sms.com/dev/wallet' => Http::response([
                'return' => true,
                'wallet' => '220.00',
            ], 200),
        ]);

        $setting = OtpSetting::current();
        $setting->update([
            'active_driver' => 'fast2sms',
            'fast2sms_settings' => [
                'api_key' => 'F2S_BALANCE_TEST_KEY',
                'low_balance_threshold' => 50,
            ],
        ]);
        OtpSetting::clearCache();

        $user = User::query()->first();
        $this->actingAs($user);

        Livewire::test(ManageOtpSettings::class)
            ->callAction('checkBalance')
            ->assertHasNoActionErrors();
    }

    public function test_sms_usage_overview_widget_renders(): void
    {
        $user = User::query()->first();
        $this->actingAs($user);

        Livewire::test(SmsUsageOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee('SMS Dispatched Today')
            ->assertSee('SMS Sent This Month')
            ->assertSee('OTP Verification Rate');
    }

    public function test_sms_balance_alert_widget_renders_when_balance_is_low(): void
    {
        Http::fake([
            'www.fast2sms.com/dev/wallet' => Http::response([
                'return' => true,
                'wallet' => '12.50',
            ], 200),
        ]);

        $setting = OtpSetting::current();
        $setting->update([
            'active_driver' => 'fast2sms',
            'fast2sms_settings' => [
                'api_key' => 'F2S_LOW_BAL_KEY',
                'low_balance_threshold' => 50,
            ],
        ]);
        OtpSetting::clearCache();

        $user = User::query()->first();
        $this->actingAs($user);

        $this->assertTrue(SmsBalanceAlertWidget::canView());

        Livewire::test(SmsBalanceAlertWidget::class)
            ->assertSuccessful()
            ->assertSee('Fast2SMS Gateway Low Balance Alert')
            ->assertSee('12.50')
            ->assertSee('50.00');
    }
}
