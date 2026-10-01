<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\TimelineItem;
use App\Models\VideoShort;
use App\Models\Winner;
use Illuminate\Database\Seeder;

class DemoEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Flagship Event
        $event = Event::updateOrCreate(
            ['slug' => 'sharod-samman-2026'],
            [
                'name' => 'Dib 24x7 Sharod Samman 2026',
                'display_name' => 'Dib 24x7 Sharod Samman 2026',
                'year' => 2026,
                'slug' => 'sharod-samman-2026',
                'is_registration_active' => true,
                'is_voting_active' => true,
                'is_awards_active' => true,
                'is_timeline_active' => true,
                'tagline' => "Bengal's Most Prestigious Festival & Puja Excellence Awards",
                'about_text' => '<p>Dib 24x7 Sharod Samman celebrates the pinnacle of cultural artistry, community celebration, and architectural brilliance across Bengal. Organized annually, the awards honor the tireless efforts of artisans, lighting wizards, theme designers, and community clubs who transform Kolkata into the world\'s largest open-air art exhibition.</p>',
                'criteria_text' => '<p>Entries are evaluated by an eminent jury comprising master sculptors, architects, and art critics across four core parameters: Aesthetic Originality, Cultural Relevance, Illumination Excellence, and Crowd Management & Eco-Friendly Execution.</p>',
                'closure_message' => 'Voting and registration for Dib 24x7 Sharod Samman 2026 have formally concluded. Thank you to millions of voters and contenders!',
                'countdown_datetime' => '2026-10-18 19:00:00',
                'terms_and_conditions' => '<p>1. Only officially registered community clubs and Puja committees are eligible.<br>2. All submissions must follow municipal fire and electrical safety regulations.<br>3. Public voting results are tabulated under independent audit observation.<br>4. Decisions of the grand jury and organizing committee are final and binding.</p>',
                'og_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=1200&q=80',
                'meta_title' => 'Dib 24x7 Sharod Samman 2026 | Grand Festival Microsite',
                'meta_description' => 'Explore the finest pandals, vote for your favorite club, and watch live event shorts on Dib 24x7 Sharod Samman 2026.',
                'contact_email' => 'events@dib24x7.com',
                'contact_phone' => '+91 98300 12345',
                'external_link' => 'https://dib24x7.com',
                'status' => 'active',
            ]
        );

        // 2. Multi-tier Sponsors (Presenting + 5 Associate Sponsors)
        $sponsorsData = [
            [
                'title' => 'Fortune Oil & Foods',
                'display_name' => 'Fortune',
                'sponsor_type' => 'presenting_partner',
                'sponsor_tag' => 'Title Presenting Partner',
                'logo' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/fortune',
                'slot_order' => 1,
            ],
            [
                'title' => 'Senco Gold & Diamonds',
                'display_name' => 'Senco Gold',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 1',
                'logo' => 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/senco',
                'slot_order' => 1,
            ],
            [
                'title' => 'Asian Paints',
                'display_name' => 'Asian Paints',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 2',
                'logo' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/asianpaints',
                'slot_order' => 2,
            ],
            [
                'title' => 'Boroline',
                'display_name' => 'Boroline',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 3',
                'logo' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/boroline',
                'slot_order' => 3,
            ],
            [
                'title' => 'Tata Tea Gold',
                'display_name' => 'Tata Tea Gold',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 4',
                'logo' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/tatatea',
                'slot_order' => 4,
            ],
            [
                'title' => 'Kalyan Jewellers',
                'display_name' => 'Kalyan Jewellers',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 5',
                'logo' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=300&q=80',
                'landing_url' => 'https://dib24x7.com/kalyan',
                'slot_order' => 5,
            ],
        ];

        foreach ($sponsorsData as $s) {
            Sponsor::updateOrCreate(
                ['event_id' => $event->id, 'title' => $s['title']],
                array_merge($s, ['year' => 2026, 'is_active' => true])
            );
        }

        // 3. ATF Banners (Desktop 1920x600 & Mobile 768x960)
        $bannersData = [
            [
                'title' => 'The Grand Celebration of Art & Devotion',
                'image_desktop' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=1920&q=80',
                'image_mobile' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=768&q=80',
                'cta_link' => '#participants',
                'sort_order' => 1,
            ],
            [
                'title' => 'Cast Your Vote for People\'s Choice Award 2026',
                'image_desktop' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=1920&q=80',
                'image_mobile' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=768&q=80',
                'cta_link' => '#vote',
                'sort_order' => 2,
            ],
        ];

        foreach ($bannersData as $b) {
            Banner::updateOrCreate(
                ['event_id' => $event->id, 'title' => $b['title']],
                array_merge($b, ['year' => 2026, 'is_active' => true])
            );
        }

        // 4. Awards
        $awardsData = [
            [
                'name' => 'Best Puja Overall (Serar Sera)',
                'display_name' => 'Best Puja Overall (সেরার সেরা)',
                'category' => 'Grand Championship',
                'prize_money_or_award' => '₹2,50,000 + Gold Trophy',
                'description' => 'Awarded to the committee demonstrating extraordinary cohesion of theme, idol, and execution.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Best Idol Concept (Sera Pratima)',
                'display_name' => 'Best Idol Concept (সেরা প্রতিমা)',
                'category' => 'Art & Sculpture',
                'prize_money_or_award' => '₹1,00,000 + Trophy',
                'description' => 'Honoring the sculptor who captures unmatched spiritual elegance and aesthetic beauty.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Best Lighting & Illumination (Sera Aalokshojja)',
                'display_name' => 'Best Illumination (সেরা আলোকসজ্জা)',
                'category' => 'Lighting Design',
                'prize_money_or_award' => '₹75,000 + Trophy',
                'description' => 'Celebrating the most magical dynamic lighting and energy-efficient displays.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Best Eco-Friendly Pandal (Paribesh Bandhav)',
                'display_name' => 'Best Eco-Friendly Pandal',
                'category' => 'Sustainability',
                'prize_money_or_award' => '₹75,000 + Trophy',
                'description' => 'Special award honoring sustainable biodegradable building materials and safety.',
                'sort_order' => 4,
            ],
            [
                'name' => 'People\'s Choice Puja (Janapriya Samman)',
                'display_name' => 'People\'s Choice Award',
                'category' => 'Public Voting',
                'prize_money_or_award' => '₹1,50,000 + Trophy',
                'description' => 'Decided entirely by certified digital votes from viewers and attendees.',
                'sort_order' => 5,
            ],
        ];

        foreach ($awardsData as $a) {
            Award::updateOrCreate(
                ['event_id' => $event->id, 'name' => $a['name']],
                array_merge($a, ['year' => 2026, 'is_active' => true])
            );
        }

        // 5. Participants / Contenders (with Cultural / Puja Fields)
        $participantsData = [
            [
                'name' => 'Ballygunge Cultural Association',
                'display_name' => 'Ballygunge Cultural Association',
                'mobile_number' => '9830011223',
                'zone' => 'South Kolkata',
                'locality' => 'Ballygunge Place',
                'landmark' => 'Near Lake Market',
                'short_introduction' => 'Celebrated for traditional elegance and innovative contemporary eco-sculptures since 1951.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1951,
                'puja_theme' => 'Echoes of Terracotta & Clay',
                'idol_artist' => 'Sanatan Dinda',
                'theme_artist' => 'Sanatan Dinda',
                'light_designer' => 'Bablu Lights, Chandannagar',
                'sound_designer' => 'Bickram Ghosh Studio',
                'concept_note_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
                'key_contact_1_name' => 'Dr. Subir Sen',
                'key_contact_1_phone' => '9830011223',
            ],
            [
                'name' => 'Hatibagan Sarbojanin Durgotsav',
                'display_name' => 'Hatibagan Sarbojanin',
                'mobile_number' => '9831122334',
                'zone' => 'North Kolkata',
                'locality' => 'Hatibagan',
                'landmark' => 'Beside Star Theatre',
                'short_introduction' => 'A ninety-year-old traditional giant honoring authentic Bengal temple architectural replicas.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1935,
                'puja_theme' => 'Temple Architecture of Bishnupur',
                'idol_artist' => 'Pradip Rudra Pal',
                'theme_artist' => 'Ramen Mukherjee',
                'light_designer' => 'Sarkar Electric, Howrah',
                'key_contact_1_name' => 'Aniruddha Dasgupta',
                'key_contact_1_phone' => '9831122334',
            ],
            [
                'name' => 'Chetla Agrani Club',
                'display_name' => 'Chetla Agrani Club',
                'mobile_number' => '9832233445',
                'zone' => 'South Kolkata',
                'locality' => 'Chetla',
                'landmark' => 'Opposite Chetla Lock Gate',
                'short_introduction' => 'Pioneers of thought-provoking social installations and immersive experiential pandal spaces.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1959,
                'puja_theme' => 'Ray of Enlightenment (আলোর দিশারী)',
                'idol_artist' => 'Anirban Das',
                'theme_artist' => 'Anirban Das',
                'light_designer' => 'Dinesh Electric',
                'sound_designer' => 'Debojyoti Mishra',
                'key_contact_1_name' => 'Somnath Chatterjee',
                'key_contact_1_phone' => '9832233445',
            ],
            [
                'name' => 'College Square Sarbojanin Durgotsav',
                'display_name' => 'College Square Sarbojanin',
                'mobile_number' => '9833344556',
                'zone' => 'Central Kolkata',
                'locality' => 'College Square',
                'landmark' => 'College Square Swimming Pool',
                'short_introduction' => 'World-famous for its majestic mirror illumination reflected on the water reservoir.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1948,
                'puja_theme' => 'The Imperial Palace of Light',
                'idol_artist' => 'Sanatan Pal',
                'light_designer' => 'Chandan Roy, Chandannagar',
                'key_contact_1_name' => 'Bikash Ghosh',
                'key_contact_1_phone' => '9833344556',
            ],
            [
                'name' => 'Tala Barowari Durgotsab',
                'display_name' => 'Tala Barowari',
                'mobile_number' => '9834455667',
                'zone' => 'North Kolkata',
                'locality' => 'Tala',
                'landmark' => 'Near Tala Tank',
                'short_introduction' => 'A century of rich community tradition rooted in Bengal folk art and wooden handicraft.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => false,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1921,
                'puja_theme' => 'Aaronyak - Forest Reverie',
                'idol_artist' => 'Shibshankar Das',
                'theme_artist' => 'Tapan Ghosh',
                'key_contact_1_name' => 'Kamal Bose',
                'key_contact_1_phone' => '9834455667',
            ],
            [
                'name' => 'Suruchi Sangha',
                'display_name' => 'Suruchi Sangha',
                'mobile_number' => '9835566778',
                'zone' => 'South Kolkata',
                'locality' => 'New Alipore',
                'landmark' => 'Near B.P. Poddar Hospital',
                'short_introduction' => 'Representing state-wide unity and pan-Indian indigenous crafts every autumn season.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1962,
                'puja_theme' => 'Colors of the Coastal Heritage',
                'idol_artist' => 'Subhabrata Nandi',
                'theme_artist' => 'Subhabrata Nandi',
                'key_contact_1_name' => 'Swarup Biswas',
                'key_contact_1_phone' => '9835566778',
            ],
        ];

        $createdParticipants = [];
        foreach ($participantsData as $p) {
            $createdParticipants[] = Participant::updateOrCreate(
                ['event_id' => $event->id, 'name' => $p['name']],
                array_merge($p, ['year' => 2026, 'registration_status' => 'approved'])
            );
        }

        // 6. Winners Podium Linking
        $bestPujaAward = Award::where('event_id', $event->id)->where('name', 'like', '%Best Puja Overall%')->first();
        $bestIdolAward = Award::where('event_id', $event->id)->where('name', 'like', '%Best Idol%')->first();
        $bestLightAward = Award::where('event_id', $event->id)->where('name', 'like', '%Best Lighting%')->first();

        if ($bestPujaAward && isset($createdParticipants[0])) {
            Winner::updateOrCreate([
                'event_id' => $event->id,
                'award_id' => $bestPujaAward->id,
                'participant_id' => $createdParticipants[0]->id,
                'rank_order' => '1st',
            ], [
                'year' => 2026,
                'status' => 'published',
            ]);
        }

        if ($bestIdolAward && isset($createdParticipants[2])) {
            Winner::updateOrCreate([
                'event_id' => $event->id,
                'award_id' => $bestIdolAward->id,
                'participant_id' => $createdParticipants[2]->id,
                'rank_order' => '1st',
            ], [
                'year' => 2026,
                'status' => 'published',
            ]);
        }

        if ($bestLightAward && isset($createdParticipants[3])) {
            Winner::updateOrCreate([
                'event_id' => $event->id,
                'award_id' => $bestLightAward->id,
                'participant_id' => $createdParticipants[3]->id,
                'rank_order' => '1st',
            ], [
                'year' => 2026,
                'status' => 'published',
            ]);
        }

        // 7. Video Shorts (9:16 Aspect Ratio)
        $shortsData = [
            [
                'name' => 'Sculpting Immortality with Sanatan Dinda',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
                'video_id' => 'dQw4w9WgXcQ',
                'caption' => 'A masterclass behind the scenes at Ballygunge Cultural Pratima workshop.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=400&q=80',
                'sort_order' => 1,
            ],
            [
                'name' => 'Magical Illumination Mirror Reflections at College Square',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/jNQXAC9IVRw',
                'video_id' => 'jNQXAC9IVRw',
                'caption' => 'Over 100,000 LEDs creating an aquatic wonderland.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=400&q=80',
                'sort_order' => 2,
            ],
            [
                'name' => 'Celebrity Jury Unveiling at Chetla Agrani',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/3JZ_D3ELwOQ',
                'video_id' => '3JZ_D3ELwOQ',
                'caption' => 'Exclusive walkthrough of the Ray of Enlightenment theme.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=400&q=80',
                'sort_order' => 3,
            ],
        ];

        foreach ($shortsData as $sh) {
            VideoShort::updateOrCreate(
                ['event_id' => $event->id, 'name' => $sh['name']],
                array_merge($sh, ['year' => 2026, 'is_active' => true])
            );
        }

        // 8. Timeline Items
        $timelineData = [
            [
                'title' => 'Nominations & Candidate Registrations Open',
                'milestone_date' => '15 Aug - 15 Sep 2026',
                'description' => 'Online submission of concept notes, themes, and club details.',
                'status' => 'completed',
                'sort_order' => 1,
            ],
            [
                'title' => 'Jury Inspection & First Shortlisting',
                'milestone_date' => '20 Sep - 30 Sep 2026',
                'description' => 'Expert committee field visits to inspect pandals and artistic sculptures.',
                'status' => 'completed',
                'sort_order' => 2,
            ],
            [
                'title' => 'Public Voting Phase Live',
                'milestone_date' => '01 Oct - 15 Oct 2026',
                'description' => 'Digital voting opens worldwide for People\'s Choice awards.',
                'status' => 'ongoing',
                'sort_order' => 3,
            ],
            [
                'title' => 'Grand Finale & Award Gala Ceremony',
                'milestone_date' => '18 Oct 2026',
                'description' => 'Live televised ceremony honoring winners with trophies and cash awards.',
                'status' => 'upcoming',
                'sort_order' => 4,
            ],
        ];

        foreach ($timelineData as $t) {
            TimelineItem::updateOrCreate(
                ['event_id' => $event->id, 'title' => $t['title']],
                $t
            );
        }
    }
}
