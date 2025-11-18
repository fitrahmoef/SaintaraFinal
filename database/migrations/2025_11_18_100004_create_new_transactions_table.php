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
        // Rename existing transactions to old_transactions
        if (Schema::hasTable('transactions')) {
            Schema::rename('transactions', 'old_transactions');
        }

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways')->onDelete('set null');

            // Payment details
            $table->string('kode_transaksi', 50)->unique(); // TRX-2025-00001
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('status_pembayaran', ['pending', 'dibayar', 'gagal', 'kadaluarsa', 'refund'])->default('pending');
            $table->string('metode_pembayaran')->nullable(); // BCA Transfer, GoPay, dll

            // Gateway specific
            $table->string('gateway_transaction_id')->nullable(); // ID dari payment gateway
            $table->text('payment_url')->nullable(); // URL pembayaran
            $table->json('payment_metadata')->nullable(); // Data tambahan dari gateway

            // Timestamps
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->timestamp('waktu_dibayar')->nullable();
            $table->timestamp('waktu_kadaluarsa')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index('package_id');
            $table->index('status_pembayaran');
            $table->index('kode_transaksi');
            $table->index('waktu_dibuat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

        // Restore old transactions table
        if (Schema::hasTable('old_transactions')) {
            Schema::rename('old_transactions', 'transactions');
        }
    }
};
