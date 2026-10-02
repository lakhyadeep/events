<?php

namespace Tests\Feature;

use App\Livewire\Frontend\ParticipantsShowcase;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\User;
use App\Models\Zone;
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

    public function test_zone_and_locality_masters_exist_and_relate_to_participants(): void
    {
        $this->assertGreaterThan(0, Zone::count());
        $this->assertGreaterThan(0, Locality::count());

        $southKolkata = Zone::where('name', 'South Kolkata')->first();
        $this->assertNotNull($southKolkata);
        $this->assertGreaterThan(0, $southKolkata->localities()->count());

        $participant = Participant::where('zone_id', $southKolkata->id)->first();
        $this->assertNotNull($participant);
        $this->assertEquals('South Kolkata', $participant->zone);
        $this->assertEquals('South Kolkata', $participant->zone()->first()->name);
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

    public function test_headless_api_shorts_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/events/sharod-samman-2026/shorts');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'platform',
                        'video_url',
                    ],
                ],
            ]);
    }

    public function test_headless_api_winners_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/events/sharod-samman-2026/winners');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'rank_order',
                        'status',
                        'award',
                        'participant',
                    ],
                ],
            ]);
    }

    public function test_storage_link_utility_route_functions(): void
    {
        // Unauthenticated guests should be redirected to login
        $guestResponse = $this->get('/admin-tools/storage-link');
        $guestResponse->assertRedirect(route('filament.admin.auth.login'));

        // Authenticated admin users can execute
        $user = User::factory()->create();
        $authResponse = $this->actingAs($user)->get('/admin-tools/storage-link');
        $authResponse->assertStatus(200);
        $this->assertStringContainsString('Storage link', $authResponse->getContent());
    }

    public function test_media_url_resolution_handles_both_http_and_relative_paths(): void
    {
        $participantWithHttp = new Participant([
            'primary_display_image' => 'https://example.com/photo.jpg',
            'image_1' => 'participants/gallery/custom.webp',
        ]);

        $this->assertEquals('https://example.com/photo.jpg', $participantWithHttp->primary_image_url);
        $this->assertStringContainsString('/storage/participants/gallery/custom.webp', $participantWithHttp->image_1_url);
    }

    public function test_livewire_participants_showcase_zone_and_shortlist_reset_page(): void
    {
        $event = Event::where('slug', 'sharod-samman-2026')->first();

        Livewire::test(ParticipantsShowcase::class, ['eventId' => $event->id])
            ->call('setZone', 'South Kolkata')
            ->assertSet('selectedZone', 'South Kolkata')
            ->call('toggleShortlisted')
            ->assertSet('shortlistedOnly', true);
    }

    public function test_banner_media_url_resolution(): void
    {
        $banner = new Banner([
            'image_desktop' => 'banners/desktop/sample.jpg',
            'image_mobile' => 'banners/mobile/sample.jpg',
        ]);

        $this->assertStringContainsString('/storage/banners/desktop/sample.jpg', $banner->desktop_image_url);
        $this->assertStringContainsString('/storage/banners/mobile/sample.jpg', $banner->mobile_image_url);
    }
}
