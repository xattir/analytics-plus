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
        if (Schema::hasTable('advertisement_clicks')) {
            return;
        }
        
        Schema::create('advertisement_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->unsignedBigInteger('site_id');
            $table->string('session_id')->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->nullable();
            $table->char('country_code', 2)->nullable();
            $table->unsignedBigInteger('url_pattern_id')->nullable();
            $table->string('selector');
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            $table->foreign('url_pattern_id')->references('id')->on('analytics_url_patterns')->onDelete('set null');
            $table->index(['advertisement_id', 'created_at']);
            $table->index(['site_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_clicks');
    }
};

