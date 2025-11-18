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
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('package_type', ['dasar', 'standar', 'premium'])->default('dasar');
            $table->integer('token_amount'); // Number of tokens
            $table->decimal('price', 10, 2); // Price in IDR
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // e.g., "transfer_bank", "e-wallet"
            $table->text('payment_proof')->nullable(); // Path to payment proof image
            $table->integer('tokens_used')->default(0); // Tokens already used
            $table->date('expiry_date')->nullable(); // Token expiration date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
