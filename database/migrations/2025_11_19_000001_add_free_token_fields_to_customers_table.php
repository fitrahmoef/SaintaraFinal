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
        Schema::table('customers', function (Blueprint $table) {
            // Track if free tokens have been granted
            $table->boolean('free_tokens_granted')->default(false)->after('kota');

            // Track remaining free tokens (default 1 for first-time users)
            $table->integer('free_token_count')->default(0)->after('free_tokens_granted');

            // Track when free tokens were granted
            $table->timestamp('free_tokens_granted_at')->nullable()->after('free_token_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['free_tokens_granted', 'free_token_count', 'free_tokens_granted_at']);
        });
    }
};
