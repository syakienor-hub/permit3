<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat koreksi detail permit oleh Staff HSE sebelum approval ke Manager.
     * Disimpan sebagai JSON: daftar entri { at, by, role, changes[] }.
     */
    public function up(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->json('edit_history')->nullable()->after('catatan_revisi');
        });
    }

    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->dropColumn('edit_history');
        });
    }
};
