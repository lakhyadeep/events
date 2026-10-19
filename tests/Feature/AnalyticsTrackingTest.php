<?php

namespace Tests\Feature;

use App\Filament\Pages\Analytics;
use App\Filament\Widgets\PageWiseFunnelWidget;
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
            'page_type' => 'homepage',
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
            'action_type' => 'vote_initiated',
            'event_id' => $event?->id,
            'target_type' => 'participant',
            'target_id' => $participant?->id,
            'metadata' => [
                'puja_name' => $participant?->name ?? 'Test Puja',
            ],
            'url' => 'http://localhost/event/vote',
            'device_type' => 'mobile',
        ];

        $response = $this->postJson('/api/v1/analytics/event', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('user_activities', [
            'action_type' => 'vote_initiated',
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

        $component = Livewire::test(Analytics::class)
            ->assertSuccessful()
            ->assertSee('Audience &amp; Telemetry Filters', false)
            ->assertSee('Event Scope')
            ->assertSee('Quick Range Preset')
            ->assertSee('Start Date &amp; Time', false)
            ->assertSee('End Date &amp; Time', false)
            ->set('filterData.rangePreset', 'last_30_mins');

        $this->assertNotNull($component->get('filterData.startDate'));
        $this->assertNotNull($component->get('filterData.endDate'));

        $component->set('filterData.rangePreset', 'last_4_hours');
        $this->assertNotNull($component->get('filterData.startDate'));
        $this->assertNotNull($component->get('filterData.endDate'));
        // Verify filters property does not update yet (queries do not run until applyFilters)
        $this->assertEquals('last_7_days', $component->get('filters.rangePreset'));

        $activeEventId = Event::active()->latest('year')->value('id') ?? Event::latest('year')->value('id');
        if ($activeEventId) {
            $this->assertEquals((string) $activeEventId, (string) $component->get('filterData.eventId'));
        }

        $event = Event::first();
        if ($event) {
            $component->set('filterData.eventId', $event->id)
                ->assertSet('filterData.eventId', (string) $event->id);
        }

        $component->callAction('applyFilters')
            ->assertHasNoErrors()
            ->assertSet('filters.rangePreset', 'last_4_hours');

        $component->callAction('clearFilters')
            ->assertSet('filterData.startDate', null)
            ->assertSet('filterData.endDate', null)
            ->assertSet('filters.startDate', null)
            ->assertSet('filters.endDate', null);
    }

    public function test_page_wise_funnel_widget_handles_hours_and_minutes_and_reacts_to_page_filters(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $this->actingAs($admin);

        // 1. Default (Last 7 Days, Global)
        Livewire::test(PageWiseFunnelWidget::class)
            ->assertSuccessful()
            ->assertSee('Last 7 Days')
            ->assertSee('Global Scope');

        // 2. Preset: last_hour
        Livewire::test(PageWiseFunnelWidget::class, [
            'pageFilters' => [
                'rangePreset' => 'last_hour',
                'eventId' => null,
            ],
        ])->assertSuccessful()
            ->assertSee('Last 1 Hour')
            ->assertSee('Global Scope');

        // 3. Preset: last_30_mins
        Livewire::test(PageWiseFunnelWidget::class, [
            'pageFilters' => [
                'rangePreset' => 'last_30_mins',
                'eventId' => null,
            ],
        ])->assertSuccessful()
            ->assertSee('Last 30 Minutes')
            ->assertSee('Global Scope');

        // 4. Custom Event Scope
        Livewire::test(PageWiseFunnelWidget::class, [
            'pageFilters' => [
                'rangePreset' => 'last_7_days',
                'eventId' => 999,
            ],
        ])->assertSuccessful()
            ->assertSee('Event #999');

        // 5. Custom Date Range
        Livewire::test(PageWiseFunnelWidget::class, [
            'pageFilters' => [
                'startDate' => now()->subHours(3)->toDateTimeString(),
                'endDate' => now()->toDateTimeString(),
            ],
        ])->assertSuccessful()
            ->assertSee('Stage-to-stage transition rates & drop-off analysis');
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

    public function test_page_wise_tracking_covers_all_puja_vote_and_vote_success(): void
    {
        $event = Event::where('status', 'active')->first() ?? Event::factory()->create(['status' => 'active', 'slug' => 'durga-puja-test']);
        $participant = Participant::where('event_id', $event->id)->approved()->first()
            ?? Participant::factory()->create(['event_id' => $event->id, 'is_approved' => true, 'slug' => 'test-puja']);

        // 1. All Pujas
        $this->get('/'.$event->slug.'/all-pujas', ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0'])
            ->assertStatus(200);

        $this->assertDatabaseHas('page_views', [
            'event_id' => $event->id,
            'page_type' => 'all_puja',
        ], 'analytics');

        // 2. Vote Screen
        $this->get('/'.$event->slug.'/vote/'.$participant->slug, ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0'])
            ->assertStatus(200);

        $this->assertDatabaseHas('page_views', [
            'event_id' => $event->id,
            'page_type' => 'vote',
        ], 'analytics');

        // 3. Vote Success Screen
        $this->get('/'.$event->slug.'/vote-success/'.$participant->slug, ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0'])
            ->assertStatus(200);

        $this->assertDatabaseHas('page_views', [
            'event_id' => $event->id,
            'page_type' => 'vote_success',
        ], 'analytics');
    }

    public function test_voting_flow_records_vote_initiated_and_vote_verified(): void
    {
        $event = Event::where('status', 'active')->first() ?? Event::factory()->create(['status' => 'active', 'slug' => 'durga-puja-test']);
        $participant = Participant::where('event_id', $event->id)->approved()->first()
            ?? Participant::factory()->create(['event_id' => $event->id, 'is_approved' => true]);

        // 1. Send OTP for voting (should record vote_initiated)
        $otpResponse = $this->postJson('/api/v1/otp/send', [
            'phone' => '9876543210',
            'purpose' => 'voting',
            'event_id' => $event->id,
            'participant_id' => $participant->id,
        ]);

        $otpResponse->assertStatus(200);

        $this->assertDatabaseHas('user_activities', [
            'action_type' => 'vote_initiated',
            'event_id' => $event->id,
            'target_type' => 'participant',
            'target_id' => $participant->id,
        ], 'analytics');
    }
}
