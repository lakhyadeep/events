<?php

namespace Database\Factories;

use App\Models\DatabaseBackup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DatabaseBackup>
 */
class DatabaseBackupFactory extends Factory
{
    protected $model = DatabaseBackup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timestamp = fake()->dateTimeBetween('-10 days', 'now')->format('Y-m-d_His');
        $filename = "events-backup-{$timestamp}-".Str::random(4).'.sqlite';

        return [
            'filename' => $filename,
            'disk' => 'local',
            'path' => "backups/{$filename}",
            'size_bytes' => fake()->numberBetween(100000, 5000000),
            'status' => 'completed',
            'trigger' => fake()->randomElement(['scheduled', 'manual', 'artisan']),
            'checksum' => hash('sha256', Str::random(32)),
            'error_message' => null,
            'completed_at' => now(),
        ];
    }

    /**
     * Indicate that the backup is pending execution.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'size_bytes' => 0,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the backup has failed.
     */
    public function failed(?string $error = 'Simulated backup failure'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => $error,
            'completed_at' => now(),
        ]);
    }
}
