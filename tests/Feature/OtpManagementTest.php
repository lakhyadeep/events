<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageOtpSettings;
use App\Models\OtpSetting;
use App\Models\User;
use App\Services\Otp\Drivers\LogSmsDriver;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
