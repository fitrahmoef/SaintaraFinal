<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PERFORMANCE FIX: Add missing indexes to frequently queried columns
     * This migration adds indexes to improve dashboard and query performance
     */
    public function up(): void
    {
        // TEST_RESULTS TABLE - Most queried table in dashboards
        Schema::table('test_results', function (Blueprint $table) {
            // Index for date-based queries (monthly stats)
            $table->index('tanggal_tes', 'idx_test_results_tanggal_tes');

            // Composite index for customer + test lookups
            $table->index(['customer_id', 'test_id'], 'idx_test_results_customer_test');

            // Index for filtering by test type (will be added in future migration)
            // $table->index('test_type', 'idx_test_results_test_type');
        });

        // TRANSACTIONS TABLE - Payment queries
        Schema::table('transactions', function (Blueprint $table) {
            // Index for status filtering
            $table->index('status_pembayaran', 'idx_transactions_status');

            // Composite index for customer + status queries
            $table->index(['customer_id', 'status_pembayaran'], 'idx_transactions_customer_status');

            // Index for date-based reporting
            $table->index('waktu_dibuat', 'idx_transactions_waktu_dibuat');
            $table->index('waktu_dibayar', 'idx_transactions_waktu_dibayar');
        });

        // TOKEN_PURCHASES TABLE - Token balance queries
        Schema::table('token_purchases', function (Blueprint $table) {
            // Composite index for customer + status
            $table->index(['customer_id', 'status'], 'idx_token_purchases_customer_status');

            // Index for expiry checks
            $table->index('tanggal_kadaluarsa', 'idx_token_purchases_expiry');

            // Index for transaction lookups
            $table->index('transaction_id', 'idx_token_purchases_transaction');
        });

        // USERS TABLE - User type filtering
        Schema::table('users', function (Blueprint $table) {
            // Index for user type filtering (admin dashboards)
            $table->index('user_type', 'idx_users_user_type');

            // Index for institution hierarchy
            // $table->index('parent_instansi_id', 'idx_users_parent_instansi');
        });

        // CUSTOMERS TABLE - Foreign key optimization
        Schema::table('customers', function (Blueprint $table) {
            // Already has user_id as foreign key, but add index for reverse lookups
            if (!$this->indexExists('customers', 'idx_customers_user_id')) {
                $table->index('user_id', 'idx_customers_user_id');
            }
        });

        // TESTS TABLE - Active tests filtering
        Schema::table('tests', function (Blueprint $table) {
            // Index for is_active filtering (already exists, skip)
            // $table->index('is_active', 'idx_tests_is_active');

            // Index for test type filtering (already exists, skip)
            // $table->index('jenis_tes', 'idx_tests_jenis_tes');
        });

        // PACKAGES TABLE - Active packages filtering
        Schema::table('packages', function (Blueprint $table) {
            // Index for is_active filtering (already exists, skip)
            // $table->index('is_active', 'idx_packages_is_active');
        });

        // CERTIFICATES TABLE - Verification lookups
        Schema::table('certificates', function (Blueprint $table) {
            // Index for certificate number verification (public endpoint)
            $table->index('nomor_sertifikat', 'idx_certificates_nomor_sertifikat');

            // Index for test result lookups
            $table->index('test_result_id', 'idx_certificates_test_result');
        });

        // TOKEN_USAGE TABLE - Usage tracking
        Schema::table('token_usage', function (Blueprint $table) {
            // Index for token purchase lookups
            $table->index('token_purchase_id', 'idx_token_usage_purchase');

            // Index for test result lookups
            $table->index('test_result_id', 'idx_token_usage_test_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('token_usage', function (Blueprint $table) {
            $table->dropIndex('idx_token_usage_purchase');
            $table->dropIndex('idx_token_usage_test_result');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('idx_certificates_nomor_sertifikat');
            $table->dropIndex('idx_certificates_test_result');
        });

        Schema::table('packages', function (Blueprint $table) {
            // Index already exists from creation, no need to drop
            // $table->dropIndex('idx_packages_status');
        });

        Schema::table('tests', function (Blueprint $table) {
            // Indexes already exist from creation, no need to drop
            // $table->dropIndex('idx_tests_status');
            // $table->dropIndex('idx_tests_jenis_tes');
        });

        Schema::table('customers', function (Blueprint $table) {
            if ($this->indexExists('customers', 'idx_customers_user_id')) {
                $table->dropIndex('idx_customers_user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_user_type');
        });

        Schema::table('token_purchases', function (Blueprint $table) {
            $table->dropIndex('idx_token_purchases_customer_status');
            $table->dropIndex('idx_token_purchases_expiry');
            $table->dropIndex('idx_token_purchases_transaction');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_status');
            $table->dropIndex('idx_transactions_customer_status');
            $table->dropIndex('idx_transactions_waktu_dibuat');
            $table->dropIndex('idx_transactions_waktu_dibayar');
        });

        Schema::table('test_results', function (Blueprint $table) {
            $table->dropIndex('idx_test_results_tanggal_tes');
            $table->dropIndex('idx_test_results_customer_test');
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL
            $result = DB::selectOne(
                "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $index]
            );
            return (bool) $result;
        } elseif ($driver === 'mysql') {
            // MySQL
            $result = DB::selectOne(
                "SHOW INDEX FROM {$table} WHERE Key_name = ?",
                [$index]
            );
            return (bool) $result;
        } else {
            // SQLite - always return false to try creating the index
            return false;
        }
    }
};
