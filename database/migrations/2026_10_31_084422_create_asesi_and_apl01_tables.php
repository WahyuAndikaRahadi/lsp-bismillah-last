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
        // 1. Profil Calon Asesi (Biodata Lengkap sesuai BNSP FR-APL-01 Bagian 1)
        Schema::create('asesi_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('nik', 20)->index();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 1)->default('L'); // L = Laki-laki, P = Perempuan
            $table->string('kebangsaan', 50)->default('Indonesia');
            
            // Alamat Domisili Bertingkat
            $table->text('alamat');
            $table->string('rt_rw', 20)->nullable();
            $table->string('provinsi_kode', 20)->nullable();
            $table->string('provinsi_nama', 100)->nullable();
            $table->string('kota_kode', 20)->nullable();
            $table->string('kota_nama', 100)->nullable();
            $table->string('kecamatan_kode', 20)->nullable();
            $table->string('kecamatan_nama', 100)->nullable();
            $table->string('kelurahan_kode', 20)->nullable();
            $table->string('kelurahan_nama', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable(); // SD, SMP, SMA/SMK, D1, D2, D3, D4/S1, S2, S3

            // Data Pekerjaan Sekarang
            $table->string('pekerjaan', 100)->nullable(); // Misal: Mahasiswa, Karyawan, ASN, Wiraswasta
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('telp_kantor', 30)->nullable();
            $table->string('email_kantor')->nullable();

            $table->timestamps();
        });

        // 2. Permohonan Sertifikasi (FR-APL-01)
        Schema::create('assessment_applications', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('assessment_schedules')->nullOnDelete();
            
            // Tujuan Asesmen sesuai FR-APL-01 Bagian 2
            $table->string('tujuan_asesmen', 80)->default('Sertifikasi Baru'); 
            // Sertifikasi Baru, Sertifikasi Ulang (RCC), Pengakuan Kompetensi Terkini (PKT), Rekognisi Pembelajaran Lampau (RPL), Lainnya

            // Status Pendaftaran & Verifikasi APL-01
            $table->string('status', 30)->default('draft'); // draft, diajukan, revisi, diterima, ditolak
            $table->text('catatan_revisi')->nullable();

            // Bagian Pengesahan & Rekomendasi Verifikator LSP
            $table->string('rekomendasi_admin', 100)->nullable(); // Diterima sebagai peserta sertifikasi / Tidak diterima
            $table->text('catatan_admin')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admin_verified_at')->nullable();

            // Tanda Tangan & Konfirmasi Asesi
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->text('ttd_asesi')->nullable();
            $table->timestamp('tgl_ttd_asesi')->nullable();
            $table->text('ttd_admin')->nullable();

            $table->timestamps();
        });

        // 3. Dokumen Persyaratan Permohonan (FR-APL-01 Bagian 3: Bukti Kelengkapan)
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('assessment_applications')->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->constrained('scheme_requirements')->nullOnDelete();
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable(); // Bytes
            $table->string('file_type', 50)->nullable(); // pdf, jpg, png, etc.

            // Status Verifikasi Berkas oleh Admin
            $table->string('status_verifikasi', 30)->default('pending'); // pending, memenuhi, tidak_memenuhi
            $table->text('catatan')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('assessment_applications');
        Schema::dropIfExists('asesi_profiles');
    }
};
