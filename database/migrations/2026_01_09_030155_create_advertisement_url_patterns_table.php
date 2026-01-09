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
        if (Schema::hasTable('advertisement_url_patterns')) {
            return;
        }
        
        Schema::create('advertisement_url_patterns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->unsignedBigInteger('url_pattern_id');
            $table->timestamps();
            
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade');
            $table->foreign('url_pattern_id')->references('id')->on('analytics_url_patterns')->onDelete('cascade');
            $table->unique(['advertisement_id', 'url_pattern_id'], 'ad_url_patterns_unique');
            $table->index('url_pattern_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_url_patterns');
    }
};

