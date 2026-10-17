<?php

namespace Tests\Feature;

use App\Filament\Pages\Analytics;
use App\Models\Event;
use App\Models\PageView;
use App\Models\Participant;
use App\Models\User;
use App\Models\UserActivity;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $testDb = database_path('analytics_test.sqlite');
        if (! file_exists($testDb)) {
            touch($testDb);
        }
        $this->artisan('migrate', ['--database' => 'analytics']);
        PageView::truncate();
        UserActivity::truncate();
    }

    public function test_frontend_page_visit_records_page_view_and_sets_cookie(): void
    {
        $event = Event::where('status', 'active')->first() ?? Event::factory()->create(['status' => 'active', 'slug' => 'durga-puja-test']);

        $response = $this->get('/'.$event->slug, [
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
        ]);

        $response->assertStatus(200);
        $response->assertCookie('analytics_vid');

        $this->assertDatabaseHas('page_views', [
            'event_id' => $event->id,
            'page_type' => 'landing',
            'device_type' => 'mobile',
            'platform' => 'iOS',
            'browser' => 'Safari',
        ], 'analytics');
    }

    public function test_bot_user_agents_are_not_recorded(): void
    {
        $event = Event::where('status', 'active')->first() ?? Event::factory()->create(['status' => 'active', 'slug' => 'durga-puja-test']);

        $response = $this->get('/'.$event->slug, [
            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseEmpty('page_views', 'analytics');
    }

    public function test_admin_routes_are_not_tracked_in_page_views(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $this->assertDatabaseEmpty('page_views', 'analytics');
    }

    public function test_behavioral_event_api_records_user_activity(): void
    {
        $event = Event::first();
        $participant = Participant::first();

        $payload = [
            'action_type' => 'map_directions_click',
            'event_id' => $event?->id,
            'target_type' => 'participant',
            'target_id' => $participant?->id,
            'metadata' => [
                'puja_name' => $participant?->name ?? 'Test Puja',
                'map_url' => 'https://maps.google.com/?q=Test',
            ],
            'url' => 'http://localhost/event/all-pujas',
            'device_type' => 'mobile',
        ];

        $response = $this->postJson('/api/v1/analytics/event', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('user_activities', [
            'action_type' => 'map_directions_click',
            'target_type' => 'participant',
            'target_id' => $participant?->id,
            'device_type' => 'mobile',
        ], 'analytics');
    }

    public function test_authenticated_admin_can_access_analytics_page(): void
    {
        $admin = User::first() ?? User::factory()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Audience Behavior & Page Analytics');
    }

    public function test_analytics_livewire_page_mounts_and_filters_correctly(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $this->actingAs($admin);

        Livewire::test(Analytics::class)
            ->assertSuccessful()
            ->set('period', 'today')
            ->assertSet('period', 'today')
            ->set('period', '30days')
            ->assertSet('period', '30days');
    }

    public function test_prune_command_deletes_records_older_than_days(): void
    {
        $oldDate = now()->subDays(100);
        PageView::factory()->create(['created_at' => $oldDate]);
        UserActivity::factory()->create(['created_at' => $oldDate]);

        $recentDate = now()->subDays(5);
        $recentView = PageView::factory()->create(['created_at' => $recentDate]);
        $recentActivity = UserActivity::factory()->create(['created_at' => $recentDate]);

        $this->artisan('analytics:prune', ['--days' => 90])
            ->assertSuccessful();

        $this->assertDatabaseMissing('page_views', ['created_at' => $oldDate], 'analytics');
        $this->assertDatabaseMissing('user_activities', ['created_at' => $oldDate], 'analytics');

        $this->assertDatabaseHas('page_views', ['id' => $recentView->id], 'analytics');
        $this->assertDatabaseHas('user_activities', ['id' => $recentActivity->id], 'analytics');
    }
}
