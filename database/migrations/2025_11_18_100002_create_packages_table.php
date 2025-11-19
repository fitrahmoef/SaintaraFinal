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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->decimal('harga', 12, 2); // Support hingga 999,999,999.99
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_paket', ['personal', 'instansi', 'sekolah', 'gift', 'social_gift'])->default('personal');
            $table->integer('jumlah_token')->default(1); // Jumlah token yang didapat
            $table->integer('masa_aktif_hari')->default(365); // Berapa hari token berlaku
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional package features and metadata
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tipe_paket');
            $table->index('is_active');
        });

        // Add check constraints for PostgreSQL
        DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_packages_harga_positive CHECK (harga >= 0)');
        DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_packages_token_positive CHECK (jumlah_token > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
