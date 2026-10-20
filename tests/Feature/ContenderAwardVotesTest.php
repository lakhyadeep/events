<?php

namespace Tests\Feature;

use App\Filament\Widgets\TopContendersVotesChartWidget;
use App\Filament\Widgets\VotingLeaderboardWidget;
use App\Models\Award;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use App\Models\Vote;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContenderAwardVotesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@dib24x7.com')->firstOrFail();
        $this->event = Event::firstOrFail();
    }

    public function test_participant_model_calculates_award_categorywise_vote_counts(): void
    {
        $participant = Participant::where('event_id', $this->event->id)->firstOrFail();
        $awards = Award::where('event_id', $this->event->id)
            ->where('is_active', true)
            ->where('is_voting_allowed', true)
            ->where('name', 'not like', '%Best Puja%')
            ->get();

        $this->assertGreaterThanOrEqual(2, $awards->count());

        $award1 = $awards[0];
        $award2 = $awards[1];

        // Cast 3 votes for award1 and 1 vote for award2
        for ($i = 0; $i < 3; $i++) {
            Vote::create([
                'event_id' => $this->event->id,
                'participant_id' => $participant->id,
                'award_id' => $award1->id,
                'mobile_number' => '987654321'.$i,
            ]);
        }

        Vote::create([
            'event_id' => $this->event->id,
            'participant_id' => $participant->id,
            'award_id' => $award2->id,
            'mobile_number' => '9876543299',
        ]);

        $breakdown = $participant->getVoteCountsByAward();

        // Best Puja should be excluded from vote share
        $this->assertNull($breakdown->firstWhere('name', 'Best Puja Overall'));

        $item1 = $breakdown->firstWhere('award_id', $award1->id);
        $item2 = $breakdown->firstWhere('award_id', $award2->id);

        $this->assertNotNull($item1);
        $this->assertNotNull($item2);
        $this->assertEquals(3, $item1['votes_count']);
        $this->assertEquals(75.0, $item1['percentage']);
        $this->assertEquals(1, $item2['votes_count']);
        $this->assertEquals(25.0, $item2['percentage']);
    }

    public function test_vote_model_aggregates_category_counts_for_multiple_participants(): void
    {
        $participants = Participant::where('event_id', $this->event->id)->take(2)->get();
        $this->assertCount(2, $participants);

        $award = Award::where('event_id', $this->event->id)->firstOrFail();

        Vote::create([
            'event_id' => $this->event->id,
            'participant_id' => $participants[0]->id,
            'award_id' => $award->id,
            'mobile_number' => '9111111111',
        ]);

        Vote::create([
            'event_id' => $this->event->id,
            'participant_id' => $participants[1]->id,
            'award_id' => $award->id,
            'mobile_number' => '9222222222',
        ]);

        $results = Vote::getCountsByParticipantAndAward($participants->pluck('id')->all());

        $this->assertGreaterThanOrEqual(2, $results->count());
        $p1Votes = $results->where('participant_id', $participants[0]->id)->sum('votes_count');
        $p2Votes = $results->where('participant_id', $participants[1]->id)->sum('votes_count');

        $this->assertGreaterThanOrEqual(1, $p1Votes);
        $this->assertGreaterThanOrEqual(1, $p2Votes);
    }

    public function test_top_contenders_votes_chart_widget_renders_on_dashboard(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TopContendersVotesChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Top 10 Contenders: Vote Count by Award Category');
    }

    public function test_voting_leaderboard_widget_has_category_breakdown_column_without_action_modal(): void
    {
        $this->actingAs($this->admin);

        $participant = Participant::where('event_id', $this->event->id)->firstOrFail();
        $award = Award::where('event_id', $this->event->id)->where('is_voting_allowed', true)->firstOrFail();

        Vote::create([
            'event_id' => $this->event->id,
            'participant_id' => $participant->id,
            'award_id' => $award->id,
            'mobile_number' => '9555555555',
        ]);

        Livewire::test(VotingLeaderboardWidget::class)
            ->assertSuccessful()
            ->assertSee('Award Category Breakdown')
            ->assertTableActionDoesNotExist('view_award_breakdown');
    }

    public function test_contender_award_breakdown_modal_view_renders_correctly(): void
    {
        $participant = Participant::where('event_id', $this->event->id)->firstOrFail();
        $award = Award::where('event_id', $this->event->id)
            ->where('is_voting_allowed', true)
            ->where('name', 'not like', '%Best Puja%')
            ->firstOrFail();

        Vote::create([
            'event_id' => $this->event->id,
            'participant_id' => $participant->id,
            'award_id' => $award->id,
            'mobile_number' => '9666666666',
        ]);

        $breakdown = $participant->getVoteCountsByAward();

        $view = $this->view('filament.widgets.contender-award-breakdown-modal', [
            'record' => $participant,
            'breakdown' => $breakdown,
            'startDate' => null,
            'endDate' => null,
        ]);

        $view->assertSee($participant->display_name ?: $participant->name);
        $view->assertSee('Stacked Award Category Vote Counts');
        $view->assertSee('Total Verified Votes');
        $view->assertSee($award->display_name ?: $award->name);
    }
}
