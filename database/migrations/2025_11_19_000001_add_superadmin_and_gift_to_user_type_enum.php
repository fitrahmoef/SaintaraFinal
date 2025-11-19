<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to modify the enum
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, so we skip for SQLite
            // The enum constraint is not enforced in SQLite anyway
            return;
        }

        // For MySQL/PostgreSQL
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('personal', 'admin', 'instansi', 'gift', 'superadmin') DEFAULT 'personal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Remove superadmin and gift from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('personal', 'admin', 'instansi') DEFAULT 'personal'");
    }
};
