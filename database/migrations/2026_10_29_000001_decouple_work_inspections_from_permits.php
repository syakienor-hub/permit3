<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permits')
            ->where('status', 'Menunggu Daftar Periksa')
            ->update(['status' => 'Review Staff']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Submitted', 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }

        // SQLite cannot drop columns that are referenced by a foreign key
        // definition, and test runs use the legacy schema anyway.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasColumn('permits', 'work_inspection_id')) {
            Schema::table('permits', function (Blueprint $table) {
                $table->dropForeign(['work_inspection_id']);
            });

            Schema::table('permits', function (Blueprint $table) {
                $table->dropColumn('work_inspection_id');
            });
        }

        if (Schema::hasColumn('work_inspections', 'requested_permit_id')) {
            Schema::table('work_inspections', function (Blueprint $table) {
                $table->dropForeign(['requested_permit_id']);
            });

            Schema::table('work_inspections', function (Blueprint $table) {
                $table->dropColumn('requested_permit_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('permits', 'work_inspection_id')) {
            Schema::table('permits', function (Blueprint $table) {
                $table->foreignId('work_inspection_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('work_inspections')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('work_inspections', 'requested_permit_id')) {
            Schema::table('work_inspections', function (Blueprint $table) {
                $table->foreignId('requested_permit_id')
                    ->nullable()
                    ->after('site')
                    ->constrained('permits')
                    ->nullOnDelete();
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Menunggu Daftar Periksa', 'Submitted', 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }
    }
};
