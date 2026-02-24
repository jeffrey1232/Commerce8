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
        Schema::create('logs_systeme', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('level');
            $table->string('message');
            $table->enum('context', ['payment', 'reversement', 'colis', 'auth', 'notification', 'system', 'security'])->default('system');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('action')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('resource_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->text('exception')->nullable();
            $table->string('request_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['level', 'created_at']);
            $table->index(['context', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_systeme');
    }
};
