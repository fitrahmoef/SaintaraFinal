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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('token_purchase_id')->nullable()->constrained('token_purchases')->onDelete('set null');

            // Hasil tes
            $table->string('hasil_karakter', 200)->nullable(); // Misal: "INTJ - The Architect"
            $table->text('deskripsi_hasil')->nullable();
            $table->integer('skor')->nullable();
            $table->json('jawaban')->nullable(); // Menyimpan semua jawaban user
            $table->json('analisis')->nullable(); // Detail analisis hasil

            // Metadata
            $table->timestamp('tanggal_tes')->useCurrent();
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('durasi_detik')->nullable(); // Will be calculated in application or model accessor
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('test_id');
            $table->index('customer_id');
            $table->index('token_purchase_id');
            $table->index('tanggal_tes');
            $table->index(['customer_id', 'test_id']); // Composite index untuk query user history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
