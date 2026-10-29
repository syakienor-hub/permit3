<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('work_inspections', 'otorisasi')) {
                $table->string('otorisasi')->nullable()->after('site');
            }

            if (! Schema::hasColumn('work_inspections', 'material')) {
                $table->string('material')->nullable()->after('jenis_peralatan');
            }

            if (! Schema::hasColumn('work_inspections', 'petugas_pemeriksa')) {
                $table->string('petugas_pemeriksa')->nullable()->after('tanggal_mulai');
            }

            if (! Schema::hasColumn('work_inspections', 'manager_nama')) {
                $table->string('manager_nama')->nullable()->after('safety_officer_nama');
            }

            if (! Schema::hasColumn('work_inspections', 'tanggal_terbit')) {
                $table->date('tanggal_terbit')->nullable()->after('no_periksa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            foreach (['otorisasi', 'material', 'petugas_pemeriksa', 'manager_nama', 'tanggal_terbit'] as $column) {
                if (Schema::hasColumn('work_inspections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
