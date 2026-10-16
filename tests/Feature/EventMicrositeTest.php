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

    protected function getApprovedParticipant(): Participant
    {
        return Participant::where('registration_status', 'approved')->first()
            ?? Participant::factory()->approved()->create();
    }

    protected function getActiveEvent(): Event
    {
        return Event::where('status', 'active')->first()
            ?? Event::factory()->active()->create();
    }

    public function test_flagship_event_microsite_renders_successfully(): void
    {
        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dibrugarh 24x7');
        $response->assertSee('About Dibrugarh Durgotsav Awards 2026');
        $response->assertSee('ORGANISED BY');
        $response->assertSee('TITLE SPONSOR');
        $response->assertSee('ASSOCIATE PARTNERS');
        $response->assertSee('Vote Now');
        $response->assertSee('Thana Chariali Sarbajanin');
    }

    public function test_hero_banner_renders_dynamic_background_and_separate_award_logo(): void
    {
        $response = $this->get('/sharod-samman-2026');

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

    public function test_awards_section_renders_grand_champion_and_award_categories(): void
    {
        $response = $this->get('/sharod-samman-2026');

        $response->assertStatus(200);

        // Pinnacle Grand Champion card
        $response->assertSee('award-card award-card-crown', false);
        $response->assertSee('BEST OF BEST • GRAND CHAMPION', false);
        $response->assertSee('crown-gold-title', false);

        // Category Cards (Theme, Pandal, Idol)
        $response->assertSee('award-card-body', false);
        $response->assertSee('Winner', false);
        $response->assertSee('Runner Up', false);
        $response->assertSee('Second Runner Up', false);
    }

    public function test_slug_based_event_route_works(): void
    {
        $response = $this->get('/sharod-samman-2026');

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

        $centralDibrugarh = Zone::where('name', 'Central Dibrugarh')->first()
            ?? Zone::factory()->create(['name' => 'Central Dibrugarh']);
        $this->assertNotNull($centralDibrugarh);
        if ($centralDibrugarh->localities()->count() === 0) {
            $centralDibrugarh->localities()->create(['name' => 'Thana Chariali']);
        }
        $this->assertGreaterThan(0, $centralDibrugarh->localities()->count());

        $participant = Participant::where('zone_id', $centralDibrugarh->id)->first()
            ?? Participant::factory()->approved()->create([
                'zone_id' => $centralDibrugarh->id,
                'zone' => $centralDibrugarh->name,
            ]);
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
        $response = $this->get('/sharod-samman-2026/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('Thana Chariali Sarbajanin');
        $response->assertSee('pujaSearchInput');
        $response->assertViewHas('pujasData');
        $response->assertViewHas('filterZones');
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

        $response = $this->get('/sharod-samman-2026/all-pujas');
        $response->assertStatus(200);
        $response->assertSee('Flagship Award');
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

    public function test_all_pujas_directory_renders_successfully(): void
    {
        $response = $this->get('/sharod-samman-2026/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('Dibrugarh Durga Pujas');
        $response->assertSee('Search and vote for your favourite puja committee');
        $response->assertSee('pujaSearchInput');
        $response->assertSee('filter-chips-wrap');
        $response->assertSee('All Pujas');
        $response->assertSee('allpujas.js');
        $response->assertSee('window.dynamicPujasData');
        $response->assertSee('Thana Chariali Sarbajanin');
        $response->assertSee('puja-card-');
    }

    public function test_slug_based_all_pujas_route_works(): void
    {
        $response = $this->get('/sharod-samman-2026/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('Dibrugarh Durga Pujas');
    }

    public function test_home_page_includes_explore_all_pujas_cta_and_vote_page_includes_otp_modal(): void
    {
        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
        $response->assertSee('All Pujas');
        $response->assertSee('Vote Now');

        // Plain /vote is not permitted and yields 404
        $this->get('/vote')->assertStatus(404);

        // Dedicated voting page in v2 flow must be accessed via /{event}/vote/{participant}
        $participant = $this->getApprovedParticipant();
        $voteResponse = $this->get('/'.$participant->event->slug.'/vote/'.$participant->slug);
        $voteResponse->assertStatus(200);
        $voteResponse->assertSee('otpModal');
        $voteResponse->assertSee('voterMobile');
        $voteResponse->assertSee('alreadyVotedAlert');
        $voteResponse->assertSee('btnSubmitYourVote');
    }

    public function test_frontend_sections_hidden_when_dynamic_data_not_available_while_nav_is_retained(): void
    {
        $bareEvent = Event::create([
            'name' => 'Bare Minimal Event',
            'display_name' => 'Bare Minimal Event',
            'year' => 2026,
            'slug' => 'bare-minimal-event',
            'status' => 'active',
            'is_voting_active' => false,
            'about_text' => null,
            'terms_and_conditions' => null,
        ]);

        $response = $this->get('/bare-minimal-event');

        $response->assertStatus(200);

        // Sections must NOT be rendered when dynamic data is absent
        $response->assertDontSee('<section class="durgo-utsav-section', false);
        $response->assertDontSee('<section class="sponsors-section-wrapper"', false);
        $response->assertDontSee('<section class="pujas-voting-section"', false);
        $response->assertDontSee('<div class="awards-embedded-box', false);
        $response->assertDontSee('<section class="awards-announced-section', false);
        $response->assertDontSee('<section class="glimpses-section"', false);
        $response->assertDontSee('<section class="timeline-section"', false);
        $response->assertDontSee('<section class="terms-conditions-section', false);

        // Nav links and vote button ARE retained in the navigation
        $response->assertSee('data-target="about"', false);
        $response->assertSee('All Pujas', false);
        $response->assertSee('data-target="awards-announced"', false);
        $response->assertSee('data-target="gallery"', false);
        $response->assertSee('data-target="events"', false);
        $response->assertSee('Voting Closed', false);
    }

    public function test_all_pujas_shows_empty_state_without_fake_fallback_data(): void
    {
        $bareEvent = Event::create([
            'name' => 'Empty Directory Event',
            'display_name' => 'Empty Directory Event',
            'year' => 2026,
            'slug' => 'empty-directory-event',
            'status' => 'active',
        ]);

        $response = $this->get('/empty-directory-event/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('No Pujas Found');
        $response->assertSee('No registered pandals available yet.');
        $response->assertDontSee('id="pujaSearchInput"', false);
        $response->assertDontSee('id="btnLoadMore"', false);
        $response->assertSee('window.dynamicPujasData = []', false);
    }

    public function test_public_page_includes_otp_meta_and_modal_elements(): void
    {
        $response = $this->followingRedirects()->get('/');

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
        $response = $this->get('/sharod-samman-2026/all-pujas');

        $response->assertStatus(200);
        $response->assertSee('name="csrf-token"', false);
        $response->assertSee('id="otpModal"', false);
        $response->assertSee('id="otpInputContainer"', false);
    }

    public function test_category_group_is_not_displayed_in_frontend(): void
    {
        $response = $this->get('/sharod-samman-2026');

        $response->assertStatus(200);
        $response->assertDontSee('<span class="text-muted small"><i class="bi bi-star-fill text-warning me-1"></i> Grand Championship</span>', false);
    }

    public function test_dedicated_vote_page_renders_ballot_and_preferences(): void
    {
        $participant = $this->getApprovedParticipant();
        $response = $this->get('/'.$participant->event->slug.'/vote/'.$participant->slug);

        $response->assertStatus(200);
        $response->assertSee('Select an Award Category to Vote For');
        $response->assertSee('voterMobile');
        $response->assertSee('btnSubmitYourVote');
        $response->assertSee('id="formErrorAlert"', false);
        $response->assertSee('id="alreadyVotedAlert"', false);
        $response->assertSee('otpModal');
        $response->assertSee('vote.js', false);
    }

    public function test_dedicated_vote_success_page_renders_voucher_and_social_share(): void
    {
        $participant = $this->getApprovedParticipant();
        $eventSlug = $participant->event->slug;
        $response = $this->get('/'.$eventSlug.'/vote-success/'.$participant->slug);

        $response->assertStatus(200);
        $response->assertSee('Thank You! Your Vote Has Been Counted');
        $response->assertSee('Toni&Guy24x7', false);
        $response->assertSee('btnCopyCoupon');
        $response->assertSee('btnShareWhatsApp');
        $response->assertSee('https://wa.me/?text=', false);
        $response->assertSee(rawurlencode(route('events.all-pujas', ['event' => $eventSlug])), false);
        $response->assertSee('data-all-pujas-url="'.route('events.all-pujas', ['event' => $eventSlug]).'"', false);
        $response->assertSee('success.js', false);
    }

    public function test_vote_success_page_strictly_accessible_via_participant_slug(): void
    {
        $participant = $this->getApprovedParticipant();
        $eventSlug = $participant->event->slug;

        // 1. Valid participant slug renders successfully
        $response = $this->get('/'.$eventSlug.'/vote-success/'.$participant->slug);
        $response->assertStatus(200);
        $response->assertSee('Thank You! Your Vote Has Been Counted');

        // 2. Bare /vote-success without slug is not permitted (404)
        $this->get('/vote-success')->assertStatus(404);
        $this->get('/'.$eventSlug.'/vote-success')->assertStatus(404);
    }

    public function test_vote_page_hides_optional_fields_and_avoids_static_default_text(): void
    {
        $customEvent = Event::create([
            'name' => 'Clean Slate Event',
            'display_name' => 'Clean Slate Event',
            'year' => 2027,
            'slug' => 'clean-slate-event',
            'status' => 'active',
            'is_voting_active' => true,
            'tagline' => null,
            'about_text' => null,
        ]);

        $participant = Participant::create([
            'event_id' => $customEvent->id,
            'name' => 'Minimal Puja Pandal',
            'display_name' => 'Minimal Puja Pandal',
            'mobile_number' => '9876543210',
            'registration_status' => 'approved',
            'is_shortlisted' => true,
            'year' => 2027,
            'primary_display_image' => 'sample.jpg',
            'puja_theme' => null,
            'short_introduction' => null,
            'locality' => '',
            'zone' => '',
        ]);

        $response = $this->get('/'.$customEvent->slug.'/vote/'.$participant->slug);

        $response->assertStatus(200);
        // Ensure static default texts are NOT rendered
        $response->assertDontSee('Dibrugarh, Assam');
        $response->assertDontSee('Sharad Samman 2026 Nominee');
        $response->assertDontSee('Recognizing Dibrugarh\'s most beloved Durga Puja celebrations', false);
        $response->assertDontSee('Cast your vote for your favourite puja committee');
        $response->assertDontSee('Maa Durga Clay Art');
        $response->assertDontSee('Architecture & Decor');
        $response->assertDontSee('Loading Selected Pandal...');

        // Ensure optional fields are hidden in DOM when empty
        $response->assertSee('id="previewPandalLocWrap"  style="display: none;"', false);
        $response->assertSee('id="previewPandalThemeWrap"  style="display: none;"', false);
        $response->assertSee('No award categories available for voting.');
    }

    public function test_vote_url_must_be_only_accessible_via_vote_participant(): void
    {
        $participant = $this->getApprovedParticipant();
        $eventSlug = $participant->event->slug;

        // 1. Direct /{event}/vote/{participant} is 200 OK
        $response = $this->get('/'.$eventSlug.'/vote/'.$participant->slug);
        $response->assertStatus(200);

        // 2. Legacy /vote/{participant} without event prefix is 404
        $this->get('/vote/'.$participant->slug)->assertStatus(404);

        // 3. Plain /vote without participant is NOT permitted (404)
        $this->get('/vote')->assertStatus(404);
        $this->get('/vote?participant='.$participant->id)->assertStatus(404);
        $this->get('/vote?pandal='.urlencode($participant->name))->assertStatus(404);

        // 4. Invalid participant slug is 404
        $this->get('/'.$eventSlug.'/vote/non-existent-participant-slug')->assertStatus(404);
        $this->get('/'.$eventSlug.'/vote/'.$participant->id)->assertStatus(404);
    }

    public function test_seo_friendly_vote_url_resolves_and_renders_canonical_meta(): void
    {
        $participant = $this->getApprovedParticipant();
        $eventSlug = $participant->event->slug;

        $response = $this->get('/'.$eventSlug.'/vote/'.$participant->slug);

        $response->assertStatus(200);
        $displayName = $participant->display_name ?: $participant->name;
        $response->assertSee('Vote for '.$displayName);
        $expectedCanonical = route('events.vote', ['event' => $eventSlug, 'participant' => $participant->slug]);
        $response->assertSee('<link rel="canonical" href="'.$expectedCanonical.'">', false);
        $response->assertSee('window.selectedParticipantSlug = "'.$participant->slug.'"', false);
    }

    public function test_all_pujas_url_redirects_to_active_event_all_pujas_directory(): void
    {
        $activeEvent = $this->getActiveEvent();

        $response = $this->get('/all-pujas');

        $response->assertRedirect('/'.$activeEvent->slug.'/all-pujas');

        $followed = $this->followingRedirects()->get('/all-pujas');
        $followed->assertStatus(200);
        $followed->assertSee('Dibrugarh Durga Pujas');
        $followed->assertSee('Search and vote for your favourite puja committee');
    }

    public function test_all_pujas_url_preserves_query_parameters_on_redirect(): void
    {
        $activeEvent = $this->getActiveEvent();

        $response = $this->get('/all-pujas?zone=Central+Dibrugarh&search=Thana');

        $response->assertRedirect('/'.$activeEvent->slug.'/all-pujas?zone=Central%20Dibrugarh&search=Thana');
    }

    public function test_event_og_image_renders_as_fully_qualified_absolute_url(): void
    {
        $activeEvent = $this->getActiveEvent();
        $activeEvent->update(['og_image' => 'events/og/01M527VN0GYGVFAG47S8MDSK0C.jpg']);

        $response = $this->get('/'.$activeEvent->slug);

        $response->assertStatus(200);
        $expectedUrl = asset('storage/events/og/01M527VN0GYGVFAG47S8MDSK0C.jpg');
        $response->assertSee('<meta property="og:image" content="'.$expectedUrl.'">', false);
    }
}
