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
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('site_id');
            $table->char('session_id', 36);
            
            // time
            $table->dateTime('first_seen');
            $table->dateTime('last_seen');
            $table->integer('duration_ms')->default(0);
            
            // navigation
            $table->string('entry_path', 2048);
            $table->string('exit_path', 2048);
            $table->integer('pages_count')->default(1);
            
            // engagement
            $table->tinyInteger('max_scroll_percent')->nullable();
            $table->integer('active_time_ms')->nullable();
            $table->integer('idle_time_ms')->nullable();
            
            // agent / device
            $table->text('user_agent');
            $table->char('device_fingerprint', 64)->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->nullable();
            $table->string('os', 64)->nullable();
            $table->string('os_version', 32)->nullable();
            $table->string('browser', 64)->nullable();
            $table->string('browser_version', 32)->nullable();
            $table->string('browser_engine', 32)->nullable();
            
            // screen
            $table->integer('screen_width')->nullable();
            $table->integer('screen_height')->nullable();
            $table->integer('viewport_width')->nullable();
            $table->integer('viewport_height')->nullable();
            $table->decimal('device_pixel_ratio', 3, 2)->nullable();
            
            // network
            $table->string('network_type', 16)->nullable();
            $table->integer('rtt_ms')->nullable();
            $table->decimal('downlink_mbps', 5, 2)->nullable();
            
            // geo
            $table->char('country', 2)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('isp', 128)->nullable();
            
            // marketing
            $table->string('utm_source', 64)->nullable();
            $table->string('utm_medium', 64)->nullable();
            $table->string('utm_campaign', 128)->nullable();
            
            // flags
            $table->boolean('is_returning')->default(false);
            $table->boolean('is_bounce')->default(false);
            $table->boolean('is_bot')->default(false);
            
            // security
            $table->binary('ip')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // constraints
            $table->unique(['site_id', 'session_id'], 'uniq_site_session');
            
            // core query indexes
            $table->index(['site_id', 'first_seen'], 'idx_site_time');
            $table->index(['site_id', 'last_seen'], 'idx_site_last_seen');
            
            // page analytics
            // Note: entry_path and exit_path are VARCHAR(2048), too long for composite indexes
            // We index site_id separately and filter by path in WHERE clauses
            $table->index('pages_count', 'idx_pages_count');
            
            // quality / engagement
            $table->index(['site_id', 'is_bounce'], 'idx_bounce');
            $table->index(['site_id', 'duration_ms'], 'idx_duration');
            $table->index('max_scroll_percent', 'idx_scroll');
            
            // device / agent
            $table->index('device_type', 'idx_device_type');
            $table->index('browser', 'idx_browser');
            $table->index('browser_engine', 'idx_browser_engine');
            $table->index('os', 'idx_os');
            
            // fingerprint / bot
            $table->index('device_fingerprint', 'idx_fingerprint');
            $table->index('is_bot', 'idx_bot');
            
            // geo
            $table->index('country', 'idx_country');
            $table->index(['country', 'city'], 'idx_country_city');
            
            // marketing
            $table->index('utm_source', 'idx_utm_source');
            $table->index('utm_campaign', 'idx_utm_campaign');
            
            // network / perf
            $table->index('network_type', 'idx_network_type');
            $table->index('rtt_ms', 'idx_rtt');
            
            $table->foreign('site_id')->references('id')->on('analytics_sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
