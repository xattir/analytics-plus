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
        // Check for and clean up any duplicate entries that might cause issues
        // This can happen if there were concurrent inserts or race conditions
        $hasDuplicates = DB::selectOne("
            SELECT COUNT(*) as cnt
            FROM (
                SELECT site_id, date, dimension_type, dimension_value
                FROM analytics_daily_dimensions
                GROUP BY site_id, date, dimension_type, dimension_value
                HAVING COUNT(*) > 1
            ) as dupes
        ")->cnt > 0;
        
        if ($hasDuplicates) {
            // Use a temporary table to aggregate duplicates efficiently
            DB::statement("
                CREATE TEMPORARY TABLE temp_daily_dimensions_aggregated AS
                SELECT 
                    site_id,
                    date,
                    dimension_type,
                    dimension_value,
                    SUM(count) as total_count
                FROM analytics_daily_dimensions
                GROUP BY site_id, date, dimension_type, dimension_value
            ");
            
            // Delete all existing data
            DB::table('analytics_daily_dimensions')->truncate();
            
            // Insert aggregated data back
            DB::statement("
                INSERT INTO analytics_daily_dimensions (site_id, date, dimension_type, dimension_value, count)
                SELECT site_id, date, dimension_type, dimension_value, total_count
                FROM temp_daily_dimensions_aggregated
            ");
            
            // Drop temporary table
            DB::statement("DROP TEMPORARY TABLE temp_daily_dimensions_aggregated");
        }
        
        // Now safely modify the enum
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
