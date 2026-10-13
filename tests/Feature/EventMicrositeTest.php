<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Event;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\DemoEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('Dibrugarh 24x7');
        $response->assertSee('About Dibrugarh Durgo Utsav 2026');
        $response->assertSee('ORGANISED BY');
        $response->assertSee('TITLE SPONSOR');
        $response->assertSee('ASSOCIATE PARTNERS');
        $response->assertSee('Vote Now');
        $response->assertSee('Thana Chariali Sarbajanin');
    }

    public function test_hero_banner_renders_dynamic_background_and_separate_award_logo(): void
    {
        $response = $this->get('/events/sharod-samman-2026');

        $response->assertStatus(200);

        $event = Event::where('slug', 'sharod-samman-2026')->first();
        $banner = $event->banners()->where('is_active', true)->first();

        // Banner desktop and mobile image URLs must be in the background CSS
        $response->assertSee($banner->desktop_image_url, false);
        $response->assertSee($banner->mobile_image_url, false);

        // Logo inside the container must be the award logo asset, NOT the banner image
        $response->assertSee('event-assets/images/logo-award2026.png', false);
        $response->assertDontSee('<img src="'.$banner->desktop_image_url.'"', false);
    }

    public function test_voting_section_renders_category_headers_with_borders_and_segment_dividers(): void
    {
        $response = $this->get('/events/sharod-samman-2026');

        $response->assertStatus(200);

        // Category header wrappers with border/theme classes
        $response->assertSee('category-title-wrapper cat-header-puja', false);
        $response->assertSee('category-title-wrapper cat-header-pratima', false);
        $response->assertSee('category-title-wrapper cat-header-lighting', false);

        // Category eyebrow labels
        $response->assertSee('CATEGORY 01 • FLAGSHIP', false);
        $response->assertSee('CATEGORY 02 • IDOL ARTISTRY', false);
        $response->assertSee('CATEGORY 03 • ILLUMINATION', false);

        // Segment dividers between categories
        $response->assertSee('category-segment-divider', false);
        $response->assertSee('category-segment-divider-icon', false);

        // Navigation tabs wrapper
        $response->assertSee('pujas-nav-tabs-wrapper', false);
        $response->assertSee('pujas-nav-btn nav-btn-puja', false);
        $response->assertSee('pujas-nav-btn nav-btn-pratima', false);
        $response->assertSee('pujas-nav-btn nav-btn-lighting', false);
    }

    public function test_slug_based_event_route_works(): void
    {
        $response = $this->get('/events/sharod-samman-2026');

        $response->assertStatus(200);
        $response->assertSee('Dibrugarh 24x7');
    }

    public function test_no_events_view_renders_when_no_events_exist(): void
    {
        Event::query()->delete();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('No Active Event Configured Yet');
        $response->assertSee('Initial Setup: Create Superadmin');

        User::factory()->create();

        $responseWithUser = $this->get('/');
        $responseWithUser->assertSee('Go to Admin Dashboard');
    }

    public function test_zone_and_locality_masters_exist_and_relate_to_participants(): void
    {
        $this->assertGreaterThan(0, Zone::count());
        $this->assertGreaterThan(0, Locality::count());

        $centralDibrugarh = Zone::where('name', 'Central Dibrugarh')->first();
        $this->assertNotNull($centralDibrugarh);
        $this->assertGreaterThan(0, $centralDibrugarh->localities()->count());

        $participant = Participant::where('zone_id', $centralDibrugarh->id)->first();
        $this->assertNotNull($participant);
        $this->assertEquals('Central Dibrugarh', $participant->zone);
        $this->assertEquals('Central Dibrugarh', $participant->zone()->first()->name);
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
        $response = $this->getJson('/api/v1/events/sharod-samman-2026/participants?zone=Central Dibrugarh');

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

    public function test_standard_blade_participants_showcase_renders_candidates(): void
    {
        $response = $this->get('/events/sharod-samman-2026');

        $response->assertStatus(200);
        $response->assertSee('Thana Chariali Sarbajanin');
        $response->assertSee('Vote for the Best');
        $response->assertViewHas('participants');
        $response->assertViewHas('zones');
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

    public function test_standard_blade_participants_data_contains_shortlisted_candidates(): void
    {
        $event = Event::where('slug', 'sharod-samman-2026')->first();
        $this->assertNotNull($event);

        $shortlistedCount = $event->participants()->shortlisted()->count();
        $this->assertGreaterThan(0, $shortlistedCount);

        $response = $this->get('/events/sharod-samman-2026');
        $response->assertStatus(200);
        $response->assertSee('shortlisted entries');
    }

    public function test_banner_media_url_resolution(): void
    {
        $banner = new Banner([
l̥
    public function test_all_pujas_shows_empty_state_without_fake_fallback_data(): void
    {
        $bareEvent = Event::create([
            'name' => 'Empty Directory Event',
            'display_name' => 'Empty Directory Event',
            'year' => 2026,
            'slug' => 'empty-directory-event',
            'status' => 'active',
        ]);

        $response = $this->get('/events/empty-directory-event/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('No Pujas Found');
        $response->assertSee('No registered pandals available yet.');
        $response->assertDontSee('id="pujaSearchInput"', false);
        $response->assertDontSee('id="btnLoadMore"', false);
        $response->assertSee('window.dynamicPujasData = []', false);
    }

    public function test_public_page_includes_otp_meta_and_modal_elements(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('name="csrf-token"', false);
        $response->assertSee('name="otp-length"', false);
        $response->assertSee('id="voteModal"', false);
        $response->assertSee('id="otpModal"', false);
        $response->assertSee('id="otpInputContainer"', false);
        $response->assertSee('btnVerifyOtp', false);
        $response->assertSee('btnResendOtp', false);
    }

    public function test_all_pujas_page_includes_otp_modal_elements(): void
    {
        $response = $this->get('/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('name="csrf-token"', false);
        $response->assertSee('id="otpModal"', false);
        $response->assertSee('id="otpInputContainer"', false);
    }
}
