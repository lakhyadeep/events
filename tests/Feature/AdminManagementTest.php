<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterSuperadmin;
use App\Filament\Resources\Awards\Pages\CreateAward;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Http\Resources\ParticipantResource;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Winner;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
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
        $response->assertSee('Active Events');
        $response->assertSee('Approved Candidates');
        $response->assertSee('Sponsors & Partners');
        $response->assertSee('Puja Contest Entries');
        $response->assertSee('Shortlisted Contenders');
        $response->assertDontSee('Filament community');

        // Test second visit when stats are loaded from cache
        $cachedResponse = $this->actingAs($admin)->get('/admin');
        $cachedResponse->assertStatus(200);
        $cachedResponse->assertSee('Active Events');
    }

    public function test_associate_sponsors_scope_limits_to_five(): void
    {
        $event = Event::first();

        // Count associate sponsors retrieved via scope
        $associateSponsors = Sponsor::where('event_id', $event->id)->associate()->get();

        $this->assertLessThanOrEqual(5, $associateSponsors->count());
        $this->assertGreaterThan(0, $associateSponsors->count());
    }

    public function test_winner_model_relationships(): void
    {
        $winner = Winner::with(['event', 'award', 'participant'])->first();

        $this->assertNotNull($winner);
        $this->assertNotNull($winner->event);
        $this->assertNotNull($winner->award);
        $this->assertNotNull($winner->participant);
        $this->assertEquals('published', $winner->status);
    }

    public function test_admin_can_load_all_resource_list_pages(): void
    {
        $admin = User::where('email', 'admin@dib24x7.com')->first();

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
        $admin = User::where('email', 'admin@dib24x7.com')->first();
        $participant = Participant::first();

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
        $admin = User::where('email', 'admin@dib24x7.com')->first();
        $event = Event::first();

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
        $admin = User::where('email', 'admin@dib24x7.com')->first();
        $participant = Participant::first();
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
        $participant = Participant::first();
        $participant->update([
            'google_map_url' => 'https://maps.google.com/?q=27.4728,94.9120',
        ]);

        $resource = (new ParticipantResource($participant))->toArray(request());

        $this->assertArrayHasKey('google_map_url', $resource);
        $this->assertSame('https://maps.google.com/?q=27.4728,94.9120', $resource['google_map_url']);
    }
}
