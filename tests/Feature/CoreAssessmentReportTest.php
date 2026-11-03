<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\AssessmentReport;
use App\Models\CompetencyUnit;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreAssessmentReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor1;
    private User $assessor2;
    private User $foreignAssessor;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private CompetencyUnit $unit1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP',
            'email' => 'admin@lsp.test',
        ]);

        $this->assessor1 = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Satu',
            'email' => 'assessor1.rep@lsp.test',
        ]);

        $this->assessor2 = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Dua',
            'email' => 'assessor2.rep@lsp.test',
        ]);

        $this->foreignAssessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Asing',
            'email' => 'assessor.foreign@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.rep@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.rep@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-REP-01',
            'nama_tuk' => 'TUK Otomotif Terpadu',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-01-2026'],
            ['name' => 'Sepeda Motor Karburator', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-01-2026-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );

        $this->unit1 = CompetencyUnit::firstOrCreate(
            ['code' => 'G.45OTO01.001.2'],
            ['scheme_id' => $this->scheme->id, 'title' => 'Melaksanakan K3', 'unit_type' => 'core']
        );
    }

    private function createMultiAssessorSchedule(): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-REP-' . rand(100, 999),
            'name' => 'Jadwal Multi Asesor',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach([$this->assessor1->id, $this->assessor2->id]);

        $assessment1 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor1->id, // Assigned to Assessor 1
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'rekomendasi_asesor' => 'K',
            'status' => 'COMPLETED',
        ]);

        $assessment2 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi2->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor2->id, // Assigned to Assessor 2
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => 'BK',
            'status' => 'COMPLETED',
        ]);

        return [$schedule, $assessment1, $assessment2];
    }

    public function test_multi_assessor_in_same_schedule_do_not_overwrite_each_other_reports(): void
    {
        [$schedule, $assessment1, $assessment2] = $this->createMultiAssessorSchedule();

        // 1. Assessor 1 submits AK-05
        $this->actingAs($this->assessor1);

        $postData1 = [
            'aspects_positive' => 'Asesi 1 sangat memahami prosedur K3 di tempat kerja.',
            'aspects_negative' => 'Kondisi pencahayaan area kerja perlu ditambah.',
            'improvements' => 'Peningkatan fasilitas penerangan TUK.',
            'notes' => 'Catatan asesor 1.',
        ];

        $res1 = $this->post(route('asesor.schedule.ak05.store', $schedule->id), $postData1);
        $res1->assertSessionHasNoErrors();
        $res1->assertRedirect();

        // 2. Assessor 2 submits AK-05 on the same schedule
        $this->actingAs($this->assessor2);

        $postData2 = [
            'aspects_positive' => 'Asesi 2 teliti dalam persiapan APD.',
            'aspects_negative' => 'Keterampilan pengukuran micrometer belum memenuhi standar.',
            'improvements' => 'Pelatihan lanjutan penggunaan alat ukur presisi.',
            'notes' => 'Catatan asesor 2.',
        ];

        $res2 = $this->post(route('asesor.schedule.ak05.store', $schedule->id), $postData2);
        $res2->assertSessionHasNoErrors();
        $res2->assertRedirect();

        // 3. Verification: Two distinct records exist; neither overwritten!
        $report1 = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $this->assessor1->id)
            ->where('report_type', 'AK05')
            ->first();

        $report2 = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $this->assessor2->id)
            ->where('report_type', 'AK05')
            ->first();

        $this->assertNotNull($report1);
        $this->assertNotNull($report2);
        $this->assertNotEquals($report1->id, $report2->id);

        // Verification: Assessor 1 roster has Asesi 1; Assessor 2 roster has Asesi 2
        $this->assertEquals($this->asesi1->name, $report1->roster_json[0]['asesi_name']);
        $this->assertEquals($this->asesi2->name, $report2->roster_json[0]['asesi_name']);
    }

    public function test_ak05_all_fields_round_trip_with_roster_snapshot(): void
    {
        [$schedule, $assessment1] = $this->createMultiAssessorSchedule();

        $this->actingAs($this->assessor1);

        $postData = [
            'aspects_positive' => 'Seluruh tahapan kerja terlaksana aman.',
            'aspects_negative' => 'Waktu pengerjaan sempat tertunda 5 menit karena kendala teknis.',
            'rejections_record' => 'Tidak ada penolakan hasil asesmen.',
            'improvements' => 'Pemeriksaan alat berkala sebelum asesmen dimulai.',
            'notes' => 'Catatan evaluasi komprehensif.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.schedule.ak05.store', $schedule->id), $postData);
        $response->assertSessionHasNoErrors();

        $report = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $this->assessor1->id)
            ->where('report_type', 'AK05')
            ->first();

        $this->assertNotNull($report);
        $this->assertEquals(1, $report->revision);
        $this->assertEquals('SUBMITTED', $report->status);
        $this->assertEquals('Seluruh tahapan kerja terlaksana aman.', $report->content_json['aspects_positive']);
        $this->assertNotEmpty($report->roster_json);
    }

    public function test_ak06_all_procedure_matrix_cells_and_five_dimensions_round_trip(): void
    {
        [$schedule] = $this->createMultiAssessorSchedule();

        $this->actingAs($this->assessor1);

        $matrix = [
            0 => ['name' => '1. Perencanaan Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            1 => ['name' => '2. Pelaksanaan Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            2 => ['name' => '3. Keputusan Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            3 => ['name' => '4. Umpan Balik Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
        ];

        $dimensions = [
            'task_skills' => 'Mampu melakukan tugas pengukuran komponen secara mandiri.',
            'task_management_skills' => 'Mampu mengatur urutan pekerjaan sesuai SOP bengkel.',
            'contingency_management_skills' => 'Mampu menghentikan mesin dan evakuasi saat simulasi darurat.',
            'job_role_environment_skills' => 'Menerapkan 5R dan komunikasi rekan kerja yang efektif.',
            'transfer_skills' => 'Mampu menerapkan teknik pengukuran pada tipe kendaraan berbeda.',
        ];

        $postData = [
            'procedure_matrix' => $matrix,
            'dimensions_review' => $dimensions,
            'evidence_references' => 'FR.IA.01, FR.IA.02, FR.IA.05',
            'comments' => 'Pelaksanaan asesmen memenuhi standar mutu BNSP.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.schedule.ak06.store', $schedule->id), $postData);
        $response->assertSessionHasNoErrors();

        $report = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $this->assessor1->id)
            ->where('report_type', 'AK06')
            ->first();

        $this->assertNotNull($report);
        $this->assertEquals(1, $report->revision);
        $this->assertEquals('SUBMITTED', $report->status);
        $this->assertCount(4, $report->content_json['procedure_matrix']);
        $this->assertEquals('Mampu melakukan tugas pengukuran komponen secara mandiri.', $report->content_json['dimensions_review']['task_skills']);
    }

    public function test_ak06_na_without_reason_is_rejected(): void
    {
        [$schedule] = $this->createMultiAssessorSchedule();

        $this->actingAs($this->assessor1);

        // Row 1 has N/A but notes is empty -> MUST BE REJECTED
        $matrixWithNaNoReason = [
            0 => ['name' => '1. Perencanaan Asesmen', 'valid' => 'N/A', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''], // Empty notes!
            1 => ['name' => '2. Pelaksanaan Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            2 => ['name' => '3. Keputusan Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            3 => ['name' => '4. Umpan Balik Asesmen', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
        ];

        $dimensions = [
            'task_skills' => 'Keterampilan tugas memadai.',
            'task_management_skills' => 'Manajemen tugas memadai.',
            'contingency_management_skills' => 'Tanggap darurat memadai.',
            'job_role_environment_skills' => 'Lingkungan kerja memadai.',
            'transfer_skills' => 'Transfer skill memadai.',
        ];

        $postData = [
            'procedure_matrix' => $matrixWithNaNoReason,
            'dimensions_review' => $dimensions,
        ];

        $response = $this->post(route('asesor.schedule.ak06.store', $schedule->id), $postData);
        $response->assertSessionHasErrors(['procedure_matrix.0.notes']);
    }

    public function test_stale_revision_conflict_detection(): void
    {
        [$schedule] = $this->createMultiAssessorSchedule();

        // Pre-create existing AK05 report with lock_version = 2
        AssessmentReport::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $this->assessor1->id,
            'report_type' => 'AK05',
            'revision' => 1,
            'content_json' => ['aspects_positive' => 'Awal'],
            'lock_version' => 2,
        ]);

        $this->actingAs($this->assessor1);

        // Submitting with stale lock_version = 1
        $postData = [
            'aspects_positive' => 'Perubahan aspek positif.',
            'aspects_negative' => 'Perubahan kendala.',
            'improvements' => 'Perubahan saran.',
            'lock_version' => 1, // Stale!
        ];

        $response = $this->post(route('asesor.schedule.ak05.store', $schedule->id), $postData);
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function test_unauthorized_assessor_cannot_submit_report_for_schedule(): void
    {
        [$schedule] = $this->createMultiAssessorSchedule();

        // Foreign assessor is NOT attached to the schedule
        $this->actingAs($this->foreignAssessor);

        $postData = [
            'aspects_positive' => 'Mencoba mengisi laporan.',
            'aspects_negative' => 'Tidak ditugaskan.',
            'improvements' => 'Saran.',
        ];

        $response = $this->post(route('asesor.schedule.ak05.store', $schedule->id), $postData);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
