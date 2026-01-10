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
        Schema::table('analytics_url_patterns', function (Blueprint $table) {
            $table->boolean('is_generated')->default(true)->after('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_url_patterns', function (Blueprint $table) {
            $table->dropColumn('is_generated');
        });
    }
};
