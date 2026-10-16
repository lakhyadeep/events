<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Enums\VotingMode;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Event '.fake()->year().' '.Str::random(5);

        return [
            'name' => $name,
            'display_name' => $name,
            'year' => 2026,
            'slug' => Str::slug($name),
            'is_registration_active' => true,
            'is_voting_active' => true,
            'voting_mode' => VotingMode::ParticipantAndAward,
            'is_awards_active' => true,
            'is_timeline_active' => true,
            'tagline' => fake()->sentence(),
            'about_text' => fake()->paragraph(),
            'status' => EventStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => EventStatus::Active,
            'is_voting_active' => true,
        ]);
    }
}
