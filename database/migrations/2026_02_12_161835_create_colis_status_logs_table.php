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
        Schema::create('colis_status_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('colis_id')->constrained()->onDelete('cascade');
            $table->enum('old_status', [
                'created', 'deposited', 'pending_withdrawal', 'in_fitting',
                'refused', 'paid', 'reversed', 'in_storage', 'returned'
            ])->nullable();
            $table->enum('new_status', [
                'created', 'deposited', 'pending_withdrawal', 'in_fitting',
                'refused', 'paid', 'reversed', 'in_storage', 'returned'
            ]);
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('change_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['colis_id', 'created_at']);
            $table->index(['new_status', 'created_at']);
            $table->index('changed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis_status_logs');
    }
};
