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
        Schema::table('advertisements', function (Blueprint $table) {
            $table->integer('padding_x')->default(20)->after('url');
            $table->integer('padding_y')->default(20)->after('padding_x');
            $table->integer('interval_period')->nullable()->after('padding_y')->comment('Interval in seconds for Interstitial ads');
        });
        
        // Update enum to only 4 types
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `type` ENUM('in_content', 'pop_from_bottom', 'pop_from_top', 'Interstitial') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['padding_x', 'padding_y', 'interval_period']);
        });
        
        // Revert enum to original
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `type` ENUM('html', 'image', 'video', 'text', 'script', 'pop-bottom', 'pop-top', 'interstitial') NOT NULL");
    }
};
