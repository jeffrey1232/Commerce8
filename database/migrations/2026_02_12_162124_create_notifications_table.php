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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('notifiable');
            $table->string('type');
            $table->enum('channel', ['sms', 'email', 'whatsapp', 'push', 'database'])->default('database');
            $table->string('recipient');
            $table->text('subject')->nullable();
            $table->longText('content');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'cancelled'])->default('pending');
            $table->text('provider_response')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['channel', 'status']);
            $table->index('notifiable_type', 'notifiable_id');
            $table->index('next_retry_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
