<?php

namespace App\Services;

use App\Models\Award;
use App\Models\Event;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\Winner;
use App\Models\Zone;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParticipantCsvSyncService
{
    /**
     * Resolve the CSV file path from given custom path or default project locations.
     */
    public function resolveDefaultCsvPath(?string $customPath = null): ?string
    {
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $candidates = [
            database_path('seeders/data/all-puja.csv'),
            storage_path('all-puja.csv'),
            storage_path('app/public/all-puja.csv'),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Synchronize participants and localities from a CSV file non-destructively.
     *
     * @return array{created: int, updated: int, total: int, message: string}
     */
    public function syncFromCsv(Event $event, string $csvPath): array
    {
        if (! file_exists($csvPath)) {
            return [
                'created' => 0,
                'updated' => 0,
                'total' => 0,
                'message' => "CSV file not found at: {$csvPath}",
            ];
        }

        Storage::disk('public')->makeDirectory('participants/primary');
        Storage::disk('public')->makeDirectory('participants/gallery');

        // Preload zones and localities caches
        $zonesCache = Zone::all()->keyBy('name')->all();
        $localitiesCache = Locality::all()->mapWithKeys(fn ($loc) => [$loc->zone_id.'::'.$loc->name => $loc])->all();

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return [
                'created' => 0,
                'updated' => 0,
                'total' => 0,
                'message' => "Unable to open CSV file at: {$csvPath}",
            ];
        }

        // Dynamically find the header row, skipping blank lines or title rows
        $header = null;
        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isHeaderRow($row)) {
                $header = array_map(fn ($col) => trim((string) $col), $row);
                break;
            }
        }

        if (! $header) {
            fclose($handle);

            return [
                'created' => 0,
                'updated' => 0,
                'total' => 0,
                'message' => 'No valid header row found in the CSV. Expected headers like "PUJA COMMITTEE NAME", "AREA", "PHONE NUMBER", etc.',
            ];
        }

        $colIndex = [
            'reg_no' => $this->findColumnIndex($header, ['REGD', 'REG NO', 'REG #', 'PUJA #', 'SL NO', 'SL', 'REG']),
            'name' => $this->findColumnIndex($header, [
                'PUJA COMMITTEE NAME', 'PUJA COMMITEE NAME',
                'NAME OF THE PUJA COMMITTEES', 'NAME OF THE PUJA COMMITTEE', 'NAME OF THE PUJA COMMITEE',
                'COMMITTEE NAME', 'COMMITEE NAME', 'PUJA NAME',
                'PUJA COMMITTEE', 'PUJA COMMITEE',
                'COMMITTEE', 'COMMITEE', 'NAME',
            ], [
                'PRESIDENT', 'SECRETARY', 'CONTACT', 'PERSON',
            ]),
            'area' => $this->findColumnIndex($header, ['AMC AREA / POLICE OUTPOST / AREA', 'AMC AREA', 'AREA', 'ZONE', 'POLICE OUTPOST'], ['MAP']),
            'phone' => $this->findColumnIndex($header, ['PHONE NUMBER', 'PHONE', 'MOBILE NUMBER', 'MOBILE', 'CONTACT NO', 'MOBILE NO']),
            'contact' => $this->findColumnIndex($header, [
                'PRESIDENT/SECRETARY NAME', 'PRESIDENT / SECRETARY', 'PRESIDENT', 'SECRETARY',
                'NAME OF THE CONTACT PERSON', 'CONTACT PERSON', 'CONTACT NAME', 'CONTACT', 'REPRESENTATIVE',
            ], [
                'PUJA', 'COMMITTEE', 'COMMITEE',
            ]),
            'theme' => $this->findColumnIndex($header, ['PUJA THEME', 'THEME']),
            'milestone' => $this->findColumnIndex($header, ['MILESTONE YEAR', 'MILESTONE', 'YEARS', 'YEAR OF ESTABLISHMENT']),
            'map' => $this->findColumnIndex($header, ['GOOGLE MAP', 'MAP LOCATION', 'MAP URL', 'COORDINATES', 'MAP', 'LOCATION']),
        ];

        if ($colIndex['name'] === null) {
            fclose($handle);

            return [
                'created' => 0,
                'updated' => 0,
                'total' => 0,
                'message' => 'Could not find the Puja Committee Name column in the CSV file.',
            ];
        }

        $createdCount = 0;
        $updatedCount = 0;
        $createdParticipants = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                continue;
            }

            $rawName = $colIndex['name'] !== null ? trim((string) ($row[$colIndex['name']] ?? '')) : '';
            if (empty($rawName) || Str::contains($rawName, 'TOTAL', true) || (Str::contains($rawName, 'COMMITTEE', true) && strlen($rawName) < 15)) {
                continue;
            }

            if (strtoupper($rawName) === 'MADHYA CHJOW') {
                continue;
            }

            $regNo = $colIndex['reg_no'] !== null ? (int) ($row[$colIndex['reg_no']] ?? 0) : 0;
            $rawArea = $colIndex['area'] !== null ? ($row[$colIndex['area']] ?? '') : '';
            $rawPhone = $colIndex['phone'] !== null ? ($row[$colIndex['phone']] ?? '') : '';
            $rawContact = $colIndex['contact'] !== null ? ($row[$colIndex['contact']] ?? '') : '';
            $rawTheme = $colIndex['theme'] !== null ? ($row[$colIndex['theme']] ?? '') : '';
            $rawMilestone = $colIndex['milestone'] !== null ? ($row[$colIndex['milestone']] ?? '') : '';
            $rawMap = $colIndex['map'] !== null ? ($row[$colIndex['map']] ?? '') : '';

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

            // Normalize Locality
            $localityName = $this->deriveLocalityName($rawName, $zoneName);
            $localityCacheKey = $zone->id.'::'.$localityName;

            if (! isset($localitiesCache[$localityCacheKey])) {
                $existingLoc = Locality::where('zone_id', $zone->id)->where('name', $localityName)->first();
                if (! $existingLoc) {
                    $existingLoc = Locality::create([
                        'zone_id' => $zone->id,
                        'name' => $localityName,
                        'slug' => Str::slug($localityName),
                        'sort_order' => 10,
                        'is_active' => true,
                    ]);
                }
                $localitiesCache[$localityCacheKey] = $existingLoc;
            }
            $locality = $localitiesCache[$localityCacheKey];

            // Normalize Phone
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

            $firstYear = $this->calculateFirstYearOfPuja($rawMilestone, $event->year ?? 2026);
            $displayName = $this->formatDisplayName($rawName);

            $slug = Str::slug($rawName);
            $primaryImage = $this->resolvePrimaryImage($slug, $rawName);
            $galleryImage1 = $this->resolveGalleryImage($slug, 1);
            $galleryImage2 = $this->resolveGalleryImage($slug, 2);

            $participantData = [
                'event_id' => $event->id,
                'zone_id' => $zone->id,
                'locality_id' => $locality->id,
                'mobile_number' => $cleanPhone,
                'name' => $rawName,
                'display_name' => $displayName,
                'short_introduction' => ! empty($rawMilestone)
                    ? "Celebrating {$rawMilestone} of traditional Sarbajanin Durga Puja festivities in {$localityName}, Dibrugarh."
                    : "Prestigious Sarbajanin Durga Puja celebration organized in {$localityName}, Dibrugarh.",
                'zone' => $zoneName,
                'locality' => $localityName,
                'address' => "{$localityName}, {$zoneName}, Dibrugarh, Assam",
                'landmark' => $localityName,
                'key_contact_1_name' => ! empty($cleanContact) ? $cleanContact : 'General Secretary',
                'key_contact_1_phone' => $cleanPhone,
                'year' => $event->year ?? 2026,
                'registration_status' => 'approved',
                'is_shortlisted' => true,
                'is_puja_contest' => true,
                'first_year_of_puja' => $firstYear,
                'puja_theme' => ! empty($rawTheme) ? $rawTheme : null,
                'google_map_url' => $mapUrl,
            ];

            $existing = Participant::where('event_id', $event->id)
                ->where(function ($query) use ($rawName, $displayName, $cleanPhone, $rawPhone) {
                    $query->where('name', $rawName)
                        ->orWhere('display_name', $displayName);

                    if (! empty($rawPhone) && strlen(preg_replace('/[^\d]/', '', $rawPhone)) >= 10) {
                        $query->orWhere('mobile_number', $cleanPhone);
                    }
                })
                ->first();

            if ($existing) {
                // Non-destructive update: preserve custom UI-uploaded images
                if (empty($existing->primary_display_image) || $existing->primary_display_image === 'events/participants/sample.jpg') {
                    $participantData['primary_display_image'] = $primaryImage;
                    $participantData['image_1'] = $galleryImage1;
                    $participantData['image_2'] = $galleryImage2;
                }

                $existing->update($participantData);
                $updatedCount++;
                $createdParticipants[] = $existing;
            } else {
                $participantData['primary_display_image'] = $primaryImage;
                $participantData['image_1'] = $galleryImage1;
                $participantData['image_2'] = $galleryImage2;

                $created = Participant::create($participantData);
                $createdCount++;
                $createdParticipants[] = $created;
            }
        }

        fclose($handle);

        // Optionally link sample winners if zero winners exist
        if (Winner::where('event_id', $event->id)->count() === 0 && ! empty($createdParticipants)) {
            $awards = Award::where('event_id', $event->id)->get();
            if ($awards->isNotEmpty()) {
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
            }
        }

        return [
            'created' => $createdCount,
            'updated' => $updatedCount,
            'total' => $createdCount + $updatedCount,
            'message' => "Successfully processed {$createdCount} new and {$updatedCount} updated puja committees.",
        ];
    }

    public function findColumnIndex(array $header, array $potentialNames, array $excludeNames = []): ?int
    {
        foreach ($header as $index => $colName) {
            $clean = strtoupper(trim((string) $colName));
            if ($clean === '') {
                continue;
            }

            $isExcluded = false;
            foreach ($excludeNames as $ex) {
                if (str_contains($clean, strtoupper($ex))) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) {
                continue;
            }

            foreach ($potentialNames as $name) {
                if (str_contains($clean, strtoupper($name))) {
                    return $index;
                }
            }
        }

        return null;
    }

    public function isHeaderRow(array $row): bool
    {
        $nonEmpty = array_filter(array_map(fn ($col) => strtoupper(trim((string) $col)), $row));
        if (empty($nonEmpty)) {
            return false;
        }

        $headerIndicators = [
            'PUJA', 'COMMITTEE', 'COMMITEE', 'SAMITI', 'SAMITY',
            'REG', 'REGD', 'SL NO', 'AREA', 'PHONE', 'MOBILE',
            'CONTACT', 'PRESIDENT', 'SECRETARY', 'THEME', 'MILESTONE', 'MAP', 'LOCATION',
        ];

        $matches = 0;
        foreach ($nonEmpty as $cell) {
            foreach ($headerIndicators as $indicator) {
                if (str_contains($cell, $indicator)) {
                    $matches++;
                    break;
                }
            }
        }

        return $matches >= 2;
    }

    public function normalizeZoneName(string $rawArea): string
    {
        $areaUpper = strtoupper($rawArea);

        if (str_contains($areaUpper, 'AMC')) {
            return 'Amc Area';
        }
        if (str_contains($areaUpper, 'CENTRAL') || str_contains($areaUpper, 'CHOWKIDINGHEE')) {
            return 'Central Dibrugarh';
        }
        if (str_contains($areaUpper, 'MARKET') || str_contains($areaUpper, 'MARWARI') || str_contains($areaUpper, 'NEW MARKET')) {
            return 'Market Area';
        }

        return 'Others';
    }

    public function deriveLocalityName(string $pujaName, string $zoneName): string
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
        if (str_contains($nameUpper, 'CHIRING CHAPORI')) {
            return 'Chiring Chapori';
        }
        if (str_contains($nameUpper, 'N財LIAPOOL') || str_contains($nameUpper, 'NALIAPOOL')) {
            return 'Naliapool';
        }
        if (str_contains($nameUpper, 'KHALIHAMARI')) {
            return 'Khalihamari';
        }
        if (str_contains($nameUpper, 'BOIRAGIMOTH')) {
            return 'Boiragimoth';
        }
        if (str_contains($nameUpper, 'SANTIPARA') || str_contains($nameUpper, 'SHANTIPARA')) {
            return 'Shantipara';
        }
        if (str_contains($nameUpper, 'AMOLAPATTY')) {
            return 'Amolapatty';
        }
        if (str_contains($nameUpper, 'NEW MARKET')) {
            return 'New Market';
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
        if (str_contains($nameUpper, 'GANGAPARA')) {
            return 'Gangapara';
        }
        if (str_contains($nameUpper, 'MASTERPARA')) {
            return 'Masterpara';
        }
        if (str_contains($nameUpper, 'SUBHASHPALLY')) {
            return 'Subhashpally';
        }

        return $zoneName;
    }

    public function calculateFirstYearOfPuja(string $milestone, int $eventYear): ?int
    {
        $milestone = trim($milestone);
        if (empty($milestone)) {
            return null;
        }

        if (Str::contains($milestone, 'PLATINUM', true)) {
            return $eventYear - 75;
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

    public function formatDisplayName(string $rawName): string
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

    public function resolvePrimaryImage(string $slug, string $rawName): string
    {
        $targetDiskPath = 'participants/primary/'.$slug.'.jpg';

        if (Storage::disk('public')->exists($targetDiskPath)) {
            return $targetDiskPath;
        }

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
        }

        if ($knownKey && Storage::disk('public')->exists('participants/primary/'.$knownKey)) {
            return 'participants/primary/'.$knownKey;
        }

        return 'participants/primary/thana-chariali-sarbajanin.jpg';
    }

    public function resolveGalleryImage(string $slug, int $index): ?string
    {
        $galleryPath = "participants/gallery/{$slug}-{$index}.jpg";

        if (Storage::disk('public')->exists($galleryPath)) {
            return $galleryPath;
        }

        return null;
    }
}
