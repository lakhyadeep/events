<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Fixed Admin User Credentials
        User::updateOrCreate(
            ['email' => 'admin@dib24x7.com'],
            [
                'name' => 'Dib 24x7 Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Event, Sponsors, Banners, Participants, Awards & Milestones
        $this->call([
            DemoEventSeeder::class,
        ]);
    }
}
