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
            // site_id + is_bounce + first_seen (used for bounce rate calculations)
            $table->index(['site_id', 'is_bounce', 'first_seen'], 'idx_site_bounce_first_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_site_bounce_first_seen');
        });
    }
};
