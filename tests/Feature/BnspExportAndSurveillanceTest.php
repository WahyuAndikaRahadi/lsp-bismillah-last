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
use App\Models\Complaint;
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

    public function test_admin_can_view_bnsp_export_interface_and_preview(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exports.bnsp.index'));
        $response->assertStatus(200);
        $response->assertSee('Ekspor Data Format Sisfo BNSP');
        $response->assertSee('3271012345670001');
        $response->assertSee('Pemrograman Web Madya');
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

    public function test_admin_can_download_bnsp_spreadsheet_xls(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exports.bnsp.download'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.ms-excel');

        // Test download per schedule
        $scheduleResponse = $this->actingAs($this->admin)->get(route('admin.exports.bnsp.schedule', $this->schedule));
        $scheduleResponse->assertStatus(200);
        $scheduleResponse->assertHeader('content-type', 'application/vnd.ms-excel');
    }

    public function test_public_can_submit_complaint_and_receive_ticket(): void
    {
        $res = $this->get(route('pengaduan.create'));
        $res->assertStatus(200);
        $res->assertSee('Layanan Pengaduan');

        $postData = [
            'nama_pelapor' => 'Ahmad Fauzi',
            'telepon' => '081298765432',
            'email' => 'fauzi@example.com',
            'jenis_responden' => 'Asesi',
            'judul' => 'Kendala Server Uji TUK Lab',
            'isi_aduan' => 'Koneksi jaringan sempat terputus selama 15 menit saat simulasi.',
        ];

        $postRes = $this->post(route('pengaduan.store'), $postData);
        $postRes->assertRedirect(route('pengaduan.create'));
        $postRes->assertSessionHas('ticket_success');

        $this->assertDatabaseHas('complaints', [
            'nama_pelapor' => 'Ahmad Fauzi',
            'status' => 'diproses',
        ]);

        $complaint = Complaint::where('nama_pelapor', 'Ahmad Fauzi')->first();
        $this->assertStringStartsWith('TKT-', $complaint->ticket_number);

        // Tracking page
        $trackRes = $this->get(route('pengaduan.tracking', ['ticket' => $complaint->ticket_number]));
        $trackRes->assertStatus(200);
        $trackRes->assertSee($complaint->ticket_number);
        $trackRes->assertSee('Kendala Server Uji TUK Lab');
    }

    public function test_admin_can_manage_and_resolve_complaint(): void
    {
        $complaint = Complaint::create([
            'ticket_number' => 'TKT-20261031-099',
            'nama_pelapor' => 'Siti Rahma',
            'telepon' => '081122334455',
            'jenis_responden' => 'Umum',
            'judul' => 'Pertanyaan Jadwal Pelatihan',
            'isi_aduan' => 'Kapan skema multimedia dibuka kembali?',
            'status' => 'diproses',
        ]);

        $res = $this->actingAs($this->admin)->get(route('admin.complaints.index'));
        $res->assertStatus(200);
        $res->assertSee($complaint->ticket_number);

        // Admin updates status to selesai with tindak lanjut note
        $updateRes = $this->actingAs($this->admin)->patch(
            route('admin.complaints.update-status', $complaint),
            [
                'status' => 'selesai',
                'tindak_lanjut' => 'Jadwal sertifikasi multimedia akan dibuka pada kuartal pertama tahun depan. Informasi telah disampaikan via WhatsApp.',
            ]
        );

        $updateRes->assertRedirect(route('admin.complaints.show', $complaint));

        $complaint->refresh();
        $this->assertEquals('selesai', $complaint->status);
        $this->assertNotNull($complaint->tgl_tindak_lanjut);
        $this->assertEquals($this->admin->id, $complaint->petugas_id);
    }

    public function test_public_can_submit_surveillance_and_admin_verifies(): void
    {
        $res = $this->get(route('surveillance.create'));
        $res->assertStatus(200);
        $res->assertSee('Layanan Surveilans Pemegang Sertifikat');

        $postData = [
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '3271012345670001',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
            'no_sertifikat' => 'BNSP-SKM-TI-001-2026-0001',
            'tempat_kerja' => 'PT Solusi Teknologi',
            'jabatan' => 'Senior Backend Developer',
            'proyek_saat_ini' => 'Pengembangan Sistem ERP Nasional',
            'kesesuaian_skk' => 'Y',
            'keterangan' => 'Kompetensi aktif diterapkan.',
        ];

        $postRes = $this->post(route('surveillance.store'), $postData);
        $postRes->assertRedirect(route('surveillance.create'));
        $postRes->assertSessionHas('success');

        $this->assertDatabaseHas('surveillance_records', [
            'nik' => '3271012345670001',
            'no_sertifikat' => 'BNSP-SKM-TI-001-2026-0001',
            'status' => 'menunggu_verifikasi',
        ]);

        $record = SurveillanceRecord::where('no_sertifikat', 'BNSP-SKM-TI-001-2026-0001')->first();

        // Admin verifies surveillance
        $verifyRes = $this->actingAs($this->admin)->patch(
            route('admin.surveillance.verify', $record),
            [
                'status' => 'terverifikasi',
            ]
        );

        $verifyRes->assertRedirect();
        $record->refresh();
        $this->assertEquals('terverifikasi', $record->status);
        $this->assertEquals($this->admin->id, $record->verified_by);
        $this->assertNotNull($record->tgl_verifikasi);
    }
}
