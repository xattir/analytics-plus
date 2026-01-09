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
        // Update enum to include new ad types
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `type` ENUM('html', 'image', 'video', 'text', 'script', 'pop-bottom', 'pop-top', 'interstitial') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `type` ENUM('html', 'image', 'video', 'text', 'script') NOT NULL");
    }
};
