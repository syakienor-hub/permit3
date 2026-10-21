<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate in-flight permits
        DB::table('permits')
            ->where('status', 'Review Senior Manager')
            ->update(['status' => 'Active']);

        // Remove senior-manager users
        DB::table('users')
            ->where('role', 'senior-manager')
            ->delete();

        // Alter enum: remove 'Review Senior Manager' (MySQL only, SQLite ignores enums)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Submitted', 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }
    }

    public function down(): void
    {
        // Re-add enum value (data loss expected)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Submitted', 'Review Staff', 'Review Manager', 'Review Senior Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }
    }
};
