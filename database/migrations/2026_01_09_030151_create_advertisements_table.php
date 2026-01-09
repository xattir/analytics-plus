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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['html', 'image', 'video', 'text', 'script']);
            $table->text('content');
            $table->string('url')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('priority');
            $table->index(['is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables with foreign keys first
        Schema::dropIfExists('advertisement_clicks');
        Schema::dropIfExists('advertisement_impressions');
        Schema::dropIfExists('advertisement_subdomains');
        Schema::dropIfExists('advertisement_selectors');
        Schema::dropIfExists('advertisement_excluded_patterns');
        Schema::dropIfExists('advertisement_url_patterns');
        Schema::dropIfExists('advertisement_devices');
        Schema::dropIfExists('advertisement_countries');
        Schema::dropIfExists('advertisement_sites');
        
        // Finally drop the main table
        Schema::dropIfExists('advertisements');
    }
};

