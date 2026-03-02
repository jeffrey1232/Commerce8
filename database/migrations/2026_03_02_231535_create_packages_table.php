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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('vendor_id')->constrained();
            $table->string('client_name', 255);
            $table->string('client_phone', 20);
            $table->string('product_name', 255);
            $table->text('product_description');
            $table->json('product_images')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->enum('status', ['pending', 'deposited', 'sold', 'returned', 'overdue'])->default('pending');
            $table->timestamp('deposited_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
