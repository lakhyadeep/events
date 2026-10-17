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

        Schema::connection($this->getConnection())->dropIfExists('page_views');

        Schema::connection($this->getConnection())->create('page_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->string('visitor_id', 64)->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('page_type', 32)->default('other')->index();
            $table->string('url', 500);
            $table->string('route_name', 100)->nullable();
            $table->string('referrer_host', 150)->nullable()->index();
            $table->text('referrer')->nullable();
            $table->string('device_type', 20)->default('desktop')->index();
            $table->string('browser', 50)->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'created_at']);
            $table->index(['page_type', 'created_at']);
            $table->index(['created_at', 'device_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnection())->dropIfExists('page_views');
    }
};
