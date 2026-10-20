<?php

namespace Tests\Feature;

use App\Enums\WinnerRank;
use App\Models\Award;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JointWinnersPodiumTest extends TestCase
{
    use RefreshDatabase;

    public function test_joint_runner_up_winners_use_same_second_card_design_and_position(): void
    {
        $event = Event::factory()->active()->create([
            'slug' => 'test-durga-puja-2026',
        ]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
            'name' => 'Best Pratima',
            'display_name' => 'Best Pratima Award',
            'is_voting_allowed' => true,
            'is_active' => true,
        ]);

        $p1st = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Gold First Puja',
            'display_name' => 'Gold First Puja',
        ]);

        $p2ndA = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Silver Puja Alpha',
            'display_name' => 'Joint Silver Puja Alpha',
        ]);

        $p2ndB = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Silver Puja Beta',
            'display_name' => 'Joint Silver Puja Beta',
        ]);

        $p3rd = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Bronze Third Puja',
            'display_name' => 'Bronze Third Puja',
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $p1st->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::First,
            'status' => 'published',
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $p2ndA->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::Second,
            'status' => 'published',
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $p2ndB->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::Second,
            'status' => 'published',
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $p3rd->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::Third,
            'status' => 'published',
        ]);

        $response = $this->get('/'.$event->slug);

        $response->assertStatus(200);

        $content = $response->getContent();

        // Both joint 2nd runners are present
        $response->assertSee('Joint Silver Puja Alpha');
        $response->assertSee('Joint Silver Puja Beta');

        // Check within awards announced section
        $this->assertStringContainsString('id="awards-announced"', $content);
        $sectionContent = substr($content, strpos($content, 'id="awards-announced"'));

        // Both use the same 2nd runner up silver card design
        $this->assertEquals(
            2,
            substr_count($sectionContent, 'winner-podium-card winner-silver'),
            'Both 2nd runner up participants must use the winner-silver podium card design.'
        );

        $this->assertEquals(
            2,
            substr_count($sectionContent, 'badge-silver'),
            'Both 2nd runner up participants must use the badge-silver badge.'
        );

        // Check positioning: 1st place -> Joint 2nd A & B -> 3rd place
        $pos1st = strpos($sectionContent, 'Gold First Puja');
        $pos2ndA = strpos($sectionContent, 'Joint Silver Puja Alpha');
        $pos2ndB = strpos($sectionContent, 'Joint Silver Puja Beta');
        $pos3rd = strpos($sectionContent, 'Bronze Third Puja');

        $this->assertNotFalse($pos1st);
        $this->assertNotFalse($pos2ndA);
        $this->assertNotFalse($pos2ndB);
        $this->assertNotFalse($pos3rd);

        $this->assertLessThan($pos2ndA, $pos1st, '1st place should appear before 2nd place Alpha');
        $this->assertLessThan($pos2ndB, $pos1st, '1st place should appear before 2nd place Beta');
        $this->assertLessThan($pos3rd, $pos2ndA, '2nd place Alpha should appear before 3rd place');
        $this->assertLessThan($pos3rd, $pos2ndB, '2nd place Beta should appear before 3rd place');
    }

    public function test_joint_first_and_joint_third_winners_use_matching_card_design_and_positions(): void
    {
        $event = Event::factory()->active()->create([
            'slug' => 'test-durga-puja-joint-ranks',
        ]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
            'name' => 'Best Lighting',
            'display_name' => 'Best Illumination Award',
            'is_voting_allowed' => true,
            'is_active' => true,
        ]);

        $p1stA = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Gold 1',
            'display_name' => 'Joint Gold 1',
        ]);

        $p1stB = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Gold 2',
            'display_name' => 'Joint Gold 2',
        ]);

        $p2nd = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Solo Silver',
            'display_name' => 'Solo Silver',
        ]);

        $p3rdA = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Bronze 1',
            'display_name' => 'Joint Bronze 1',
        ]);

        $p3rdB = Participant::factory()->approved()->create([
            'event_id' => $event->id,
            'name' => 'Joint Bronze 2',
            'display_name' => 'Joint Bronze 2',
        ]);

        foreach ([$p1stA, $p1stB] as $p) {
            Winner::create([
                'event_id' => $event->id,
                'award_id' => $award->id,
                'participant_id' => $p->id,
                'year' => $event->year,
                'rank_order' => WinnerRank::First,
                'status' => 'published',
            ]);
        }

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $p2nd->id,
            'year' => $event->year,
            'rank_order' => WinnerRank::Second,
            'status' => 'published',
        ]);

        foreach ([$p3rdA, $p3rdB] as $p) {
            Winner::create([
                'event_id' => $event->id,
                'award_id' => $award->id,
                'participant_id' => $p->id,
                'year' => $event->year,
                'rank_order' => WinnerRank::Third,
                'status' => 'published',
            ]);
        }

        $response = $this->get('/'.$event->slug);
        $response->assertStatus(200);

        $content = $response->getContent();
        $sectionContent = substr($content, strpos($content, 'id="awards-announced"'));

        $this->assertEquals(2, substr_count($sectionContent, 'winner-podium-card winner-gold'));
        $this->assertEquals(1, substr_count($sectionContent, 'winner-podium-card winner-silver'));
        $this->assertEquals(2, substr_count($sectionContent, 'winner-podium-card winner-bronze'));

        $pos1stA = strpos($sectionContent, 'Joint Gold 1');
        $pos1stB = strpos($sectionContent, 'Joint Gold 2');
        $pos2nd = strpos($sectionContent, 'Solo Silver');
        $pos3rdA = strpos($sectionContent, 'Joint Bronze 1');
        $pos3rdB = strpos($sectionContent, 'Joint Bronze 2');

        $this->assertLessThan($pos2nd, $pos1stA);
        $this->assertLessThan($pos2nd, $pos1stB);
        $this->assertLessThan($pos3rdA, $pos2nd);
        $this->assertLessThan($pos3rdB, $pos2nd);
    }
}
