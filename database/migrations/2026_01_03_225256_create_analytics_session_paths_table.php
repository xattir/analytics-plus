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
        Schema::create('analytics_session_paths', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('site_id');
            $table->char('session_id', 36);

            $table->string('path', 2048);
            $table->char('path_hash', 64); // sha256 hash of path

            $table->integer('position');

            $table->tinyInteger('scroll_percent')->nullable();
            $table->integer('time_spent_ms')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // core indexes
            $table->index(['site_id', 'path_hash', 'created_at'], 'idx_site_path_time');
            $table->index(['site_id', 'path_hash'], 'idx_site_path');
            $table->index('session_id', 'idx_session');

            // analytics
            $table->index('time_spent_ms', 'idx_time_spent');
            $table->index('scroll_percent', 'idx_scroll');

            $table->foreign('site_id')
                ->references('id')
                ->on('analytics_sites')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_session_paths');
    }
};
