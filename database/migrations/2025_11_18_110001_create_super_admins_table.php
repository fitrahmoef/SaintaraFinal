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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_super_admin');
            $table->string('nomor_telepon', 20)->nullable();
            $table->enum('status_akun', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->text('catatan')->nullable(); // Untuk catatan tambahan
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('user_id'); // Satu user hanya bisa jadi satu super admin
            $table->index('status_akun');
            $table->index('nama_super_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
