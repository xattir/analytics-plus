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
     * Adds missing last_seen_date column if it doesn't exist
     */
    public function up(): void
    {
        if (!Schema::hasColumn('analytics_sessions', 'last_seen_date')) {
            Schema::table('analytics_sessions', function (Blueprint $table) {
                $table->date('last_seen_date')
                    ->nullable()
                    ->after('last_seen')
                    ->storedAs('DATE(last_seen)')
                    ->comment('Generated column for DATE(last_seen) to enable index usage in GROUP BY');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('analytics_sessions', 'last_seen_date')) {
            Schema::table('analytics_sessions', function (Blueprint $table) {
                $table->dropColumn('last_seen_date');
            });
        }
    }
};

