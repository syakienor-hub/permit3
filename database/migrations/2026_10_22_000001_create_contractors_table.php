<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('token', 40)->unique();
            $table->string('vendor');
            $table->string('pic');
            $table->string('jenis_pekerjaan');
            $table->string('telepon');
            $table->text('nama_pekerjaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('jumlah_pekerja');
            $table->string('lokasi');
            $table->string('site');
            $table->string('status')->default('Menunggu HSE Plan')->index();
            $table->foreignId('hse_plan_id')->nullable()->constrained('hse_plans')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('contractor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('jenis');
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index(['contractor_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_documents');
        Schema::dropIfExists('contractors');
    }
};
