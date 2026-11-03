<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\UnitElement;
use App\Models\User;
use App\Services\SchemeInstrumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorePracticalPmoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP',
            'email' => 'admin@lsp.test',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Praktik',
            'email' => 'assessor.pmo@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.pmo@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.pmo@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-AUTO-01',
            'nama_tuk' => 'TUK Otomotif Mandiri',
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
    }

    private function createScheduleAndAssessments(): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-PMO-' . rand(100, 999),
            'name' => 'Jadwal Praktik dan PMO',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        $assessment1 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'APPROVED',
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
        ]);

        $assessment2 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi2->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'APPROVED',
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
        ]);

        return [$schedule, $assessment1, $assessment2];
    }

    public function test_ia02_tpd_verbatim_scenario_and_tasks_preserved_without_auto_competence_decision(): void
    {
        [$schedule, $assessment1] = $this->createScheduleAndAssessments();

        $this->actingAs($this->assessor);

        $postData = [
            'tasks' => [
                ['id' => '1', 'instruction' => 'Gunakan APD', 'result' => 'SESUAI'],
                ['id' => '2', 'instruction' => 'Siapkan peralatan tangan', 'result' => 'SESUAI'],
            ],
            'equipment_checked' => ['Wearpack', 'Safety Shoes', 'Tool set mekanik'],
            'general_notes' => 'Praktik demonstrasi terlaksana sesuai SOP TUK.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia02']), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $iaResponse = AssessmentIaResponse::where('assessment_id', $assessment1->id)
            ->where('instrument_code', 'IA02')
            ->first();

        $this->assertNotNull($iaResponse);
        $this->assertEquals('Praktik demonstrasi terlaksana sesuai SOP TUK.', $iaResponse->assessor_notes);
        $this->assertNotEmpty($iaResponse->data_json['tasks']);

        // Verifikasi: Tidak ada keputusan kompetensi otomatis (final_result dan rekomendasi asesor tetap belum ditetapkan)
        $assessment1->refresh();
        $this->assertNotEquals('KOMPETEN', $assessment1->final_result);
        $this->assertNotEquals('K', $assessment1->final_result);
        $this->assertNull($assessment1->rekomendasi_asesor);
    }

    public function test_ia03_conflicting_pmo_skema3_not_auto_selected(): void
    {
        $scheme3 = Scheme::firstOrCreate(
            ['code' => 'SKM-03-2026'],
            ['name' => 'Periodical Maintenance EV', 'category' => 'Automotive', 'is_active' => true]
        );

        $service = app(SchemeInstrumentService::class);

        // 1. Calling getPmoQuestions without explicit approved variant returns empty array (no auto-pick)
        $autoQuestions = $service->getPmoQuestions($scheme3, null);
        $this->assertEmpty($autoQuestions, 'Conflicting PMO skema3 tidak boleh dipilih secara otomatis tanpa varian eksplisit.');

        // 2. Calling with explicit approved variant returns questions
        $variant1Questions = $service->getPmoQuestions($scheme3, 'kelompok_1');
        $this->assertNotEmpty($variant1Questions);

        // 3. Submitting to IA03 without pmo_variant for scheme 3 is rejected by validation
        $schedule = Schedule::create([
            'scheme_id' => $scheme3->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-S3-' . rand(100, 999),
            'name' => 'Jadwal EV PMO',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);
        $schedule->assessors()->attach($this->assessor->id);

        $assessmentS3 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $scheme3->id,
            'apl_status' => 'APPROVED',
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->assessor);

        $postDataNoVariant = [
            'questions' => [
                [
                    'question' => 'Prosedur keselamatan tegangan tinggi?',
                    'tanggapan' => 'Menggunakan safety gloves 1000V',
                    'pencapaian' => 'YA',
                ],
            ],
            // 'pmo_variant' omitted intentionally
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessmentS3->id, 'ia03']), $postDataNoVariant);
        $response->assertSessionHasErrors(['pmo_variant']);
    }

    public function test_ia03_empty_tanggapan_cannot_be_auto_sufficient(): void
    {
        [$schedule, $assessment1] = $this->createScheduleAndAssessments();

        $this->actingAs($this->assessor);

        // Tanggapan kosong dan tidak ada bukti observasi, tapi ditandai YA -> HARUS DITOLAK
        $postData = [
            'questions' => [
                [
                    'question' => 'Jelaskan prosedur kalibrasi alat ukur?',
                    'tanggapan' => '', // kosong!
                    'is_fulfilled_by_observation' => false,
                    'pencapaian' => 'YA',
                ],
            ],
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia03']), $postData);
        $response->assertSessionHasErrors(['questions.0.tanggapan']);
    }

    public function test_ia03_fulfilled_by_observation_requires_valid_reference_from_same_assessment(): void
    {
        [$schedule, $assessment1, $assessment2] = $this->createScheduleAndAssessments();

        $this->actingAs($this->assessor);

        // Case A: fulfilled by observation tapi observation_reference kosong -> DITOLAK
        $postDataNoRef = [
            'questions' => [
                [
                    'question' => 'Bagaimana cara membaca vernier caliper?',
                    'tanggapan' => '',
                    'is_fulfilled_by_observation' => true,
                    'observation_reference' => '', // kosong!
                    'pencapaian' => 'YA',
                ],
            ],
        ];

        $responseA = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia03']), $postDataNoRef);
        $responseA->assertSessionHasErrors(['questions.0.observation_reference']);

        // Case B: observation reference merujuk ke foreign assessment ID (asesi lain) -> DITOLAK
        $postDataForeignRef = [
            'questions' => [
                [
                    'question' => 'Bagaimana cara membaca vernier caliper?',
                    'tanggapan' => '',
                    'is_fulfilled_by_observation' => true,
                    'observation_reference' => 'KUK 1.2 demonstrasi pengukuran',
                    'observation_assessment_id' => $assessment2->id, // Foreign assessment!
                    'pencapaian' => 'YA',
                ],
            ],
        ];

        $responseB = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia03']), $postDataForeignRef);
        $responseB->assertSessionHasErrors(['questions.0.observation_reference']);

        // Case C: observation reference valid milik assessment yang sama -> DITERIMA
        $postDataValid = [
            'questions' => [
                [
                    'question' => 'Bagaimana cara membaca vernier caliper?',
                    'tanggapan' => 'Kompetensi terkonfirmasi langsung saat unjuk kerja.',
                    'is_fulfilled_by_observation' => true,
                    'observation_reference' => 'KUK 1.2 pada demonstrasi tugas 2',
                    'observation_assessment_id' => $assessment1->id, // Same assessment!
                    'pencapaian' => 'YA',
                ],
            ],
            'general_notes' => 'Seluruh klarifikasi tercatat.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $responseC = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia03']), $postDataValid);
        $responseC->assertSessionHasNoErrors();
        $responseC->assertRedirect();
    }

    public function test_ia03_supplementary_questions_stored_per_assessment_without_mutating_master_questions(): void
    {
        [$schedule, $assessment1, $assessment2] = $this->createScheduleAndAssessments();

        $service = app(SchemeInstrumentService::class);
        $masterCountBefore = count($service->getPmoQuestions($this->scheme));
        $qbCountBefore = QuestionBank::count();

        $this->actingAs($this->assessor);

        $postData = [
            'questions' => [
                [
                    'question' => 'Pertanyaan PMO standar #1',
                    'tanggapan' => 'Jawaban teknis asesi 1',
                    'pencapaian' => 'YA',
                ],
            ],
            'supplementary_questions' => [
                [
                    'question' => 'Pertanyaan klarifikasi khusus: Berapa momen pengencangan baut kepala silinder?',
                    'tanggapan' => '32 Nm bertahap menyilang',
                    'pencapaian' => 'YA',
                    'reason' => 'Memastikan asesi memahami spesifikasi manual servis terkait perakitan',
                ],
            ],
            'general_notes' => 'Pertanyaan tambahan diajukan asesor.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment1->id, 'ia03']), $postData);
        $response->assertSessionHasNoErrors();

        // 1. Supplementary question disimpan pada respon asesmen 1
        $iaResponse1 = AssessmentIaResponse::where('assessment_id', $assessment1->id)
            ->where('instrument_code', 'IA03')
            ->first();
        $this->assertNotNull($iaResponse1);
        $this->assertCount(1, $iaResponse1->data_json['supplementary_questions']);
        $this->assertEquals(
            'Pertanyaan klarifikasi khusus: Berapa momen pengencangan baut kepala silinder?',
            $iaResponse1->data_json['supplementary_questions'][0]['question']
        );
        $this->assertEquals($this->assessor->id, $iaResponse1->data_json['supplementary_questions'][0]['asked_by_id']);

        // 2. Master questions tidak termutasi sama sekali
        $masterCountAfter = count($service->getPmoQuestions($this->scheme));
        $this->assertEquals($masterCountBefore, $masterCountAfter, 'Master PMO questions tidak boleh termutasi oleh pertanyaan tambahan.');
        $this->assertEquals($qbCountBefore, QuestionBank::count(), 'QuestionBank master tidak boleh bertambah oleh pertanyaan tambahan asesi.');

        // 3. Asesmen asesi 2 tidak terpengaruh oleh supplementary question asesi 1
        $iaResponse2 = AssessmentIaResponse::where('assessment_id', $assessment2->id)
            ->where('instrument_code', 'IA03')
            ->first();
        $this->assertNull($iaResponse2);
    }
}
