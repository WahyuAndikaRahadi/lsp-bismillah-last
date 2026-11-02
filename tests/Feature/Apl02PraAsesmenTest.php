<?php

namespace Tests\Feature;

use App\Enums\Apl02Status;
use App\Enums\ApplicationStatus;
use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\Apl02Assessment;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\Event;
use App\Models\ScheduleAsesor;
use App\Models\Tuk;
use App\Models\User;
use App\Models\WorkKeyCriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Apl02PraAsesmenTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected CertificationScheme $scheme;

    protected AssessmentSchedule $schedule;

    protected AssessmentApplication $application;

    protected WorkKeyCriteria $kuk;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Users
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->asesor = User::factory()->create([
            'role' => UserRole::Asesor,
            'name' => 'Dr. Bambang Asesor, M.Kom.',
        ]);
        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'name' => 'Fajar Pratama',
        ]);

        // 2. Skema & Unit & Element & KUK
        $this->scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-DEV-01',
            'nama_skema' => 'Junior Web Developer',
            'jenis_skema' => 'Okupasi',
            'is_active' => true,
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'kode_unit' => 'J.620100.004.01',
            'judul_unit' => 'Menggunakan Struktur Data',
            'jenis_standar' => 'SKKNI',
            'urutan' => 1,
        ]);

        $elem = CompetencyElement::create([
            'unit_id' => $unit->id,
            'nomor_elemen' => 1,
            'nama_elemen' => 'Mengidentifikasi konsep struktur data',
            'urutan' => 1,
        ]);

        $this->kuk = WorkKeyCriteria::create([
            'element_id' => $elem->id,
            'nomor_kuk' => '1.1',
            'pernyataan' => 'Konsep tipe data dan struktur data diidentifikasi sesuai spesifikasi bahasa pemrograman.',
            'urutan' => 1,
        ]);

        // 3. Event & Schedule
        $event = Event::create([
            'kode_event' => 'EVT-01',
            'nama_event' => 'PSKK BNSP',
            'tahun_anggaran' => 2026,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
        ]);

        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-01',
            'nama_tuk' => 'Lab IT',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $tuk->id,
            'kode_jadwal' => 'SCH-01',
            'nama_jadwal' => 'Uji Nov 2026',
            'tgl_asesmen' => '2026-11-20',
            'kuota_peserta' => 20,
            'status' => ScheduleStatus::Pendaftaran,
        ]);

        // 4. Accepted Application
        $this->application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0010',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'schedule_id' => $this->schedule->id,
            'status' => ApplicationStatus::Diterima,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);
    }

    public function test_admin_can_assign_asesor_to_schedule_with_surat_tugas(): void
    {
        $response = $this->actingAs($this->admin)->post(
            route('admin.schedules.asesors.assign', $this->schedule),
            [
                'asesor_id' => $this->asesor->id,
                'peran' => 'Lead Asesor',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('schedule_asesors', [
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'peran' => 'Lead Asesor',
        ]);

        $assignment = ScheduleAsesor::where('schedule_id', $this->schedule->id)
            ->where('asesor_id', $this->asesor->id)
            ->first();
        $this->assertNotNull($assignment->no_surat_tugas);
        $this->assertStringContainsString('/ST-LSP/', $assignment->no_surat_tugas);
    }

    public function test_admin_can_plot_asesi_to_assigned_asesor(): void
    {
        $response = $this->actingAs($this->admin)->post(
            route('admin.schedules.plot-asesor', $this->schedule),
            [
                'application_id' => $this->application->id,
                'asesor_id' => $this->asesor->id,
            ]
        );

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertEquals($this->asesor->id, $this->application->asesor_id);
    }

    public function test_asesi_can_access_and_submit_apl02_mandiri(): void
    {
        // 1. Access create APL-02 page
        $res = $this->actingAs($this->asesi)->get(
            route('asesi.applications.apl02.create', $this->application)
        );
        $res->assertOk()->assertSee('FR-APL-02');

        // 2. Submit APL-02
        $payload = [
            'items' => [
                $this->kuk->id => [
                    'penilaian_mandiri' => 'K',
                    'bukti_pendukung' => 'Portofolio Proyek Web Dev',
                ],
            ],
            'ttd_asesi' => 'data:image/png;base64,signature_data',
            'pernyataan_asesmen' => '1',
        ];

        $postRes = $this->actingAs($this->asesi)->post(
            route('asesi.applications.apl02.store', $this->application),
            $payload
        );

        $postRes->assertRedirect(route('asesi.applications.apl02.show', $this->application));

        $this->assertDatabaseHas('apl02_assessments', [
            'application_id' => $this->application->id,
            'status' => Apl02Status::Diajukan->value,
        ]);

        $this->assertDatabaseHas('apl02_items', [
            'kuk_id' => $this->kuk->id,
            'penilaian_mandiri' => 'K',
        ]);
    }

    public function test_asesor_can_view_assigned_apl02_and_submit_vatm_review(): void
    {
        // Setup APL-02 submitted
        $apl02 = Apl02Assessment::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'status' => Apl02Status::Diajukan,
        ]);

        $item = $apl02->items()->create([
            'kuk_id' => $this->kuk->id,
            'penilaian_mandiri' => 'K',
            'bukti_pendukung' => 'Sertifikat Pelatihan',
        ]);

        // 1. Asesor views review page
        $res = $this->actingAs($this->asesor)->get(
            route('asesor.apl02.review', $apl02)
        );
        $res->assertOk()->assertSee('Peninjauan Bukti FR-APL-02');

        // 2. Asesor submits VATM review
        $payload = [
            'status' => 'diverifikasi',
            'rekomendasi_asesor' => 'Asesmen dapat dilanjutkan',
            'catatan_asesor' => 'Bukti sangat memadai dan otentik.',
            'items' => [
                $item->id => [
                    'v' => '1',
                    'a' => '1',
                    't' => '1',
                    'm' => '1',
                    'catatan' => 'Valid',
                ],
            ],
            'ttd_asesor' => 'data:image/png;base64,asesor_sign',
        ];

        $updateRes = $this->actingAs($this->asesor)->put(
            route('asesor.apl02.update', $apl02),
            $payload
        );

        $updateRes->assertRedirect(route('asesor.apl02.index'));

        $apl02->refresh();
        $this->assertEquals(Apl02Status::Diverifikasi, $apl02->status);
        $this->assertEquals('Asesmen dapat dilanjutkan', $apl02->rekomendasi_asesor);
        $this->assertNotNull($apl02->tgl_ttd_asesor);

        $item->refresh();
        $this->assertTrue($item->is_valid);
        $this->assertTrue($item->is_asli);
        $this->assertTrue($item->is_terkini);
        $this->assertTrue($item->is_memadai);
    }
}
