<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\Ak01Agreement;
use App\Models\Ak02Assessment;
use App\Models\Ak03Feedback;
use App\Models\Ak05Report;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\CompetencyUnit;
use App\Models\Event;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentExecutionTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesor;
    protected User $asesi;
    protected CertificationScheme $scheme;
    protected CompetencyUnit $unit;
    protected AssessmentSchedule $schedule;
    protected AssessmentApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->asesor = User::factory()->create([
            'role' => UserRole::Asesor,
            'name' => 'Bambang Asesor, M.Kom.',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'name' => 'Rian Hidayat',
        ]);

        $this->scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-NET-01',
            'nama_skema' => 'Network Administrator',
            'jenis_skema' => 'Okupasi',
            'is_active' => true,
        ]);

        $this->unit = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'kode_unit' => 'J.620900.001.01',
            'judul_unit' => 'Memasang Perangkat Jaringan',
            'jenis_standar' => 'SKKNI',
            'urutan' => 1,
        ]);

        $event = Event::create([
            'kode_event' => 'EVT-TEST-01',
            'nama_event' => 'Uji Reguler LSP',
            'tahun_anggaran' => 2026,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
        ]);

        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-NET',
            'nama_tuk' => 'Lab Jaringan Komputer',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $tuk->id,
            'kode_jadwal' => 'SCH-NET-01',
            'nama_jadwal' => 'Uji Jaringan Komputer Nov 2026',
            'tgl_asesmen' => '2026-11-25',
            'kuota_peserta' => 10,
            'status' => ScheduleStatus::Asesmen,
        ]);

        $this->application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0020',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diterima,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);
    }

    public function test_asesi_and_asesor_can_sign_ak01_agreement(): void
    {
        // 1. Asesi views and signs AK-01
        $res = $this->actingAs($this->asesi)->get(route('asesi.applications.ak01', $this->application));
        $res->assertOk()->assertSee('FR-AK-01');

        $signRes = $this->actingAs($this->asesi)->post(
            route('asesi.applications.ak01.sign', $this->application),
            [
                'setuju' => '1',
                'ttd_asesi' => 'data:image/png;base64,sample_asesi_sign',
            ]
        );
        $signRes->assertRedirect();

        $this->assertDatabaseHas('ak01_agreements', [
            'application_id' => $this->application->id,
            'is_asesi_agreed' => true,
        ]);

        // 2. Asesor signs AK-01
        $asesorSignRes = $this->actingAs($this->asesor)->post(
            route('asesor.ak01.sign', $this->application),
            [
                'ttd_asesor' => 'data:image/png;base64,sample_asesor_sign',
            ]
        );
        $asesorSignRes->assertRedirect();

        $ak01 = Ak01Agreement::where('application_id', $this->application->id)->first();
        $this->assertTrue($ak01->isFullySigned());
    }

    public function test_asesor_can_submit_digital_assessment_ak02(): void
    {
        $res = $this->actingAs($this->asesor)->get(route('asesor.ak02.form', $this->application));
        $res->assertOk()->assertSee('FR-AK-02');

        $payload = [
            'rekomendasi_akhir' => 'K',
            'komentar_asesor' => 'Demonstrasi unjuk kerja dilakukan secara mandiri dan sangat baik.',
            'units' => [
                $this->unit->id => [
                    'metode_l' => '1',
                    'metode_tl' => '1',
                    'metode_t' => '1',
                    'rekomendasi_unit' => 'K',
                    'catatan' => 'Kompeten',
                ],
            ],
            'ttd_asesor' => 'data:image/png;base64,asesor_ak02_sign',
        ];

        $storeRes = $this->actingAs($this->asesor)->post(
            route('asesor.ak02.store', $this->application),
            $payload
        );

        $storeRes->assertRedirect(route('asesor.apl02.index'));

        $this->assertDatabaseHas('ak02_assessments', [
            'application_id' => $this->application->id,
            'rekomendasi_akhir' => 'K',
        ]);

        $this->assertDatabaseHas('ak02_unit_assessments', [
            'unit_id' => $this->unit->id,
            'rekomendasi_unit' => 'K',
            'metode_observasi_langsung' => true,
        ]);
    }

    public function test_asesi_can_confirm_and_sign_ak02(): void
    {
        $ak02 = Ak02Assessment::create([
            'application_id' => $this->application->id,
            'asesor_id' => $this->asesor->id,
            'rekomendasi_akhir' => 'K',
        ]);

        $ak02->units()->create([
            'unit_id' => $this->unit->id,
            'rekomendasi_unit' => 'K',
        ]);

        $res = $this->actingAs($this->asesi)->get(route('asesi.applications.ak02', $this->application));
        $res->assertOk()->assertSee('Kompeten (K)');

        $signRes = $this->actingAs($this->asesi)->post(
            route('asesi.applications.ak02.sign', $this->application),
            [
                'konfirmasi' => '1',
                'ttd_asesi' => 'data:image/png;base64,asesi_confirmed',
            ]
        );

        $signRes->assertRedirect();
        $ak02->refresh();
        $this->assertNotNull($ak02->ttd_asesi);
    }

    public function test_asesi_can_fill_feedback_ak03(): void
    {
        $payload = [
            'q1_penjelasan' => '1',
            'q2_kesepakatan' => '1',
            'q3_hak_banding' => '1',
            'q4_adil_objektif' => '1',
            'q5_umpan_balik' => '1',
            'catatan_rekomendasi' => 'Proses uji sangat terstruktur dan nyaman.',
        ];

        $res = $this->actingAs($this->asesi)->post(
            route('asesi.applications.ak03.store', $this->application),
            $payload
        );

        $res->assertRedirect(route('asesi.applications.show', $this->application));

        $this->assertDatabaseHas('ak03_feedbacks', [
            'application_id' => $this->application->id,
            'q1_penjelasan' => true,
            'q4_adil_objektif' => true,
        ]);
    }

    public function test_asesor_can_generate_and_save_ak05_report(): void
    {
        $res = $this->actingAs($this->asesor)->get(route('asesor.ak05.report', $this->schedule));
        $res->assertOk()->assertSee('FR-AK-05');

        $payload = [
            'nomor_laporan' => 'LAP-AK05/SCH-NET-01/10/2026',
            'aspek_positif' => 'Fasilitas TUK sangat lengkap dan peserta disiplin.',
            'kendala_hambatan' => 'Koneksi switch sempat restart di awal.',
            'saran_perbaikan' => 'Periksa catu daya sebelum uji dimulai.',
            'ttd_asesor_lead' => 'data:image/png;base64,lead_sign',
        ];

        $postRes = $this->actingAs($this->asesor)->post(
            route('asesor.ak05.store', $this->schedule),
            $payload
        );

        $postRes->assertRedirect();

        $this->assertDatabaseHas('ak05_reports', [
            'schedule_id' => $this->schedule->id,
            'nomor_laporan' => 'LAP-AK05/SCH-NET-01/10/2026',
        ]);
    }
}
