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
     * First cleans up duplicate entries by aggregating them
     */
    public function up(): void
    {
        // Check if table exists but unique index is missing
        if (Schema::hasTable('analytics_daily_paths') && !$this->indexExists('analytics_daily_paths', 'idx_site_date_path_unique')) {
            // First, clean up duplicate entries by aggregating views
            // This handles cases where the same path appears multiple times for the same site/date
            // due to the prefix index limitation (path(191))
            $this->info('Cleaning up duplicate entries in analytics_daily_paths...');
            
            // Find and merge duplicates
            // Group by site_id, date, and path prefix (first 191 chars)
            // Sum the views and keep one record
            DB::statement("
                CREATE TEMPORARY TABLE temp_daily_paths_cleanup AS
                SELECT 
                    site_id,
                    date,
                    SUBSTRING(path, 1, 191) as path_prefix,
                    SUM(views) as total_views,
                    MIN(id) as keep_id,
                    MAX(path) as full_path
                FROM analytics_daily_paths
                GROUP BY site_id, date, path_prefix
                HAVING COUNT(*) > 1
            ");
            
            // Delete duplicates (keep the one with minimum id)
            DB::statement("
                DELETE p1 FROM analytics_daily_paths p1
                INNER JOIN temp_daily_paths_cleanup t
                ON p1.site_id = t.site_id
                AND p1.date = t.date
                AND SUBSTRING(p1.path, 1, 191) = t.path_prefix
                AND p1.id != t.keep_id
            ");
            
            // Update the kept record with aggregated views and full path
            DB::statement("
                UPDATE analytics_daily_paths p
                INNER JOIN temp_daily_paths_cleanup t
                ON p.id = t.keep_id
                SET p.views = t.total_views,
                    p.path = t.full_path
            ");
            
            // Drop temp table
            DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_daily_paths_cleanup");
            
            // Now create the unique index
            DB::statement("
                CREATE UNIQUE INDEX idx_site_date_path_unique 
                ON analytics_daily_paths (site_id, date, path(191))
            ");
        }
    }
    
    /**
     * Helper method to output info messages
     */
    private function info($message)
    {
        if (method_exists($this, 'command') && $this->command) {
            $this->command->info($message);
        } else {
            echo $message . "\n";
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

