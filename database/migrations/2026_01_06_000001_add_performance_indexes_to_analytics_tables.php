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
            // Composite indexes for common query patterns
            // site_id + is_bot + first_seen (used in most queries)
            $table->index(['site_id', 'is_bot', 'first_seen'], 'idx_site_bot_first_seen');
            
            // site_id + is_bot + last_seen (used for active users)
            $table->index(['site_id', 'is_bot', 'last_seen'], 'idx_site_bot_last_seen');
            
            // site_id + country + first_seen (used for country queries)
            $table->index(['site_id', 'country', 'first_seen'], 'idx_site_country_first_seen');
            
            // site_id + browser + first_seen (used for browser queries)
            $table->index(['site_id', 'browser', 'first_seen'], 'idx_site_browser_first_seen');
            
            // site_id + is_bot + device_fingerprint (used for unique visitors)
            $table->index(['site_id', 'is_bot', 'device_fingerprint'], 'idx_site_bot_fingerprint');
            
            // site_id + is_returning + first_seen (used for returning visitors)
            $table->index(['site_id', 'is_returning', 'first_seen'], 'idx_site_returning_first_seen');
            
            // site_id + referrer_source + first_seen (used for traffic sources)
            $table->index(['site_id', 'referrer_source', 'first_seen'], 'idx_site_referrer_first_seen');
            
            // Note: entry_path and exit_path are VARCHAR(2048), too long for composite indexes
            // They are indexed separately in the original migration
        });
        
        Schema::table('analytics_session_paths', function (Blueprint $table) {
            // Composite index for session_id + site_id (used in joins)
            $table->index(['session_id', 'site_id'], 'idx_session_site');
            
            // Composite index for site_id + session_id + created_at (used in time-based queries)
            $table->index(['site_id', 'session_id', 'created_at'], 'idx_site_session_created');
            
            // Composite index for site_id + created_at (used in time-based queries)
            $table->index(['site_id', 'created_at'], 'idx_site_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_site_bot_first_seen');
            $table->dropIndex('idx_site_bot_last_seen');
            $table->dropIndex('idx_site_country_first_seen');
            $table->dropIndex('idx_site_browser_first_seen');
            $table->dropIndex('idx_site_bot_fingerprint');
            $table->dropIndex('idx_site_returning_first_seen');
            $table->dropIndex('idx_site_referrer_first_seen');
        });
        
        Schema::table('analytics_session_paths', function (Blueprint $table) {
            $table->dropIndex('idx_session_site');
            $table->dropIndex('idx_site_session_created');
            $table->dropIndex('idx_site_created');
        });
    }
};

