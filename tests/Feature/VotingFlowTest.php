<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\VotingMode;
use App\Models\Award;
use App\Models\Event;
use App\Models\Otp;
use App\Models\OtpSetting;
use App\Models\Participant;
use App\Models\Vote;
use App\Services\Otp\Drivers\LogSmsDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingFlowTest extends TestCase
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
                'message_template' => 'Your code is :otp.',
            ]
        );
        OtpSetting::clearCache();
    }

    protected function createEvent(array $attributes = []): Event
    {
        return Event::create(array_merge([
            'name' => 'Sharad Samman 2026',
            'display_name' => 'Sharad Samman 2026',
            'year' => 2026,
            'slug' => 'sharad-samman-2026',
            'status' => EventStatus::Active,
            'is_voting_active' => true,
            'voting_mode' => VotingMode::Participant,
        ], $attributes));
    }

    protected function createParticipant(Event $event, array $attributes = []): Participant
    {
        return Participant::create(array_merge([
            'event_id' => $event->id,
            'name' => 'Test Puja Committee '.uniqid(),
            'display_name' => 'Test Puja Committee',
            'mobile_number' => '9123456789',
            'zone' => 'Central',
            'locality' => 'Main Road',
            'primary_display_image' => 'pandal.jpg',
            'year' => 2026,
            'registration_status' => 'approved',
        ], $attributes));
    }

    protected function createAward(Event $event, array $attributes = []): Award
    {
        return Award::create(array_merge([
            'event_id' => $event->id,
            'name' => 'Award '.uniqid(),
            'display_name' => 'Award Display Name',
            'category' => 'Puja Excellence',
            'year' => 2026,
            'sort_order' => 1,
            'is_active' => true,
        ], $attributes));
    }

    protected function getLastOtpCode(): string
    {
        $lastMessage = end(LogSmsDriver::$sentMessages);
        preg_match('/\d{6}/', $lastMessage['message'] ?? '', $matches);

        return $matches[0] ?? '';
    }

    public function test_participant_mode_allows_one_vote_per_mobile_for_entire_event(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
            'voting_mode' => VotingMode::Participant,
        ]);

        $participant1 = $this->createParticipant($event);
        $participant2 = $this->createParticipant($event);

        $mobile = '9876543210';

        // 1. Initial eligibility check passes
        $checkResponse = $this->postJson('/api/v1/votes/check', [
            'event_id' => $event->id,
            'phone' => $mobile,
        ]);
        $checkResponse->assertStatus(200)->assertJson(['allowed' => true]);

        // 2. Request OTP
        $otpResponse = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
        ]);
        $otpResponse->assertStatus(200);

        $code = $this->getLastOtpCode();
        $this->assertNotEmpty($code);

        // 3. Cast first vote
        $voteResponse = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'phone' => $mobile,
            'otp' => $code,
        ]);
        $voteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'voting_mode' => 'participant',
                ],
            ]);

        $this->assertDatabaseHas('votes', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'mobile_number' => $mobile,
        ]);

        // 4. Subsequent eligibility check fails
        $checkAgain = $this->postJson('/api/v1/votes/check', [
            'event_id' => $event->id,
            'phone' => $mobile,
        ]);
        $checkAgain->assertStatus(422)->assertJson(['allowed' => false]);

        // 5. Pre-flight OTP send fails (saving SMS cost)
        $otpAgain = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
        ]);
        $otpAgain->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // 6. Attempting to cast another vote for participant2 fails
        $secondVote = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant2->id,
            'phone' => $mobile,
            'otp' => '123456',
        ]);
        $secondVote->assertStatus(422);
        $this->assertSame(1, Vote::where('event_id', $event->id)->where('mobile_number', $mobile)->count());
    }

    public function test_participant_and_award_mode_allows_one_vote_per_award_category(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
            'voting_mode' => VotingMode::ParticipantAndAward,
        ]);

        $award1 = $this->createAward($event, [
            'name' => 'Best Puja',
            'display_name' => 'Best Puja',
        ]);
        $award2 = $this->createAward($event, [
            'name' => 'Best Pratima',
            'display_name' => 'Best Pratima',
        ]);

        $participantA = $this->createParticipant($event);
        $participantB = $this->createParticipant($event);

        $mobile = '9876543210';

        // 1. Send OTP and Vote for Award 1 (Best Puja)
        $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'award_id' => $award1->id,
        ])->assertStatus(200);

        $code1 = $this->getLastOtpCode();

        $vote1 = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participantA->id,
            'award_id' => $award1->id,
            'phone' => $mobile,
            'otp' => $code1,
        ]);
        $vote1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'voting_mode' => 'participant_and_award',
                ],
            ]);

        $this->assertDatabaseHas('votes', [
            'event_id' => $event->id,
            'participant_id' => $participantA->id,
            'award_id' => $award1->id,
            'mobile_number' => $mobile,
        ]);

        // 2. Pre-flight OTP for Award 1 is now rejected (already voted in this award)
        $otpAward1Again = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'award_id' => $award1->id,
        ]);
        $otpAward1Again->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // 3. BUT pre-flight OTP for Award 2 (Best Pratima) is allowed!
        // Clear cooldown for test so we can request OTP for award 2
        Otp::query()->delete();

        $otpAward2 = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'award_id' => $award2->id,
        ]);
        $otpAward2->assertStatus(200);

        $code2 = $this->getLastOtpCode();

        // 4. Casting vote for Award 2 succeeds
        $vote2 = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participantB->id,
            'award_id' => $award2->id,
            'phone' => $mobile,
            'otp' => $code2,
        ]);
        $vote2->assertStatus(200);

        $this->assertDatabaseHas('votes', [
            'event_id' => $event->id,
            'participant_id' => $participantB->id,
            'award_id' => $award2->id,
            'mobile_number' => $mobile,
        ]);

        // Total 2 votes cast: 1 for Award 1 and 1 for Award 2
        $this->assertSame(2, Vote::where('event_id', $event->id)->where('mobile_number', $mobile)->count());
    }

    public function test_participant_and_award_mode_requires_award_id(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
            'voting_mode' => VotingMode::ParticipantAndAward,
        ]);
        $participant = $this->createParticipant($event);

        $response = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'phone' => '9876543210',
            'otp' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please select an award category for this vote.',
            ]);
    }

    public function test_cannot_vote_when_event_voting_is_inactive(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => false,
            'voting_mode' => VotingMode::Participant,
        ]);
        $participant = $this->createParticipant($event);

        // OTP pre-flight rejects
        $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'voting',
            'event_id' => $event->id,
        ])->assertStatus(422)->assertJson(['message' => 'Voting is currently closed for this event.']);

        // Cast rejects
        $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'phone' => '9876543210',
            'otp' => '123456',
        ])->assertStatus(422)->assertJson(['message' => 'Voting has officially concluded for this event.']);
    }
}
