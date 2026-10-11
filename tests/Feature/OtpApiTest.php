<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\OtpSetting;
use App\Services\Otp\Drivers\LogSmsDriver;
use App\Services\Otp\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        LogSmsDriver::clearSentMessages();
        OtpSetting::clearCache();

        OtpSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'active_driver' => 'log',
                'otp_length' => 6,
                'expiry_minutes' => 5,
                'resend_cooldown_seconds' => 60,
                'max_attempts' => 5,
                'message_template' => 'Your test code is :otp.',
            ]
        );
        OtpSetting::clearCache();
    }

    public function test_can_send_otp_via_api(): void
    {
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'verification',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP sent successfully to your mobile number.',
            ])
            ->assertJsonStructure([
                'data' => ['resend_after_seconds', 'expires_in_seconds'],
            ]);

        $this->assertDatabaseHas('otps', [
            'phone' => '9876543210',
            'purpose' => 'verification',
        ]);

        $this->assertCount(1, LogSmsDriver::$sentMessages);
        $this->assertSame('9876543210', LogSmsDriver::$sentMessages[0]['phone']);
    }

    public function test_send_otp_enforces_cooldown_period(): void
    {
        $first = $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);
        $first->assertStatus(200);

        $second = $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);
        $second->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('data.resend_after_seconds', fn ($sec) => $sec > 0);
    }

    public function test_can_verify_otp_successfully(): void
    {
        $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);

        $message = LogSmsDriver::$sentMessages[0]['message'];
        preg_match('/\d{6}/', $message, $matches);
        $otpCode = $matches[0];

        $response = $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => $otpCode,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);

        /** @var Otp $otpRecord */
        $otpRecord = Otp::query()->where('phone', '9876543210')->first();
        $this->assertNotNull($otpRecord->verified_at);
        $this->assertTrue($otpRecord->isVerified());
    }

    public function test_invalid_otp_increments_attempts(): void
    {
        $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);

        $response = $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => '000000',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('data.attempts_remaining', 4);

        $otpRecord = Otp::query()->where('phone', '9876543210')->first();
        $this->assertSame(1, $otpRecord->attempts);
    }

    public function test_otp_locks_out_after_max_attempts(): void
    {
        $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/otp/verify', [
                'phone' => '9876543210',
                'code' => '000000',
            ]);
        }

        // Sixth attempt should be locked out
        $lockoutResponse = $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => '000000',
        ]);

        $lockoutResponse->assertStatus(422);
    }

    public function test_expired_otp_is_rejected(): void
    {
        Otp::create([
            'phone' => '9876543210',
            'code_hash' => hash('sha256', '123456'),
            'purpose' => 'verification',
            'expires_at' => now()->subMinutes(10),
            'attempts' => 0,
        ]);

        $response = $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'This OTP has expired. Please request a new one.',
            ]);
    }

    public function test_master_otp_verifies_test_phones(): void
    {
        $setting = OtpSetting::current();
        $setting->update([
            'master_otp' => '999888',
            'test_phone_numbers' => '919876543210, 9999999999',
        ]);
        OtpSetting::clearCache();

        $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);

        // Test phone should verify with master OTP
        $response = $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => '999888',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_otp_service_is_verified_helper(): void
    {
        /** @var OtpService $service */
        $service = app(OtpService::class);

        $this->assertFalse($service->isVerified('9876543210'));

        $this->postJson('/api/v1/otp/send', ['phone' => '9876543210']);
        $message = LogSmsDriver::$sentMessages[0]['message'];
        preg_match('/\d{6}/', $message, $matches);

        $this->postJson('/api/v1/otp/verify', [
            'phone' => '9876543210',
            'code' => $matches[0],
        ]);

        $this->assertTrue($service->isVerified('9876543210'));
    }
}
