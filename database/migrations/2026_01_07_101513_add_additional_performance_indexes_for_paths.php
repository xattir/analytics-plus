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
        // Covering index for getTopPages queries
        // Note: path is VARCHAR(2048), so we need to use prefix index
        $connection = Schema::getConnection();
        
        if (!$this->indexExists('analytics_session_paths', 'idx_site_session_path')) {
            $connection->statement('CREATE INDEX idx_site_session_path ON analytics_session_paths (site_id, session_id, path(191))');
        }
        
        // Note: entry_path and exit_path are VARCHAR(2048) which is too long for full index
        // MySQL has a limit of 767 bytes for index key length
        // We'll use raw SQL to create prefix indexes
        $connection = Schema::getConnection();
        
        if (!$this->indexExists('analytics_sessions', 'idx_entry_path')) {
            $connection->statement('CREATE INDEX idx_entry_path ON analytics_sessions (entry_path(191))');
        }
        
        if (!$this->indexExists('analytics_sessions', 'idx_exit_path')) {
            $connection->statement('CREATE INDEX idx_exit_path ON analytics_sessions (exit_path(191))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();
        
        if ($this->indexExists('analytics_session_paths', 'idx_site_session_path')) {
            $connection->statement('DROP INDEX idx_site_session_path ON analytics_session_paths');
        }
        
        $connection = Schema::getConnection();
        
        if ($this->indexExists('analytics_sessions', 'idx_entry_path')) {
            $connection->statement('DROP INDEX idx_entry_path ON analytics_sessions');
        }
        
        if ($this->indexExists('analytics_sessions', 'idx_exit_path')) {
            $connection->statement('DROP INDEX idx_exit_path ON analytics_sessions');
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
