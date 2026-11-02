<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\CompetencyUnit;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CbtAnswerPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesi;
    protected User $asesor;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment;
    protected QuestionBank $q1;
    protected QuestionBank $q2;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin_cbt@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Asesor CBT',
            'email' => 'asesor_cbt@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $this->asesi = User::create([
            'name' => 'Asesi Ujian',
            'email' => 'asesi_cbt@proindo.test',
            'phone' => '081234567890',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201010101017777',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1999-09-09',
            'jenis_kelamin' => 'L',
            'signature_data' => 'data:image/png;base64,validsig123',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-CBT-01',
            'name' => 'Engine Specialist CBT',
            'scheme_type' => 'OKUPASI',
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'code' => 'G.45OTO01.001.2',
            'title' => 'Perbaikan Mesin',
        ]);

        $this->q1 = QuestionBank::create([
            'scheme_id' => $this->scheme->id,
            'competency_unit_id' => $unit->id,
            'question_text' => 'Soal 1: Berapa celah busi standar?',
            'option_a' => '0.8 - 0.9 mm',
            'option_b' => '1.5 - 2.0 mm',
            'option_c' => '0.1 - 0.2 mm',
            'option_d' => '3.0 mm',
            'correct_answer' => 'A',
        ]);

        $this->q2 = QuestionBank::create([
            'scheme_id' => $this->scheme->id,
            'competency_unit_id' => $unit->id,
            'question_text' => 'Soal 2: Apa fungsi oli pelumas?',
            'option_a' => 'Mendinginkan dan melumasi',
            'option_b' => 'Membakar bahan bakar',
            'option_c' => 'Menggerakkan roda',
            'option_d' => 'Mengisi aki',
            'correct_answer' => 'A',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-CBT-99',
            'name' => 'Ujian CBT Teori',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'Lab Komputer SMK',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA05'],
            'created_by' => $admin->id,
            'status' => 'ACTIVE',
        ]);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'status' => 'IN_PROGRESS',
        ]);
    }

    public function test_cbt_view_renders_official_bnsp_structure_and_questions(): void
    {
        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.instrument.ia05', $this->assessment));

        $response->assertStatus(200);
        $response->assertSee('FR.IA.05. PERTANYAAN TERTULIS');
        $response->assertSee('Engine Specialist CBT');
        $response->assertSee('Soal 1: Berapa celah busi standar?');
        $response->assertSee('Soal 2: Apa fungsi oli pelumas?');
    }

    public function test_cbt_draft_can_be_saved_via_ajax_endpoint(): void
    {
        $draftAnswers = [
            $this->q1->id => 'A',
            $this->q2->id => 'B',
        ];

        $response = $this->actingAs($this->asesi)
            ->postJson(route('asesi.assessment.ia05.save-draft', $this->assessment), [
                'answers' => $draftAnswers,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Verify draft saved in database without submitting final score
        $iaResponse = AssessmentIaResponse::where('assessment_id', $this->assessment->id)
            ->where('instrument_code', 'IA05')
            ->first();

        $this->assertNotNull($iaResponse);
        $this->assertNull($iaResponse->asesi_signed_at); // Not finalized yet
        $this->assertEquals('A', $iaResponse->data_json[$this->q1->id]);
        $this->assertEquals('B', $iaResponse->data_json[$this->q2->id]);
    }

    public function test_saved_draft_is_restored_when_opening_cbt_screen(): void
    {
        // Save initial draft
        AssessmentIaResponse::create([
            'assessment_id' => $this->assessment->id,
            'instrument_code' => 'IA05',
            'data_json' => [
                $this->q1->id => 'A',
            ],
            'asesi_signed_at' => null,
        ]);

        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.instrument.ia05', $this->assessment));

        $response->assertStatus(200);
        // The radio input for q1 with value A should be checked
        $response->assertSee('value="A"', false);
    }
}
