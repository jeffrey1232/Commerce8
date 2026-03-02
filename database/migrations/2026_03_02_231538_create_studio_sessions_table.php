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
        Schema::create('studio_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('package_id')->constrained();
            $table->enum('session_type', ['photoshoot', 'video', 'smoothing', 'makeup']);
            $table->timestamp('scheduled_time');
            $table->integer('duration')->default(60); // minutes
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('photos_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_sessions');
    }
};
