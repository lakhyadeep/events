<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'analytics';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure sqlite file exists if using sqlite driver
        $connectionConfig = config('database.connections.'.$this->getConnection(), []);
        if (($connectionConfig['driver'] ?? null) === 'sqlite') {
            $dbPath = $connectionConfig['database'] ?? null;
            if ($dbPath && $dbPath !== ':memory:' && ! file_exists($dbPath)) {
                touch($dbPath);
            }
        }

        Schema::connection($this->getConnection())->dropIfExists('user_activities');

        Schema::connection($this->getConnection())->create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->string('visitor_id', 64)->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('action_type', 50)->index();
            $table->string('target_type', 50)->nullable();
            $table->unsignedBigInteger('target_id')->nullable()->index();
            $table->string('url', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->timestamps();

            $table->index(['event_id', 'action_type', 'created_at']);
            $table->index(['action_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnection())->dropIfExists('user_activities');
    }
};
