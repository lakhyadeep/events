<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Sarbajanin Durga Puja';
        $zoneName = 'Central Dibrugarh';

        return [
            'event_id' => Event::factory(),
            'zone_id' => Zone::factory(),
            'locality_id' => null,
            'mobile_number' => '98'.fake()->numerify('########'),
            'name' => $name,
            'display_name' => $name,
            'short_introduction' => fake()->paragraph(),
            'zone' => $zoneName,
            'locality' => 'Thana Chariali',
            'address' => fake()->address(),
            'landmark' => 'Near Clock Tower',
            'key_contact_1_name' => fake()->name(),
            'key_contact_1_phone' => '98'.fake()->numerify('########'),
            'primary_display_image' => tap('participants/primary/test-sample.jpg', function ($path) {
                if (! Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->put($path, 'fake-image-data');
                }
            }),
            'year' => 2026,
            'registration_status' => RegistrationStatus::Approved,
            'is_shortlisted' => true,
            'is_puja_contest' => true,
            'first_year_of_puja' => 1985,
            'puja_theme' => 'Heritage & Culture',
            'google_map_url' => 'https://maps.google.com/?q=27.4728,94.9120',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'registration_status' => RegistrationStatus::Approved,
        ]);
    }
}
