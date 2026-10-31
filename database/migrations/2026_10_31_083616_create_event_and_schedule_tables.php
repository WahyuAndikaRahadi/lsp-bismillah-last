<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Event Sertifikasi (Program / Sumber Anggaran)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('kode_event', 50)->unique();
            $table->string('nama_event');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->string('sumber_anggaran', 50)->default('Mandiri'); // BNSP / PSKK, Mandiri, APBN, APBD, Lainnya
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Jadwal Uji Kompetensi (Assessment Schedules)
        Schema::create('assessment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->foreignId('tuk_id')->constrained('tuks')->cascadeOnDelete();
            $table->string('kode_jadwal', 50)->unique();
            $table->string('nama_jadwal');
            $table->date('tgl_pra_asesmen')->nullable();
            $table->date('tgl_asesmen');
            $table->date('tgl_selesai')->nullable();
            $table->date('tgl_buka_daftar')->nullable();
            $table->date('tgl_tutup_daftar')->nullable();
            $table->unsignedInteger('kuota_peserta')->default(20);
            $table->string('id_jadwal_bnsp', 100)->nullable();
            $table->string('status', 30)->default('direncanakan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_schedules');
        Schema::dropIfExists('events');
    }
};
