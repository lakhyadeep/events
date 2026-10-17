<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\PageView;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PageView>
 */
class PageViewFactory extends Factory
{
    protected $model = PageView::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $devices = ['mobile', 'mobile', 'mobile', 'desktop', 'tablet'];
        $browsers = ['Chrome', 'Safari', 'Firefox', 'Edge'];
        $platforms = ['Android', 'iOS', 'Windows', 'macOS'];
        $pages = ['landing', 'all_pujas', 'vote_screen', 'vote_success'];
        $referrers = ['whatsapp.com', 'l.instagram.com', 'facebook.com', 'google.com', null];

        $pageType = fake()->randomElement($pages);
        $referrerHost = fake()->randomElement($referrers);

        return [
            'event_id' => Event::factory(),
            'visitor_id' => (string) Str::uuid(),
            'session_id' => Str::random(32),
            'page_type' => $pageType,
            'url' => '/event/'.$pageType,
            'route_name' => 'events.'.$pageType,
            'referrer_host' => $referrerHost,
            'referrer' => $referrerHost ? 'https://'.$referrerHost.'/link' : null,
            'device_type' => fake()->randomElement($devices),
            'browser' => fake()->randomElement($browsers),
            'platform' => fake()->randomElement($platforms),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => fake()->dateTimeBetween('-14 days', 'now'),
        ];
    }
}
