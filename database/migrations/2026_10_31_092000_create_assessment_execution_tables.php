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
        // 1. Formulir Persetujuan Asesmen & Kerahasiaan (FR-AK-01)
        Schema::create('ak01_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('assessment_applications')->cascadeOnDelete();
            $table->foreignId('asesor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('hari_tanggal');
            $table->string('waktu', 50)->default('09:00 - Selesai WIB');
            $table->string('tuk_nama');
            $table->boolean('is_asesi_agreed')->default(true);
            $table->boolean('is_asesor_agreed')->default(true);
            $table->text('ttd_asesi')->nullable();
            $table->timestamp('tgl_ttd_asesi')->nullable();
            $table->text('ttd_asesor')->nullable();
            $table->timestamp('tgl_ttd_asesor')->nullable();
            $table->timestamps();
        });

        // 2. Formulir Rekaman Asesmen Kompetensi (FR-AK-02)
        Schema::create('ak02_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('assessment_applications')->cascadeOnDelete();
            $table->foreignId('asesor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rekomendasi_akhir', 5)->default('K'); // K (Kompeten), BK (Belum Kompeten)
            $table->text('tindak_lanjut')->nullable(); // Jika BK
            $table->text('komentar_asesor')->nullable();
            $table->text('ttd_asesi')->nullable();
            $table->timestamp('tgl_ttd_asesi')->nullable();
            $table->text('ttd_asesor')->nullable();
            $table->timestamp('tgl_ttd_asesor')->nullable();
            $table->timestamps();
        });

        // 3. Penilaian per Unit Kompetensi (FR-AK-02 Units)
        Schema::create('ak02_unit_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ak02_assessment_id')->constrained('ak02_assessments')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('competency_units')->cascadeOnDelete();
            
            // Metode Asesmen yang Digunakan
            $table->boolean('metode_observasi_langsung')->default(true); // L
            $table->boolean('metode_portofolio')->default(false);        // TL
            $table->boolean('metode_pertanyaan')->default(true);         // T
            
            $table->string('rekomendasi_unit', 5)->default('K'); // K / BK
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Formulir Umpan Balik dan Catatan Asesmen (FR-AK-03)
        Schema::create('ak03_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('assessment_applications')->cascadeOnDelete();
            $table->boolean('q1_penjelasan')->default(true);
            $table->boolean('q2_kesepakatan')->default(true);
            $table->boolean('q3_hak_banding')->default(true);
            $table->boolean('q4_adil_objektif')->default(true);
            $table->boolean('q5_umpan_balik')->default(true);
            $table->text('catatan_rekomendasi')->nullable();
            $table->text('ttd_asesi')->nullable();
            $table->timestamp('tgl_ttd_asesi')->nullable();
            $table->timestamps();
        });

        // 5. Laporan Asesmen Jadwal (FR-AK-05)
        Schema::create('ak05_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->unique()->constrained('assessment_schedules')->cascadeOnDelete();
            $table->string('nomor_laporan', 100)->unique();
            $table->text('aspek_positif')->nullable();
            $table->text('kendala_hambatan')->nullable();
            $table->text('saran_perbaikan')->nullable();
            $table->text('ttd_asesor_lead')->nullable();
            $table->date('tgl_laporan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ak05_reports');
        Schema::dropIfExists('ak03_feedbacks');
        Schema::dropIfExists('ak02_unit_assessments');
        Schema::dropIfExists('ak02_assessments');
        Schema::dropIfExists('ak01_agreements');
    }
};
