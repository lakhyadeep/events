<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AnalyticsQuickStatsWidget;
use App\Filament\Widgets\SmsUsageOverviewWidget;
use App\Filament\Widgets\VotingLeaderboardWidget;
use App\Filament\Widgets\VotingStatsOverview;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@dib24x7.com')->firstOrFail();
    }

    public function test_admin_dashboard_shows_date_and_time_filters(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Date &amp; Time Filters', false);
        $response->assertSee('Start Date &amp; Time', false);
        $response->assertSee('End Date &amp; Time', false);
        $response->assertSee('Quick Range Preset');
    }

    public function test_dashboard_has_full_width_filter_layout(): void
    {
        $dashboard = new Dashboard;
        $this->assertEquals(Width::Full, $dashboard->getMaxContentWidth());

        $form = $dashboard->getFiltersForm();
        $this->assertEquals(1, $form->getColumns('default'));
    }

    public function test_dashboard_filters_form_can_set_custom_date_and_time(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(Dashboard::class)
            ->set('filters.startDate', '2026-10-18 10:00:00')
            ->set('filters.endDate', '2026-10-18 18:30:00')
            ->assertSet('filters.startDate', '2026-10-18 10:00:00')
            ->assertSet('filters.endDate', '2026-10-18 18:30:00')
            ->assertHasNoErrors();
    }

    public function test_dashboard_preset_selection_populates_start_and_end_datetime(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(Dashboard::class)
            ->set('filters.rangePreset', 'today');

        $startDate = $component->get('filters.startDate');
        $endDate = $component->get('filters.endDate');

        $this->assertNotNull($startDate);
        $this->assertNotNull($endDate);
        $this->assertStringContainsString(now()->format('Y-m-d'), $startDate);
        $this->assertStringContainsString(now()->format('Y-m-d'), $endDate);

        // Test 30 minutes preset
        $component->set('filters.rangePreset', 'last_30_mins');
        $this->assertNotNull($component->get('filters.startDate'));
        $this->assertNotNull($component->get('filters.endDate'));

        // Test 1 hour preset
        $component->set('filters.rangePreset', 'last_hour');
        $this->assertNotNull($component->get('filters.startDate'));
        $this->assertNotNull($component->get('filters.endDate'));

        // Test 4 hours preset
        $component->set('filters.rangePreset', 'last_4_hours');
        $this->assertNotNull($component->get('filters.startDate'));
        $this->assertNotNull($component->get('filters.endDate'));
    }

    public function test_dashboard_clear_filters_action_resets_datetime_filters(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(Dashboard::class)
            ->set('filters.startDate', '2026-10-18 09:00:00')
            ->set('filters.endDate', '2026-10-18 21:00:00')
            ->callAction('clearFilters')
            ->assertSet('filters.startDate', null)
            ->assertSet('filters.endDate', null);
    }

    public function test_voting_stats_widget_adapts_to_page_filters(): void
    {
        $this->actingAs($this->admin);

        // Without filters - default label
        Livewire::test(VotingStatsOverview::class)
            ->assertSee('Votes Cast Today');

        // With date & time filters - filtered label
        Livewire::test(VotingStatsOverview::class, [
            'pageFilters' => [
                'startDate' => now()->subHours(4)->toDateTimeString(),
                'endDate' => now()->toDateTimeString(),
            ],
        ])->assertSee('Votes Cast in Window');
    }

    public function test_analytics_quick_stats_widget_adapts_to_page_filters(): void
    {
        $this->actingAs($this->admin);

        // Without filters - default today label
        Livewire::test(AnalyticsQuickStatsWidget::class)
            ->assertSee('Today&#039;s Verified Votes', false);

        // With filters - filtered label
        Livewire::test(AnalyticsQuickStatsWidget::class, [
            'pageFilters' => [
                'startDate' => now()->subDay()->toDateTimeString(),
                'endDate' => now()->toDateTimeString(),
            ],
        ])->assertSee('Verified Votes (Filtered)');
    }

    public function test_sms_usage_widget_adapts_to_page_filters(): void
    {
        $this->actingAs($this->admin);

        // Without filters - default label
        Livewire::test(SmsUsageOverviewWidget::class)
            ->assertSee('SMS Dispatched Today');

        // With filters - filtered label
        Livewire::test(SmsUsageOverviewWidget::class, [
            'pageFilters' => [
                'startDate' => now()->subHours(6)->toDateTimeString(),
                'endDate' => now()->toDateTimeString(),
            ],
        ])->assertSee('SMS Dispatched (Filtered)');
    }

    public function test_voting_leaderboard_widget_heading_adapts_to_page_filters(): void
    {
        $this->actingAs($this->admin);

        // Without filters
        Livewire::test(VotingLeaderboardWidget::class)
            ->assertSee('Live Public Voting Leaderboard');

        // With filters
        Livewire::test(VotingLeaderboardWidget::class, [
            'pageFilters' => [
                'startDate' => '2026-10-18 10:00:00',
                'endDate' => '2026-10-18 20:00:00',
            ],
        ])->assertSee('Live Public Voting Leaderboard (Filtered:');
    }
}
