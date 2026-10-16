<?php

namespace Database\Factories;

use App\Enums\WinnerRank;
use App\Models\Award;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Winner>
 */
class WinnerFactory extends Factory
{
    protected $model = Winner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'award_id' => Award::factory(),
            'participant_id' => Participant::factory(),
            'year' => 2026,
            'rank_order' => WinnerRank::First,
            'status' => 'published',
        ];
    }
}
