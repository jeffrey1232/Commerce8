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
        Schema::create('reversements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference')->unique();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('net_amount', 15, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->enum('provider', ['wave', 'orange_money', 'mtn', 'bank_transfer'])->nullable();
            $table->string('provider_transaction_id')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_account')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('failure_reason')->nullable();
            $table->json('batch_details')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['vendor_id', 'status']);
            $table->index('reference');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversements');
    }
};
