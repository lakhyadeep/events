<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterSuperadmin;
use App\Filament\Resources\Awards\Pages\CreateAward;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Votes\Pages\ListVotes;
use App\Filament\Resources\Votes\Pages\ViewVote;
use App\Filament\Resources\Votes\RelationManagers\OtpsRelationManager;
use App\Filament\Resources\Votes\RelationManagers\SameIpVotesRelationManager;
use App\Filament\Resources\Votes\Schemas\VoteInfolist;
use App\Filament\Resources\Votes\VoteResource;
use App\Http\Resources\ParticipantResource;
use App\Models\Event;
use App\Models\Otp;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Vote;
use App\Models\Winner;
use Database\Seeders\DatabaseSeeder;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_user_can_access_filament_login(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_login_page_shows_setup_text_only_when_zero_users_exist(): void
    {
        $this->assertGreaterThan(0, User::count());
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertDontSee('Initial Setup: Create Superadmin');

        User::query()->delete();

        $responseEmpty = $this->get('/admin/login');
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertSee('Initial Setup: Create Superadmin');
    }

    public function test_superadmin_can_register_via_ui_when_zero_users_exist(): void
    {
        User::query()->delete();

        $component = Livewire::test(RegisterSuperadmin::class)
            ->fillForm([
                'name' => 'Initial Superadmin',
                'email' => 'superadmin@dib24x7.com',
                'password' => 'SecurePass123!',
                'passwordConfirmation' => 'SecurePass123!',
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin');

        $user = User::where('email', 'superadmin@dib24x7.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Initial Superadmin', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('SecurePass123!', $user->password));
    }

    public function test_registration_is_permanently_locked_once_user_exists(): void
    {
        $this->assertGreaterThan(0, User::count());

        $response = $this->get('/admin/register');

        $response->assertRedirect('/admin/login');
    }

    public function test_fixed_admin_credentials_can_authenticate(): void
    {
        $this->assertTrue(Auth::attempt([
            'email' => 'admin@dib24x7.com',
            'password' => 'password',
        ]));

        $admin = User::where('email', 'admin@dib24x7.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Today&#039;s Verified Votes', false);
        $response->assertSee('Unique Voters');
        $response->assertSee('Votes Cast Today');
        $response->assertSee('SMS Dispatched Today');
        $response->assertSee('Live Public Voting Leaderboard');
        $response->assertDontSee('Filament community');

        // Test second visit when stats are loaded from cache
        $cachedResponse = $this->actingAs($admin)->get('/admin');
        $cachedResponse->assertStatus(200);
        $cachedResponse->assertSee('Today&#039;s Verified Votes', false);
    }

    public function test_associate_sponsors_scope_limits_to_five(): void
    {
        $event = Event::first() ?? Event::factory()->create();

        if (Sponsor::where('event_id', $event->id)->associate()->count() === 0) {
            Sponsor::create([
                'event_id' => $event->id,
                'name' => 'Sample Sponsor',
                'category' => 'associate',
                'is_active' => true,
            ]);
        }

        // Count associate sponsors retrieved via scope
        $associateSponsors = Sponsor::where('event_id', $event->id)->associate()->get();

        $this->assertLessThanOrEqual(5, $associateSponsors->count());
        $this->assertGreaterThan(0, $associateSponsors->count());
    }

    public function test_winner_model_relationships(): void
    {
        $winner = Winner::factory()->create([
            'status' => 'published',
        ]);

        $this->assertNotNull($winner);
        $this->assertNotNull($winner->event);
        $this->assertNotNull($winner->award);
        $this->assertNotNull($winner->participant);
        $this->assertEquals('published', $winner->status);
    }

    public function test_admin_can_load_all_resource_list_pages(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $routes = [
            '/admin/events',
            '/admin/banners',
            '/admin/timeline-items',
            '/admin/participants',
            '/admin/awards',
            '/admin/winners',
            '/admin/sponsors',
            '/admin/video-shorts',
            '/admin/zones',
            '/admin/localities',
            '/admin/users',
            '/admin/votes',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_admin_can_load_participant_edit_page(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();
        $participant = Participant::factory()->create();

        $this->assertNotNull($participant);

        $response = $this->actingAs($admin)->get("/admin/participants/{$participant->id}/edit");
        $response->assertStatus(200);
    }

    public function test_admin_can_create_and_manage_users(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first();

        $newUser = User::create([
            'name' => 'Editor Person',
            'email' => 'editor@dib24x7.com',
            'password' => 'secret123',
            'email_verified_at' => now(),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'editor@dib24x7.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('editor@dib24x7.com');
    }

    public function test_event_slug_is_automatically_generated_from_display_name(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first();

        Livewire::actingAs($admin)
            ->test(CreateEvent::class)
            ->fillForm([
                'display_name' => 'Kolkata Festival 2026',
            ])
            ->assertFormSet([
                'slug' => 'kolkata-festival-2026',
            ]);
    }

    public function test_delete_action_requires_valid_administrator_password(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first();

        $userToDelete = User::factory()->create([
            'name' => 'Temporary User',
            'email' => 'temp@dib24x7.com',
        ]);

        // Attempt delete with incorrect password
        Livewire::actingAs($admin)
            ->test(EditUser::class, [
                'record' => $userToDelete->id,
            ])
            ->callAction('delete', data: [
                'current_password' => 'wrong-password-123',
            ])
            ->assertHasActionErrors(['current_password']);

        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);

        // Attempt delete with correct administrator password
        Livewire::actingAs($admin)
            ->test(EditUser::class, [
                'record' => $userToDelete->id,
            ])
            ->callAction('delete', data: [
                'current_password' => 'password',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_can_create_award_without_category_group(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();
        $event = Event::first() ?? Event::factory()->create();

        Livewire::actingAs($admin)
            ->test(CreateAward::class)
            ->fillForm([
                'name' => 'Special Heritage Award',
                'display_name' => 'Special Heritage Award (ঐতিহ্যবাহী সম্মাননা)',
                'category' => null,
                'event_id' => $event->id,
                'year' => 2026,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('awards', [
            'name' => 'Special Heritage Award',
            'category' => null,
        ]);
    }

    public function test_admin_can_update_participant_google_map_url(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();
        $participant = Participant::factory()->create();
        $mapUrl = 'https://maps.app.goo.gl/example123';

        Livewire::actingAs($admin)
            ->test(EditParticipant::class, [
                'record' => $participant->id,
            ])
            ->fillForm([
                'google_map_url' => $mapUrl,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'google_map_url' => $mapUrl,
        ]);
    }

    public function test_participant_resource_includes_google_map_url(): void
    {
        $participant = Participant::factory()->create([
            'google_map_url' => 'https://maps.google.com/?q=27.4728,94.9120',
        ]);

        $resource = (new ParticipantResource($participant))->toArray(request());

        $this->assertArrayHasKey('google_map_url', $resource);
        $this->assertSame('https://maps.google.com/?q=27.4728,94.9120', $resource['google_map_url']);
    }

    public function test_votes_export_csv_includes_voter_name(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id]);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Rahul Sharma',
            'mobile_number' => '9876543210',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1',
        ]);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => null,
            'mobile_number' => '9876543211',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',
        ]);

        $component = Livewire::actingAs($admin)
            ->test(ListVotes::class);

        $action = $component->instance()->getAction('export_csv');
        $this->assertNotNull($action);

        $response = $action->call();
        $this->assertInstanceOf(StreamedResponse::class, $response);

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Voter Name', $csvContent);
        $this->assertStringContainsString('Rahul Sharma', $csvContent);
        $this->assertStringContainsString('Anonymous', $csvContent);
        $this->assertStringContainsString('Device', $csvContent);
        $this->assertStringContainsString('iPhone', $csvContent);
        $this->assertStringContainsString('Windows PC', $csvContent);
        $this->assertStringNotContainsString('AppleWebKit', $csvContent);
    }

    public function test_votes_table_same_ip_filter_works(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id]);

        $vote1 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543201',
            'ip_address' => '192.168.1.50',
        ]);

        $vote2 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543202',
            'ip_address' => '192.168.1.50',
        ]);

        $vote3 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => '9876543203',
            'ip_address' => '192.168.1.99',
        ]);

        // Filter for shared/duplicate IP
        Livewire::actingAs($admin)
            ->test(ListVotes::class)
            ->filterTable('same_ip', 'shared')
            ->assertCanSeeTableRecords([$vote1, $vote2])
            ->assertCanNotSeeTableRecords([$vote3]);

        // Filter for unique IP
        Livewire::actingAs($admin)
            ->test(ListVotes::class)
            ->filterTable('same_ip', 'unique')
            ->assertCanSeeTableRecords([$vote3])
            ->assertCanNotSeeTableRecords([$vote1, $vote2]);
    }

    public function test_vote_otp_audit_trail_resolves_correctly(): void
    {
        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id]);

        $phone = '9876543299';
        Otp::create([
            'phone' => $phone,
            'code_hash' => hash('sha256', '123456'),
            'purpose' => 'voting',
            'expires_at' => now()->addMinutes(10),
            'verified_at' => now()->subMinute(),
            'attempts' => 1,
            'ip_address' => '10.0.0.1',
        ]);

        $vote = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'mobile_number' => $phone,
            'ip_address' => '10.0.0.1',
        ]);

        $trail = $vote->otp_audit_trail;

        $this->assertTrue($trail['has_otp']);
        $this->assertSame('Verified', $trail['status']);
        $this->assertSame('success', $trail['status_color']);
        $this->assertTrue($trail['ip_matches_vote']);
        $this->assertSame(1, $trail['attempts']);
        $this->assertSame('10.0.0.1', $trail['otp_ip']);
        $this->assertCount(1, $trail['recent_otps']);
    }

    public function test_vote_same_ip_relation_manager_can_bulk_void_votes(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id]);

        $vote1 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Bulk Keep',
            'mobile_number' => '9876543301',
            'ip_address' => '10.50.0.9',
        ]);

        $vote2 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Bulk Void A',
            'mobile_number' => '9876543302',
            'ip_address' => '10.50.0.9',
        ]);

        $vote3 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Bulk Void B',
            'mobile_number' => '9876543303',
            'ip_address' => '10.50.0.9',
        ]);

        Livewire::actingAs($admin)
            ->test(SameIpVotesRelationManager::class, [
                'ownerRecord' => $vote1,
                'pageClass' => ViewVote::class,
            ])
            ->assertCanSeeTableRecords([$vote2, $vote3])
            ->assertCanNotSeeTableRecords([$vote1])
            ->assertDontSee('Bulk Keep')
            ->assertSee('Bulk Void A')
            ->assertSee('Bulk Void B')
            ->callTableBulkAction('delete', [$vote2, $vote3], data: ['current_password' => 'password'])
            ->assertCanNotSeeTableRecords([$vote2, $vote3]);

        $this->assertDatabaseMissing('votes', ['id' => $vote2->id]);
        $this->assertDatabaseMissing('votes', ['id' => $vote3->id]);
        $this->assertDatabaseHas('votes', ['id' => $vote1->id]);
    }

    public function test_vote_infolist_can_be_configured_and_rendered(): void
    {
        $schema = new Schema;
        $configured = VoteInfolist::configure($schema);

        $this->assertInstanceOf(Schema::class, $configured);
        $this->assertNotEmpty($configured->getComponents());
    }

    public function test_admin_can_view_vote_infolist_modal(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id, 'display_name' => 'Puja Committee Alpha']);

        $vote = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Dipankar Roy',
            'mobile_number' => '9876543888',
            'ip_address' => '192.168.10.15',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
        ]);

        Livewire::actingAs($admin)
            ->test(ListVotes::class)
            ->assertTableActionExists('view')
            ->assertTableActionExists('same_ip_votes');
    }

    public function test_admin_can_access_view_vote_page(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id, 'display_name' => 'Puja Committee Gamma']);

        $vote = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Kalyan Mukherjee',
            'mobile_number' => '9876543999',
            'ip_address' => '192.168.10.99',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
        ]);

        $url = VoteResource::getUrl('view', ['record' => $vote]);

        $response = $this->actingAs($admin)->get($url);
        $response->assertStatus(200);
        $response->assertSee('Kalyan Mukherjee');
        $response->assertSee('Puja Committee Gamma');
        $response->assertSee('Cryptographic OTP Verification Trail');
        $response->assertSee('Integrity &amp; Fraud Check', false);

        Livewire::actingAs($admin)
            ->test(ViewVote::class, ['record' => $vote->id])
            ->assertActionExists('blacklist')
            ->assertActionExists('delete');
    }

    public function test_vote_otps_relation_manager_renders_audit_trail(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id]);

        $vote = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Rahul Roy',
            'mobile_number' => '9876543110',
            'ip_address' => '10.0.0.5',
        ]);

        $otp = Otp::create([
            'phone' => '9876543110',
            'code_hash' => Hash::make('123456'),
            'purpose' => 'vote_verification',
            'expires_at' => now()->addMinutes(5),
            'verified_at' => now(),
            'attempts' => 0,
            'ip_address' => '10.0.0.5',
        ]);

        Livewire::actingAs($admin)
            ->test(OtpsRelationManager::class, [
                'ownerRecord' => $vote,
                'pageClass' => ViewVote::class,
            ])
            ->assertCanSeeTableRecords([$otp])
            ->assertSee('Verified')
            ->assertSee('10.0.0.5');
    }

    public function test_vote_same_ip_relation_manager_renders_and_can_void_votes(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first() ?? User::factory()->admin()->create();

        $event = Event::first() ?? Event::factory()->create();
        $participant = Participant::factory()->create(['event_id' => $event->id, 'display_name' => 'Puja Committee Zeta']);

        $vote1 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Same IP First',
            'mobile_number' => '9876543211',
            'ip_address' => '192.168.99.1',
        ]);

        $vote2 = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'voter_name' => 'Same IP Second',
            'mobile_number' => '9876543212',
            'ip_address' => '192.168.99.1',
        ]);

        Livewire::actingAs($admin)
            ->test(SameIpVotesRelationManager::class, [
                'ownerRecord' => $vote1,
                'pageClass' => ViewVote::class,
            ])
            ->assertCanSeeTableRecords([$vote2])
            ->assertCanNotSeeTableRecords([$vote1])
            ->assertDontSee('Same IP First')
            ->assertTableActionExists('view', record: $vote2)
            ->callTableAction('delete', $vote2, data: ['current_password' => 'password'])
            ->assertCanNotSeeTableRecords([$vote2]);

        $this->assertDatabaseMissing('votes', ['id' => $vote2->id]);
        $this->assertDatabaseHas('votes', ['id' => $vote1->id]);
    }
}
