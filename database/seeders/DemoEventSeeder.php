<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\TimelineItem;
use App\Models\VideoShort;
use App\Models\Winner;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                'tagline' => "Dibrugarh & Upper Assam's Most Prestigious Festival & Puja Excellence Awards",
                'about_text' => '<p>Dib 24x7 Sharod Samman celebrates the pinnacle of cultural artistry, community celebration, and architectural brilliance across Dibrugarh and Upper Assam. Organized annually on the historic banks of the Brahmaputra, the awards honor the tireless efforts of artisans, lighting wizards, theme designers, and community clubs who illuminate the Tea City of India.</p>',
                'criteria_text' => '<p>Entries are evaluated by an eminent jury across core parameters: Aesthetic Originality, Cultural Relevance, Traditional & Contemporary Artistry, Illumination Excellence, and Crowd Management & Eco-Friendly Execution.</p>',
                'closure_message' => 'Voting and registration for Dib 24x7 Sharod Samman 2026 have formally concluded. Thank you to all participating committees and citizens of Upper Assam!',
                'countdown_datetime' => '2026-10-18 19:00:00',
                'terms_and_conditions' => '<p>1. Only officially registered community clubs and Puja committees in Dibrugarh and Upper Assam are eligible.<br>2. All submissions must adhere to district administration, fire safety, and electrical regulations.<br>3. Public voting results are tabulated under independent audit observation.<br>4. Decisions of the grand jury and organizing committee are final and binding.</p>',
                'og_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=1200&q=80',
                'meta_title' => 'Dib 24x7 Sharod Samman 2026 | Dibrugarh Grand Festival Microsite',
                'meta_description' => 'Explore the finest pandals in Dibrugarh, vote for your favorite club, and watch live event shorts on Dib 24x7 Sharod Samman 2026.',
                'contact_email' => 'events@dib24x7.com',
                'contact_phone' => '+91 94350 12345',
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
                'title' => 'The Grand Celebration of Art & Devotion in Dibrugarh',
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
                'display_name' => 'Best Puja Overall (শ্ৰেষ্ঠ পূজা)',
                'category' => 'Grand Championship',
                'prize_money_or_award' => '₹2,50,000 + Gold Trophy',
                'description' => 'Awarded to the committee demonstrating extraordinary cohesion of theme, idol, and execution in Dibrugarh.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Best Idol Concept (Sera Pratima)',
                'display_name' => 'Best Idol Concept (শ্ৰেষ্ঠ প্ৰতিমা)',
                'category' => 'Art & Sculpture',
                'prize_money_or_award' => '₹1,00,000 + Trophy',
                'description' => 'Honoring the sculptor who captures unmatched spiritual elegance and aesthetic beauty.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Best Lighting & Illumination (Sera Aalokshojja)',
                'display_name' => 'Best Illumination (শ্ৰেষ্ঠ আলোকসজ্জা)',
                'category' => 'Lighting Design',
                'prize_money_or_award' => '₹75,000 + Trophy',
                'description' => 'Celebrating the most magical dynamic lighting and energy-efficient displays.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Best Eco-Friendly Pandal (Paribesh Bandhav)',
                'display_name' => 'Best Eco-Friendly Pandal (পৰিৱেশ অনুকূল মণ্ডপ)',
                'category' => 'Sustainability',
                'prize_money_or_award' => '₹75,000 + Trophy',
                'description' => 'Special award honoring sustainable biodegradable building materials and safety.',
                'sort_order' => 4,
            ],
            [
                'name' => 'People\'s Choice Puja (Janapriya Samman)',
                'display_name' => 'People\'s Choice Award (জনপ্ৰিয় পূজা সন্মান)',
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

        // 5. Seed Geographic Masters (Dibrugarh Zones & Localities)
        $zonesHierarchy = [
            'Central Dibrugarh' => ['Chowkidinghee', 'Thana Chariali', 'New Market', 'Marwari Patty', 'Graham Bazar'],
            'East Dibrugarh' => ['Amolapatty', 'Mancotta Road', 'Jalan Nagar', 'Khalihamari'],
            'West Dibrugarh' => ['Naliapool', 'Santipara', 'Paltan Bazar', 'Mohanaghat'],
            'South Dibrugarh' => ['Boiragimoth', 'Milan Nagar', 'Jhapojabari'],
            'Suburban & Outskirts' => ['Maijan', 'Dibrugarh University Campus', 'Lepetkatta'],
        ];

        $zoneCache = [];
        $localityCache = [];
        $zoneSort = 1;

        foreach ($zonesHierarchy as $zName => $locs) {
            $zoneModel = Zone::updateOrCreate(
                ['name' => $zName],
                [
                    'slug' => Str::slug($zName),
                    'sort_order' => $zoneSort++,
                    'is_active' => true,
                ]
            );
            $zoneCache[$zName] = $zoneModel;

            $locSort = 1;
            foreach ($locs as $lName) {
                $locModel = Locality::updateOrCreate(
                    ['zone_id' => $zoneModel->id, 'name' => $lName],
                    [
                        'slug' => Str::slug($lName),
                        'sort_order' => $locSort++,
                        'is_active' => true,
                    ]
                );
                $localityCache[$zName.'::'.$lName] = $locModel;
            }
        }

        // 6. Participants / Contenders (Dibrugarh Puja Committees)
        $participantsData = [
            [
                'name' => 'Chowkidinghee Sarbajanin Durga Puja Committee',
                'display_name' => 'Chowkidinghee Sarbajanin Durga Puja Committee',
                'mobile_number' => '9435011223',
                'zone' => 'Central Dibrugarh',
                'locality' => 'Chowkidinghee',
                'landmark' => 'Near Chowkidinghee Playground',
                'short_introduction' => 'Renowned for colossal pandal architecture, eco-friendly themes, and mesmerizing cultural evenings in the heart of Dibrugarh.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1958,
                'puja_theme' => 'Heritage of Assam: Muga & Cane Craftsmanship',
                'idol_artist' => 'Nuruddin Ahmed',
                'theme_artist' => 'Pranab Baruah',
                'light_designer' => 'Maa Electric, Chowkidinghee',
                'sound_designer' => 'Brahmaputra Acoustics',
                'concept_note_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
                'key_contact_1_name' => 'Ranjit Phukan',
                'key_contact_1_phone' => '9435011223',
            ],
            [
                'name' => 'Graham Bazar Sarbajanin Durga Puja Samiti',
                'display_name' => 'Graham Bazar Sarbajanin Durga Puja Samiti',
                'mobile_number' => '9435122334',
                'zone' => 'Central Dibrugarh',
                'locality' => 'Graham Bazar',
                'landmark' => 'Near Graham Bazar Girls High School',
                'short_introduction' => 'One of the oldest traditional celebrations in the Tea City, celebrated for exquisite classical idol craftsmanship and heritage rituals.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1601972599720-36938d4ecd31?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1942,
                'puja_theme' => 'Temple Architecture of Kamakhya',
                'idol_artist' => 'Gauranga Pal',
                'theme_artist' => 'Biren Singha',
                'light_designer' => 'Roy Electric Works',
                'key_contact_1_name' => 'Anirban Dutta',
                'key_contact_1_phone' => '9435122334',
            ],
            [
                'name' => 'Amolapatty Sarbajanin Durga Puja',
                'display_name' => 'Amolapatty Sarbajanin Durga Puja',
                'mobile_number' => '9435233445',
                'zone' => 'East Dibrugarh',
                'locality' => 'Amolapatty',
                'landmark' => 'Near Amolapatty Natya Mandir',
                'short_introduction' => 'Known for artistic illumination, spiritual serenity, and deeply engaging community cultural programs on Mancotta Road.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=800&q=80',
                'image_1' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1964,
                'puja_theme' => 'Echoes of the Brahmaputra',
                'idol_artist' => 'Dhiren Pal',
                'theme_artist' => 'Mridul Bordoloi',
                'light_designer' => 'Chandan Illuminations',
                'sound_designer' => 'Bhupen Hazarika Cultural Wing',
                'key_contact_1_name' => 'Sanjay Gohain',
                'key_contact_1_phone' => '9435233445',
            ],
            [
                'name' => 'Naliapool Sarbajanin Durga Puja',
                'display_name' => 'Naliapool Sarbajanin Durga Puja',
                'mobile_number' => '9435344556',
                'zone' => 'West Dibrugarh',
                'locality' => 'Naliapool',
                'landmark' => 'Near Railway Colony Field',
                'short_introduction' => 'Famed across Upper Assam for pioneering creative themes, light spectacles, and welcoming hundreds of thousands of devotees.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1952,
                'puja_theme' => 'The Palace of Golden Tea Blossoms',
                'idol_artist' => 'Sanatan Pal',
                'light_designer' => 'Assam Light House',
                'key_contact_1_name' => 'Bikash Debnath',
                'key_contact_1_phone' => '9435344556',
            ],
            [
                'name' => 'Boiragimoth Sarbajanin Durga Puja Samiti',
                'display_name' => 'Boiragimoth Sarbajanin Durga Puja Samiti',
                'mobile_number' => '9435455667',
                'zone' => 'South Dibrugarh',
                'locality' => 'Boiragimoth',
                'landmark' => 'Near Boiragimoth Namghar',
                'short_introduction' => 'Celebrated for its warm neighborhood spirit, folk-themed traditional decorations, and exemplary crowd safety.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => false,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1971,
                'puja_theme' => 'Xophura: Folk Art of Majuli & Upper Assam',
                'idol_artist' => 'Ratan Chitrakar',
                'theme_artist' => 'Jiten Hazarika',
                'key_contact_1_name' => 'Pradip Saikia',
                'key_contact_1_phone' => '9435455667',
            ],
            [
                'name' => 'Santipara Sarbajanin Durga Puja',
                'display_name' => 'Santipara Sarbajanin Durga Puja',
                'mobile_number' => '9435566778',
                'zone' => 'West Dibrugarh',
                'locality' => 'Santipara',
                'landmark' => 'Near Santipara Rail Gate',
                'short_introduction' => 'Emphasizing social harmony, green eco-materials, and unity across communities in Dibrugarh.',
                'primary_display_image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=800&q=80',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => 1968,
                'puja_theme' => 'Shakti: Conservation of Assam Flora & Fauna',
                'idol_artist' => 'Tarun Paul',
                'theme_artist' => 'Subhashish Das',
                'key_contact_1_name' => 'Dipak Bhattacharjee',
                'key_contact_1_phone' => '9435566778',
            ],
        ];

        $createdParticipants = [];
        foreach ($participantsData as $p) {
            $zModel = $zoneCache[$p['zone']] ?? null;
            $lModel = $localityCache[$p['zone'].'::'.$p['locality']] ?? null;
            if (! $lModel && $zModel && ! empty($p['locality'])) {
                $lModel = Locality::firstOrCreate(
                    ['zone_id' => $zModel->id, 'name' => $p['locality']],
                    ['slug' => Str::slug($p['locality'])]
                );
            }

            $createdParticipants[] = Participant::updateOrCreate(
                ['event_id' => $event->id, 'name' => $p['name']],
                array_merge($p, [
                    'zone_id' => $zModel?->id,
                    'locality_id' => $lModel?->id,
                    'year' => 2026,
                    'registration_status' => 'approved',
                ])
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
                'name' => 'Pandal Making Behind the Scenes at Chowkidinghee',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
                'video_id' => 'dQw4w9WgXcQ',
                'caption' => 'Master artisans crafting bamboo and cane wonders at Chowkidinghee.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=400&q=80',
                'sort_order' => 1,
            ],
            [
                'name' => 'Magical Night Illumination at Naliapool Field',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/jNQXAC9IVRw',
                'video_id' => 'jNQXAC9IVRw',
                'caption' => 'Over 100,000 LEDs creating an aquatic wonderland in West Dibrugarh.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=400&q=80',
                'sort_order' => 2,
            ],
            [
                'name' => 'Celebrity Jury Tour at Graham Bazar & Amolapatty',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/3JZ_D3ELwOQ',
                'video_id' => '3JZ_D3ELwOQ',
                'caption' => 'Exclusive walkthrough of traditional idol craftsmanship and cultural exhibitions.',
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
