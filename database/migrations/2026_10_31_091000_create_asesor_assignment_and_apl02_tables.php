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
        // 1. Penugasan Asesor ke Jadwal Uji Kompetensi (Surat Tugas)
        Schema::create('schedule_asesors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('assessment_schedules')->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('users')->cascadeOnDelete();
            $table->string('peran', 50)->default('Anggota'); // Lead Asesor, Anggota
            $table->string('no_surat_tugas', 100)->nullable();
            $table->date('tgl_surat_tugas')->nullable();
            $table->timestamps();

            $table->unique(['schedule_id', 'asesor_id']);
        });

        // 2. Tambahkan asesor_id ke permohonan asesi (Plotting Asesi ke Asesor Penguji Tertentu)
        Schema::table('assessment_applications', function (Blueprint $table) {
            $table->foreignId('asesor_id')->nullable()->after('schedule_id')->constrained('users')->nullOnDelete();
        });

        // 3. Formulir Asesmen Mandiri (FR-APL-02)
        Schema::create('apl02_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('assessment_applications')->cascadeOnDelete();
            $table->foreignId('asesor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft'); // draft, diajukan, diverifikasi, perlu_revisi
            $table->string('rekomendasi_asesor', 100)->nullable(); // Asesmen dapat dilanjutkan, Asesmen tidak dapat dilanjutkan
            $table->text('catatan_asesor')->nullable();
            $table->text('ttd_asesi')->nullable();
            $table->timestamp('tgl_ttd_asesi')->nullable();
            $table->text('ttd_asesor')->nullable();
            $table->timestamp('tgl_ttd_asesor')->nullable();
            $table->timestamps();
        });

        // 4. Rincian Penilaian Mandiri per KUK (FR-APL-02 Items)
        Schema::create('apl02_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apl02_assessment_id')->constrained('apl02_assessments')->cascadeOnDelete();
            $table->foreignId('kuk_id')->constrained('work_key_criterias')->cascadeOnDelete();
            $table->string('penilaian_mandiri', 5)->default('K'); // K (Kompeten) / BK (Belum Kompeten)
            $table->string('bukti_pendukung')->nullable(); // Kode bukti / Nama berkas portofolio

            // Verifikasi Tinjauan Asesor (VATM: Valid, Asli, Terkini, Memadai)
            $table->boolean('is_valid')->default(true);
            $table->boolean('is_asli')->default(true);
            $table->boolean('is_terkini')->default(true);
            $table->boolean('is_memadai')->default(true);
            $table->text('catatan_asesor')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apl02_items');
        Schema::dropIfExists('apl02_assessments');

        Schema::table('assessment_applications', function (Blueprint $table) {
            $table->dropForeign(['asesor_id']);
            $table->dropColumn('asesor_id');
        });

        Schema::dropIfExists('schedule_asesors');
    }
};
