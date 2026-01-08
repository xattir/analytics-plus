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
        Schema::create('analytics_url_patterns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->string('domain', 255); // Full domain (e.g., subdomain.example.com or example.com)
            $table->string('pattern', 2048);
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index(['site_id', 'domain'], 'idx_site_domain');
            $table->index('site_id', 'idx_site');
            $table->index('generated_at', 'idx_generated_at');
            
            // Foreign key
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_url_patterns');
    }
};
