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
        Schema::create('character_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Sang Analis", "Pemimpin Ekstrovert"
            $table->string('code', 50)->unique(); // e.g., "SA", "PE", "THINKING_INTROVERT"
            $table->text('description');
            $table->json('strengths'); // Array of strengths
            $table->json('challenges'); // Array of challenges
            $table->text('communication_style')->nullable();
            $table->string('image_path')->nullable(); // Path to character image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_types');
    }
};
