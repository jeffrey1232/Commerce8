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
        Schema::create('services_digitaux', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['photo', 'video', 'packaging', 'insurance', 'express_delivery', 'gift_wrap'])->default('photo');
            $table->decimal('price', 10, 2);
            $table->enum('pricing_model', ['fixed', 'percentage', 'tiered'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('options')->nullable();
            $table->json('pricing_tiers')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_digitaux');
    }
};
