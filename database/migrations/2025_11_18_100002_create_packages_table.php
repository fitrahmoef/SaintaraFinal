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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->decimal('harga', 12, 2); // Support hingga 999,999,999.99
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_paket', ['dasar', 'standar', 'premium'])->default('dasar');
            $table->integer('jumlah_token')->default(1); // Jumlah token yang didapat
            $table->integer('masa_aktif_hari')->default(365); // Berapa hari token berlaku
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tipe_paket');
            $table->index('is_active');

            // Constraints
            $table->check('harga >= 0', 'chk_packages_harga_positive');
            $table->check('jumlah_token > 0', 'chk_packages_token_positive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
