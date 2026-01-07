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
        // Daily summary table for main statistics
        if (!Schema::hasTable('analytics_daily_summary')) {
            Schema::create('analytics_daily_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->date('date');
            $table->integer('total_sessions')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->integer('total_pageviews')->default(0);
            $table->decimal('avg_duration', 10, 2)->default(0);
            $table->decimal('avg_pages_per_session', 5, 2)->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('new_visitors')->default(0);
            $table->integer('returning_visitors')->default(0);
            $table->integer('bot_sessions')->default(0);
            $table->integer('real_sessions')->default(0);
            $table->integer('high_quality')->default(0);
            $table->integer('low_quality')->default(0);
            
            $table->unique(['site_id', 'date']);
            $table->index(['site_id', 'date']);
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            });
        }

        // Daily top pages summary
        if (!Schema::hasTable('analytics_top_pages_daily')) {
            Schema::create('analytics_top_pages_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->date('date');
            $table->string('path', 500);
            $table->integer('views')->default(0);
            
            $table->unique(['site_id', 'date', 'path'], 'unique_site_date_path');
            $table->index(['site_id', 'date', 'views']);
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            });
        }

        // Daily entry/exit pages summary
        if (!Schema::hasTable('analytics_entry_exit_daily')) {
            Schema::create('analytics_entry_exit_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->date('date');
            $table->string('entry_path', 500)->nullable();
            $table->string('exit_path', 500)->nullable();
            $table->integer('entry_count')->default(0);
            $table->integer('exit_count')->default(0);
            
            $table->index(['site_id', 'date', 'entry_path']);
            $table->index(['site_id', 'date', 'exit_path']);
            $table->index(['site_id', 'date', 'entry_count']);
            $table->index(['site_id', 'date', 'exit_count']);
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            });
        }

        // Daily browser/device/country summary
        if (!Schema::hasTable('analytics_dimensions_daily')) {
            Schema::create('analytics_dimensions_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->date('date');
            $table->string('dimension_type', 50); // browser, device_type, os, country
            $table->string('dimension_value', 255);
            $table->integer('count')->default(0);
            
            $table->index(['site_id', 'date', 'dimension_type', 'count'], 'idx_dimensions');
            $table->unique(['site_id', 'date', 'dimension_type', 'dimension_value'], 'unique_dimension');
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_dimensions_daily');
        Schema::dropIfExists('analytics_entry_exit_daily');
        Schema::dropIfExists('analytics_top_pages_daily');
        Schema::dropIfExists('analytics_daily_summary');
    }
};
