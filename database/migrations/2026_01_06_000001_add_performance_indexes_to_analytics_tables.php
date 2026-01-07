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
        Schema::table('analytics_sessions', function (Blueprint $table) {
            // Composite indexes for common query patterns
            // Check if index exists before adding to avoid duplicate key errors
            $indexes = [
                ['columns' => ['site_id', 'is_bot', 'first_seen'], 'name' => 'idx_site_bot_first_seen'],
                ['columns' => ['site_id', 'is_bot', 'last_seen'], 'name' => 'idx_site_bot_last_seen'],
                ['columns' => ['site_id', 'country', 'first_seen'], 'name' => 'idx_site_country_first_seen'],
                ['columns' => ['site_id', 'browser', 'first_seen'], 'name' => 'idx_site_browser_first_seen'],
                ['columns' => ['site_id', 'is_bot', 'device_fingerprint'], 'name' => 'idx_site_bot_fingerprint'],
                ['columns' => ['site_id', 'is_returning', 'first_seen'], 'name' => 'idx_site_returning_first_seen'],
                ['columns' => ['site_id', 'referrer_source', 'first_seen'], 'name' => 'idx_site_referrer_first_seen'],
            ];
            
            foreach ($indexes as $index) {
                if (!$this->indexExists('analytics_sessions', $index['name'])) {
                    $table->index($index['columns'], $index['name']);
                }
            }
            
            // Note: entry_path and exit_path are VARCHAR(2048), too long for composite indexes
            // They are indexed separately in the original migration
        });
        
        Schema::table('analytics_session_paths', function (Blueprint $table) {
            $indexes = [
                ['columns' => ['session_id', 'site_id'], 'name' => 'idx_session_site'],
                ['columns' => ['site_id', 'session_id', 'created_at'], 'name' => 'idx_site_session_created'],
                ['columns' => ['site_id', 'created_at'], 'name' => 'idx_site_created'],
            ];
            
            foreach ($indexes as $index) {
                if (!$this->indexExists('analytics_session_paths', $index['name'])) {
                    $table->index($index['columns'], $index['name']);
                }
            }
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

