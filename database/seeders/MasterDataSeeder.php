<?php

namespace Database\Seeders;

use App\Models\AsesorProfile;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\LegacyCompetencyUnit as CompetencyUnit;
use App\Models\LspProfile;
use App\Models\SchemeRequirement;
use App\Models\Standard;
use App\Models\Tuk;
use App\Models\User;
use App\Models\WorkKeyCriteria;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Profil Lembaga LSP
        $lsp = LspProfile::firstOrCreate(
            ['nama_lsp' => 'Lembaga Sertifikasi Profesi Teknologi Informasi & Komunikasi (LSP TIK)'],
            [
                'kode_lsp' => 'LSP-021-ID',
                'no_lisensi_bnsp' => 'BNSP-LSP-021-ID',
                'alamat' => 'Gedung Pusat Sertifikasi Profesi Lt. 3, Jl. Merdeka Barat No. 45, Jakarta Pusat',
                'telepon' => '021-5551234',
                'email' => 'info@lsp-tik.id',
                'website' => 'https://lsp-tik.id',
                'nama_ketua' => 'Dr. Ir. Hendra Gunawan, M.Kom.',
                'no_sk_lisensi' => 'KEP.0123/BNSP/III/2024',
                'masa_berlaku_lisensi' => '2029-03-15',
                'kop_surat' => "LEMBAGA SERTIFIKASI PROFESI TEKNOLOGI INFORMASI & KOMUNIKASI\nLisensi BNSP: BNSP-LSP-021-ID\nJl. Merdeka Barat No. 45, Jakarta Pusat | Telp: 021-5551234",
            ]
        );

        // 2. Standar Acuan (SKKNI)
        $skkniDev = Standard::firstOrCreate(
            ['kode_standar' => 'SKKNI No. 282 Tahun 2016'],
            [
                'judul_standar' => 'Kategori Informasi dan Komunikasi Golongan Pokok Aktivitas Pemrograman, Konsultasi Komputer Subbidang Pemrograman',
                'jenis_standar' => 'SKKNI',
                'nomor_sk' => 'KEP.282/MEN/IX/2016',
                'tahun' => 2016,
                'is_active' => true,
            ]
        );

        $skkniNet = Standard::firstOrCreate(
            ['kode_standar' => 'SKKNI No. 321 Tahun 2016'],
            [
                'judul_standar' => 'Bidang Keahlian Jaringan Komputer dan Sistem Administrasi Server',
                'jenis_standar' => 'SKKNI',
                'nomor_sk' => 'KEP.321/MEN/X/2016',
                'tahun' => 2016,
                'is_active' => true,
            ]
        );

        // 3. Skema 1: Junior Web Developer
        $schemeJwd = CertificationScheme::firstOrCreate(
            ['kode_skema' => 'SKM-JWD-01'],
            [
                'standard_id' => $skkniDev->id,
                'nama_skema' => 'Junior Web Developer',
                'jenis_skema' => 'Okupasi',
                'kategori' => 'Teknologi Informasi',
                'biaya' => 850000,
                'ringkasan' => 'Skema sertifikasi okupasi nasional bagi pengembang web tingkat junior yang mencakup perancangan struktur data, pengkodean logika server-side, antarmuka web interaktif, dan pengujian program.',
                'is_active' => true,
            ]
        );

        // Syarat Dokumen JWD
        $jwdRequirements = [
            ['nama_dokumen' => 'Kartu Tanda Penduduk (KTP)', 'is_required' => true, 'keterangan' => 'Scan KTP asli berwarna', 'urutan' => 1],
            ['nama_dokumen' => 'Ijazah Terakhir Minimal D3/S1 / Surat Keterangan Aktif Kuliah', 'is_required' => true, 'keterangan' => 'Bidang Informatika / Ilmu Komputer / Teknik Terkait', 'urutan' => 2],
            ['nama_dokumen' => 'Transkrip Nilai atau Sertifikat Pelatihan Pemrograman Web', 'is_required' => true, 'keterangan' => 'Memuat mata kuliah atau materi pemrograman web dasar', 'urutan' => 3],
            ['nama_dokumen' => 'Portofolio Proyek Aplikasi Web Mandiri', 'is_required' => false, 'keterangan' => 'Tautan repository GitHub atau tangkapan layar website yang pernah dibuat', 'urutan' => 4],
            ['nama_dokumen' => 'Pas Foto 3x4 Background Merah', 'is_required' => true, 'keterangan' => 'Format JPG/PNG untuk pencetakan blangko sertifikat BNSP', 'urutan' => 5],
        ];

        foreach ($jwdRequirements as $req) {
            SchemeRequirement::firstOrCreate(
                ['scheme_id' => $schemeJwd->id, 'nama_dokumen' => $req['nama_dokumen']],
                $req
            );
        }

        // Unit Kompetensi JWD
        $unit1 = CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeJwd->id, 'kode_unit' => 'J.620100.004.01'],
            [
                'judul_unit' => 'Menggunakan Struktur Data',
                'jenis_standar' => 'SKKNI',
                'urutan' => 1,
            ]
        );

        // Elemen & KUK Unit 1
        $elem1_1 = CompetencyElement::firstOrCreate(
            ['unit_id' => $unit1->id, 'nomor_elemen' => 1],
            ['nama_elemen' => 'Mengidentifikasi konsep dan struktur data yang dibutuhkan', 'urutan' => 1]
        );
        WorkKeyCriteria::firstOrCreate(
            ['element_id' => $elem1_1->id, 'nomor_kuk' => '1.1'],
            [
                'pernyataan' => 'Konsep struktur data diidentifikasi sesuai dengan kebutuhan pemrosesan data algoritma program.',
                'pertanyaan_observasi' => 'Dapatkah Anda menjelaskan perbedaan mendasar antara Array dan Linked List dalam konteks penggunaan memori?',
                'urutan' => 1,
            ]
        );
        WorkKeyCriteria::firstOrCreate(
            ['element_id' => $elem1_1->id, 'nomor_kuk' => '1.2'],
            [
                'pernyataan' => 'Alternatif struktur data dibandingkan kelebihan dan kekurangannya untuk skenario implementasi tertentu.',
                'pertanyaan_observasi' => 'Bagaimana Anda memilih struktur data terbaik untuk pencarian data yang sering dibaca dibandingkan data yang sering ditulis?',
                'urutan' => 2,
            ]
        );

        $elem1_2 = CompetencyElement::firstOrCreate(
            ['unit_id' => $unit1->id, 'nomor_elemen' => 2],
            ['nama_elemen' => 'Menerapkan struktur data dalam kode program', 'urutan' => 2]
        );
        WorkKeyCriteria::firstOrCreate(
            ['element_id' => $elem1_2->id, 'nomor_kuk' => '2.1'],
            [
                'pernyataan' => 'Struktur data yang dipilih diimplementasikan ke dalam bahasa pemrograman sesuai spesifikasi algoritma.',
                'pertanyaan_observasi' => 'Tunjukkan implementasi array asosiatif / hash table dalam modul kode yang Anda susun!',
                'urutan' => 1,
            ]
        );

        $unit2 = CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeJwd->id, 'kode_unit' => 'J.620100.005.01'],
            [
                'judul_unit' => 'Mengimplementasikan Algoritma Pemrograman',
                'jenis_standar' => 'SKKNI',
                'urutan' => 2,
            ]
        );
        $elem2_1 = CompetencyElement::firstOrCreate(
            ['unit_id' => $unit2->id, 'nomor_elemen' => 1],
            ['nama_elemen' => 'Menentukan varian algoritma pemrograman', 'urutan' => 1]
        );
        WorkKeyCriteria::firstOrCreate(
            ['element_id' => $elem2_1->id, 'nomor_kuk' => '1.1'],
            [
                'pernyataan' => 'Kompleksitas algoritma dianalisis untuk menentukan efisiensi waktu eksekusi kode.',
                'pertanyaan_observasi' => 'Bagaimana Anda memastikan loop perulangan tidak menyebabkan overhead komputasi berlebih?',
                'urutan' => 1,
            ]
        );

        $unit3 = CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeJwd->id, 'kode_unit' => 'J.620100.016.01'],
            [
                'judul_unit' => 'Menulis Kode dengan Prinsip Sesuai Guidelines dan Best Practices',
                'jenis_standar' => 'SKKNI',
                'urutan' => 3,
            ]
        );
        $elem3_1 = CompetencyElement::firstOrCreate(
            ['unit_id' => $unit3->id, 'nomor_elemen' => 1],
            ['nama_elemen' => 'Menerapkan coding convention standar industri', 'urutan' => 1]
        );
        WorkKeyCriteria::firstOrCreate(
            ['element_id' => $elem3_1->id, 'nomor_kuk' => '1.1'],
            [
                'pernyataan' => 'Penamaan variabel, fungsi, dan kelas mengikuti aturan standar arsitektur clean code.',
                'pertanyaan_observasi' => 'Jelaskan kaidah PSR / naming convention yang Anda terapkan pada proyek web ini!',
                'urutan' => 1,
            ]
        );

        $unit4 = CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeJwd->id, 'kode_unit' => 'J.620100.017.01'],
            [
                'judul_unit' => 'Mengimplementasikan Pemrograman Berorientasi Objek',
                'jenis_standar' => 'SKKNI',
                'urutan' => 4,
            ]
        );

        $unit5 = CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeJwd->id, 'kode_unit' => 'J.620100.023.01'],
            [
                'judul_unit' => 'Membuat Dokumen Petunjuk Pengoperasian Program',
                'jenis_standar' => 'SKKNI',
                'urutan' => 5,
            ]
        );

        // Skema 2: Network Administrator
        $schemeNa = CertificationScheme::firstOrCreate(
            ['kode_skema' => 'SKM-NA-02'],
            [
                'standard_id' => $skkniNet->id,
                'nama_skema' => 'Network Administrator',
                'jenis_skema' => 'Okupasi',
                'kategori' => 'Jaringan Komputer',
                'biaya' => 950000,
                'ringkasan' => 'Skema kualifikasi administrator jaringan komputer mencakup instalasi switch & router, subnetting IP, konfigurasi firewall, dan monitoring performa traffic.',
                'is_active' => true,
            ]
        );

        SchemeRequirement::firstOrCreate(
            ['scheme_id' => $schemeNa->id, 'nama_dokumen' => 'Kartu Tanda Penduduk (KTP)'],
            ['is_required' => true, 'keterangan' => 'Scan KTP asli', 'urutan' => 1]
        );
        SchemeRequirement::firstOrCreate(
            ['scheme_id' => $schemeNa->id, 'nama_dokumen' => 'Ijazah Minimal SMK TKJ / D3 / S1 Komputer'],
            ['is_required' => true, 'keterangan' => 'Jurusan Teknik Komputer Jaringan atau sejenis', 'urutan' => 2]
        );

        CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeNa->id, 'kode_unit' => 'J.611000.001.01'],
            ['judul_unit' => 'Mengumpulkan Kebutuhan Teknis Pengguna Jaringan', 'jenis_standar' => 'SKKNI', 'urutan' => 1]
        );
        CompetencyUnit::firstOrCreate(
            ['scheme_id' => $schemeNa->id, 'kode_unit' => 'J.611000.008.01'],
            ['judul_unit' => 'Merancang Topologi Jaringan Komputer', 'jenis_standar' => 'SKKNI', 'urutan' => 2]
        );

        // 4. Master TUK (Tempat Uji Kompetensi)
        $tuk1 = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-001'],
            [
                'nama_tuk' => 'TUK Sewaktu Kampus Utama',
                'jenis_tuk' => 'Sewaktu',
                'alamat' => 'Kampus A Lt. 4, Jl. Salemba Raya No. 12, Jakarta Pusat',
                'telepon' => '021-3901234',
                'email' => 'tuk-kampus@lsp-tik.id',
                'penanggung_jawab' => 'Rustam Massikki, S.Kom.',
                'pic_phone' => '081298765432',
                'is_active' => true,
            ]
        );

        $tuk2 = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-002'],
            [
                'nama_tuk' => 'TUK Mandiri Lab Jaringan & Cloud',
                'jenis_tuk' => 'Mandiri',
                'alamat' => 'Kawasan Industri Rungkut Megah Blok B-12, Surabaya',
                'telepon' => '031-8765432',
                'email' => 'tuk-surabaya@lsp-tik.id',
                'penanggung_jawab' => 'Ir. Dewi Sartika, M.T.',
                'pic_phone' => '081345678901',
                'is_active' => true,
            ]
        );

        $tuk3 = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-003'],
            [
                'nama_tuk' => 'TUK Tempat Kerja PT Solusi Cyber Global',
                'jenis_tuk' => 'Tempat Kerja',
                'alamat' => 'Dago Cyber Park Lt. 2, Jl. Ir. H. Juanda No. 88, Bandung',
                'telepon' => '022-2509876',
                'email' => 'tuk-bandung@lsp-tik.id',
                'penanggung_jawab' => 'Ahmad Fauzi, S.T.',
                'pic_phone' => '081122334455',
                'is_active' => true,
            ]
        );

        // 5. Asesor Profile untuk User Asesor Default
        $asesorUser = User::where('email', 'asesor@lsp.localhost')->first();
        if ($asesorUser) {
            $asesorProfile = AsesorProfile::firstOrCreate(
                ['user_id' => $asesorUser->id],
                [
                    'no_met' => 'MET.000.003456 2021',
                    'no_blangko' => 'B-987654',
                    'no_sertifikat' => 'REG.TIK.021.0045',
                    'masa_berlaku' => '2028-12-31',
                    'bidang_keahlian' => 'Software Development & Rekayasa Web',
                    'is_active' => true,
                ]
            );

            // Hubungkan asesor dengan skema Junior Web Developer
            $asesorProfile->schemes()->syncWithoutDetaching([$schemeJwd->id]);
        }
    }
}
