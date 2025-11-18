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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Employee name
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('department', ['IT', 'Keuangan', 'Marketing', 'Operasional', 'SDM'])->default('Operasional');
            $table->string('position')->nullable(); // e.g., "Manager", "Staff"
            $table->string('avatar')->nullable(); // Profile picture path
            $table->decimal('salary', 12, 2)->nullable();
            $table->decimal('commission', 10, 2)->default(0); // Commission earned
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->date('join_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
