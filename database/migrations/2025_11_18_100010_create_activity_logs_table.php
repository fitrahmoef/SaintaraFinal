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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship - bisa customer, admin, atau system
            $table->nullableMorphs('user'); // Creates user_id & user_type columns

            // Activity details
            $table->string('action', 100); // login, logout, purchase_token, take_test, dll
            $table->text('description')->nullable(); // Deskripsi detail
            $table->string('module', 50)->nullable(); // auth, payment, test, certificate

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_url')->nullable();
            $table->string('request_method', 10)->nullable(); // GET, POST, PUT, DELETE

            // Additional data
            $table->json('properties')->nullable(); // Data tambahan dalam JSON
            $table->enum('log_level', ['info', 'warning', 'error', 'critical'])->default('info');

            $table->timestamp('created_at')->useCurrent(); // Hanya created_at, tidak perlu updated

            // Indexes untuk query cepat
            // Note: nullableMorphs already creates index on [user_type, user_id]
            $table->index('action');
            $table->index('module');
            $table->index('log_level');
            $table->index('created_at');
            $table->index(['user_type', 'user_id', 'created_at']); // Composite untuk user activity timeline
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
