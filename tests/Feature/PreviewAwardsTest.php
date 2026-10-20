<?php

namespace Tests\Feature;

use App\Enums\WinnerRank;
use App\Models\Award;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviewAwardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_awards_are_hidden_on_public_homepage_but_visible_on_preview_link(): void
    {
        $event = Event::factory()->active()->create([
            'slug' => 'test-durga-puja-preview',
        ]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
            'name' => 'Best Pandal Decor',
            'display_name' => 'Best Pandal Decoration Award',
            'is_voting_allowed' => true,
            'is_active' => true,
        ]);

        $participant = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Draft Winner Club',
            'display_name' => 'Draft Winner Club',
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $participant->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::First,
            'status' => 'draft', // DRAFT
        ]);

        // 1. Regular public homepage should NOT show awards announced or draft winner
        $publicResponse = $this->get('/'.$event->slug);
        $publicResponse->assertStatus(200);
        $publicResponse->assertDontSee('id="awards-announced"', false);
        $publicResponse->assertDontSee('Preview Mode');

        // 2. Dedicated preview route should display full homepage with draft awards announced and preview banner
        $previewResponse = $this->get('/'.$event->slug.'/preview-awards');
        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('id="awards-announced"', false);
        $previewResponse->assertSee('Preview Mode');
        $previewResponse->assertSee('Draft Winner Club');
        $previewResponse->assertSee('DRAFT');

        // 3. Query parameter preview=awards on homepage route should also activate preview mode
        $queryPreviewResponse = $this->get('/'.$event->slug.'?preview=awards');
        $queryPreviewResponse->assertStatus(200);
        $queryPreviewResponse->assertSee('id="awards-announced"', false);
        $queryPreviewResponse->assertSee('Preview Mode');
        $queryPreviewResponse->assertSee('Draft Winner Club');

        // 4. Root /preview-awards route redirects to current active event preview route
        $rootRedirectResponse = $this->get('/preview-awards');
        $rootRedirectResponse->assertRedirect('/'.$event->slug.'/preview-awards');
    }
}
