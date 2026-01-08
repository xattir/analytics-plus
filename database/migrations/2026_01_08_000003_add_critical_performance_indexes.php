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
     * Adds critical indexes for all slow query patterns:
     * - Core range scan indexes (site_id + time columns)
     * - Covering indexes for JOINs and DISTINCT queries
     * - Segment indexes (country, browser, etc.)
     */
    public function up(): void
    {
        $connection = Schema::getConnection();
        
        // ============================================
        // ANALYTICS_SESSIONS INDEXES
        // ============================================
        
        // A1: Core index for first_seen range queries (most common pattern)
        if (!$this->indexExists('analytics_sessions', 'idx_site_first_seen_core')) {
            $connection->statement("
                CREATE INDEX idx_site_first_seen_core 
                ON analytics_sessions (site_id, first_seen)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A2: Core index for last_seen range queries (exit pages, active users)
        if (!$this->indexExists('analytics_sessions', 'idx_site_last_seen_core')) {
            $connection->statement("
                CREATE INDEX idx_site_last_seen_core 
                ON analytics_sessions (site_id, last_seen)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A3: Index for first_seen + is_bot (most common filter combination)
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_core')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_first_seen_core 
                ON analytics_sessions (site_id, is_bot, first_seen)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A4: Index for last_seen + is_bot
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_last_seen_core')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_last_seen_core 
                ON analytics_sessions (site_id, is_bot, last_seen)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A5: Covering index for JOIN queries that need session_id
        // Used in getTopPages: JOIN analytics_sessions ON session_id WHERE site_id + first_seen + is_bot
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_session')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_first_seen_session 
                ON analytics_sessions (site_id, is_bot, first_seen, session_id)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A6: Covering index for COUNT(DISTINCT device_fingerprint) with is_bot filter
        // Query 5: COUNT(DISTINCT device_fingerprint) WHERE site_id + first_seen_date + is_bot
        if (!$this->indexExists('analytics_sessions', 'idx_site_date_bot_fingerprint')) {
            $connection->statement("
                CREATE INDEX idx_site_date_bot_fingerprint 
                ON analytics_sessions (site_id, first_seen_date, is_bot, device_fingerprint)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A7: Index for country breakdown (Query 6)
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_country')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_first_seen_country 
                ON analytics_sessions (site_id, is_bot, first_seen, country)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A8: Index for browser breakdown
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_browser')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_first_seen_browser 
                ON analytics_sessions (site_id, is_bot, first_seen, browser)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A9: Index for device_type breakdown
        if (!$this->indexExists('analytics_sessions', 'idx_site_first_seen_device')) {
            $connection->statement("
                CREATE INDEX idx_site_first_seen_device 
                ON analytics_sessions (site_id, first_seen, device_type)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A10: Index for OS breakdown
        if (!$this->indexExists('analytics_sessions', 'idx_site_first_seen_os')) {
            $connection->statement("
                CREATE INDEX idx_site_first_seen_os 
                ON analytics_sessions (site_id, first_seen, os)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A11: Index for entry_path breakdown
        if (!$this->indexExists('analytics_sessions', 'idx_site_first_seen_entry')) {
            $connection->statement("
                CREATE INDEX idx_site_first_seen_entry 
                ON analytics_sessions (site_id, first_seen, entry_path(191))
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A12: Index for exit_path breakdown (Query 3)
        if (!$this->indexExists('analytics_sessions', 'idx_site_last_seen_exit')) {
            $connection->statement("
                CREATE INDEX idx_site_last_seen_exit 
                ON analytics_sessions (site_id, last_seen, exit_path(191))
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // A13: Index for referrer_source breakdown
        if (!$this->indexExists('analytics_sessions', 'idx_site_bot_first_seen_referrer')) {
            $connection->statement("
                CREATE INDEX idx_site_bot_first_seen_referrer 
                ON analytics_sessions (site_id, is_bot, first_seen, referrer_source)
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // ============================================
        // ANALYTICS_SESSION_PATHS INDEXES
        // ============================================
        
        // B1: Critical index for JOIN queries (Query 1)
        // Used in: JOIN analytics_session_paths ON session_id WHERE site_id
        if (!$this->indexExists('analytics_session_paths', 'idx_site_session_path')) {
            $connection->statement("
                CREATE INDEX idx_site_session_path 
                ON analytics_session_paths (site_id, session_id, path(191))
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
        
        // B2: Index for path-only queries (if needed separately)
        if (!$this->indexExists('analytics_session_paths', 'idx_site_path')) {
            $connection->statement("
                CREATE INDEX idx_site_path 
                ON analytics_session_paths (site_id, path(191))
                ALGORITHM=INPLACE
                LOCK=NONE
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();
        
        // Drop analytics_sessions indexes
        $sessionIndexes = [
            'idx_site_first_seen_core',
            'idx_site_last_seen_core',
            'idx_site_bot_first_seen_core',
            'idx_site_bot_last_seen_core',
            'idx_site_bot_first_seen_session',
            'idx_site_date_bot_fingerprint',
            'idx_site_bot_first_seen_country',
            'idx_site_bot_first_seen_browser',
            'idx_site_first_seen_device',
            'idx_site_first_seen_os',
            'idx_site_first_seen_entry',
            'idx_site_last_seen_exit',
            'idx_site_bot_first_seen_referrer',
        ];
        
        foreach ($sessionIndexes as $index) {
            if ($this->indexExists('analytics_sessions', $index)) {
                $connection->statement("DROP INDEX {$index} ON analytics_sessions");
            }
        }
        
        // Drop analytics_session_paths indexes
        $pathIndexes = [
            'idx_site_session_path',
            'idx_site_path',
        ];
        
        foreach ($pathIndexes as $index) {
            if ($this->indexExists('analytics_session_paths', $index)) {
                $connection->statement("DROP INDEX {$index} ON analytics_session_paths");
            }
        }
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

