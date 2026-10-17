<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserActivity>
 */
class UserActivityFactory extends Factory
{
    protected $model = UserActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actionTypes = [
            'search',
            'zone_filter',
            'puja_view',
            'map_directions_click',
            'banner_click',
            'video_play',
            'vote_initiated',
            'vote_completed',
        ];

        $actionType = fake()->randomElement($actionTypes);
        $metadata = [];

        if ($actionType === 'search') {
            $metadata = ['query' => fake()->randomElement(['Mitra Sangha', 'Graham Bazar', 'Station Road', 'New Market'])];
        } elseif ($actionType === 'zone_filter') {
            $metadata = ['zone' => fake()->randomElement(['Central Dibrugarh', 'South Dibrugarh', 'Heritage Old City'])];
        }

        return [
            'event_id' => Event::factory(),
            'visitor_id' => (string) Str::uuid(),
            'session_id' => Str::random(32),
            'action_type' => $actionType,
            'target_type' => in_array($actionType, ['puja_view', 'map_directions_click', 'vote_initiated']) ? 'participant' : ($actionType === 'banner_click' ? 'banner' : null),
            'target_id' => fake()->numberBetween(1, 50),
            'url' => '/event/all-pujas',
            'metadata' => $metadata,
            'device_type' => fake()->randomElement(['mobile', 'mobile', 'desktop', 'tablet']),
            'created_at' => fake()->dateTimeBetween('-14 days', 'now'),
        ];
    }
}
