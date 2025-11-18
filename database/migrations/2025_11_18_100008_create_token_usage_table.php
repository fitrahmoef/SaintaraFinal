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
        Schema::create('token_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_purchase_id')->constrained('token_purchases')->onDelete('cascade');
            $table->foreignId('test_result_id')->constrained('test_results')->onDelete('cascade');

            // Usage details
            $table->integer('jumlah_digunakan')->default(1);
            $table->string('keterangan')->nullable(); // Tambahan info jika perlu
            $table->timestamp('tanggal_penggunaan')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('token_purchase_id');
            $table->index('test_result_id');
            $table->index('tanggal_penggunaan');

            // Ensure one token usage per test result
            $table->unique('test_result_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_usage');
    }
};
