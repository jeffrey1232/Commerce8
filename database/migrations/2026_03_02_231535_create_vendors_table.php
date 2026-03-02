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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('store_name', 255);
            $table->text('store_address');
            $table->string('store_phone', 20);
            $table->string('business_license', 255)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_packages')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
