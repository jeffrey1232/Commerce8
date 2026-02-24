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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transaction_id')->unique();
            $table->string('idempotency_key')->unique();
            $table->foreignId('colis_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('XOF');
            $table->enum('provider', ['wave', 'orange_money', 'mtn', 'cash'])->default('cash');
            $table->string('provider_transaction_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('provider_response')->nullable();
            $table->string('webhook_signature')->nullable();
            $table->timestamp('webhook_received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('fees', 10, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['provider', 'status']);
            $table->index('transaction_id');
            $table->index('idempotency_key');
            $table->index('colis_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
