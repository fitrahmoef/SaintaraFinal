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
        Schema::create('admin_instansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_admin');
            $table->string('nama_instansi');
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('email_instansi')->nullable();
            $table->text('alamat_instansi')->nullable();
            $table->string('kota_instansi', 100)->nullable();
            $table->string('provinsi_instansi', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->enum('status_akun', ['aktif', 'tidak_aktif', 'pending'])->default('pending');
            $table->date('tanggal_bergabung')->nullable();
            $table->date('tanggal_berakhir')->nullable(); // Untuk masa aktif akun
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('user_id'); // Satu user hanya bisa jadi satu admin instansi
            $table->index('status_akun');
            $table->index('nama_instansi');
            $table->index('nama_admin');
            $table->index('kota_instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_instansi');
    }
};
