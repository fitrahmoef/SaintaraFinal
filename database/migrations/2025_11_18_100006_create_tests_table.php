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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tes');
            $table->text('deskripsi_tes')->nullable();
            $table->enum('jenis_tes', ['kepribadian', 'minat_bakat', 'kecerdasan', 'lainnya'])->default('kepribadian');
            $table->integer('jumlah_soal')->default(0);
            $table->integer('durasi_menit')->default(30); // Durasi tes dalam menit
            $table->integer('token_required')->default(1); // Berapa token dibutuhkan
            $table->json('metadata')->nullable(); // Data tambahan seperti kategori soal, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('jenis_tes');
            $table->index('is_active');
            $table->index('nama_tes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
