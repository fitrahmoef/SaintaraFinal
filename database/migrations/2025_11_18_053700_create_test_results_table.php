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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_type_id')->constrained()->onDelete('cascade');
            $table->enum('test_type', ['personal', 'instansi', 'gift'])->default('personal');
            $table->json('answers')->nullable(); // Store test answers
            $table->integer('score')->nullable();
            $table->string('institution_name')->nullable(); // For instansi tests
            $table->date('test_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
