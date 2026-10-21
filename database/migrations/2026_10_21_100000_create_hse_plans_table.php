<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hse_plans', function (Blueprint $table) {
            $table->id();
            $table->string('no_hse_plan')->unique();
            $table->string('no_po')->nullable();
            $table->string('site');
            $table->string('tipe');
            $table->string('divisi_pengaju');
            $table->string('nama_kontraktor');
            $table->string('nama_pimpinan_proyek');
            $table->string('telepon');
            $table->text('deskripsi_pekerjaan');
            $table->string('lama_pekerjaan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->json('personil')->nullable();
            $table->json('apd')->nullable();
            $table->string('apd_lainnya')->nullable();
            $table->json('peralatan')->nullable();
            $table->boolean('bahan_b3')->default(false);
            $table->boolean('limbah_b3')->default(false);
            $table->boolean('prosedur_kerja')->default(false);
            $table->boolean('rencana_inspeksi')->default(false);
            $table->boolean('tanggap_darurat')->default(false);
            $table->boolean('struktur_organisasi')->default(false);
            $table->longText('tanda_tangan');
            $table->string('status')->default('Review Manager')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->longText('manager_signature')->nullable();
            $table->timestamps();
        });

        Schema::create('hse_plan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hse_plan_id')->constrained()->cascadeOnDelete();
            $table->string('jenis');
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index(['hse_plan_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hse_plan_documents');
        Schema::dropIfExists('hse_plans');
    }
};
