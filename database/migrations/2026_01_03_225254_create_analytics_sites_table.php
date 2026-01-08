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
        Schema::create('analytics_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_key', 64)->unique();
            $table->string('domain', 255);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sites');
    }
};
