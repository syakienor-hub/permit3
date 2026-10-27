<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Menunggu Daftar Periksa', 'Submitted', 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }

        Schema::table('work_inspections', function (Blueprint $table) {
            $table->foreignId('requested_permit_id')
                ->nullable()
                ->after('site')
                ->constrained('permits')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        DB::table('permits')
            ->where('status', 'Menunggu Daftar Periksa')
            ->update(['status' => 'Review Staff']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permits MODIFY status ENUM('Draft', 'Submitted', 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled') DEFAULT 'Draft'");
        }

        Schema::table('work_inspections', function (Blueprint $table) {
            $table->dropForeign(['requested_permit_id']);
            $table->dropColumn('requested_permit_id');
        });
    }
};
