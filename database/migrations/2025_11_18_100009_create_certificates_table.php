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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_result_id')->constrained('test_results')->onDelete('cascade');

            // Certificate details
            $table->string('nomor_sertifikat', 50)->unique(); // CERT-2025-00001
            $table->string('diterbitkan_oleh', 100)->default('Saintara');
            $table->string('ttd_digital')->nullable(); // Path ke file signature
            $table->string('url_verifikasi')->unique()->nullable(); // URL untuk verifikasi sertifikat
            $table->enum('format_file', ['pdf', 'jpg', 'png'])->default('pdf');
            $table->string('file_path')->nullable(); // Path ke file sertifikat

            // Status & metadata
            $table->boolean('is_active')->default(true); // Bisa di-revoke
            $table->timestamp('tanggal_terbit')->useCurrent();
            $table->json('metadata')->nullable(); // Data tambahan
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('test_result_id');
            $table->index('nomor_sertifikat');
            $table->index('url_verifikasi');
            $table->index('is_active');
            $table->index('tanggal_terbit');

            // Ensure one certificate per test result
            $table->unique('test_result_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
