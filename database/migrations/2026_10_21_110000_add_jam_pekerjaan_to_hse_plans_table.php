<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hse_plans', function (Blueprint $table) {
            $table->string('jam_mulai', 5)->nullable()->after('tanggal_selesai');
            $table->string('jam_selesai', 5)->nullable()->after('jam_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('hse_plans', function (Blueprint $table) {
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
        });
    }
};
