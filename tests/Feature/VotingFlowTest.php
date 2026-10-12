<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\VotingMode;
use App\Models\Award;
use App\Models\Event;
use App\Models\Otp;
use App\Models\OtpSetting;
use App\Models\Participant;
use App\Models\User;
use App\Models\Vote;
use App\Services\Otp\Drivers\LogSmsDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
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
        $this->withoutMiddleware(ThrottleRequests::class);
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

    public function test_candidate_voting_allows_one_vote_per_candidate_and_allows_voting_for_other_candidates(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
        ]);

        $participant1 = $this->createParticipant($event);
        $participant2 = $this->createParticipant($event);

        $mobile = '9876543210';

        // 1. Initial eligibility check passes for participant 1
        $checkResponse = $this->postJson('/api/v1/votes/check', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'phone' => $mobile,
        ]);
        $checkResponse->assertStatus(200)->assertJson(['allowed' => true]);

        // 2. Request OTP for participant 1
        $otpResponse = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
        ]);
        $otpResponse->assertStatus(200);

        $code = $this->getLastOtpCode();
        $this->assertNotEmpty($code);

        // 3. Cast vote for participant 1
        $voteResponse = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'phone' => $mobile,
            'otp' => $code,
        ]);
        $voteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('votes', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'mobile_number' => $mobile,
        ]);

        // 4. Duplicate vote attempt for participant 1 fails
        $checkAgain = $this->postJson('/api/v1/votes/check', [
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
            'phone' => $mobile,
        ]);
        $checkAgain->assertStatus(422)->assertJson(['allowed' => false]);

        $otpAgain = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'participant_id' => $participant1->id,
        ]);
        $otpAgain->assertStatus(422)->assertJson(['success' => false]);

        // 5. BUT the same mobile CAN vote for participant 2!
        Otp::query()->delete(); // Clear cooldown for testing

        $checkP2 = $this->postJson('/api/v1/votes/check', [
            'event_id' => $event->id,
            'participant_id' => $participant2->id,
            'phone' => $mobile,
        ]);
        $checkP2->assertStatus(200)->assertJson(['allowed' => true]);

        $otpP2 = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'participant_id' => $participant2->id,
        ]);
        $otpP2->assertStatus(200);
        $code2 = $this->getLastOtpCode();

        $voteP2 = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant2->id,
            'phone' => $mobile,
            'otp' => $code2,
        ]);
        $voteP2->assertStatus(200);

        $this->assertDatabaseHas('votes', [
            'event_id' => $event->id,
            'participant_id' => $participant2->id,
            'mobile_number' => $mobile,
        ]);
        $this->assertSame(2, Vote::where('event_id', $event->id)->where('mobile_number', $mobile)->count());
    }

    public function test_award_voting_allows_one_vote_per_award_category_and_allows_voting_across_multiple_award_categories(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
        ]);

        $award1 = $this->createAward($event, [
            'name' => 'Best Puja',
            'display_name' => 'Best Puja',
        ]);
        $award2 = $this->createAward($event, [
            'name' => 'Best Pratima',
            'display_name' => 'Best Pratima',
        ]);
        $award3 = $this->createAward($event, [
            'name' => 'Best Lighting',
            'display_name' => 'Best Lighting',
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
        Otp::query()->delete();

        $otpAward2 = $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'award_id' => $award2->id,
        ]);
        $otpAward2->assertStatus(200);

        $code2 = $this->getLastOtpCode();

        // 4. Casting vote for Award 2 succeeds (even for the same participant!)
        $vote2 = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participantA->id,
            'award_id' => $award2->id,
            'phone' => $mobile,
            'otp' => $code2,
        ]);
        $vote2->assertStatus(200);

        // 5. Pre-flight OTP for Award 3 (Best Lighting) is also allowed!
        Otp::query()->delete();

        $this->postJson('/api/v1/otp/send', [
            'phone' => $mobile,
            'purpose' => 'voting',
            'event_id' => $event->id,
            'award_id' => $award3->id,
        ])->assertStatus(200);

        $code3 = $this->getLastOtpCode();

        $vote3 = $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participantB->id,
            'award_id' => $award3->id,
            'phone' => $mobile,
            'otp' => $code3,
        ]);
        $vote3->assertStatus(200);

        // Total 3 votes casted across 3 categories
        $this->assertSame(3, Vote::where('event_id', $event->id)->where('mobile_number', $mobile)->count());
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
        ])->assertStatus(422)->assertJson(['message' => 'Voting is currently closed for this event.']);

        // Frontend views show closed state
        $homeResponse = $this->get('/events/'.$event->slug);
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Voting Closed');
        $homeResponse->assertSee('Voting is Closed');

        $directoryResponse = $this->get('/events/'.$event->slug.'/all-pujas');
        $directoryResponse->assertStatus(200);
        $directoryResponse->assertSee('Voting is currently closed for this event.');
    }

    public function test_cannot_vote_when_countdown_deadline_has_passed(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
            'countdown_datetime' => now()->subHour(),
            'voting_mode' => VotingMode::Participant,
        ]);
        $participant = $this->createParticipant($event);

        // Pre-flight OTP rejects with concluded message
        $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'voting',
            'event_id' => $event->id,
        ])->assertStatus(422)->assertJson(['message' => 'Voting has officially concluded for this event.']);

        // Cast rejects
        $this->postJson('/api/v1/votes/cast', [
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'phone' => '9876543210',
            'otp' => '123456',
        ])->assertStatus(422)->assertJson(['message' => 'Voting has officially concluded for this event.']);

        // Frontend views show closed state
        $homeResponse = $this->get('/events/'.$event->slug);
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Voting Closed');
        $homeResponse->assertSee('Voting has officially concluded for this event.');
    }

    public function test_can_vote_when_countdown_deadline_is_in_future(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => true,
            'countdown_datetime' => now()->addDays(5),
            'voting_mode' => VotingMode::Participant,
        ]);
        $participant = $this->createParticipant($event);

        // Pre-flight OTP succeeds
        $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'voting',
            'event_id' => $event->id,
        ])->assertStatus(200)->assertJson(['success' => true]);

        // Home view renders countdown clock
        $homeResponse = $this->get('/events/'.$event->slug);
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('votingCountdownClock');
        $homeResponse->assertSee('Vote Now');
    }

    public function test_custom_closure_message_is_used_when_provided(): void
    {
        $event = $this->createEvent([
            'is_voting_active' => false,
            'closure_message' => 'Special notice: Voting is closed for maintenance.',
            'voting_mode' => VotingMode::Participant,
        ]);
        $participant = $this->createParticipant($event);

        $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'voting',
            'event_id' => $event->id,
        ])->assertStatus(422)->assertJson(['message' => 'Special notice: Voting is closed for maintenance.']);

        $homeResponse = $this->get('/events/'.$event->slug);
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Special notice: Voting is closed for maintenance.');
    }

    public function test_participant_votes_count_is_rendered_on_microsite_and_directory(): void
    {
        $event = $this->createEvent(['is_voting_active' => true]);
        $participant = $this->createParticipant($event, ['registration_status' => 'approved']);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543210',
        ]);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543211',
        ]);

        $homeResponse = $this->get('/events/'.$event->slug);
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('live-vote-count-'.$participant->id);

        $directoryResponse = $this->get('/events/'.$event->slug.'/all-pujas');
        $directoryResponse->assertStatus(200);
        $directoryResponse->assertSee('count-puja-'.$participant->id);
    }

    public function test_admin_can_access_votes_resource(): void
    {
        $event = $this->createEvent();
        $participant = $this->createParticipant($event);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543210',
            'ip_address' => '127.0.0.1',
        ]);

        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/votes');
        $response->assertStatus(200);
        $response->assertSee('Votes');
    }
}
