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
        Schema::table('users', function (Blueprint $table) {
            $table->string('namapanggilan')->nullable()->after('name');
            $table->string('notelp')->nullable()->after('email');
            $table->string('negara')->nullable()->after('notelp');
            $table->string('kota')->nullable()->after('negara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['namapanggilan', 'notelp', 'negara', 'kota']);
        });
    }
};
