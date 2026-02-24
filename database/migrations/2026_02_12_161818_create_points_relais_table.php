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
        Schema::create('points_relais', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('address');
            $table->string('city');
            $table->string('country')->default('Sénégal');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('manager_name');
            $table->string('manager_phone');
            $table->string('manager_email')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->integer('storage_capacity')->default(100);
            $table->integer('current_storage')->default(0);
            $table->json('operating_hours')->nullable();
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'city']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_relais');
    }
};
