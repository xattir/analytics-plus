<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds:
     * 1. Generated column first_seen_date for efficient GROUP BY DATE queries
     * 2. Quality flags (is_high_quality, is_low_quality) precomputed at insert time
     * 3. Supporting indexes for optimal query performance
     */
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            // Add generated date column for efficient GROUP BY operations
            // This allows MySQL to use indexes for date-based grouping
            $table->date('first_seen_date')
                ->nullable()
                ->after('first_seen')
                ->storedAs('DATE(first_seen)')
                ->comment('Generated column for DATE(first_seen) to enable index usage in GROUP BY');
            
            // Add quality flags precomputed at insert time
            // This eliminates expensive CASE expressions in aggregate queries
            $table->boolean('is_high_quality')
                ->default(false)
                ->after('is_bot')
                ->comment('Precomputed: is_bot=0 AND pages_count>1 AND duration_ms>30000 AND max_scroll_percent>50');
            
            $table->boolean('is_low_quality')
                ->default(false)
                ->after('is_high_quality')
                ->comment('Precomputed: is_bot=0 AND (pages_count=1 OR duration_ms<5000 OR max_scroll_percent<10)');
        });
        
        // Add indexes using raw SQL for better control
        $connection = Schema::getConnection();
        
        // Critical: Index for date range queries with filters
        // This is the PRIMARY index for most dashboard queries
        if (!$this->indexExists('analytics_sessions', 'idx_site_first_seen_date')) {
            $connection->statement('
                CREATE INDEX idx_site_first_seen_date 
                ON analytics_sessions (site_id, first_seen_date)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
        
        // Critical: Index for date range queries with is_bot filter (most common)
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_date')) {
            $connection->statement('
                CREATE INDEX idx_site_bot_first_seen_date 
                ON analytics_sessions (site_id, is_bot, first_seen_date)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
        
        // Critical: Covering index for COUNT(DISTINCT device_fingerprint) queries
        // This allows index-only scans without touching table data
        if (!$this->indexExists('analytics_sessions', 'idx_site_date_fingerprint_covering')) {
            $connection->statement('
                CREATE INDEX idx_site_date_fingerprint_covering 
                ON analytics_sessions (site_id, first_seen_date, device_fingerprint)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
        
        // Critical: Index for quality-based aggregations
        // Supports fast SUM(is_high_quality=1) and SUM(is_low_quality=1) queries
        if (!$this->indexExists('analytics_sessions', 'idx_site_date_quality')) {
            $connection->statement('
                CREATE INDEX idx_site_date_quality 
                ON analytics_sessions (site_id, first_seen_date, is_bot, is_high_quality, is_low_quality)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
        
        // Critical: Index for session_id lookups with date range
        // Used in getTopPages and similar queries that need session_ids first
        if (!$this->indexExists('analytics_sessions', 'idx_site_date_session_covering')) {
            $connection->statement('
                CREATE INDEX idx_site_date_session_covering 
                ON analytics_sessions (site_id, first_seen_date, session_id)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
        
        // Index for last_seen queries (active users, real-time)
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_last_seen_date')) {
            $connection->statement('
                CREATE INDEX idx_site_bot_last_seen_date 
                ON analytics_sessions (site_id, is_bot, last_seen)
                ALGORITHM=INPLACE, LOCK=NONE
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();
        
        // Drop indexes
        $indexes = [
            'idx_site_first_seen_date',
            'idx_site_bot_first_seen_date',
            'idx_site_date_fingerprint_covering',
            'idx_site_date_quality',
            'idx_site_date_session_covering',
            'idx_site_bot_last_seen_date',
        ];
        
        foreach ($indexes as $index) {
            if ($this->indexExists('analytics_sessions', $index)) {
                $connection->statement("DROP INDEX {$index} ON analytics_sessions");
            }
        }
        
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropColumn(['first_seen_date', 'is_high_quality', 'is_low_quality']);
        });
    }
    
    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->selectOne(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result->count > 0;
    }
};

