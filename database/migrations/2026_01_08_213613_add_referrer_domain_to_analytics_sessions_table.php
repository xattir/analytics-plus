<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            // Add referrer_domain column to store actual domain with subdomain
            if (!Schema::hasColumn('analytics_sessions', 'referrer_domain')) {
                $table->string('referrer_domain', 255)->nullable()->after('referrer_source');
                $table->index('referrer_domain', 'idx_referrer_domain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('analytics_sessions', 'referrer_domain')) {
                $table->dropIndex('idx_referrer_domain');
                $table->dropColumn('referrer_domain');
            }
        });
    }
};
