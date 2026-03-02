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
        Schema::create('fitting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->string('current_client', 255)->nullable();
            $table->enum('guarantee_type', ['id_card', 'phone', 'cash'])->nullable();
            $table->text('guarantee_details')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitting_rooms');
    }
};
