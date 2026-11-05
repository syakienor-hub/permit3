<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('work_inspections', 'catatan_tambahan')) {
                $table->text('catatan_tambahan')->nullable()->after('answers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            if (Schema::hasColumn('work_inspections', 'catatan_tambahan')) {
                $table->dropColumn('catatan_tambahan');
            }
        });
    }
};
