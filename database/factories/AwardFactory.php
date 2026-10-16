<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Award>
 */
class AwardFactory extends Factory
{
    protected $model = Award::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true).' Award';

        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'display_name' => $name,
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Pandal', 'Idol', 'Lighting', 'Theme']),
            'prize_money_or_award' => '₹50,000 Trophy',
            'year' => 2026,
            'sort_order' => 1,
            'is_active' => true,
            'is_voting_allowed' => true,
        ];
    }
}
