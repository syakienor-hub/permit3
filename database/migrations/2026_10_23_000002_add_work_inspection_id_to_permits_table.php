<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->foreignId('work_inspection_id')
                ->nullable()
                ->after('user_id')
                ->constrained('work_inspections')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->dropForeign(['work_inspection_id']);
            $table->dropColumn('work_inspection_id');
        });
    }
};
