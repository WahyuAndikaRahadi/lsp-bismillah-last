<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AppealInvestigation;
use App\Models\Assessment;
use App\Models\AssessmentAppeal;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Setting;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CoreAppealTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $involvedAssessor;
    private User $independentAssessor;
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
            'name' => 'Admin LSP ProIndo',
            'email' => 'admin.appeal@lsp.test',
        ]);

        $this->involvedAssessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Penguji Kasus',
            'email' => 'assessor.involved@lsp.test',
        ]);

        $this->independentAssessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Independen',
            'email' => 'assessor.independent@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Pemohon Banding',
            'email' => 'asesi1.appeal@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Lain',
            'email' => 'asesi2.appeal@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-APL-01',
            'nama_tuk' => 'TUK Uji Banding',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-APL-01'],
            ['name' => 'Pemeliharaan Engine', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-APL-01-V1', 'title' => 'SKKNI 2020', 'is_active' => true]
        );
    }

    private function createAssessmentWithBkResult(?string $finalResultDate = null): Assessment
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-APL-' . rand(100, 999),
            'name' => 'Jadwal Asesmen Banding',
            'date' => $finalResultDate ?? now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        return Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->involvedAssessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => 'BK',
            'final_result_at' => $finalResultDate ? now()->parse($finalResultDate) : now(),
            'status' => 'COMPLETED',
        ]);
    }

    public function test_filing_ak04_within_configured_deadline_succeeds_without_auto_competence(): void
    {
        // 1. Set deadline window 5 business days
        Setting::set('appeal_window_days', '5');

        $assessment = $this->createAssessmentWithBkResult(now()->toDateString());

        $this->actingAs($this->asesi1);

        $response = $this->post(route('asesi.assessment.appeal.submit', $assessment->id), [
            'prosedur_dijelaskan' => 1,
            'diskusi_dengan_asesor' => 1,
            'melibatkan_orang_lain' => 0,
            'appeal_reasons' => 'Terdapat kesalahan pembacaan hasil pengukuran kompresi silinder.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('asesi.jadwal.index'));

        // Permohonan banding tercatat
        $appeal = AssessmentAppeal::where('assessment_id', $assessment->id)->firstOrFail();
        $this->assertEquals('SUBMITTED', $appeal->status);
        $this->assertEquals('Terdapat kesalahan pembacaan hasil pengukuran kompresi silinder.', $appeal->appeal_reasons);

        // Hasil akhir asesmen TIDAK otomatis berubah menjadi kompeten
        $assessment->refresh();
        $this->assertEquals('BELUM_KOMPETEN', $assessment->final_result);
        $this->assertEquals('BK', $assessment->rekomendasi_asesor);
    }

    public function test_filing_ak04_past_deadline_is_rejected(): void
    {
        // Set deadline window 3 days
        Setting::set('appeal_window_days', '3');

        // Asesmen selesai 10 hari yang lalu (tenggat waktu terlampaui)
        $pastDate = now()->subDays(10)->toDateString();
        $assessment = $this->createAssessmentWithBkResult($pastDate);

        $this->actingAs($this->asesi1);

        $response = $this->post(route('asesi.assessment.appeal.submit', $assessment->id), [
            'prosedur_dijelaskan' => 1,
            'diskusi_dengan_asesor' => 1,
            'melibatkan_orang_lain' => 0,
            'appeal_reasons' => 'Mengajukan banding setelah melewati batas waktu resmi.',
        ]);

        $response->assertSessionHasErrors('appeal_reasons');
        $this->assertEquals(0, AssessmentAppeal::where('assessment_id', $assessment->id)->count());
    }

    public function test_involved_assessor_is_strictly_forbidden_as_investigator(): void
    {
        Setting::where('key', 'appeal_window_days')->delete();

        $assessment = $this->createAssessmentWithBkResult();
        $appeal = AssessmentAppeal::create([
            'assessment_id' => $assessment->id,
            'appeal_date' => now()->toDateString(),
            'prosedur_dijelaskan' => true,
            'diskusi_dengan_asesor' => true,
            'melibatkan_orang_lain' => false,
            'appeal_reasons' => 'Keberatan atas hasil observasi pengukuran kelistrikan.',
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($this->admin);

        // Admin mencoba menugaskan $this->involvedAssessor (yang menguji kandidat) -> Wajib ditolak!
        $response = $this->post(route('admin.appeals.investigate', $appeal->id), [
            'investigator_id' => $this->involvedAssessor->id,
            'investigation_date' => now()->toDateString(),
            'investigation_place' => 'Kantor LSP ProIndo',
            'factual_findings' => 'Investigasi internal dilakukan.',
            'recommendation' => 'PERTAHANKAN_KEPUTUSAN',
        ]);

        $response->assertSessionHasErrors('investigator_id');

        // Tidak ada record investigasi yang tersimpan
        $this->assertEquals(0, AppealInvestigation::count());
    }

    public function test_independent_investigation_records_minutes_fr_sop_07_2_without_automatic_competence(): void
    {
        $assessment = $this->createAssessmentWithBkResult();
        $appeal = AssessmentAppeal::create([
            'assessment_id' => $assessment->id,
            'appeal_date' => now()->toDateString(),
            'prosedur_dijelaskan' => true,
            'diskusi_dengan_asesor' => true,
            'melibatkan_orang_lain' => false,
            'appeal_reasons' => 'Perbedaan penafsiran toleransi batas ukur vernier caliper.',
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($this->admin);

        // Tugaskan asesor independen
        $response = $this->post(route('admin.appeals.investigate', $appeal->id), [
            'investigator_id' => $this->independentAssessor->id,
            'investigation_date' => now()->toDateString(),
            'investigation_place' => 'Ruang Sidang LSP ProIndo',
            'factual_findings' => 'Telah dilakukan pemeriksaan ulang terhadap lembar observasi IA.01 dan video rekaman. Ditemukan bahwa toleransi alat ukur masih memenuhi kriteria standar SKKNI.',
            'recommendation' => 'ASESMEN_ULANG',
            'recommendation_notes' => 'Direkomendasikan pelaksanaan uji observasi ulang terbatas pada unit alat ukur presisi.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.appeals.show', $appeal->id));

        // Verifikasi record FR-SOP-07.2
        $investigation = AppealInvestigation::where('appeal_id', $appeal->id)->firstOrFail();
        $this->assertEquals($this->independentAssessor->id, $investigation->investigator_id);
        $this->assertEquals('Ruang Sidang LSP ProIndo', $investigation->investigation_place);
        $this->assertEquals('ASESMEN_ULANG', $investigation->recommendation);
        $this->assertEquals($this->admin->id, $investigation->decided_by);

        // Status appeal di-update menjadi ACCEPTED
        $appeal->refresh();
        $this->assertEquals('ACCEPTED', $appeal->status);

        // Sesuai SOP 07: Keputusan asesmen ulang TIDAK otomatis mengubah hasil kandidat jadi KOMPETEN
        $assessment->refresh();
        $this->assertEquals('BELUM_KOMPETEN', $assessment->final_result);
    }

    public function test_confidentiality_isolation_denies_unauthorized_parties(): void
    {
        $assessment = $this->createAssessmentWithBkResult();
        $appeal = AssessmentAppeal::create([
            'assessment_id' => $assessment->id,
            'appeal_date' => now()->toDateString(),
            'prosedur_dijelaskan' => true,
            'diskusi_dengan_asesor' => true,
            'melibatkan_orang_lain' => false,
            'appeal_reasons' => 'Kerahasiaan dokumen banding.',
            'status' => 'SUBMITTED',
        ]);

        // 1. Asesi 1 (pemilik berkas) dapat melihat status bandingnya
        $this->actingAs($this->asesi1);
        $response1 = $this->get(route('asesi.appeals.show', $appeal->id));
        $response1->assertOk();

        // 2. Asesi 2 (orang lain) mencoba melihat banding Asesi 1 -> 403 Forbidden
        $this->actingAs($this->asesi2);
        $response2 = $this->get(route('asesi.appeals.show', $appeal->id));
        $response2->assertForbidden();

        // 3. Policy check: Asesor terkait tidak memiliki hak kelola banding
        $this->assertFalse(Gate::forUser($this->involvedAssessor)->allows('manage', $appeal));
    }
}
