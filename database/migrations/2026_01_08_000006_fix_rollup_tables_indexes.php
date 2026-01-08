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
     * Fixes missing unique index on analytics_daily_paths if table exists but index doesn't
     */
    public function up(): void
    {
        // Check if table exists but unique index is missing
        if (Schema::hasTable('analytics_daily_paths') && !$this->indexExists('analytics_daily_paths', 'idx_site_date_path_unique')) {
            DB::statement("
                CREATE UNIQUE INDEX idx_site_date_path_unique 
                ON analytics_daily_paths (site_id, date, path(191))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('analytics_daily_paths', 'idx_site_date_path_unique')) {
            DB::statement("DROP INDEX idx_site_date_path_unique ON analytics_daily_paths");
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

