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
            // site_id + is_bounce + first_seen (used for bounce rate calculations)
            if (!$this->indexExists('analytics_sessions', 'idx_site_bounce_first_seen')) {
                $table->index(['site_id', 'is_bounce', 'first_seen'], 'idx_site_bounce_first_seen');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            if ($this->indexExists('analytics_sessions', 'idx_site_bounce_first_seen')) {
                $table->dropIndex('idx_site_bounce_first_seen');
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
};
