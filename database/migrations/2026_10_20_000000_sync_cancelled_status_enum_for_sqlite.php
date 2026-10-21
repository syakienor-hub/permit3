<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SQLite menyimpan enum sebagai CHECK constraint, sehingga nilai 'Cancelled'
     * perlu disinkronkan ulang agar test (sqlite :memory:) dapat menyimpan status tersebut.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        Schema::table('permits', function (Blueprint $table) {
            $table->enum('status', [
                'Draft',
                'Submitted',
                'Review Staff',
                'Review Manager',
                'Revision',
                'Active',
                'Closed',
                'Cancelled',
            ])->default('Draft')->change();
        });
    }

    public function down(): void
    {
        // Tidak dikembalikan: data berstatus Cancelled bisa melanggar constraint lama.
    }
};
