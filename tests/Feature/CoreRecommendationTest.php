<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\AssessmentIaResponse;
use App\Models\AttendanceEvent;
use App\Models\CompetencyUnit;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\UnitElement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreRecommendationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assignedAssessor;
    private User $foreignAssessor;
    private User $asesi;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private CompetencyUnit $unit1;
    private CompetencyUnit $unit2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP',
            'email' => 'admin@lsp.test',
        ]);

        $this->assignedAssessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Ditugaskan',
            'email' => 'assessor.assigned@lsp.test',
        ]);

        $this->foreignAssessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Luar',
            'email' => 'assessor.foreign@lsp.test',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Uji',
            'email' => 'asesi.rec@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-REC-01',
            'nama_tuk' => 'TUK Mandiri Otomotif',
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

        $this->unit2 = CompetencyUnit::firstOrCreate(
            ['code' => 'G.45OTO01.002.2'],
            ['scheme_id' => $this->scheme->id, 'title' => 'Menggunakan Alat Ukur', 'unit_type' => 'core']
        );
    }

    private function createReadyAssessment(): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-REC-' . rand(100, 999),
            'name' => 'Jadwal Rekomendasi Asesmen',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assignedAssessor->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assignedAssessor->id,
            'apl_status' => 'APPROVED',
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
            'lock_version' => 1,
        ]);
        $assessment->refresh();

        AttendanceEvent::create([
            'schedule_id' => $schedule->id,
            'user_id' => $this->asesi->id,
            'event_type' => 'asesi_hadir',
            'recorded_by' => $this->assignedAssessor->id,
            'occurred_at' => now(),
            'notes' => 'Asesi hadir di TUK',
        ]);

        return [$schedule, $assessment];
    }

    public function test_get_ak02_does_not_mutate_database_state(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        $this->actingAs($this->assignedAssessor);

        $itemsBefore = AssessmentAk02Item::where('assessment_id', $assessment->id)->count();
        $this->assertEquals(0, $itemsBefore);
        $resultBefore = $assessment->final_result;

        // GET request to showAk02
        $response = $this->get(route('asesor.assessment.ak02', $assessment->id));
        $response->assertStatus(200);

        // Verification: No items created, no auto result
        $itemsAfter = AssessmentAk02Item::where('assessment_id', $assessment->id)->count();
        $this->assertEquals(0, $itemsAfter, 'GET request ke showAk02 dilarang memutasi item temuan asesmen.');

        $assessment->refresh();
        $this->assertEquals($resultBefore, $assessment->final_result, 'GET request ke showAk02 dilarang mengubah final_result.');
    }

    public function test_perfect_cbt_score_without_practical_observation_cannot_be_finalized_as_competent(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        // Asesi has perfect 100 CBT score, BUT NO IA01/IA02 observation
        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA05',
            'score' => 100,
            'data_json' => ['score' => 100, 'answers' => []],
        ]);

        $this->actingAs($this->assignedAssessor);

        $postData = [
            'final_result' => 'KOMPETEN',
            'units' => [
                $this->unit1->id => [
                    'is_competent' => 1,
                    'tulis' => 1,
                    'finding_notes' => 'Lulus tes CBT',
                ],
                $this->unit2->id => [
                    'is_competent' => 1,
                    'tulis' => 1,
                    'finding_notes' => 'Lulus tes CBT',
                ],
            ],
            'final_notes' => 'CBT 100 persen',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postData);
        $response->assertSessionHasErrors(['final_result']);

        $assessment->refresh();
        $this->assertNotEquals('KOMPETEN', $assessment->final_result);
    }

    public function test_missing_unit_findings_blocks_finalization(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        // Observation exists
        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA01',
            'data_json' => ['kuks' => []],
        ]);

        $this->actingAs($this->assignedAssessor);

        // Only unit 1 is submitted; unit 2 is missing!
        $postData = [
            'final_result' => 'KOMPETEN',
            'units' => [
                $this->unit1->id => [
                    'is_competent' => 1,
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'K3 terpenuhi',
                ],
                // unit 2 is missing
            ],
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postData);
        $response->assertSessionHasErrors(['units']);
    }

    public function test_any_unit_marked_bk_forces_overall_recommendation_to_belum_kompeten(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA01',
            'data_json' => ['kuks' => []],
        ]);

        $this->actingAs($this->assignedAssessor);

        // Unit 1 is K, but Unit 2 is BK
        $postDataForged = [
            'final_result' => 'KOMPETEN', // Contradictory to unit 2 BK!
            'units' => [
                $this->unit1->id => [
                    'is_competent' => 1,
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Kompeten',
                ],
                $this->unit2->id => [
                    'is_competent' => 0, // BK!
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Penggunaan micrometer belum presisi',
                ],
            ],
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        // Should be rejected because Unit 2 is BK
        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postDataForged);
        $response->assertSessionHasErrors(['final_result']);

        // Now submit with legitimate BELUM_KOMPETEN recommendation
        $postDataLegit = [
            'final_result' => 'BELUM_KOMPETEN',
            'units' => [
                $this->unit1->id => [
                    'is_competent' => 1,
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Kompeten',
                ],
                $this->unit2->id => [
                    'is_competent' => 0, // BK
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Penggunaan micrometer belum presisi',
                ],
            ],
            'final_notes' => 'Perlu asesmen ulang pada unit alat ukur presisi.',
            'follow_up_action' => 'Pelatihan lanjutan penggunaan micrometer.',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response2 = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postDataLegit);
        $response2->assertSessionHasNoErrors();
        $response2->assertRedirect();

        $assessment->refresh();
        $this->assertEquals('BELUM_KOMPETEN', $assessment->final_result);
        $this->assertEquals('BK', $assessment->rekomendasi_asesor);
        $this->assertEquals('COMPLETED', $assessment->status);
    }

    public function test_unauthorized_assessor_cannot_finalize_or_replace_findings(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        // Foreign assessor is NOT attached to the schedule or assessment
        $this->actingAs($this->foreignAssessor);

        $postData = [
            'final_result' => 'KOMPETEN',
            'units' => [
                $this->unit1->id => ['is_competent' => 1, 'observasi_demonstrasi' => 1],
                $this->unit2->id => ['is_competent' => 1, 'observasi_demonstrasi' => 1],
            ],
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postData);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_stale_save_conflict_detected_on_concurrent_finalization(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA01',
            'data_json' => ['kuks' => []],
        ]);

        $this->actingAs($this->assignedAssessor);

        // Assessment lock_version is 1. We submit with lock_version = 99 (stale)
        $postDataStale = [
            'final_result' => 'KOMPETEN',
            'units' => [
                $this->unit1->id => ['is_competent' => 1, 'observasi_demonstrasi' => 1],
                $this->unit2->id => ['is_competent' => 1, 'observasi_demonstrasi' => 1],
            ],
            'lock_version' => 99,
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postDataStale);
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function test_valid_explicit_competent_finalization_with_all_units_k_and_observation(): void
    {
        [$schedule, $assessment] = $this->createReadyAssessment();

        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA01',
            'data_json' => ['kuks' => []],
        ]);

        $this->actingAs($this->assignedAssessor);

        $postData = [
            'final_result' => 'KOMPETEN',
            'units' => [
                $this->unit1->id => [
                    'is_competent' => 1,
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Menerapkan K3 dengan disiplin',
                ],
                $this->unit2->id => [
                    'is_competent' => 1,
                    'observasi_demonstrasi' => 1,
                    'finding_notes' => 'Pengukuran presisi vernier caliper tepat',
                ],
            ],
            'final_notes' => 'Seluruh kriteria unjuk kerja terpenuhi.',
            'follow_up_action' => 'Diusulkan sertifikat kompetensi BNSP.',
            'lock_version' => 1,
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesor.assessment.ak02.update', $assessment->id), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $assessment->refresh();
        $this->assertEquals('KOMPETEN', $assessment->final_result);
        $this->assertEquals('K', $assessment->rekomendasi_asesor);
        $this->assertEquals('COMPLETED', $assessment->status);
        $this->assertEquals(2, $assessment->lock_version);

        // Verify findings recorded with assessor attribution
        $items = AssessmentAk02Item::where('assessment_id', $assessment->id)->get();
        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertTrue($item->is_competent);
            $this->assertEquals($this->assignedAssessor->id, $item->assessor_id);
            $this->assertNotNull($item->recorded_at);
        }
    }
}
