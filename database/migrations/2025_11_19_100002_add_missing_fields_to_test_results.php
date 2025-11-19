<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * DATABASE SCHEMA FIX: Add missing fields to test_results table
     * These fields are referenced in controllers but don't exist in the schema
     */
    public function up(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            // Add test_type field for filtering (personal/instansi/gift)
            $table->enum('test_type', ['personal', 'instansi', 'gift'])
                ->default('personal')
                ->after('customer_id')
                ->comment('Type of test: personal, institution, or gift');

            // Add character_type_id foreign key for linking to character types
            $table->foreignId('character_type_id')
                ->nullable()
                ->after('hasil_karakter')
                ->constrained('character_types')
                ->nullOnDelete()
                ->comment('Link to character_types table for structured data');

            // Add institution_name for institution tests
            $table->string('institution_name', 255)
                ->nullable()
                ->after('character_type_id')
                ->comment('Name of institution (for instansi tests)');

            // Add index for test_type filtering
            $table->index('test_type', 'idx_test_results_test_type');

            // Add index for character_type_id lookups
            $table->index('character_type_id', 'idx_test_results_character_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_test_results_test_type');
            $table->dropIndex('idx_test_results_character_type');

            // Drop foreign key
            $table->dropForeign(['character_type_id']);

            // Drop columns
            $table->dropColumn([
                'test_type',
                'character_type_id',
                'institution_name',
            ]);
        });
    }
};
