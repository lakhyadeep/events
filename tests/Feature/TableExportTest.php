<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\RegistrationStatus;
use App\Enums\WinnerRank;
use App\Models\Award;
use App\Models\Event;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\Vote;
use App\Models\Winner;
use App\Models\Zone;
use App\Services\TableExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TableExportTest extends TestCase
{
    use RefreshDatabase;

    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = storage_path('framework/testing/exports_test_'.uniqid());
    }

    protected function tearDown(): void
    {
        if (File::isDirectory($this->tempDir)) {
            File::deleteDirectory($this->tempDir);
        }
        parent::tearDown();
    }

    public function test_participants_export_resolves_relations_to_names(): void
    {
        $event = Event::create([
            'name' => 'Dibrugarh Test Durgotsav',
            'display_name' => 'Dibrugarh Test Durgotsav',
            'year' => 2026,
            'slug' => 'dibrugarh-test-2026',
            'status' => EventStatus::Active,
        ]);

        $zone = Zone::create(['name' => 'North Zone', 'slug' => 'north-zone']);
        $locality = Locality::create(['zone_id' => $zone->id, 'name' => 'Brahmaputra Bank', 'slug' => 'brahmaputra-bank']);

        Participant::factory()->create([
            'event_id' => $event->id,
            'zone_id' => $zone->id,
            'locality_id' => $locality->id,
            'zone' => 'North Zone',
            'locality' => 'Brahmaputra Bank',
            'name' => 'RIVERVIEW DURGA PUJA CLUB',
            'display_name' => 'Riverview Durga Puja Club',
            'mobile_number' => '9876543210',
            'registration_status' => RegistrationStatus::Approved,
            'is_shortlisted' => true,
            'is_puja_contest' => true,
        ]);

        $service = app(TableExportService::class);
        $file = $this->tempDir.'/participants.csv';

        $count = $service->exportParticipants($file);

        $this->assertEquals(1, $count);
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content); // UTF-8 BOM
        $this->assertStringContainsString('Dibrugarh Test Durgotsav', $content);
        $this->assertStringContainsString('Riverview Durga Puja Club', $content);
        $this->assertStringContainsString('North Zone', $content);
        $this->assertStringContainsString('Brahmaputra Bank', $content);
    }

    public function test_votes_export_resolves_relations_to_names(): void
    {
        $event = Event::create([
            'name' => 'Grand Test Festival',
            'display_name' => 'Grand Test Festival',
            'year' => 2026,
            'slug' => 'grand-test-festival',
            'status' => EventStatus::Active,
        ]);

        $participant = Participant::factory()->create([
            'event_id' => $event->id,
            'name' => 'HERITAGE PUJA SAMITY',
            'display_name' => 'Heritage Puja Samiti',
        ]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
            'name' => 'Best Art & Decoration',
            'display_name' => 'Best Art & Decoration',
            'category' => 'Artistry',
            'year' => 2026,
            'is_active' => true,
            'is_voting_allowed' => true,
        ]);

        Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'award_id' => $award->id,
            'voter_name' => 'Debolina Sharma',
            'mobile_number' => '9123456789',
            'ip_address' => '10.0.0.1',
            'user_agent' => 'TestBrowser/1.0',
        ]);

        $service = app(TableExportService::class);
        $file = $this->tempDir.'/votes.csv';

        $count = $service->exportVotes($file);

        $this->assertEquals(1, $count);
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('Grand Test Festival', $content);
        $this->assertStringContainsString('Heritage Puja Samiti', $content);
        $this->assertStringContainsString('Best Art & Decoration', $content);
        $this->assertStringContainsString('Artistry', $content);
        $this->assertStringContainsString('Debolina Sharma', $content);
    }

    public function test_winners_export_resolves_relations_to_names(): void
    {
        $event = Event::create([
            'name' => 'Championship 2026',
            'display_name' => 'Championship 2026',
            'year' => 2026,
            'slug' => 'championship-2026',
            'status' => EventStatus::Active,
        ]);

        $participant = Participant::factory()->create([
            'event_id' => $event->id,
            'name' => 'VICTORY CLUB',
            'display_name' => 'Victory Club',
        ]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
            'name' => 'Best Idol Design',
            'display_name' => 'Best Idol Design',
            'category' => 'Sculpture',
            'year' => 2026,
            'is_active' => true,
        ]);

        Winner::create([
            'event_id' => $event->id,
            'award_id' => $award->id,
            'participant_id' => $participant->id,
            'year' => 2026,
            'rank_order' => WinnerRank::First,
            'status' => 'published',
        ]);

        $service = app(TableExportService::class);
        $file = $this->tempDir.'/winners.csv';

        $count = $service->exportWinners($file);

        $this->assertEquals(1, $count);
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('Championship 2026', $content);
        $this->assertStringContainsString('Best Idol Design', $content);
        $this->assertStringContainsString('Sculpture', $content);
        $this->assertStringContainsString('Victory Club', $content);
        $this->assertStringContainsString('1st', $content);
        $this->assertStringContainsString('1st Prize (Champion / Gold Trophy)', $content);
    }

    public function test_artisan_export_command_generates_all_csv_files(): void
    {
        $this->artisan('db:export', ['--path' => $this->tempDir])
            ->assertSuccessful();

        $this->assertFileExists($this->tempDir.'/participants.csv');
        $this->assertFileExists($this->tempDir.'/votes.csv');
        $this->assertFileExists($this->tempDir.'/winners.csv');
    }
}
