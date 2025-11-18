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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense', 'agent_commission'])->default('income');
            $table->string('category'); // e.g., "Token Sales", "Office Rent", "Agent Commission"
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // For user-related transactions
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null'); // For team/agent commissions
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('transaction_date');
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
