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
        // Backup old tokens table
        if (Schema::hasTable('tokens')) {
            Schema::rename('tokens', 'old_tokens');
        }

        Schema::create('token_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');

            // Token details
            $table->string('kode_token', 50)->unique(); // TKN-2025-00001
            $table->integer('jumlah_token'); // Total token yang dibeli
            $table->integer('jumlah_terpakai')->default(0); // Token yang sudah digunakan
            $table->integer('jumlah_tersisa')->storedAs('jumlah_token - jumlah_terpakai'); // Computed column (PostgreSQL uses STORED)

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

        // Add check constraints using raw SQL
        DB::statement('ALTER TABLE token_purchases ADD CONSTRAINT chk_token_jumlah_positive CHECK (jumlah_token > 0)');
        DB::statement('ALTER TABLE token_purchases ADD CONSTRAINT chk_token_terpakai_positive CHECK (jumlah_terpakai >= 0)');
        DB::statement('ALTER TABLE token_purchases ADD CONSTRAINT chk_token_terpakai_valid CHECK (jumlah_terpakai <= jumlah_token)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_purchases');

        // Restore old tokens table
        if (Schema::hasTable('old_tokens')) {
            Schema::rename('old_tokens', 'tokens');
        }
    }
};
