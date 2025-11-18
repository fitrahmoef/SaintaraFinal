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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gateway'); // Midtrans, Xendit, dll
            $table->string('kode_gateway', 50)->unique(); // MIDTRANS, XENDIT
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // API keys, settings, dll
            $table->timestamps();

            // Indexes
            $table->index('kode_gateway');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
