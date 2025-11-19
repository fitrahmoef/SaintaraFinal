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
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->foreignId('token_purchase_id')->nullable()->constrained('token_purchases')->onDelete('set null');

            // Session data
            $table->string('session_token')->unique(); // Unique token untuk session ini
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'expired'])->default('in_progress');

            // Test data
            $table->json('jawaban')->nullable(); // Store answers as they progress
            $table->integer('current_question')->default(0); // Current question index
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamp('waktu_expired')->nullable(); // When session expires

            // Lock token to prevent concurrent usage
            $table->boolean('token_locked')->default(true);

            // Metadata
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('session_token');
            $table->index('status');
            $table->index(['customer_id', 'test_id']);
            $table->index('token_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
