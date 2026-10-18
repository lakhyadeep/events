<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\VotingMode;
use App\Models\Award;
use App\Models\Event;
use App\Models\Otp;
use App\Models\OtpSetting;
use App\Models\Participant;
use App\Services\Fraud\FraudPreventionService;
use App\Services\Otp\Drivers\LogSmsDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FraudPreventionTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected Participant $participant;

    protected Award $award;

    protected function setUp(): void
    {
        parent::setUp();
        LogSmsDriver::clearSentMessages();
        Cache::flush();
        OtpSetting::clearCache();

        OtpSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'active_driver' => 'log',
                'otp_length' => 4,
                'expiry_minutes' => 5,
                'resend_cooldown_seconds' => 60,
                'max_attempts' => 5,
                'message_template' => 'Your OTP is :otp.',
            ]
        );
        OtpSetting::clearCache();
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->event = Event::create([
            'name' => 'Test Festival 2026',
            'display_name' => 'Test Festival 2026',
            'year' => 2026,
            'slug' => 'test-festival-2026',
            'status' => EventStatus::Active,
            'is_voting_active' => true,
            'voting_mode' => VotingMode::Participant,
        ]);

        $this->participant = Participant::create([
            'event_id' => $this->event->id,
            'name' => 'Puja Committee Alpha',
            'display_name' => 'Puja Committee Alpha',
            'mobile_number' => '9123456789',
            'zone' => 'Central',
            'locality' => 'Main Road',
            'primary_display_image' => 'pandal.jpg',
            'year' => 2026,
            'registration_status' => 'approved',
            'slug' => 'puja-alpha',
            'is_active' => true,
        ]);

        $this->award = Award::create([
            'event_id' => $this->event->id,
            'name' => 'Best Decoration',
            'display_name' => 'Best Decoration',
            'category' => 'Puja Excellence',
            'year' => 2026,
            'sort_order' => 1,
            'is_active' => true,
            'is_voting_allowed' => true,
        ]);
    }

    public function test_rejects_non_indian_and_invalid_format_mobile_numbers(): void
    {
        // US burner number (+1...)
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => '+12025550123',
            'purpose' => 'voting',
            'event_id' => $this->event->id,
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please provide a valid 10-digit Indian mobile number.',
            ]);

        // Dummy number starting with 0-5
        $response2 = $this->postJson('/api/v1/otp/send', [
            'phone' => '1234567890',
            'purpose' => 'voting',
            'event_id' => $this->event->id,
        ]);
        $response2->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please provide a valid 10-digit Indian mobile number.',
            ]);
    }

    public function test_normalizes_and_accepts_valid_indian_mobile_numbers(): void
    {
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => '+91 98765 43210',
            'purpose' => 'verification',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('otps', [
            'phone' => '9876543210',
        ]);
    }

    public function test_blocks_disposable_and_virtual_blacklist_numbers(): void
    {
        // 7011982736 is configured in fraud.disposable_numbers
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => '7011982736',
            'purpose' => 'voting',
            'event_id' => $this->event->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'This mobile number is flagged as a virtual or temporary service and cannot be used for voting.',
            ]);
    }

    public function test_blocks_repeating_dummy_numbers_unless_test_phone(): void
    {
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => '8888888888',
            'purpose' => 'voting',
            'event_id' => $this->event->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'This mobile number is flagged as a virtual or temporary service and cannot be used for voting.',
            ]);
    }

    public function test_enforces_hourly_otp_limit_per_phone(): void
    {
        $phone = '9811122233';

        // Seed 5 recent OTP requests in database for this phone
        for ($i = 0; $i < 5; $i++) {
            Otp::create([
                'phone' => $phone,
                'code_hash' => hash('sha256', '1234'),
                'purpose' => 'verification',
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
                'created_at' => now()->subMinutes(10),
            ]);
        }

        // 6th request within the hour should be blocked
        $response = $this->postJson('/api/v1/otp/send', [
            'phone' => $phone,
            'purpose' => 'verification',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Too many OTP requests for this mobile number. Please try again after some time.',
            ]);
    }

    public function test_device_phone_limit_blocks_excessive_numbers_from_same_device(): void
    {
        $visitorCookie = 'device-visitor-uuid-12345';

        // Record 3 distinct phones for this visitor in FraudPreventionService
        $fraudService = app(FraudPreventionService::class);
        $fraudService->recordDevicePhone($visitorCookie, $this->event->id, '9800000001');
        $fraudService->recordDevicePhone($visitorCookie, $this->event->id, '9800000002');
        $fraudService->recordDevicePhone($visitorCookie, $this->event->id, '9800000003');

        // Now attempt 4th phone from same device
        $response = $this->withHeader('X-Visitor-Id', $visitorCookie)
            ->postJson('/api/v1/otp/send', [
                'phone' => '9800000004',
                'purpose' => 'voting',
                'event_id' => $this->event->id,
                'participant_id' => $this->participant->id,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Maximum voting limit reached for this device. Multiple votes using different phone numbers on the same device are not permitted.',
            ]);

        // Attempt eligibility check with 4th phone
        $checkResponse = $this->withHeader('X-Visitor-Id', $visitorCookie)
            ->postJson('/api/v1/votes/check', [
                'phone' => '9800000004',
                'event_id' => $this->event->id,
                'participant_id' => $this->participant->id,
            ]);

        $checkResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'allowed' => false,
                'message' => 'Maximum voting limit reached for this device. Multiple votes using different phone numbers on the same device are not permitted.',
            ]);
    }
}
