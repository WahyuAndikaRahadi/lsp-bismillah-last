<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\CertificationDecision;
use App\Models\CompetencyUnit;
use App\Models\PlenoMeeting;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Services\CertificationDecisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CoreCertificationDecisionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $committeeChair;
    private User $assessor;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private CompetencyUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP',
            'email' => 'admin.pln@lsp.test',
        ]);

        $this->committeeChair = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Ketua Komite Teknis Independen',
            'email' => 'chair.pln@lsp.test',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Penguji Lapangan',
            'email' => 'assessor.pln@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.pln@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.pln@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-PLN-01',
            'nama_tuk' => 'TUK Uji Mandiri Pleno',
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

        $this->unit = CompetencyUnit::firstOrCreate(
            ['code' => 'OTO.SM02.001.01'],
            ['title' => 'Memelihara Engine Sepeda Motor', 'scheme_id' => $this->scheme->id, 'is_active' => true]
        );
    }

    private function createScheduleWithAssessedCandidates(): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-PLN-' . rand(100, 999),
            'name' => 'Jadwal Asesmen Siap Pleno',
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
            'assessor_id' => $this->assessor->id, // Assessed by this->assessor!
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'rekomendasi_asesor' => 'K',
            'status' => 'COMPLETED',
        ]);

        $assessment2 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi2->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => 'BK',
            'status' => 'COMPLETED',
        ]);

        return [$schedule, $assessment1, $assessment2];
    }

    public function test_conflict_of_interest_assessor_cannot_decide_for_their_own_candidate(): void
    {
        [$schedule, $assessment1] = $this->createScheduleWithAssessedCandidates();

        $service = app(CertificationDecisionService::class);

        // Assessor who assessed candidate 1 attempts to record decision for candidate 1
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('conflict of interest');

        $service->recordDecision(
            assessment: $assessment1,
            decision: 'KOMPETEN',
            actor: $this->assessor, // Forbidden: assessor of this candidate!
            authorityRole: 'Asesor Penguji'
        );
    }

    public function test_positive_recommendation_does_not_auto_certify(): void
    {
        [$schedule, $assessment1] = $this->createScheduleWithAssessedCandidates();

        // 1. Assessment has positive recommendation (K) from assessor
        $this->assertEquals('KOMPETEN', $assessment1->final_result);
        $this->assertEquals('K', $assessment1->rekomendasi_asesor);

        // 2. But pleno decision is still null, and no CertificationDecision exists
        $this->assertNull($assessment1->pleno_decision);
        $this->assertEquals(0, CertificationDecision::where('assessment_id', $assessment1->id)->count());

        // 3. Now independent committee chair records explicit decision
        $service = app(CertificationDecisionService::class);
        $decision = $service->recordDecision(
            assessment: $assessment1,
            decision: 'KOMPETEN',
            actor: $this->committeeChair,
            authorityRole: 'Ketua Komite Teknis',
            decisionNotes: 'Portofolio dan observasi diverifikasi sah oleh Komite Teknis.'
        );

        $this->assertNotNull($decision);
        $this->assertEquals('KOMPETEN', $decision->decision);
        $this->assertFalse($decision->is_superseded);

        $assessment1->refresh();
        $this->assertEquals('KOMPETEN', $assessment1->pleno_decision);
    }

    public function test_correction_requested_decision_reverts_status_without_mutating_assessor_findings(): void
    {
        [$schedule, $assessment1] = $this->createScheduleWithAssessedCandidates();

        // Pre-create an assessor finding
        $item = AssessmentAk02Item::create([
            'assessment_id' => $assessment1->id,
            'competency_unit_id' => $this->unit->id,
            'is_competent' => true,
            'finding_notes' => 'Catatan asli asesor penguji',
            'assessor_id' => $this->assessor->id,
        ]);

        $service = app(CertificationDecisionService::class);

        // Committee requests correction
        $decision = $service->recordDecision(
            assessment: $assessment1,
            decision: 'CORRECTION_REQUESTED',
            actor: $this->committeeChair,
            authorityRole: 'Ketua Komite Teknis',
            decisionNotes: 'Perlu verifikasi ulang kelengkapan bukti observasi.',
            correctiveAction: 'Asesor diminta melengkapi lampiran foto alat ukur yang digunakan.'
        );

        $this->assertEquals('CORRECTION_REQUESTED', $decision->decision);
        $this->assertEquals('Asesor diminta melengkapi lampiran foto alat ukur yang digunakan.', $decision->corrective_action);

        $assessment1->refresh();
        $this->assertEquals('REVISION_REQUESTED', $assessment1->status);
        $this->assertNull($assessment1->pleno_decision);

        // Assessor findings are NOT mutated by committee
        $item->refresh();
        $this->assertEquals('Catatan asli asesor penguji', $item->finding_notes);
        $this->assertEquals($this->assessor->id, $item->assessor_id);
    }

    public function test_new_decision_supersedes_prior_decision_preserving_history(): void
    {
        [$schedule, $assessment1] = $this->createScheduleWithAssessedCandidates();

        $service = app(CertificationDecisionService::class);

        // 1. Initial decision: CORRECTION_REQUESTED
        $dec1 = $service->recordDecision(
            assessment: $assessment1,
            decision: 'CORRECTION_REQUESTED',
            actor: $this->committeeChair,
            authorityRole: 'Ketua Komite Teknis',
            correctiveAction: 'Lengkapi bukti alat ukur.'
        );
        $this->assertFalse($dec1->is_superseded);
        $this->assertEquals('FINAL', $dec1->status);

        // 2. After revision, new decision: KOMPETEN
        $dec2 = $service->recordDecision(
            assessment: $assessment1,
            decision: 'KOMPETEN',
            actor: $this->admin,
            authorityRole: 'Manajer Sertifikasi LSP',
            decisionNotes: 'Koreksi telah diverifikasi lengkap.'
        );

        // Dec 1 is now superseded, but history preserved!
        $dec1->refresh();
        $this->assertTrue($dec1->is_superseded);
        $this->assertEquals('SUPERSEDED', $dec1->status);
        $this->assertEquals($this->admin->id, $dec1->superseded_by);
        $this->assertNotNull($dec1->superseded_at);

        // Dec 2 is active
        $this->assertFalse($dec2->is_superseded);
        $this->assertEquals('FINAL', $dec2->status);

        // Total 2 records in database
        $this->assertEquals(2, CertificationDecision::where('assessment_id', $assessment1->id)->count());
    }

    public function test_pleno_controller_store_flow_records_decisions_for_all_candidates(): void
    {
        [$schedule, $assessment1, $assessment2] = $this->createScheduleWithAssessedCandidates();

        $this->actingAs($this->admin);

        $postData = [
            'nomor_ba_pleno' => '001/BA-PLENO/XI/2026',
            'tanggal_pleno' => now()->toDateString(),
            'tempat' => 'Ruang Rapat Pleno LSP ProIndo',
            'ketua_komite' => 'Ketua Komite Teknis',
            'anggota_komite' => "1. Ketua Komite Teknis\n2. Manajer Sertifikasi",
            'catatan_pleno' => 'Rapat pleno penetapan hasil asesmen.',
            'decisions' => [
                $assessment1->id => [
                    'decision' => 'KOMPETEN',
                    'notes' => 'Memenuhi seluruh kriteria kompetensi.',
                ],
                $assessment2->id => [
                    'decision' => 'BELUM_KOMPETEN',
                    'notes' => 'Diperlukan asesmen ulang pada unit alat ukur.',
                ],
            ],
        ];

        $response = $this->post(route('admin.pleno.store', $schedule->id), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.pleno.show', $schedule->id));

        // PlenoMeeting created
        $pleno = PlenoMeeting::where('schedule_id', $schedule->id)->first();
        $this->assertNotNull($pleno);
        $this->assertEquals('001/BA-PLENO/XI/2026', $pleno->nomor_ba_pleno);

        // Decisions created for both candidates
        $dec1 = CertificationDecision::where('assessment_id', $assessment1->id)->first();
        $this->assertNotNull($dec1);
        $this->assertEquals('KOMPETEN', $dec1->decision);
        $this->assertEquals($pleno->id, $dec1->pleno_meeting_id);

        $dec2 = CertificationDecision::where('assessment_id', $assessment2->id)->first();
        $this->assertNotNull($dec2);
        $this->assertEquals('BELUM_KOMPETEN', $dec2->decision);
        $this->assertEquals($pleno->id, $dec2->pleno_meeting_id);

        // Schedule status is FINISHED
        $schedule->refresh();
        $this->assertEquals('FINISHED', $schedule->status);
    }
}
