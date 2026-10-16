<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Locality;
use App\Models\OtpSetting;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\TimelineItem;
use App\Models\VideoShort;
use App\Models\Winner;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LiveMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Seeding live master data...');

        // 1. Seed Live Flagship Event
        $event = $this->seedLiveEvent();

        // 2. Seed Multi-tier Sponsors
        $this->seedLiveSponsors($event);

        // 3. Seed Hero & Marketing Banners
        $this->seedLiveBanners($event);

        // 4. Seed Official Awards
        $this->seedLiveAwards($event);

        // 5. Seed Timeline Milestones
        $this->seedLiveTimeline($event);

        // 6. Seed Video Shorts
        $this->seedLiveVideoShorts($event);

        // 7. Seed Production OTP Settings
        $this->seedLiveOtpSettings();

        // 8. Seed Geographic Master Zones
        $zonesCache = $this->seedLiveZones();

        $this->command?->info('Master event, sponsors, banners, awards, timeline, shorts, and zones seeded successfully.');

        // 9. Seed / Sync Puja Committees from CSV
        $this->seedPujaCommitteesFromCsv($event, $zonesCache);
    }

    /**
     * Seed the live flagship event configuration.
     */
    protected function seedLiveEvent(): Event
    {
        return Event::firstOrCreate(
            ['slug' => 'durga-puja-awards-2026'],
            [
                'name' => 'Dib 24x7 Sharod Samman 2026',
                'display_name' => 'DIBRUGARH DURGOTSAV 2026',
                'year' => 2026,
                'slug' => 'durga-puja-awards-2026',
                'is_registration_active' => true,
                'is_voting_active' => true,
                'is_awards_active' => true,
                'is_timeline_active' => true,
                'tagline' => "People's Choice Awards  #YouAreTheJury",
                'about_text' => '<p>Dibrugarh 24x7 People&#039;s Favorite Award celebrates the pinnacle of cultural artistry, community celebration, and architectural brilliance across Dibrugarh and Upper Assam. Organized annually on the historic banks of the Brahmaputra, the awards honor the tireless efforts of artisans, lighting wizards, theme designers, and community clubs who illuminate the Tea City of India.</p>',
                'criteria_text' => '<p>Entries are evaluated by an eminent jury across core parameters: Aesthetic Originality, Cultural Relevance, Traditional &amp; Contemporary Artistry, Illumination Excellence, and Crowd Management &amp; Eco-Friendly Execution.</p>',
                'closure_message' => 'Voting and registration for Dib 24x7 Sharod Samman 2026 have formally concluded. Thank you to all participating committees and citizens of Upper Assam!',
                'countdown_datetime' => '2026-10-20 12:00:00',
                'terms_and_conditions' => '<p><strong>1. One Vote Per User:</strong> Each voter may select only one Puja pandal from the complete list and cast only one vote for either Theme, Pandal or Pratima of that selected pandal. Multiple votes across different pandals or categories are not permitted.</p><p><strong>2. Fair Play &amp; Anti-Spam Protection:</strong> All votes are subject to verification. Automated bot scripts, IP manipulation, duplicate submissions and fraudulent voting attempts will be detected and invalid votes may be removed to maintain fair competition.</p><p><strong>3. Eligibility of Participating Committees:</strong> Only officially registered Sarbajanin Durga Puja committees operating within Dibrugarh Municipal boundaries are eligible to participate in the Dibrugarh Durgotsav Awards 2026.</p><p><strong>4. Public Voting &amp; Category Awards</strong>: Winners across the various award categories will be shortlisted and determined based on verified public votes received by each participating pandal, subject to the applicable category criteria and official evaluation rules.</p><p><strong>5. Jury Evaluation &amp; Overall Winner:</strong> A jury comprising representatives from the organising team and sponsor panel will independently evaluate the participating pandals. The cumulative jury scores will determine the overall winner and help identify the best-performing pandals.</p><p><strong>6. No Social Media Engagement Points:</strong> Likes, shares, comments, views or any other engagement on social media platforms will not be counted as voting points or influence the official voting results.</p><p><strong>7. Voting Timeline: </strong>Online voting opens on 17 October 2026 (Maha Saptami) at 12:00 noon and closes on 20 October 2026 at 12:00 noon. Voting will be disabled after the closing time, and late submissions will not be accepted.</p><p><strong>8. Final Decision &amp; Verification:</strong> All results will be subject to verification of vote counts and the applicable jury evaluation process. The decisions of the Dibrugarh Durgotsav Awards 2026 Organising Committee and Jury Board shall be final and binding, with no further contest or appeal.</p>',
                'og_image' => 'events/og/01M50HCE7E0E7EYFE4NB1GQ4WB.png',
                'meta_title' => "Dibrugarh Durgotsav Awards 2026 - People's Favourite Puja ",
                'meta_description' => 'Dibrugarh 24x7 presents first of its kind online voting for your favourite puja using secured OTP authentication with a unique mobile number eligible to vote only once. Visit and vote for your favourite Puja',
                'contact_email' => 'dibrugarh24x7@gmail.com',
                'contact_phone' => '+91 86380 17280',
                'external_link' => null,
                'status' => 'active',
                'voting_mode' => 'participant',
            ]
        );
    }

    /**
     * Seed live sponsors with exact tiers, tags, logos, and active states.
     */
    protected function seedLiveSponsors(Event $event): void
    {
        $sponsorsData = [
            [
                'title' => 'Fortune Oil & Foods',
                'display_name' => 'Fortune',
                'sponsor_type' => 'presenting_partner',
                'sponsor_tag' => 'Title Sponsor',
                'logo' => 'sponsors/logos/fortune.jpg',
                'description' => null,
                'landing_url' => null,
                'slot_order' => 1,
                'year' => 2026,
                'is_active' => true,
            ],
            [
                'title' => 'Senco Gold & Diamonds',
                'display_name' => 'Senco Gold',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Hospitality Sponsor',
                'logo' => 'sponsors/logos/senco-gold.jpg',
                'description' => null,
                'landing_url' => null,
                'slot_order' => 1,
                'year' => 2026,
                'is_active' => false,
            ],
            [
                'title' => 'Asian Paints',
                'display_name' => 'Asian Paints',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Trophy Sponsor',
                'logo' => 'sponsors/logos/asian-paints.jpg',
                'description' => null,
                'landing_url' => null,
                'slot_order' => 2,
                'year' => 2026,
                'is_active' => true,
            ],
            [
                'title' => 'Boroline',
                'display_name' => 'Boroline',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Technology Partner',
                'logo' => 'sponsors/logos/01M50B9XATM5F0WTE40GJ8Q8HT.png',
                'description' => null,
                'landing_url' => null,
                'slot_order' => 3,
                'year' => 2026,
                'is_active' => true,
            ],
            [
                'title' => 'Tata Tea Gold',
                'display_name' => 'Tata Tea Gold',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 4',
                'logo' => 'sponsors/logos/tata-tea-gold.png',
                'description' => null,
                'landing_url' => 'https://dib24x7.com/tatatea',
                'slot_order' => 4,
                'year' => 2026,
                'is_active' => false,
            ],
            [
                'title' => 'Kalyan Jewellers',
                'display_name' => 'Kalyan Jewellers',
                'sponsor_type' => 'associate_sponsor',
                'sponsor_tag' => 'Associate Sponsor 5',
                'logo' => 'sponsors/logos/kalyan-jewellers.png',
                'description' => null,
                'landing_url' => 'https://dib24x7.com/kalyan',
                'slot_order' => 5,
                'year' => 2026,
                'is_active' => false,
            ],
            [
                'title' => 'Test Sponsor',
                'display_name' => 'Tech',
                'sponsor_type' => 'powered_by',
                'sponsor_tag' => 'Co-Sponsor',
                'logo' => 'sponsors/logos/01M50B8ED1WYTQM2VR452NFZVN.jpg',
                'description' => null,
                'landing_url' => null,
                'slot_order' => 1,
                'year' => 2026,
                'is_active' => true,
            ],
        ];

        foreach ($sponsorsData as $data) {
            Sponsor::firstOrCreate(
                ['event_id' => $event->id, 'title' => $data['title']],
                $data
            );
        }
    }

    /**
     * Seed live banners for desktop and mobile viewports.
     */
    protected function seedLiveBanners(Event $event): void
    {
        $bannersData = [
            [
                'title' => 'The Grand Celebration of Art & Devotion in Dibrugarh',
                'image_desktop' => 'banners/desktop/01M3XCHZ62T8A8JRQHRFYA5ZT4.jpg',
                'image_mobile' => 'banners/mobile/01M50CR7FDR2QTPGXA34XYN8CT.jpg',
                'cta_link' => '#participants',
                'year' => 2026,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => "Cast Your Vote for People's Choice Award 2026",
                'image_desktop' => 'banners/desktop/01M4NE7K48VSWX80WAAK2AZSQT.jpg',
                'image_mobile' => 'banners/mobile/01M4NE7K4MS2WD49JPGA0MX8F4.jpg',
                'cta_link' => '#vote',
                'year' => 2026,
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($bannersData as $data) {
            Banner::firstOrCreate(
                ['event_id' => $event->id, 'title' => $data['title']],
                $data
            );
        }
    }

    /**
     * Seed live awards with voting permissions.
     */
    protected function seedLiveAwards(Event $event): void
    {
        $awardsData = [
            [
                'name' => 'Best Puja Overall',
                'display_name' => 'Best Puja Overall',
                'description' => 'Awarded to the committee demonstrating extraordinary cohesion of Pratima, Theme, and Pandal in Dibrugarh.',
                'category' => 'Grand Championship',
                'prize_money_or_award' => 'Trophy + Recognition',
                'year' => 2026,
                'sort_order' => 1,
                'is_active' => true,
                'is_voting_allowed' => false,
            ],
            [
                'name' => 'Best Pritama',
                'display_name' => 'Best Pratima',
                'description' => 'Honoring the sculptor who captures unmatched spiritual elegance and aesthetic beauty.',
                'category' => 'Art & Sculpture',
                'prize_money_or_award' => 'Trophy + Recognition',
                'year' => 2026,
                'sort_order' => 2,
                'is_active' => true,
                'is_voting_allowed' => true,
            ],
            [
                'name' => 'Best Theme',
                'display_name' => 'Best Theme',
                'description' => 'Celebrating the pujas displaying a social message or theme based concept',
                'category' => 'SOCIAL MESSAGE',
                'prize_money_or_award' => 'Trophy + Recognition',
                'year' => 2026,
                'sort_order' => 3,
                'is_active' => true,
                'is_voting_allowed' => true,
            ],
            [
                'name' => 'Best Pandal',
                'display_name' => 'Best Pandal',
                'description' => 'Recognising the pujas with beautiful architectural designs, dynamic lighting, eco-friendly approach',
                'category' => 'Designs and Sustainability',
                'prize_money_or_award' => 'Trophy + Recognition',
                'year' => 2026,
                'sort_order' => 4,
                'is_active' => true,
                'is_voting_allowed' => true,
            ],
        ];

        foreach ($awardsData as $data) {
            Award::firstOrCreate(
                ['event_id' => $event->id, 'name' => $data['name']],
                $data
            );
        }
    }

    /**
     * Seed live event timeline milestones.
     */
    protected function seedLiveTimeline(Event $event): void
    {
        $timelineData = [
            [
                'title' => 'Puja Registration',
                'milestone_date' => '10th October to 15th October',
                'description' => null,
                'status' => 'ongoing',
                'sort_order' => 1,
            ],
            [
                'title' => 'Public Voting Line Open',
                'milestone_date' => '17th October 12 noon to 20th October 12 noon',
                'description' => null,
                'status' => 'upcoming',
                'sort_order' => 2,
            ],
            [
                'title' => 'Winner Announcement',
                'milestone_date' => '20th October 12 noon',
                'description' => null,
                'status' => 'upcoming',
                'sort_order' => 3,
            ],
            [
                'title' => 'Award Gala Ceremony',
                'milestone_date' => '24th October 6 pm onwards',
                'description' => null,
                'status' => 'upcoming',
                'sort_order' => 4,
            ],
        ];

        foreach ($timelineData as $data) {
            TimelineItem::firstOrCreate(
                ['event_id' => $event->id, 'title' => $data['title']],
                $data
            );
        }
    }

    /**
     * Seed live video shorts with genuine URLs and uploaded thumbnails.
     */
    protected function seedLiveVideoShorts(Event $event): void
    {
        $shortsData = [
            [
                'name' => 'Progressive Youth Cultural Association',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/BBTMwv6GNyI',
                'video_id' => 'BBTMwv6GNyI',
                'caption' => 'Progressive Youth Cultural Association',
                'thumbnail_image' => 'shorts/thumbnails/01M51RRXYB2AEQP4Z2VVEMPX46.jfif',
                'year' => 2026,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Grahambazar Mitra Sangha',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/y3mihOhzvHU',
                'video_id' => 'y3mihOhzvHU',
                'caption' => 'Grahambazar Mitra Sangha',
                'thumbnail_image' => 'shorts/thumbnails/01M51TWV8XEQ5SC0RSTK095CJ6.png',
                'year' => 2026,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Registration Open',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/_B7a43gX0AU',
                'video_id' => '_B7a43gX0AU',
                'caption' => null,
                'thumbnail_image' => 'shorts/thumbnails/01M51VKNTW4PS3P34GBDYT64EE.png',
                'year' => 2026,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Gabharupather Sarbajanin Durga Puja',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/9MQ7rPnHy7M',
                'video_id' => '9MQ7rPnHy7M',
                'caption' => null,
                'thumbnail_image' => 'shorts/thumbnails/01M51VYR3HAH67F4YZW8V6FEJ1.jfif',
                'year' => 2026,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Maa Has Arrived',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/NPaV-ytqUcY',
                'video_id' => null,
                'caption' => null,
                'thumbnail_image' => 'shorts/thumbnails/01M51W56YYN3N3F73Q9VTM7GX5.PNG',
                'year' => 2026,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Mahalaya Dibrugarh 2026',
                'platform' => 'youtube_shorts',
                'video_url' => 'https://www.youtube.com/shorts/8FeEBNaohHQ',
                'video_id' => null,
                'caption' => null,
                'thumbnail_image' => 'shorts/thumbnails/01M51W9YX28ZYMPJGB01RVM6RF.jfif',
                'year' => 2026,
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($shortsData as $data) {
            VideoShort::firstOrCreate(
                ['event_id' => $event->id, 'name' => $data['name']],
                $data
            );
        }
    }

    /**
     * Seed live production OTP gateway configuration.
     */
    protected function seedLiveOtpSettings(): void
    {
        $activeDriver = app()->environment('testing') ? 'log' : 'fast2sms';

        OtpSetting::firstOrCreate(
            ['id' => 1],
            [
                'active_driver' => $activeDriver,
                'otp_length' => 4,
                'expiry_minutes' => 5,
                'resend_cooldown_seconds' => 60,
                'max_attempts' => 5,
                'message_template' => 'Your verification code is :otp. Valid for :minutes minutes. Please do not share this code.',
                'master_otp' => null,
                'test_phone_numbers' => null,
                'fast2sms_settings' => [
                    'api_key' => 'JDP0piX6EKvUAjmLNRGl7ZaoYWs1erB3bQM4kzqycCSf9FtOwu6adPeTZEMXKuArmkjnsoFL182glB30',
                    'low_balance_threshold' => null,
                    'route' => 'dlt',
                    'sender_id' => 'DIGPIL',
                    'entity_id' => '228072',
                ],
            ]
        );
    }

    /**
     * Seed the 4 official live geographic zones.
     *
     * @return array<string, Zone>
     */
    protected function seedLiveZones(): array
    {
        $zonesList = [
            ['name' => 'Amc Area', 'slug' => 'amc-area', 'sort_order' => 1],
            ['name' => 'Central Dibrugarh', 'slug' => 'central-dibrugarh', 'sort_order' => 2],
            ['name' => 'Market Area', 'slug' => 'market-area', 'sort_order' => 3],
            ['name' => 'Others', 'slug' => 'others', 'sort_order' => 4],
        ];

        $zonesCache = [];
        foreach ($zonesList as $z) {
            $model = Zone::firstOrCreate(
                ['slug' => $z['slug']],
                [
                    'name' => $z['name'],
                    'sort_order' => $z['sort_order'],
                    'is_active' => true,
                ]
            );
            $zonesCache[$z['name']] = $model;
        }

        return $zonesCache;
    }

    /**
     * Seed or sync Puja Committees and Localities from CSV file.
     *
     * @param  array<string, Zone>  $zonesCache
     */
    protected function seedPujaCommitteesFromCsv(Event $event, array $zonesCache): void
    {
        $csvPath = file_exists(storage_path('all-puja.csv'))
            ? storage_path('all-puja.csv')
            : storage_path('app/public/all-puja.csv');

        if (! file_exists($csvPath)) {
            $this->command?->error("CSV file not found at: {$csvPath}");

            return;
        }

        $this->command?->info("Reading puja committee records from: {$csvPath}");

        // Ensure storage directories exist
        Storage::disk('public')->makeDirectory('participants/primary');
        Storage::disk('public')->makeDirectory('participants/gallery');

        $handle = fopen($csvPath, 'r');
        $rawHeader = fgetcsv($handle);
        $header = array_map(fn ($col) => trim((string) $col), $rawHeader ?: []);

        $colIndex = [
            'reg_no' => array_search('REGD PUJA #', $header),
            'name' => array_search('PUJA COMMITEE NAME', $header),
            'area' => array_search('AREA', $header),
            'phone' => array_search('PHONE NUMBER', $header),
            'contact' => array_search('PRESIDENT/SECRETARY NAME', $header),
            'theme' => array_search('THEME', $header),
            'milestone' => array_search('MILESTONE YEAR', $header),
            'map' => array_search('MAP LOCATION', $header),
        ];

        $localitiesCache = [];
        $createdParticipants = [];
        $createdCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $row = array_map(fn ($v) => trim((string) $v), $row);

            $rawName = $row[$colIndex['name']] ?? '';
            if (empty($rawName) || strlen($rawName) < 4) {
                continue;
            }

            // Exclude accidental unfinished rows like "MADHYA CHJOW"
            if (strtoupper($rawName) === 'MADHYA CHJOW') {
                continue;
            }

            $regNo = (int) ($row[$colIndex['reg_no']] ?? 0);
            $rawArea = $row[$colIndex['area']] ?? '';
            $rawPhone = $row[$colIndex['phone']] ?? '';
            $rawContact = $row[$colIndex['contact']] ?? '';
            $rawTheme = $row[$colIndex['theme']] ?? '';
            $rawMilestone = $row[$colIndex['milestone']] ?? '';
            $rawMap = $row[$colIndex['map']] ?? '';

            // Handle row 21 anomaly where theme was placed in milestone column
            if (empty($rawTheme) && Str::contains($rawMilestone, 'Assamese Culture', true)) {
                $rawTheme = $rawMilestone;
                $rawMilestone = '';
            }

            // Normalize Zone
            $zoneName = $this->normalizeZoneName($rawArea);
            $zone = $zonesCache[$zoneName] ?? Zone::firstOrCreate(
                ['name' => $zoneName],
                ['slug' => Str::slug($zoneName), 'sort_order' => 10, 'is_active' => true]
            );
            $zonesCache[$zoneName] = $zone;

            // Derive and Normalize Locality
            $localityName = $this->deriveLocalityName($rawName, $zoneName);
            $localityCacheKey = $zone->id.'::'.$localityName;

            if (! isset($localitiesCache[$localityCacheKey])) {
                $existingLoc = Locality::where('zone_id', $zone->id)->where('name', $localityName)->first();
                if (! $existingLoc) {
                    $existingLoc = Locality::create([
                        'zone_id' => $zone->id,
                        'name' => $localityName,
                        'slug' => Str::slug($localityName),
                        'sort_order' => count($localitiesCache) + 1,
                        'is_active' => true,
                    ]);
                }
                $localitiesCache[$localityCacheKey] = $existingLoc;
            }
            $locality = $localitiesCache[$localityCacheKey];

            // Normalize Contact Phone & Fallback
            $cleanPhone = preg_replace('/[^\d]/', '', $rawPhone);
            if (empty($cleanPhone) || strlen($cleanPhone) < 10) {
                $cleanPhone = sprintf('943500%04d', $regNo > 0 ? $regNo : ($createdCount + 1));
            }

            // Normalize Contact Person
            $cleanContact = preg_replace('/\s*\(.*?\)\s*/', '', $rawContact);
            $cleanContact = trim(str_replace(["\r", "\n", ','], ' ', $cleanContact));

            // Normalize Google Map URL
            $mapUrl = null;
            if (! empty($rawMap)) {
                if (str_starts_with($rawMap, 'http://') || str_starts_with($rawMap, 'https://')) {
                    $mapUrl = $rawMap;
                } elseif (preg_match('/^\s*(-?\d+(\.\d+)?)\s*,\s*(-?\d+(\.\d+)?)\s*$/', $rawMap, $matches)) {
                    $mapUrl = "https://www.google.com/maps?q={$matches[1]},{$matches[3]}";
                }
            }

            // Calculate first year of puja from milestone
            $firstYear = $this->calculateFirstYearOfPuja($rawMilestone, $event->year ?? 2026);

            // Display Name
            $displayName = $this->formatDisplayName($rawName);

            // Assign Images
            $slug = Str::slug($rawName);
            $primaryImage = $this->resolvePrimaryImage($slug, $rawName);
            $galleryImage1 = $this->resolveGalleryImage($slug, 1);
            $galleryImage2 = $this->resolveGalleryImage($slug, 2);

            // Create or update Participant from CSV without overwriting UI-uploaded media
            $existing = Participant::where('event_id', $event->id)->where('name', $rawName)->first();

            $participantData = [
                'display_name' => $displayName,
                'zone_id' => $zone->id,
                'locality_id' => $locality->id,
                'zone' => $zone->name,
                'locality' => $locality->name,
                'mobile_number' => $cleanPhone,
                'key_contact_1_name' => $cleanContact ?: ($existing?->key_contact_1_name ?? null),
                'key_contact_1_phone' => $cleanPhone,
                'google_map_url' => $mapUrl ?: ($existing?->google_map_url ?? null),
                'puja_theme' => ! empty($rawTheme) ? trim($rawTheme) : ($existing?->puja_theme ?? null),
                'first_year_of_puja' => $firstYear ?: ($existing?->first_year_of_puja ?? null),
                'year' => $event->year ?? 2026,
                'registration_status' => 'approved',
                'is_puja_contest' => true,
                'is_shortlisted' => true,
                'short_introduction' => "Official contender for {$event->name} representing {$locality->name}, {$zone->name}.",
            ];

            if (! $existing) {
                // New participant from CSV: set images
                $participantData['event_id'] = $event->id;
                $participantData['name'] = $rawName;
                $participantData['primary_display_image'] = $primaryImage;
                $participantData['image_1'] = $galleryImage1;
                $participantData['image_2'] = $galleryImage2;
                $participantData['image_3'] = $galleryImage1;

                $createdParticipants[] = Participant::create($participantData);
            } else {
                // Existing participant: preserve UI-uploaded images
                if (empty($existing->primary_display_image)) {
                    $participantData['primary_display_image'] = $primaryImage;
                }
                $existing->update($participantData);
                $createdParticipants[] = $existing;
            }

            $createdCount++;
        }

        fclose($handle);

        // Only link sample winners on initial fresh setup if zero winners exist (never overwrite UI-managed winners)
        if (Winner::where('event_id', $event->id)->count() === 0) {
            $awards = Award::where('event_id', $event->id)->get();
            if ($awards->isNotEmpty() && ! empty($createdParticipants)) {
                $bestPujaAward = $awards->first(fn ($a) => Str::contains($a->name, 'Best Puja', true)) ?? $awards->first();
                if ($bestPujaAward && isset($createdParticipants[0])) {
                    Winner::create([
                        'event_id' => $event->id,
                        'award_id' => $bestPujaAward->id,
                        'participant_id' => $createdParticipants[0]->id,
                        'rank_order' => '1st',
                        'year' => $event->year ?? 2026,
                        'status' => 'published',
                    ]);
                }

                $bestIdolAward = $awards->first(fn ($a) => Str::contains($a->name, 'Pritama', true) || Str::contains($a->name, 'Pratima', true));
                if ($bestIdolAward && isset($createdParticipants[1])) {
                    Winner::create([
                        'event_id' => $event->id,
                        'award_id' => $bestIdolAward->id,
                        'participant_id' => $createdParticipants[1]->id,
                        'rank_order' => '1st',
                        'year' => $event->year ?? 2026,
                        'status' => 'published',
                    ]);
                }
            }
        }

        $this->command?->info("Successfully seeded {$createdCount} puja committees across ".count($zonesCache).' zones and '.count($localitiesCache).' localities.');
    }

    /**
     * Normalize area name to clean Title Case Zone matching live zones.
     */
    protected function normalizeZoneName(string $area): string
    {
        $cleaned = trim($area);
        if (empty($cleaned)) {
            return 'Central Dibrugarh';
        }

        return match (strtoupper($cleaned)) {
            'AMC AREA' => 'Amc Area',
            'DIBRUGARH MARKET AREA' => 'Market Area',
            'MARKET AREA' => 'Market Area',
            'CENTRAL DIBRUGARH' => 'Central Dibrugarh',
            'OTHERS' => 'Others',
            default => Str::title(strtolower($cleaned)),
        };
    }

    /**
     * Derive locality / neighborhood name from the puja committee name.
     */
    protected function deriveLocalityName(string $pujaName, string $zoneName): string
    {
        $nameUpper = strtoupper($pujaName);

        if (str_contains($nameUpper, 'GABHARUPATHER')) {
            return 'Gabharupather';
        }
        if (str_contains($nameUpper, 'GRAHAM BAZAR')) {
            return 'Graham Bazar';
        }
        if (str_contains($nameUpper, 'JHALUKPARA')) {
            return 'Jhalukpara';
        }
        if (str_contains($nameUpper, 'MITRA SANGH')) {
            return 'Graham Bazar';
        }
        if (str_contains($nameUpper, 'CHOWKDNGHEE') || str_contains($nameUpper, 'CHOWKIDINGHEE')) {
            if (str_contains($nameUpper, 'MADHYA')) {
                return 'Madhya Chowkidinghee';
            }
            if (str_contains($nameUpper, 'KP ROAD')) {
                return 'Chowkidinghee (KP Road)';
            }

            return 'Chowkidinghee';
        }
        if (str_contains($nameUpper, 'MARWARI')) {
            return 'Marwari Patty';
        }
        if (str_contains($nameUpper, 'GANGAPARA')) {
            return 'Gangapara';
        }
        if (str_contains($nameUpper, 'CHIRING CHAPORI')) {
            if (str_contains($nameUpper, 'GAUSHALA')) {
                return 'Chiring Chapori (Gaushala)';
            }
            if (str_contains($nameUpper, 'JYOTI RUPA')) {
                return 'Chiring Chapori (Jyoti Rupa Path)';
            }

            return 'Chiring Chapori';
        }
        if (str_contains($nameUpper, 'NAYA BAZAR')) {
            return 'Naya Bazar';
        }
        if (str_contains($nameUpper, 'JALANNAGAR') || str_contains($nameUpper, 'JALAN NAGAR')) {
            return 'Jalan Nagar';
        }
        if (str_contains($nameUpper, 'JYOTI NAGAR')) {
            return 'Jyoti Nagar';
        }
        if (str_contains($nameUpper, 'THEATREPARA') || str_contains($nameUpper, 'MALIPATTY')) {
            return 'Theatrepara / Malipatty';
        }
        if (str_contains($nameUpper, 'KALBARI') || str_contains($nameUpper, 'KALIBARI')) {
            return 'Kalibari';
        }
        if (str_contains($nameUpper, 'POLO GROUND')) {
            return 'Polo Ground';
        }
        if (str_contains($nameUpper, 'TINKUNIA')) {
            return 'Tinkunia';
        }
        if (str_contains($nameUpper, 'INDIA CLUB')) {
            return 'India Club';
        }
        if (str_contains($nameUpper, 'PALTAN BAZAR')) {
            return 'Paltan Bazar';
        }
        if (str_contains($nameUpper, 'GREENWOOD')) {
            return 'Greenwood Tea Estate';
        }
        if (str_contains($nameUpper, 'BOKEL')) {
            return 'Bokel Tea Estate';
        }
        if (str_contains($nameUpper, 'ASHOK PATH')) {
            return 'Ashok Path';
        }

        return $zoneName;
    }

    /**
     * Calculate first year of puja from milestone description.
     */
    protected function calculateFirstYearOfPuja(string $milestone, int $eventYear): ?int
    {
        $milestone = trim($milestone);
        if (empty($milestone)) {
            return null;
        }

        if (Str::contains($milestone, 'PLATINUM', true)) {
            return $eventYear - 75; // 1951
        }
        if (Str::contains($milestone, 'DIAMOND', true)) {
            return $eventYear - 60;
        }
        if (Str::contains($milestone, 'GOLDEN', true)) {
            return $eventYear - 50;
        }
        if (Str::contains($milestone, 'SILVER', true)) {
            return $eventYear - 25;
        }

        if (preg_match('/(\d+)\s*(YEARS?|YRS?)/i', $milestone, $matches)) {
            $yearsOld = (int) $matches[1];
            if ($yearsOld > 0 && $yearsOld < 300) {
                return $eventYear - $yearsOld;
            }
        }

        return null;
    }

    /**
     * Format all-caps name into beautiful Title Case.
     */
    protected function formatDisplayName(string $rawName): string
    {
        $formatted = Str::title(strtolower(trim($rawName)));

        $replacements = [
            '/\bCommit+ee?\b/i' => 'Committee',
            '/\bSamt?y\b/i' => 'Samiti',
            '/\bAmc\b/i' => 'AMC',
            '/\bKp\b/i' => 'KP',
            '/\bTe\b/i' => 'TE',
            '/\bChowkdnghee\b/i' => 'Chowkidinghee',
            '/\bSarbajann\b/i' => 'Sarbajanin',
            '/\bSarbajank\b/i' => 'Sarbajanik',
            '/\bSarbojanin\b/i' => 'Sarbajanin',
            '/\bSarvajann?in\b/i' => 'Sarbajanin',
            '/\bKalbari\b/i' => 'Kalibari',
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $formatted);
    }

    /**
     * Resolve or copy primary display image to public storage.
     */
    protected function resolvePrimaryImage(string $slug, string $rawName): string
    {
        $targetDiskPath = 'participants/primary/'.$slug.'.jpg';

        if (Storage::disk('public')->exists($targetDiskPath)) {
            return $targetDiskPath;
        }

        // Check if an existing relevant image exists in public storage
        $knownKey = null;
        $upper = strtoupper($rawName);

        if (str_contains($upper, 'GABHARUPATHER')) {
            $knownKey = 'gabharu-pather-sarbajanin-puja.jpg';
        } elseif (str_contains($upper, 'GRAHAM BAZAR')) {
            $knownKey = 'graham-bazar-sarbajanin-durga-puja-samiti.jpg';
        } elseif (str_contains($upper, 'CHOWKDNGHEE') || str_contains($upper, 'CHOWKIDINGHEE')) {
            $knownKey = 'chowkidinghee-sarbajanin-durga-puja-committee.jpg';
        } elseif (str_contains($upper, 'CHIRING CHAPORI')) {
            $knownKey = 'chiring-chapori-sarbajanin-puja.jpg';
        } elseif (str_contains($upper, 'KALBARI') || str_contains($upper, 'KALIBARI')) {
            $knownKey = 'kalibari-durga-puja-samiti.jpg';
        } elseif (str_contains($upper, 'TINKUNIA')) {
            $knownKey = 'tinkunia-sarbajanin-durga-puja.jpg';
        } elseif (str_contains($upper, 'PALTAN BAZAR')) {
            $knownKey = 'paltan-bazar-sarbajanin-durga-puja.jpg';
        } elseif (str_contains($upper, 'JALAN')) {
            $knownKey = 'jalan-nagar-sarbajanin-pratima.jpg';
        }

        if ($knownKey && Storage::disk('public')->exists('participants/primary/'.$knownKey)) {
            $content = Storage::disk('public')->get('participants/primary/'.$knownKey);
            Storage::disk('public')->put($targetDiskPath, $content);

            return $targetDiskPath;
        }

        // Fallback to sample pandal image
        $fallbackSample = public_path('event-assets/images/sample-pandal.jpg');
        if (File::exists($fallbackSample)) {
            Storage::disk('public')->put($targetDiskPath, File::get($fallbackSample));
        }

        return $targetDiskPath;
    }

    /**
     * Resolve or copy gallery images to public storage.
     */
    protected function resolveGalleryImage(string $slug, int $slot): string
    {
        $targetDiskPath = "participants/gallery/{$slug}-{$slot}.jpg";

        if (Storage::disk('public')->exists($targetDiskPath)) {
            return $targetDiskPath;
        }

        $sampleFile = $slot === 2
            ? public_path('event-assets/images/sample-lighting.jpg')
            : public_path('event-assets/images/sample-pratima.jpg');

        if (File::exists($sampleFile)) {
            Storage::disk('public')->put($targetDiskPath, File::get($sampleFile));
        }

        return $targetDiskPath;
    }
}
