<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('token_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');

            // Token details
            $table->string('kode_token', 50)->unique(); // TKN-2025-00001
            $table->integer('jumlah_token'); // Total token yang dibeli
            $table->integer('jumlah_terpakai')->default(0); // Token yang sudah digunakan
            // Note: jumlah_tersisa will be calculated in model accessor (jumlah_token - jumlah_terpakai)

            // Status & dates
            $table->enum('status', ['aktif', 'habis', 'kadaluarsa'])->default('aktif');
            $table->timestamp('tanggal_pembelian')->useCurrent();
            $table->timestamp('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('tanggal_kadaluarsa');
            $table->index('kode_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_purchases');
    }
};
