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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->integer('nomor_soal'); // Urutan soal (1, 2, 3, dst)
            $table->text('pertanyaan'); // Pertanyaan soal
            $table->enum('tipe_soal', ['multiple_choice', 'text_input', 'rating_scale'])->default('multiple_choice');
            $table->json('pilihan_jawaban')->nullable(); // JSON untuk pilihan jawaban
            $table->json('bobot_karakter')->nullable(); // JSON untuk bobot karakter per jawaban
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('test_id');
            $table->index(['test_id', 'nomor_soal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
