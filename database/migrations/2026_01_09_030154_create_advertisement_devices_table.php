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
        Schema::create('advertisement_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->enum('device_type', ['desktop', 'mobile', 'tablet']);
            $table->timestamps();
            
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade');
            $table->unique(['advertisement_id', 'device_type']);
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_devices');
    }
};

