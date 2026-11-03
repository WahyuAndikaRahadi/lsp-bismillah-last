<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AssessmentIaResponse;
use App\Models\AuditEvent;
use App\Models\CompetencyUnit;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\CbtAttemptService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class CoreExamFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi;
    private User $otherAsesi;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private CbtAttemptService $cbtService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cbtService = app(CbtAttemptService::class);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP',
            'email' => 'admin@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Asesor Penguji',
            'email' => 'asesor@proindo.id',
            'status' => 'ACTIVE',
        ]);
        AsesorProfile::create([
            'user_id' => $this->assessor->id,
            'no_met' => 'MET.44455.2026',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Candidate Uji',
            'email' => 'candidate@proindo.id',
            'status' => 'ACTIVE',
        ]);
        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201019901010009',
            'signature_data' => 'SIG-CANDIDATE',
        ]);

        $this->otherAsesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Other Candidate',
            'email' => 'other@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->tuk = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-CBT-01'],
            ['nama_tuk' => 'TUK Komputer', 'jenis_tuk' => 'Sewaktu', 'is_active' => true]
        );

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-01-2026'],
            ['name' => 'Sepeda Motor Karburator', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-01-2026-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );
    }

    private function createScheduleAndQuestions(int $questionCount = 5): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-CBT-' . rand(100, 999),
            'name' => 'Jadwal CBT Otomotif',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->name,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
        ]);

        $unit = CompetencyUnit::firstOrCreate(
            ['code' => 'G.45OTO01.001.2'],
            ['scheme_id' => $this->scheme->id, 'title' => 'K3 Otomotif', 'unit_type' => 'core']
        );

        $questions = [];
        for ($i = 1; $i <= $questionCount; $i++) {
            $questions[] = QuestionBank::create([
                'scheme_id' => $this->scheme->id,
                'competency_unit_id' => $unit->id,
                'question_text' => "Pertanyaan teori ke-{$i} tentang K3 otomotif?",
                'option_a' => 'Opsi A',
                'option_b' => 'Opsi B',
                'option_c' => 'Opsi C',
                'option_d' => 'Opsi D',
                'correct_answer' => 'A', // Known answer key
            ]);
        }

        return [$schedule, $assessment, $questions];
    }

    public function test_candidate_never_receives_correct_answer_in_response_or_view(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(3);

        $this->actingAs($this->asesi);

        $attemptData = $this->cbtService->startOrResumeAttempt($assessment);

        // Service payload verification: correct_answer is never exposed
        foreach ($attemptData['questions'] as $q) {
            $this->assertArrayNotHasKey('correct_answer', $q->toArray());
        }

        // View verification: correct_answer must not leak into HTML
        $response = $this->get(route('asesi.assessment.instrument.ia05', $assessment->id));
        $response->assertStatus(200);

        // Ensure "correct_answer" key is not anywhere in JSON or hidden attributes
        $response->assertDontSee('"correct_answer"');
        $response->assertDontSee("'correct_answer'");
    }

    public function test_server_authoritative_timer_does_not_reset_on_reload(): void
    {
        Carbon::setTestNow('2026-11-04 09:00:00');

        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(3);

        $this->actingAs($this->asesi);

        // Initial attempt starts at 09:00:00 with 3600 seconds
        $attempt1 = $this->cbtService->startOrResumeAttempt($assessment);
        $this->assertEquals(3600, $attempt1['remaining_seconds']);

        // Advance server clock by 600 seconds (10 minutes)
        Carbon::setTestNow('2026-11-04 09:10:00');

        // Reload / re-fetch attempt
        $attempt2 = $this->cbtService->startOrResumeAttempt($assessment);

        // Timer must decrement to 3000s, never reset to 3600s!
        $this->assertEquals(3000, $attempt2['remaining_seconds']);

        Carbon::setTestNow();
    }

    public function test_ak07_time_accommodation_extends_timer(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(3);

        // Record AK-07 accommodation for fatigue/flexible time
        AssessmentAk07Item::create([
            'assessment_id' => $assessment->id,
            'category_number' => 4,
            'adjustment_category' => 'Pelaksanaan asesmen secara fleksibel karena alasan keletihan',
            'decision' => 'ya',
            'assessor_id' => $this->assessor->id,
            'timing' => 'sebelum_pra_asesmen',
        ]);

        $this->actingAs($this->asesi);

        $attempt = $this->cbtService->startOrResumeAttempt($assessment);

        // Extended time allocated (5400s / 90 mins instead of 3600s)
        $this->assertEquals(5400, $attempt['duration_seconds']);
        $this->assertGreaterThanOrEqual(5395, $attempt['remaining_seconds']);
    }

    public function test_timer_expiry_blocks_draft_save_and_marks_expired(): void
    {
        Carbon::setTestNow('2026-11-04 09:00:00');

        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(3);

        $this->actingAs($this->asesi);

        $this->cbtService->startOrResumeAttempt($assessment);

        // Advance clock past duration (61 minutes)
        Carbon::setTestNow('2026-11-04 10:01:00');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Waktu pengerjaan ujian telah habis');

        $this->cbtService->saveDraft($assessment, [$questions[0]->id => 'A']);

        Carbon::setTestNow();
    }

    public function test_draft_saving_and_network_retry_are_idempotent(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(3);

        $this->actingAs($this->asesi);

        $this->cbtService->startOrResumeAttempt($assessment);

        // Save answer for Q1
        $res1 = $this->cbtService->saveDraft($assessment, [$questions[0]->id => 'A']);
        $this->assertEquals('success', $res1['status']);
        $this->assertEquals(1, $res1['count']);

        // Network retry with Q1 + Q2
        $res2 = $this->cbtService->saveDraft($assessment, [$questions[1]->id => 'B']);
        $this->assertEquals('success', $res2['status']);
        $this->assertEquals(2, $res2['count']);

        // Verify database state has merged answers
        $saved = AssessmentIaResponse::where('assessment_id', $assessment->id)->first();
        $this->assertEquals('A', $saved->data_json[$questions[0]->id]);
        $this->assertEquals('B', $saved->data_json[$questions[1]->id]);
    }

    public function test_final_submit_grades_accurately_without_auto_competence(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(5);

        $this->actingAs($this->asesi);

        // Answer 4 correctly (A) and 1 incorrectly (B)
        $answers = [
            $questions[0]->id => 'A',
            $questions[1]->id => 'A',
            $questions[2]->id => 'A',
            $questions[3]->id => 'A',
            $questions[4]->id => 'B', // Wrong
        ];

        $submitted = $this->cbtService->submitFinal($assessment, $answers, 'SIG-SUBMIT');

        // Score is 4/5 * 100 = 80.00%
        $this->assertEquals('80.00', (string) $submitted->score);
        $this->assertNotNull($submitted->submitted_at);
        $this->assertNotNull($submitted->asesi_signed_at);

        // Must NOT auto-determine competency on Assessment
        $assessment->refresh();
        $this->assertNotEquals('KOMPETEN', $assessment->final_result);
        $this->assertNotEquals('K', $assessment->final_result);
        $this->assertNull($assessment->rekomendasi_asesor);
    }

    public function test_final_submit_is_idempotent_no_duplicate_submission(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(2);

        $this->actingAs($this->asesi);

        $answers = [
            $questions[0]->id => 'A',
            $questions[1]->id => 'A',
        ];

        $first = $this->cbtService->submitFinal($assessment, $answers, 'SIG-FIRST');
        $second = $this->cbtService->submitFinal($assessment, [$questions[0]->id => 'B'], 'SIG-SECOND');

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals('100.00', (string) $second->score);

        // Exactly 1 IA05 record exists
        $this->assertEquals(1, AssessmentIaResponse::where('assessment_id', $assessment->id)->where('instrument_code', 'IA05')->count());
    }

    public function test_unauthorized_user_cannot_access_or_submit_cbt(): void
    {
        [$schedule, $assessment, $questions] = $this->createScheduleAndQuestions(2);

        $this->actingAs($this->otherAsesi);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Akses tidak terotorisasi');

        $this->cbtService->startOrResumeAttempt($assessment);
    }
}
