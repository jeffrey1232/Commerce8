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
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tracking_code')->unique();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('point_relais_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('product_photo')->nullable();
            $table->enum('status', [
                'created', 'deposited', 'pending_withdrawal', 'in_fitting',
                'refused', 'paid', 'reversed', 'in_storage', 'returned'
            ])->default('created');
            $table->boolean('fitting_option')->default(false);
            $table->decimal('fitting_fee', 10, 2)->default(0);
            $table->timestamp('deposited_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->timestamp('storage_deadline')->nullable();
            $table->decimal('storage_fee', 10, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['vendor_id', 'status']);
            $table->index(['point_relais_id', 'status']);
            $table->index('tracking_code');
            $table->index('storage_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis');
    }
};
