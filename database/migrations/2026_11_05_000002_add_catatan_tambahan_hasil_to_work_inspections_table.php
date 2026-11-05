<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('work_inspections', 'catatan_tambahan_hasil')) {
                $table->string('catatan_tambahan_hasil')->nullable()->after('catatan_tambahan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_inspections', function (Blueprint $table) {
            if (Schema::hasColumn('work_inspections', 'catatan_tambahan_hasil')) {
                $table->dropColumn('catatan_tambahan_hasil');
            }
        });
    }
};
