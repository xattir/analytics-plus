<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            // Covering index for COUNT(DISTINCT device_fingerprint) queries
            // This index covers site_id, first_seen, and device_fingerprint
            // allowing MySQL to read from index only without accessing the table
            $table->index(['site_id', 'first_seen', 'device_fingerprint'], 'idx_site_first_seen_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_site_first_seen_fingerprint');
        });
    }
};
