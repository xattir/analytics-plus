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
        Schema::create('advertisement_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->unsignedBigInteger('site_id');
            $table->timestamps();
            
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            $table->unique(['advertisement_id', 'site_id'], 'ad_sites_unique');
            $table->index('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_sites');
    }
};

