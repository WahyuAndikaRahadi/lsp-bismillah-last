<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentAk03Item;
use App\Models\AssessmentAppeal;
use App\Models\CompetencyUnit;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi1;
    private User $asesi2;
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

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Kompetensi',
            'email' => 'assessor.fb@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.fb@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.fb@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-FB-01',
            'nama_tuk' => 'TUK Uji Mandiri',
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

    private function createAssessment(string $result = 'BELUM_SELESAI'): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-FB-' . rand(100, 999),
            'name' => 'Jadwal Uji Asesmen',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => $result === 'BELUM_SELESAI' ? 'scheduled' : 'COMPLETED',
            'final_result' => $result,
            'rekomendasi_asesor' => $result === 'KOMPETEN' ? 'K' : ($result === 'BELUM_KOMPETEN' ? 'BK' : null),
            'final_result_at' => $result === 'BELUM_SELESAI' ? null : now(),
        ]);

        return [$schedule, $assessment];
    }

    public function test_ak03_cannot_be_accessed_before_assessor_recommendation_is_established(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_SELESAI');

        $this->actingAs($this->asesi1);

        $response = $this->get(route('asesi.assessment.ak03', $assessment->id));
        $response->assertRedirect(route('asesi.assessment.ruang-uji', $assessment->id));
        $response->assertSessionHas('error');
    }

    public function test_ak03_all_ten_components_round_trip_without_mutating_assessment_result(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_KOMPETEN');

        $this->actingAs($this->asesi1);

        // Ten canonical questions of FR.AK.03
        $ratings = [
            1 => ['value' => 'YA', 'comment' => 'Penjelasan awal sangat jelas'],
            2 => ['value' => 'YA', 'comment' => 'Asesor memperkenalkan diri dan ramah'],
            3 => ['value' => 'YA', 'comment' => 'Kerahasiaan dijamin'],
            4 => ['value' => 'YA', 'comment' => 'Peralatan memadai'],
            5 => ['value' => 'TIDAK', 'comment' => 'Waktu pengerjaan terasa agak sempit'],
            6 => ['value' => 'YA', 'comment' => 'Instruksi tugas praktik dapat dipahami'],
            7 => ['value' => 'YA', 'comment' => 'Pertanyaan lisan relevan dengan unjuk kerja'],
            8 => ['value' => 'YA', 'comment' => 'Umpan balik disampaikan secara konstruktif'],
            9 => ['value' => 'YA', 'comment' => 'Peluang banding dijelaskan'],
            10 => ['value' => 'YA', 'comment' => 'Pelayanan TUK baik'],
        ];

        $postData = [
            'ratings' => $ratings,
            'general_notes' => 'Secara umum proses asesmen berjalan adil dan transparan.',
        ];

        $response = $this->post(route('asesi.assessment.ak03.submit', $assessment->id), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('asesi.jadwal.index'));

        // Verify stored in AssessmentAk03Item
        $ak03 = AssessmentAk03Item::where('assessment_id', $assessment->id)->first();
        $this->assertNotNull($ak03);
        $this->assertCount(10, $ak03->ratings_json);
        $this->assertEquals('TIDAK', $ak03->ratings_json[5]['value']);
        $this->assertEquals('Secara umum proses asesmen berjalan adil dan transparan.', $ak03->general_notes);

        // Verify assessment competence is NOT mutated
        $assessment->refresh();
        $this->assertEquals('BELUM_KOMPETEN', $assessment->final_result);
        $this->assertEquals('BK', $assessment->rekomendasi_asesor);
    }

    public function test_ak04_appeal_cannot_be_filed_before_assessor_recommendation_is_established(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_SELESAI');

        $this->actingAs($this->asesi1);

        $response = $this->get(route('asesi.assessment.appeal', $assessment->id));
        $response->assertRedirect(route('asesi.assessment.ruang-uji', $assessment->id));
        $response->assertSessionHas('error');
    }

    public function test_ak04_appeal_filing_records_real_candidate_answers_without_auto_yes(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_KOMPETEN');

        $this->actingAs($this->asesi1);

        $postData = [
            'prosedur_dijelaskan' => 1, // Ya
            'diskusi_dengan_asesor' => 0, // Tidak (no auto-Ya!)
            'melibatkan_orang_lain' => 1, // Ya
            'appeal_reasons' => 'Terdapat ketidaksesuaian pada pembacaan skala vernier caliper yang dianggap salah padahal sesuai spesifikasi manual reparasi pabrikan.',
            'contested_units' => [$this->unit2->code],
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ];

        $response = $this->post(route('asesi.assessment.appeal.submit', $assessment->id), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('asesi.jadwal.index'));

        // Verify canonical appeal record created as intake
        $appeal = AssessmentAppeal::where('assessment_id', $assessment->id)->first();
        $this->assertNotNull($appeal);
        $this->assertEquals('SUBMITTED', $appeal->status);
        $this->assertTrue($appeal->prosedur_dijelaskan);
        $this->assertFalse($appeal->diskusi_dengan_asesor); // Real answer preserved!
        $this->assertTrue($appeal->melibatkan_orang_lain);
        $this->assertStringContainsString('vernier caliper', $appeal->appeal_reasons);
        $this->assertEquals([$this->unit2->code], $appeal->contested_units);

        // Verification: Appeal filing does NOT change assessment competence
        $assessment->refresh();
        $this->assertEquals('BELUM_KOMPETEN', $assessment->final_result);
        $this->assertEquals('BK', $assessment->rekomendasi_asesor);
    }

    public function test_ak04_duplicate_active_appeal_is_blocked(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_KOMPETEN');

        // Pre-create existing SUBMITTED appeal
        AssessmentAppeal::create([
            'assessment_id' => $assessment->id,
            'appeal_date' => now()->toDateString(),
            'appeal_reasons' => 'Pengajuan banding pertama yang sedang aktif.',
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($this->asesi1);

        $postData = [
            'prosedur_dijelaskan' => 1,
            'diskusi_dengan_asesor' => 1,
            'melibatkan_orang_lain' => 0,
            'appeal_reasons' => 'Mencoba mengajukan banding kedua.',
        ];

        $response = $this->post(route('asesi.assessment.appeal.submit', $assessment->id), $postData);
        $response->assertRedirect(route('asesi.jadwal.index'));
        $response->assertSessionHas('info');

        $this->assertEquals(1, AssessmentAppeal::where('assessment_id', $assessment->id)->count());
    }

    public function test_unauthorized_user_denied_access_to_other_candidate_feedback_or_appeal(): void
    {
        [$schedule, $assessment] = $this->createAssessment('BELUM_KOMPETEN');

        // Asesi 2 tries to access Asesi 1's feedback
        $this->actingAs($this->asesi2);

        $responseAk03 = $this->get(route('asesi.assessment.ak03', $assessment->id));
        $this->assertEquals(403, $responseAk03->getStatusCode());

        $responseAppeal = $this->get(route('asesi.assessment.appeal', $assessment->id));
        $this->assertEquals(403, $responseAppeal->getStatusCode());
    }
}
