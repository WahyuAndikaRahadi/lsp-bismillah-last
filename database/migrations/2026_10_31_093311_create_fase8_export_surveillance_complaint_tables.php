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
        // 1. Tabel Layanan Pengaduan Masyarakat
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->string('nama_pelapor');
            $table->string('email')->nullable();
            $table->string('telepon', 30);
            $table->string('jenis_responden', 50)->default('Umum'); // Asesi, Asesor, Instansi, TUK, Umum
            $table->string('judul');
            $table->text('isi_aduan');
            $table->string('status', 30)->default('diproses'); // diproses, selesai, ditolak
            $table->text('tindak_lanjut')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_tindak_lanjut')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Layanan Banding Asesmen (FR-AK-04)
        Schema::create('assessment_appeals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_banding', 50)->unique();
            $table->foreignId('application_id')->constrained('assessment_applications')->cascadeOnDelete();
            $table->foreignId('asesi_id')->constrained('users')->cascadeOnDelete();
            $table->text('alasan_banding');
            $table->text('bukti_pendukung')->nullable();
            $table->string('status', 30)->default('diajukan'); // diajukan, diproses, diterima, ditolak
            $table->text('keputusan_komite')->nullable();
            $table->foreignId('komite_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_keputusan')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pemeliharaan Sertifikasi (Surveillance Online)
        Schema::create('surveillance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_lengkap');
            $table->string('nik', 20)->index();
            $table->string('email')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('no_sertifikat', 100)->index();
            $table->string('no_registrasi', 100)->nullable();
            $table->foreignId('scheme_id')->nullable()->constrained('certification_schemes')->nullOnDelete();
            $table->string('tempat_kerja')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('proyek_saat_ini')->nullable();
            $table->string('kesesuaian_skk', 5)->default('Y'); // Y = Sesuai, N = Tidak Sesuai, O = Lainnya
            $table->text('keterangan')->nullable();
            $table->string('status', 30)->default('menunggu_verifikasi'); // menunggu_verifikasi, terverifikasi, tidak_aktif
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveillance_records');
        Schema::dropIfExists('assessment_appeals');
        Schema::dropIfExists('complaints');
    }
};
