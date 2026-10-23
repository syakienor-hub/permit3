<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('no_periksa')->unique();
            $table->string('site');
            $table->string('tipe');
            $table->string('divisi_pengaju');
            $table->string('nama_pekerjaan');
            $table->string('kontraktor');
            $table->string('jenis_peralatan')->nullable();
            $table->string('lokasi');
            $table->string('telepon');
            $table->date('tanggal_mulai')->nullable();
            $table->json('answers')->nullable();
            $table->string('loto_nama')->nullable();
            $table->date('loto_tanggal')->nullable();
            $table->string('loto_jam')->nullable();
            $table->longText('tanda_tangan');
            $table->string('supervisor_nama')->nullable();
            $table->date('supervisor_tanggal')->nullable();
            $table->string('supervisor_jam')->nullable();
            $table->string('safety_officer_nama')->nullable();
            $table->longText('staff_signature')->nullable();
            $table->string('status')->default('Menunggu Verifikasi')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['site', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_inspections');
    }
};
