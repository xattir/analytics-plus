<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Backfill quality flags and ensure first_seen_date is populated.
     * This runs in chunks to avoid locking the table for too long.
     */
    public function up(): void
    {
        // Note: first_seen_date is a GENERATED column, so it's automatically populated
        // We only need to backfill quality flags
        
        $chunkSize = 10000;
        $totalProcessed = 0;
        
        echo "Backfilling quality flags...\n";
        
        do {
            $updated = DB::statement("
                UPDATE analytics_sessions
                SET 
                    is_high_quality = (
                        is_bot = 0 
                        AND pages_count > 1 
                        AND duration_ms > 30000 
                        AND max_scroll_percent > 50
                    ),
                    is_low_quality = (
                        is_bot = 0 
                        AND (
                            pages_count = 1 
                            OR duration_ms < 5000 
                            OR max_scroll_percent < 10
                        )
                    )
                WHERE (is_high_quality IS NULL OR is_low_quality IS NULL)
                LIMIT {$chunkSize}
            ");
            
            $totalProcessed += $chunkSize;
            echo "Processed {$totalProcessed} rows...\n";
            
            // Small delay to avoid overwhelming the database
            usleep(100000); // 0.1 seconds
            
        } while ($updated > 0);
        
        echo "Backfill complete. Total rows processed: {$totalProcessed}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset quality flags to false
        DB::statement("
            UPDATE analytics_sessions
            SET is_high_quality = 0, is_low_quality = 0
        ");
    }
};

