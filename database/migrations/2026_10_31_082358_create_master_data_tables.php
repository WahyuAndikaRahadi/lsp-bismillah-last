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
        // 1. Profil LSP
        Schema::create('lsp_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lsp');
            $table->string('kode_lsp')->nullable();
            $table->string('no_lisensi_bnsp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('nama_ketua')->nullable();
            $table->string('no_sk_lisensi')->nullable();
            $table->date('masa_berlaku_lisensi')->nullable();
            $table->string('logo')->nullable();
            $table->string('ttd_ketua')->nullable();
            $table->text('kop_surat')->nullable();
            $table->timestamps();
        });

        // 2. Standar Kompetensi (SKKNI / Khusus / Internasional)
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('kode_standar')->unique();
            $table->string('judul_standar');
            $table->string('jenis_standar', 50)->default('SKKNI');
            $table->string('nomor_sk')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Skema Sertifikasi
        Schema::create('certification_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->nullable()->constrained('standards')->nullOnDelete();
            $table->string('kode_skema')->unique();
            $table->string('nama_skema');
            $table->string('jenis_skema', 50)->default('Okupasi'); // KKNI, Okupasi, Klaster
            $table->string('kategori')->nullable();
            $table->decimal('biaya', 14, 2)->default(0);
            $table->text('ringkasan')->nullable();
            $table->string('dokumen_skema')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Persyaratan Dokumen Skema
        Schema::create('scheme_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->string('nama_dokumen');
            $table->boolean('is_required')->default(true);
            $table->string('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 5. Unit Kompetensi
        Schema::create('competency_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->string('kode_unit');
            $table->string('judul_unit');
            $table->string('jenis_standar', 50)->default('SKKNI');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 6. Elemen Kompetensi
        Schema::create('competency_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('competency_units')->cascadeOnDelete();
            $table->unsignedInteger('nomor_elemen')->default(1);
            $table->text('nama_elemen');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 7. Kriteria Unjuk Kerja (KUK)
        Schema::create('work_key_criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained('competency_elements')->cascadeOnDelete();
            $table->string('nomor_kuk', 20); // e.g. "1.1", "1.2"
            $table->text('pernyataan');
            $table->text('pertanyaan_observasi')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 8. Master Tempat Uji Kompetensi (TUK)
        Schema::create('tuks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tuk', 50)->unique();
            $table->string('nama_tuk');
            $table->string('jenis_tuk', 50)->default('Sewaktu'); // Sewaktu, Mandiri, Tempat Kerja
            $table->text('alamat')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('pic_phone', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Profil Asesor
        Schema::create('asesor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('no_met', 50)->nullable()->unique();
            $table->string('no_blangko', 50)->nullable();
            $table->string('no_sertifikat', 100)->nullable();
            $table->date('masa_berlaku')->nullable();
            $table->string('bidang_keahlian')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Pivot Asesor & Skema
        Schema::create('asesor_scheme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesor_profile_id')->constrained('asesor_profiles')->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesor_scheme');
        Schema::dropIfExists('asesor_profiles');
        Schema::dropIfExists('tuks');
        Schema::dropIfExists('work_key_criterias');
        Schema::dropIfExists('competency_elements');
        Schema::dropIfExists('competency_units');
        Schema::dropIfExists('scheme_requirements');
        Schema::dropIfExists('certification_schemes');
        Schema::dropIfExists('standards');
        Schema::dropIfExists('lsp_profiles');
    }
};
