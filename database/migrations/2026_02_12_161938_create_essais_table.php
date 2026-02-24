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
        Schema::create('essais', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('colis_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('cabine_id')->constrained()->onDelete('cascade');
            $table->foreignId('point_relais_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['started', 'in_progress', 'completed', 'cancelled'])->default('started');
            $table->decimal('fee', 10, 2)->default(500);
            $table->boolean('fee_paid')->default(false);
            $table->foreignId('payment_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->string('id_card_number')->nullable();
            $table->string('id_card_photo')->nullable();
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('result', ['approved', 'rejected', 'pending'])->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('photos_taken')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['colis_id', 'status']);
            $table->index(['client_id', 'created_at']);
            $table->index(['cabine_id', 'status']);
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essais');
    }
};
