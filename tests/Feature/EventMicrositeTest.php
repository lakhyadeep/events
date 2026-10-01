<?php

namespace Tests\Feature;

use App\Livewire\Frontend\ParticipantsShowcase;
use App\Models\Event;
use Database\Seeders\DemoEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventMicrositeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoEventSeeder::class);
    }

    public function test_flagship_event_microsite_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dib 24x7');
        $response->assertSee('Sharod Samman 2026');
        $response->assertSee('PRESENTED BY');
        $response->assertSee('Fortune');
        $response->assertSee('Senco Gold');
        $response->assertSee('Vote Now');
    }

    public function test_slug_based_event_route_works(): void
    {
        $response = $this->get('/events/sharod-samman-2026');

        $response->assertStatus(200);
        $response->assertSee('Sharod Samman 2026');
    }

    public function test_headless_api_event_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/events/sharod-samman-2026');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.masthead.brand', 'Dib 24x7')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'event',
                    'masthead' => [
                        'brand',
                        'presenting_partner',
                        'associate_sponsors',
                    ],
                    'toggles',
                ],
            ]);
    }

    public function test_headless_api_participants_filter_by_zone(): void
    {
        $response = $this->getJson('/api/v1/events/sharod-samman-2026/participants?zone=South Kolkata');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_livewire_participants_showcase_component_filters_candidates(): void
    {
        $event = Event::where('slug', 'sharod-samman-2026')->first();

        Livewire::test(ParticipantsShowcase::class, ['eventId' => $event->id])
            ->assertStatus(200)
            ->assertSee('Ballygunge Cultural Association')
            ->set('search', 'Ballygunge')
            ->assertSee('Ballygunge Cultural Association')
            ->set('search', 'NonExistentClubXYZ')
            ->assertSee('No participants found');
    }
}
