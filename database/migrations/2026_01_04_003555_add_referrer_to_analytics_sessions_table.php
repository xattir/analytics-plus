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
            $table->string('referrer', 2048)->nullable()->after('utm_campaign');
            $table->string('referrer_source', 128)->nullable()->after('referrer');
            
            // Index for referrer source queries
            $table->index('referrer_source', 'idx_referrer_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_referrer_source');
            $table->dropColumn(['referrer', 'referrer_source']);
        });
    }
};
