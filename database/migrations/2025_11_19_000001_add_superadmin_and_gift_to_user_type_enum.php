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

        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: Drop and recreate the column with updated enum values
            // First, we need to drop the existing CHECK constraint
            $constraints = DB::select("SELECT conname FROM pg_constraint WHERE conrelid = 'users'::regclass AND conname LIKE '%user_type%'");
            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS {$constraint->conname}");
            }

            // Now recreate the column with new enum values
            DB::statement("ALTER TABLE users ALTER COLUMN user_type TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_user_type_check CHECK (user_type IN ('personal', 'admin', 'instansi', 'gift', 'superadmin'))");
        } else {
            // MySQL
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('personal', 'admin', 'instansi', 'gift', 'superadmin') DEFAULT 'personal'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL doesn't support removing enum values easily
            // This would require recreating the type, which is complex
            // For development, we'll leave it as-is
            return;
        }

        // MySQL: Remove superadmin and gift from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('personal', 'admin', 'instansi') DEFAULT 'personal'");
    }
};
