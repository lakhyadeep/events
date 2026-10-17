<?php

namespace Database\Seeders;

use App\Models\DatabaseBackup;
use Illuminate\Database\Seeder;

class DatabaseBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Keep empty for clean environments, or seed if explicitly testing.
        if (app()->environment('local') && DatabaseBackup::count() === 0) {
            DatabaseBackup::factory()->count(2)->create();
        }
    }
}
