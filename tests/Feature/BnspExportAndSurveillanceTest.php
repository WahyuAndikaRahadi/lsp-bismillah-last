<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ScheduleStatus;
use App\Models\Ak02Assessment;
use App\Models\AsesiProfile;
use App\Models\AsesorProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\SurveillanceRecord;
use App\Models\Tuk;
use App\Models\User;
use App\Services\BnspExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BnspExportAndSurveillanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected AssessmentSchedule $schedule;

    protected AssessmentApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin LSP']);
        $this->asesor = User::factory()->create(['role' => 'asesor', 'name' => 'Ir. Hartono, M.T']);
        $this->asesi = User::factory()->create(['role' => 'asesi', 'name' => 'Budi Santoso']);

        AsesorProfile::create([
            'user_id' => $this->asesor->id,
            'no_met' => 'MET.000.001234.2024',
            'no_sertifikat' => 'NO-SRT-2024-99',
            'masa_berlaku' => now()->addYears(2),
            'bidang_keahlian' => 'Teknologi Informasi',
        ]);

        AsesiProfile::create([
            'user_id' => $this->asesi->id,
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '3271012345670001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-05-12',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Tebet Barat No. 10',
            'telepon' => '081234567890',
            'pendidikan_terakhir' => 'S1 Teknik Informatika',
            'nama_perusahaan' => 'PT Teknologi Maju',
            'jabatan' => 'Software Engineer',
        ]);

        $scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-TI-001',
            'nama_skema' => 'Pemrograman Web Madya',
            'jenis_skema' => 'Klaster',
            'standar' => 'SKKNI',
            'is_active' => true,
        ]);

        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-JKT-01',
            'nama_tuk' => 'TUK Lab Komputer Cyber',
            'jenis_tuk' => 'Sewaktu',
            'penanggung_jawab' => 'Rudy Kurniawan',
            'is_active' => true,
        ]);

        $event = Event::create([
            'kode_event' => 'EVT-2026-001',
            'nama_event' => 'Sertifikasi Nasional TI',
            'tahun_anggaran' => 2026,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $scheme->id,
            'tuk_id' => $tuk->id,
            'kode_jadwal' => 'JDW-2026-001',
            'nama_jadwal' => 'Uji Web Madya Jakarta',
            'tgl_asesmen' => now()->toDateString(),
            'jam_mulai' => '08:30',
            'kuota_peserta' => 10,
            'status' => ScheduleStatus::Selesai,
        ]);

        $this->application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-2026-0001',
            'user_id' => $this->asesi->id,
            'scheme_id' => $scheme->id,
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diterima,
            'status_kelulusan' => 'kompeten',
            'no_sertifikat' => 'BNSP-SKM-TI-001-2026-0001',
            'tgl_terbit_sertifikat' => now()->toDateString(),
            'masa_berlaku_sertifikat' => now()->addYears(3)->toDateString(),
        ]);

        Ak02Assessment::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'rekomendasi_akhir' => 'K',
        ]);
    }

    public function test_admin_bnsp_export_interface_is_unavailable(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/exports/bnsp');
        $response->assertNotFound();
    }

    public function test_bnsp_export_service_generates_exact_16_columns(): void
    {
        $service = new BnspExportService;
        $rows = $service->formatRows([$this->application]);

        $this->assertCount(1, $rows);
        $row = $rows[0];

        $this->assertEquals(1, $row['NO']);
        $this->assertEquals('3271012345670001', $row['NIK']);
        $this->assertEquals('REG-2026-0001', $row['NO_PESERTA']);
        $this->assertEquals('Budi Santoso', $row['NAMA_LENGKAP']);
        $this->assertEquals('Jakarta', $row['TEMPAT_LAHIR']);
        $this->assertEquals('1995-05-12', $row['TANGGAL_LAHIR']);
        $this->assertEquals('L', $row['JENIS_KELAMIN']);
        $this->assertEquals('S1 Teknik Informatika', $row['PENDIDIKAN_TERAKHIR']);
        $this->assertEquals('SKM-TI-001', $row['KODE_SKEMA']);
        $this->assertEquals('Pemrograman Web Madya', $row['NAMA_SKEMA']);
        $this->assertEquals('TUK-JKT-01', $row['KODE_TUK']);
        $this->assertEquals('TUK Lab Komputer Cyber', $row['NAMA_TUK']);
        $this->assertEquals('MET.000.001234.2024', $row['NO_REG_ASESOR']);
        $this->assertEquals('Ir. Hartono, M.T', $row['NAMA_ASESOR']);
        $this->assertEquals('K', $row['HASIL_ASESMEN']);
        $this->assertEquals(now()->format('Y-m-d'), $row['TANGGAL_ASESMEN']);
    }

    public function test_retired_bnsp_export_routes_are_unavailable(): void
    {
        $this->actingAs($this->admin)->get('/admin/exports/bnsp')->assertNotFound();
        $this->actingAs($this->admin)->get('/admin/exports/bnsp/download')->assertNotFound();
    }

    public function test_retired_complaint_routes_are_unavailable(): void
    {
        $this->get('/pengaduan/create')->assertNotFound();
        $this->get('/pengaduan/tracking')->assertNotFound();
        $this->actingAs($this->admin)->get('/admin/complaints')->assertNotFound();
    }

    public function test_retired_unauthenticated_public_surveillance_is_unavailable(): void
    {
        $this->get('/surveillance')->assertNotFound();
        $this->get('/surveillance/create')->assertNotFound();
    }
}
