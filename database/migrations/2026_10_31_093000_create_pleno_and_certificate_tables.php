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
        // 1. Rapat Pleno Penetapan Hasil Asesmen (Admin Pleno)
        Schema::create('pleno_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->unique()->constrained('assessment_schedules')->cascadeOnDelete();
            $table->string('nomor_ba_pleno', 100)->unique();
            $table->date('tanggal_pleno');
            $table->string('waktu_mulai', 20)->default('09:00 WIB');
            $table->string('waktu_selesai', 20)->default('12:00 WIB');
            $table->string('tempat');
            $table->string('ketua_komite');
            $table->text('anggota_komite')->nullable(); // JSON atau string daftar nama anggota pleno
            $table->text('catatan_pleno')->nullable();
            $table->string('status', 30)->default('disahkan'); // draft, disahkan
            $table->text('ttd_ketua')->nullable();
            $table->timestamps();
        });

        // 2. Tambahkan field kelulusan & sertifikat resmi BNSP pada assessment_applications
        Schema::table('assessment_applications', function (Blueprint $table) {
            $table->string('status_kelulusan', 30)->nullable()->after('rekomendasi_admin'); // kompeten, belum_kompeten
            $table->string('no_sertifikat', 100)->nullable()->after('status_kelulusan');
            $table->date('tgl_terbit_sertifikat')->nullable()->after('no_sertifikat');
            $table->date('masa_berlaku_sertifikat')->nullable()->after('tgl_terbit_sertifikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_applications', function (Blueprint $table) {
            $table->dropColumn(['status_kelulusan', 'no_sertifikat', 'tgl_terbit_sertifikat', 'masa_berlaku_sertifikat']);
        });

        Schema::dropIfExists('pleno_meetings');
    }
};
