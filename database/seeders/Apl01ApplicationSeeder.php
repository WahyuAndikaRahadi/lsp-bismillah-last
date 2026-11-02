<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\UserRole;
use App\Models\ApplicationDocument;
use App\Models\AsesiProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Apl01ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin)->first();
        $asesi1 = User::where('email', 'asesi@lsp.localhost')->first();
        $scheme = CertificationScheme::with('requirements')->first();
        $schedule = AssessmentSchedule::first();

        if (! $asesi1 || ! $scheme) {
            return;
        }

        // 1. Profil Asesi 1 (Aditya)
        $profile1 = AsesiProfile::firstOrCreate(
            ['user_id' => $asesi1->id],
            [
                'nama_lengkap' => $asesi1->name,
                'nik' => $asesi1->nik,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1996-05-12',
                'jenis_kelamin' => 'L',
                'kebangsaan' => 'Indonesia',
                'alamat' => 'Jl. Tebet Timur Dalam Raya No. 45',
                'rt_rw' => '003/005',
                'provinsi_kode' => '31',
                'provinsi_nama' => 'DKI JAKARTA',
                'kota_kode' => '3171',
                'kota_nama' => 'KOTA JAKARTA SELATAN',
                'kecamatan_kode' => '317101',
                'kecamatan_nama' => 'TEBET',
                'kelurahan_kode' => '3171011001',
                'kelurahan_nama' => 'TEBET TIMUR',
                'kode_pos' => '12820',
                'telepon' => $asesi1->phone,
                'pendidikan_terakhir' => 'D4 / S1',
                'pekerjaan' => 'Software Engineer',
                'nama_perusahaan' => 'PT Solusi Teknologi Indonesia',
                'jabatan' => 'Junior Web Developer',
                'alamat_kantor' => 'Gedung Cyber Lt. 5, Jl. Kuningan Barat',
                'telp_kantor' => '021-52960000',
                'email_kantor' => 'aditya@solusitech.co.id',
            ]
        );

        // Pastikan dummy sample file ada di storage
        Storage::disk('public')->put('documents/applications/REG-202610-0001/ktp_aditya.pdf', '%PDF-1.4 Sample KTP Document PDF');
        Storage::disk('public')->put('documents/applications/REG-202610-0001/ijazah_aditya.pdf', '%PDF-1.4 Sample Ijazah Document PDF');
        Storage::disk('public')->put('documents/applications/REG-202610-0001/cv_portofolio.pdf', '%PDF-1.4 Sample CV Portofolio PDF');

        // 2. Permohonan Asesi 1: Status Diajukan (Menunggu Verifikasi)
        $app1 = AssessmentApplication::firstOrCreate(
            ['nomor_pendaftaran' => 'REG-202610-0001'],
            [
                'user_id' => $asesi1->id,
                'scheme_id' => $scheme->id,
                'schedule_id' => $schedule?->id,
                'tujuan_asesmen' => 'Sertifikasi Baru',
                'status' => ApplicationStatus::Diajukan,
                'tgl_pengajuan' => now()->subHours(4),
                'ttd_asesi' => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80"><path d="M10 50 Q 50 10 90 50 T 170 50" fill="none" stroke="black" stroke-width="2"/></svg>',
                'tgl_ttd_asesi' => now()->subHours(4),
            ]
        );

        // Dokumen Permohonan 1
        foreach ($scheme->requirements as $idx => $req) {
            $sampleName = 'sample_dokumen_'.($idx + 1).'.pdf';
            ApplicationDocument::firstOrCreate(
                [
                    'application_id' => $app1->id,
                    'requirement_id' => $req->id,
                ],
                [
                    'nama_dokumen' => $req->nama_dokumen,
                    'file_path' => "documents/applications/REG-202610-0001/{$sampleName}",
                    'file_name' => "aditya_{$sampleName}",
                    'file_size' => 1024 * 350, // 350 KB
                    'file_type' => 'pdf',
                    'status_verifikasi' => DocumentVerificationStatus::Pending,
                ]
            );
        }

        // 3. Buat Asesi 2: Rina Nuraini (Status: Revisi)
        $asesi2 = User::firstOrCreate(
            ['email' => 'rina@lsp.localhost'],
            [
                'name' => 'Rina Nuraini, S.Kom.',
                'role' => UserRole::Asesi,
                'nik' => '3273014508980003',
                'phone' => '081398765432',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        AsesiProfile::firstOrCreate(
            ['user_id' => $asesi2->id],
            [
                'nama_lengkap' => $asesi2->name,
                'nik' => $asesi2->nik,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1998-08-15',
                'jenis_kelamin' => 'P',
                'kebangsaan' => 'Indonesia',
                'alamat' => 'Jl. Dago Atas No. 88',
                'rt_rw' => '002/004',
                'provinsi_kode' => '32',
                'provinsi_nama' => 'JAWA BARAT',
                'kota_kode' => '3273',
                'kota_nama' => 'KOTA BANDUNG',
                'kecamatan_kode' => '327301',
                'kecamatan_nama' => 'COBLONG',
                'kelurahan_kode' => '3273011001',
                'kelurahan_nama' => 'DAGO',
                'kode_pos' => '40135',
                'telepon' => $asesi2->phone,
                'pendidikan_terakhir' => 'D4 / S1',
                'pekerjaan' => 'Front End Developer',
                'nama_perusahaan' => 'CV Kreatif Digital Bandung',
                'jabatan' => 'UI/UX Developer',
            ]
        );

        $app2 = AssessmentApplication::firstOrCreate(
            ['nomor_pendaftaran' => 'REG-202610-0002'],
            [
                'user_id' => $asesi2->id,
                'scheme_id' => $scheme->id,
                'schedule_id' => $schedule?->id,
                'tujuan_asesmen' => 'Sertifikasi Baru',
                'status' => ApplicationStatus::Revisi,
                'catatan_revisi' => 'Mohon unggah ulang scan Ijazah asli yang tidak terpotong (halaman belakang belum terlihat) dan pas foto resmi berwarna terbaru.',
                'tgl_pengajuan' => now()->subDays(1),
                'ttd_asesi' => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80"><path d="M10 40 Q 60 5 110 40 T 180 40" fill="none" stroke="black" stroke-width="2"/></svg>',
                'tgl_ttd_asesi' => now()->subDays(1),
                'admin_id' => $admin?->id,
                'admin_verified_at' => now()->subHours(6),
            ]
        );

        foreach ($scheme->requirements as $idx => $req) {
            $isInvalid = ($idx === 1); // Mark second requirement as invalid
            ApplicationDocument::firstOrCreate(
                [
                    'application_id' => $app2->id,
                    'requirement_id' => $req->id,
                ],
                [
                    'nama_dokumen' => $req->nama_dokumen,
                    'file_path' => "documents/applications/REG-202610-0002/rina_doc_{$idx}.pdf",
                    'file_name' => "rina_{$req->nama_dokumen}.pdf",
                    'file_size' => 1024 * 420,
                    'file_type' => 'pdf',
                    'status_verifikasi' => $isInvalid ? DocumentVerificationStatus::TidakMemenuhi : DocumentVerificationStatus::Memenuhi,
                    'catatan' => $isInvalid ? 'Scan buram dan bagian cap universitas terpotong. Mohon perbaiki.' : null,
                    'verified_by' => $admin?->id,
                    'verified_at' => now()->subHours(6),
                ]
            );
        }

        // 4. Buat Asesi 3: Budi Santoso (Status: Diterima & Belum di-plot jadwal untuk test modal plotting)
        $asesi3 = User::firstOrCreate(
            ['email' => 'budi@lsp.localhost'],
            [
                'name' => 'Budi Santoso, S.T.',
                'role' => UserRole::Asesi,
                'nik' => '3578011204920005',
                'phone' => '081211223344',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        AsesiProfile::firstOrCreate(
            ['user_id' => $asesi3->id],
            [
                'nama_lengkap' => $asesi3->name,
                'nik' => $asesi3->nik,
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1992-04-12',
                'jenis_kelamin' => 'L',
                'kebangsaan' => 'Indonesia',
                'alamat' => 'Jl. Gubeng Kertajaya IX No. 15',
                'provinsi_kode' => '35',
                'provinsi_nama' => 'JAWA TIMUR',
                'kota_kode' => '3578',
                'kota_nama' => 'KOTA SURABAYA',
                'kecamatan_kode' => '357801',
                'kecamatan_nama' => 'GUBENG',
                'kode_pos' => '60281',
                'telepon' => $asesi3->phone,
                'pendidikan_terakhir' => 'D4 / S1',
                'pekerjaan' => 'IT Consultant',
            ]
        );

        $app3 = AssessmentApplication::firstOrCreate(
            ['nomor_pendaftaran' => 'REG-202610-0003'],
            [
                'user_id' => $asesi3->id,
                'scheme_id' => $scheme->id,
                'schedule_id' => null, // Belum di-plot jadwal
                'tujuan_asesmen' => 'Sertifikasi Baru',
                'status' => ApplicationStatus::Diterima,
                'rekomendasi_admin' => 'Diterima sebagai peserta sertifikasi',
                'tgl_pengajuan' => now()->subDays(2),
                'ttd_asesi' => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80"><path d="M10 60 Q 50 15 100 60 T 170 60" fill="none" stroke="black" stroke-width="2"/></svg>',
                'tgl_ttd_asesi' => now()->subDays(2),
                'admin_id' => $admin?->id,
                'admin_verified_at' => now()->subDay(),
            ]
        );

        foreach ($scheme->requirements as $idx => $req) {
            ApplicationDocument::firstOrCreate(
                [
                    'application_id' => $app3->id,
                    'requirement_id' => $req->id,
                ],
                [
                    'nama_dokumen' => $req->nama_dokumen,
                    'file_path' => "documents/applications/REG-202610-0003/budi_doc_{$idx}.pdf",
                    'file_name' => "budi_{$req->nama_dokumen}.pdf",
                    'file_size' => 1024 * 510,
                    'file_type' => 'pdf',
                    'status_verifikasi' => DocumentVerificationStatus::Memenuhi,
                    'verified_by' => $admin?->id,
                    'verified_at' => now()->subDay(),
                ]
            );
        }
    }
}
