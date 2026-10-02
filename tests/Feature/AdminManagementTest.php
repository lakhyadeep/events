<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Winner;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
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
}
