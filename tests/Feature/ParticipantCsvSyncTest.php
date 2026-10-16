<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Participant;
use App\Services\ParticipantCsvSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantCsvSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_processes_csv_with_leading_blank_rows_and_trailing_empty_entries(): void
    {
        $event = Event::factory()->active()->create([
            'year' => 2026,
            'slug' => 'sharod-samman-2026',
        ]);

        $service = app(ParticipantCsvSyncService::class);
        $csvPath = database_path('seeders/data/all-puja.csv');

        $result = $service->syncFromCsv($event, $csvPath);

        $this->assertEquals(31, $result['total']);
        $this->assertEquals(31, $result['created']);
        $this->assertEquals(0, $result['updated']);

        // Verify Participant 1
        $p1 = Participant::where('event_id', $event->id)
            ->where('name', 'GABHARUPATHER SARBAJANIN DURGA PUJA COMMITEE')
            ->first();
        $this->assertNotNull($p1);
        $this->assertEquals('TILAK DEB ROY', $p1->key_contact_1_name);
        $this->assertEquals('REVENGE - THE RETRIBUTION', $p1->puja_theme);
        $this->assertEquals('https://maps.app.goo.gl/c8kS9iu2Bx8rJnaV8', $p1->google_map_url);
        $this->assertEquals('9435000001', $p1->mobile_number);

        // Verify Participant 2 (coordinates to Google Map URL conversion)
        $p2 = Participant::where('event_id', $event->id)
            ->where('name', 'GRAHAM BAZAR DURGA PUJA COMMITEE')
            ->first();
        $this->assertNotNull($p2);
        $this->assertEquals('9435390289', $p2->mobile_number);
        $this->assertEquals('ASSAM TEA', trim($p2->puja_theme));
        $this->assertStringContainsString('https://www.google.com/maps?q=27.485653,94.914079', $p2->google_map_url);

        // Verify newly added Participant 23
        $p23 = Participant::where('event_id', $event->id)
            ->where('name', 'like', '%TARUN SAMITY%')
            ->first();
        $this->assertNotNull($p23);
        $this->assertEquals('9435077771', $p23->mobile_number);
        $this->assertEquals('Tipu Mandal', $p23->key_contact_1_name);
        $this->assertEquals('ECOSIDE', $p23->puja_theme);

        // Verify newly added Participant 31
        $p31 = Participant::where('event_id', $event->id)
            ->where('name', 'like', '%SUBHASHPALLY%')
            ->first();
        $this->assertNotNull($p31);
        $this->assertEquals('9435000031', $p31->mobile_number);
    }

    public function test_subsequent_sync_updates_records_non_destructively(): void
    {
        $event = Event::factory()->active()->create([
            'year' => 2026,
            'slug' => 'sharod-samman-2026',
        ]);

        $service = app(ParticipantCsvSyncService::class);
        $csvPath = database_path('seeders/data/all-puja.csv');

        // First sync
        $firstResult = $service->syncFromCsv($event, $csvPath);
        $this->assertEquals(31, $firstResult['created']);

        // Second sync (updates existing)
        $secondResult = $service->syncFromCsv($event, $csvPath);
        $this->assertEquals(0, $secondResult['created']);
        $this->assertEquals(31, $secondResult['updated']);
        $this->assertEquals(31, $secondResult['total']);
    }
}
