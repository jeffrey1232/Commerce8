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
        Schema::create('cabines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('point_relais_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->decimal('fee', 10, 2)->default(500);
            $table->integer('max_capacity')->default(1);
            $table->integer('current_occupancy')->default(0);
            $table->json('equipment')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['point_relais_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabines');
    }
};
