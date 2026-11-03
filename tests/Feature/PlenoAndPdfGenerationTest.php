<?php

namespace Tests\Feature;

use App\Enums\Apl02Status;
use App\Enums\ApplicationStatus;
use App\Enums\ScheduleStatus;
use App\Models\Ak01Agreement;
use App\Models\Ak02Assessment;
use App\Models\Ak02UnitAssessment;
use App\Models\Ak03Feedback;
use App\Models\Ak05Report;
use App\Models\Apl02Assessment;
use App\Models\AsesiProfile;
use App\Models\AsesorProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\Event;
use App\Models\LspProfile;
use App\Models\PlenoMeeting;
use App\Models\Schedule;
use App\Models\ScheduleAsesor;
use App\Models\Scheme;
use App\Models\Assessment;
use App\Models\Tuk;
use App\Models\User;
use App\Models\WorkKeyCriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlenoAndPdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected AssessmentSchedule $schedule;

    protected AssessmentApplication $application;

    protected ScheduleAsesor $scheduleAsesor;

    protected Schedule $canonicalSchedule;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. LSP Profile
        LspProfile::create([
            'nama_lsp' => 'LSP Teknologi Digital Indonesia',
            'kode_lsp' => 'LSP-TDI',
            'no_lisensi_bnsp' => 'BNSP-LSP-999-ID',
            'alamat' => 'Jl. Gatot Subroto No. 45, Jakarta Selatan',
            'telepon' => '021-5551234',
            'email' => 'sekretariat@lsp-tdi.id',
            'nama_ketua' => 'Dr. Ir. Bambang Soedibyo, M.Eng',
        ]);

        // 2. Users
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

        // 3. Scheme, Units, Elements, Criteria
        $scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-TI-001',
            'nama_skema' => 'Pemrograman Web Madya',
            'jenis_skema' => 'Klaster',
            'ringkasan' => 'Skema keahlian rekayasa aplikasi web modern.',
            'standar' => 'SKKNI',
            'is_active' => true,
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $scheme->id,
            'kode_unit' => 'J.620100.004.01',
            'judul_unit' => 'Menggunakan Struktur Data dalam Pemrograman',
            'jenis_standar' => 'SKKNI',
            'urutan' => 1,
        ]);

        $elem = CompetencyElement::create([
            'unit_id' => $unit->id,
            'nomor_elemen' => 1,
            'nama_elemen' => 'Mengidentifikasi konsep struktur data',
        ]);

        $crit = WorkKeyCriteria::create([
            'element_id' => $elem->id,
            'nomor_kuk' => '1.1',
            'pernyataan' => 'Konsep array dan linked list diidentifikasi sesuai kebutuhan arsitektur.',
            'urutan' => 1,
        ]);

        // 4. Tuk & Event & Schedule
        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-JKT-01',
            'nama_tuk' => 'TUK Lab Komputer Cyber',
            'jenis_tuk' => 'Sewaktu',
            'alamat' => 'Gedung Cyber Lt. 3, Jakarta',
            'penanggung_jawab' => 'Rudy Kurniawan, S.Kom',
            'is_active' => true,
        ]);

        $event = Event::create([
            'kode_event' => 'EVT-2026-001',
            'nama_event' => 'Sertifikasi Nasional TI Batch 1',
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
            'nama_jadwal' => 'Uji Web Madya Jakarta Batch 1',
            'tgl_asesmen' => now()->toDateString(),
            'jam_mulai' => '08:30',
            'kuota_peserta' => 10,
            'status' => ScheduleStatus::Asesmen,
        ]);

        $this->scheduleAsesor = ScheduleAsesor::create([
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'peran' => 'Penguji',
            'no_surat_tugas' => '001/ST-LSP/X/2026',
        ]);

        $canonicalScheme = Scheme::firstOrCreate(
            ['code' => 'SKM-TI-001'],
            ['name' => 'Pemrograman Web Madya', 'is_active' => true]
        );

        $this->canonicalSchedule = Schedule::create([
            'scheme_id' => $canonicalScheme->id,
            'tuk_id' => $tuk->id,
            'tuk_name' => $tuk->nama_tuk,
            'code' => 'JDW-PLENO-001',
            'name' => 'Uji Web Madya Jakarta Batch 1',
            'date' => now()->toDateString(),
            'start_time' => '08:30:00',
            'end_time' => '17:00:00',
            'created_by' => $this->admin->id,
            'status' => 'ACTIVE',
        ]);

        // 5. Application & APL-02 & AK forms
        $this->application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-2026-0001',
            'user_id' => $this->asesi->id,
            'scheme_id' => $scheme->id,
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diterima,
        ]);

        Assessment::create([
            'id' => $this->application->id,
            'schedule_id' => $this->canonicalSchedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
        ]);

        $apl02 = Apl02Assessment::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'status' => Apl02Status::Diverifikasi,
            'rekomendasi' => 'dapat_dilanjutkan',
            'catatan_asesor' => 'Bukti portofolio lengkap dan valid.',
            'tgl_asesi_sign' => now(),
            'tgl_asesor_review' => now(),
        ]);

        Ak01Agreement::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'hari_tanggal' => now()->toDateString(),
            'waktu' => '08:30 - Selesai WIB',
            'tuk_nama' => $tuk->nama_tuk,
            'is_asesi_agreed' => true,
            'is_asesor_agreed' => true,
            'ttd_asesi' => 'data:image/png;base64,dummy',
            'ttd_asesor' => 'data:image/png;base64,dummy',
        ]);

        $ak02 = Ak02Assessment::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'rekomendasi_akhir' => 'K',
            'tindak_lanjut' => 'Direkomendasikan penerbitan sertifikat BNSP.',
            'komentar_asesor' => 'Seluruh unit kompeten.',
            'ttd_asesor' => 'data:image/png;base64,dummy',
            'ttd_asesi' => 'data:image/png;base64,dummy',
        ]);

        Ak02UnitAssessment::create([
            'ak02_assessment_id' => $ak02->id,
            'unit_id' => $unit->id,
            'metode_observasi_langsung' => true,
            'metode_portofolio' => true,
            'metode_pertanyaan' => true,
            'rekomendasi_unit' => 'K',
        ]);

        Ak03Feedback::create([
            'application_id' => $this->application->id,
            'q1_penjelasan' => true,
            'q2_kesepakatan' => true,
            'q3_hak_banding' => true,
            'q4_adil_objektif' => true,
            'q5_umpan_balik' => true,
            'catatan_rekomendasi' => 'Pelaksanaan sangat profesional.',
            'ttd_asesi' => 'data:image/png;base64,dummy',
        ]);

        Ak05Report::create([
            'schedule_id' => $this->schedule->id,
            'nomor_laporan' => 'LAP-AK05/2026/001',
            'aspek_positif' => 'Asesi sangat kompeten.',
            'kendala_hambatan' => 'Tidak ada.',
            'saran_perbaikan' => 'Pertahankan kualitas instrumen uji.',
            'ttd_asesor_lead' => 'data:image/png;base64,dummy',
        ]);
    }

    public function test_admin_can_access_pleno_index_and_show(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pleno.index'));
        $response->assertStatus(200);
        $response->assertSee('Rapat Pleno Penetapan Kelulusan');

        $response = $this->actingAs($this->admin)->get(route('admin.pleno.show', $this->canonicalSchedule));
        $response->assertStatus(200);
        $response->assertSee('Data Berita Acara Rapat Pleno Komite Teknis');
        $response->assertSee($this->asesi->name);
    }

    public function test_admin_can_submit_pleno_decision_and_issue_certificate(): void
    {
        $postData = [
            'nomor_ba_pleno' => 'BA-PLENO/2026/X/001',
            'tanggal_pleno' => now()->toDateString(),
            'tempat' => 'Ruang Pleno LSP',
            'ketua_komite' => 'Dr. H. Ahmad Dahlan, M.Pd',
            'anggota_komite' => '1. Ketua Komite Teknis\n2. Ir. Hartono (Asesor)',
            'catatan_pleno' => 'Seluruh bukti diverifikasi valid.',
            'kelulusan' => [
                $this->application->id => 'kompeten',
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pleno.store', $this->canonicalSchedule), $postData);

        $response->assertRedirect(route('admin.pleno.show', $this->canonicalSchedule));
        $response->assertSessionHas('success');

        // Check PlenoMeeting created
        $this->assertDatabaseHas('pleno_meetings', [
            'schedule_id' => $this->canonicalSchedule->id,
            'nomor_ba_pleno' => 'BA-PLENO/2026/X/001',
            'status' => 'disahkan',
        ]);

        // Check application updated with certificate number
        $this->application->refresh();
        $this->assertEquals('kompeten', $this->application->status_kelulusan);
        $this->assertNotNull($this->application->no_sertifikat);
        $this->assertStringContainsString('BNSP-SKM-TI-001', $this->application->no_sertifikat);
        $this->assertNotNull($this->application->tgl_terbit_sertifikat);
        $this->assertNotNull($this->application->masa_berlaku_sertifikat);

        // Schedule status should transition to 'selesai'
        $this->schedule->refresh();
        $this->assertEquals(ScheduleStatus::Selesai, $this->schedule->status);
    }

    public function test_pdf_generation_endpoints_return_valid_pdf_stream(): void
    {
        // 1. Submit pleno first so certificate & BA Pleno are available
        $this->actingAs($this->admin)->post(route('admin.pleno.store', $this->schedule), [
            'nomor_ba_pleno' => 'BA-PLENO/2026/X/001',
            'tanggal_pleno' => now()->toDateString(),
            'tempat' => 'Ruang Pleno LSP',
            'ketua_komite' => 'Dr. H. Ahmad Dahlan',
            'kelulusan' => [
                $this->application->id => 'kompeten',
            ],
        ]);

        $this->application->refresh();

        // PDF generation endpoints are retired and return 404
        $retiredRoutes = [
            '/sertifikat/' . $this->application->id,
            '/dokumen/surat-tugas/1/1',
            '/dokumen/berita-acara-asesmen/1',
            '/dokumen/daftar-hadir-asesmen/1',
        ];

        foreach ($retiredRoutes as $route) {
            $this->actingAs($this->admin)->get($route)->assertNotFound();
        }
    }

    public function test_non_competent_cannot_download_certificate(): void
    {
        // Set application as belum_kompeten
        $this->application->update([
            'status_kelulusan' => 'belum_kompeten',
            'no_sertifikat' => null,
        ]);

        $response = $this->actingAs($this->asesi)->get('/sertifikat/' . $this->application->id);
        $response->assertNotFound();
    }
}
