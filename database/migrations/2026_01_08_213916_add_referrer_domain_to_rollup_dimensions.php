<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'referrer_domain' to dimension_type enum
        DB::statement("ALTER TABLE analytics_daily_dimensions MODIFY COLUMN dimension_type ENUM('country', 'browser', 'os', 'device_type', 'entry_path', 'exit_path', 'referrer_source', 'referrer_domain')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'referrer_domain' from dimension_type enum (revert to original)
        DB::statement("ALTER TABLE analytics_daily_dimensions MODIFY COLUMN dimension_type ENUM('country', 'browser', 'os', 'device_type', 'entry_path', 'exit_path', 'referrer_source')");
    }
};
