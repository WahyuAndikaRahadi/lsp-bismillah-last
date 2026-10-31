<?php

namespace Database\Seeders;

use App\Enums\ScheduleStatus;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\Tuk;
use Illuminate\Database\Seeder;

class EventScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemeJwd = CertificationScheme::where('kode_skema', 'SKM-JWD-01')->first();
        $schemeNa = CertificationScheme::where('kode_skema', 'SKM-NA-02')->first();
        $tukKampus = Tuk::where('kode_tuk', 'TUK-001')->first();
        $tukSurabaya = Tuk::where('kode_tuk', 'TUK-002')->first();

        // 1. Event PSKK BNSP
        $eventPskk = Event::firstOrCreate(
            ['kode_event' => 'EVT-2026-PSKK'],
            [
                'nama_event' => 'Program Sertifikasi Kompetensi Kerja (PSKK) BNSP 2026',
                'tahun_anggaran' => 2026,
                'sumber_anggaran' => 'BNSP / PSKK',
                'tgl_mulai' => '2026-11-01',
                'tgl_selesai' => '2026-12-15',
                'keterangan' => 'Program subsidi uji kompetensi resmi dari Badan Nasional Sertifikasi Profesi (BNSP) untuk angkatan kerja muda.',
                'is_active' => true,
            ]
        );

        if ($schemeJwd && $tukKampus) {
            AssessmentSchedule::firstOrCreate(
                ['kode_jadwal' => 'JDW-2026-001'],
                [
                    'event_id' => $eventPskk->id,
                    'scheme_id' => $schemeJwd->id,
                    'tuk_id' => $tukKampus->id,
                    'nama_jadwal' => 'Uji Kompetensi Junior Web Developer - Gelombang I Jakarta',
                    'tgl_pra_asesmen' => '2026-11-10',
                    'tgl_asesmen' => '2026-11-14',
                    'tgl_selesai' => '2026-11-14',
                    'tgl_buka_daftar' => '2026-11-01',
                    'tgl_tutup_daftar' => '2026-11-09',
                    'kuota_peserta' => 20,
                    'id_jadwal_bnsp' => 'JDW-BNSP-2026-0041',
                    'status' => ScheduleStatus::Pendaftaran,
                    'keterangan' => 'Pelaksanaan asesmen di Lab 1 & 2 Kampus Utama. Peserta membawa laptop dan kartu identitas asli.',
                ]
            );
        }

        if ($schemeNa && $tukSurabaya) {
            AssessmentSchedule::firstOrCreate(
                ['kode_jadwal' => 'JDW-2026-002'],
                [
                    'event_id' => $eventPskk->id,
                    'scheme_id' => $schemeNa->id,
                    'tuk_id' => $tukSurabaya->id,
                    'nama_jadwal' => 'Uji Kompetensi Network Administrator - Gelombang I Surabaya',
                    'tgl_pra_asesmen' => '2026-11-17',
                    'tgl_asesmen' => '2026-11-21',
                    'tgl_selesai' => '2026-11-21',
                    'tgl_buka_daftar' => '2026-11-05',
                    'tgl_tutup_daftar' => '2026-11-16',
                    'kuota_peserta' => 15,
                    'id_jadwal_bnsp' => 'JDW-BNSP-2026-0042',
                    'status' => ScheduleStatus::Direncanakan,
                    'keterangan' => 'Pelaksanaan uji praktek konfigurasi routing MikroTik dan Cisco pada TUK Mandiri Surabaya.',
                ]
            );
        }

        // 2. Event Mandiri
        $eventMandiri = Event::firstOrCreate(
            ['kode_event' => 'EVT-2026-MDR'],
            [
                'nama_event' => 'Sertifikasi Reguler Mandiri Tenaga Profesional IT Q4 2026',
                'tahun_anggaran' => 2026,
                'sumber_anggaran' => 'Mandiri',
                'tgl_mulai' => '2026-11-15',
                'tgl_selesai' => '2026-12-30',
                'keterangan' => 'Skema pembiayaan mandiri perseorangan atau korporasi.',
                'is_active' => true,
            ]
        );

        if ($schemeJwd && $tukSurabaya) {
            AssessmentSchedule::firstOrCreate(
                ['kode_jadwal' => 'JDW-2026-003'],
                [
                    'event_id' => $eventMandiri->id,
                    'scheme_id' => $schemeJwd->id,
                    'tuk_id' => $tukSurabaya->id,
                    'nama_jadwal' => 'Sertifikasi Reguler Web Developer Mandiri Jawa Timur',
                    'tgl_pra_asesmen' => '2026-11-24',
                    'tgl_asesmen' => '2026-11-28',
                    'tgl_selesai' => '2026-11-28',
                    'tgl_buka_daftar' => '2026-11-10',
                    'tgl_tutup_daftar' => '2026-11-23',
                    'kuota_peserta' => 25,
                    'id_jadwal_bnsp' => 'JDW-BNSP-2026-0078',
                    'status' => ScheduleStatus::Pendaftaran,
                    'keterangan' => 'Pendaftaran terbuka untuk umum dan industri di Jawa Timur.',
                ]
            );
        }
    }
}
