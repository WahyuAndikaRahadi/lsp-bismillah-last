<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessorReplacementEvent;
use App\Models\ConflictDeclaration;
use App\Models\ImpartialityCommitment;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreImpartialityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor1;
    private User $assessor2;
    private User $asesi;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private Schedule $schedule;
    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP ProIndo',
            'email' => 'admin.imp@lsp.test',
        ]);

        $this->assessor1 = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Satu (Berkonflik)',
            'email' => 'assessor1.imp@lsp.test',
        ]);

        $this->assessor2 = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Dua (Pengganti)',
            'email' => 'assessor2.imp@lsp.test',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Uji',
            'email' => 'asesi.imp@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-IMP-01',
            'nama_tuk' => 'TUK Mandiri',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-IMP-01'],
            ['name' => 'Pemeliharaan Engine', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-IMP-01-V1', 'title' => 'SKKNI 2020', 'is_active' => true]
        );

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-IMP-01',
            'name' => 'Jadwal Uji Ketidakberpihakan',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->schedule->assessors()->attach([$this->assessor1->id, $this->assessor2->id]);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->assessor1->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'PENDING',
        ]);
    }

    public function test_assessor_signs_impartiality_commitment_fr_sop_01_5(): void
    {
        $this->actingAs($this->assessor1);

        $response = $this->post(route('asesor.impartiality.commitment.sign'), [
            'agreement' => '1',
            'signature_data' => 'data:image/png;base64,sample_assessor_signature',
        ]);

        $response->assertRedirect(route('asesor.impartiality.commitment'));
        $response->assertSessionHas('success');

        $commitment = ImpartialityCommitment::where('user_id', $this->assessor1->id)
            ->where('commitment_type', 'PAKTA_INTEGRITAS_ASESOR')
            ->firstOrFail();

        $this->assertTrue($commitment->is_active);
        $this->assertNotNull($commitment->signed_at);
        $this->assertStringContainsString('Bertindak independen', $commitment->statement_text);
        $this->assertEquals('data:image/png;base64,sample_assessor_signature', $commitment->signature_data);
    }

    public function test_unresolved_conflict_blocks_assessor_from_assessing_candidate(): void
    {
        // 1. Asesor 1 mendeklarasikan benturan kepentingan terhadap Asesi
        ConflictDeclaration::create([
            'declarant_id' => $this->assessor1->id,
            'schedule_id' => $this->schedule->id,
            'assessment_id' => $this->assessment->id,
            'related_asesi_id' => $this->asesi->id,
            'has_conflict' => true,
            'conflict_nature' => 'Asesi adalah keponakan kandung asesor.',
            'status' => 'OPEN',
            'declared_at' => now(),
        ]);

        // Verifikasi helper method
        $this->assertTrue(ConflictDeclaration::hasUnresolvedConflict($this->assessor1->id, $this->assessment));

        // 2. Asesor 1 mencoba memutuskan APL -> Ditolak 403 karena benturan kepentingan
        $this->actingAs($this->assessor1);

        $response = $this->post(route('asesor.apl.decide', $this->assessment->id), [
            'apl_status' => 'APPROVE',
        ]);

        $response->assertForbidden();

        // Status APL tidak berubah
        $this->assessment->refresh();
        $this->assertEquals('SUBMITTED', $this->assessment->apl_status);
    }

    public function test_assessor_replacement_fr_sop_01_7_reassigns_candidate_and_resolves_conflict(): void
    {
        // 1. Pre-condition: Ada benturan kepentingan terbuka
        $declaration = ConflictDeclaration::create([
            'declarant_id' => $this->assessor1->id,
            'schedule_id' => $this->schedule->id,
            'assessment_id' => $this->assessment->id,
            'related_asesi_id' => $this->asesi->id,
            'has_conflict' => true,
            'conflict_nature' => 'Rekan kerja satu unit bisnis.',
            'status' => 'OPEN',
            'declared_at' => now(),
        ]);

        // 2. Admin melakukan pergantian asesor resmi (FR-SOP-01.7) ke Asesor 2
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.assessments.replace-assessor', $this->assessment->id), [
            'replacement_assessor_id' => $this->assessor2->id,
            'reason' => 'Pergantian mandatori akibat benturan kepentingan rekan kerja.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verifikasi event FR-SOP-01.7 tercatat
        $event = AssessorReplacementEvent::where('assessment_id', $this->assessment->id)->firstOrFail();
        $this->assertEquals($this->assessor1->id, $event->previous_assessor_id);
        $this->assertEquals($this->assessor2->id, $event->replacement_assessor_id);
        $this->assertEquals($this->admin->id, $event->approved_by);

        // Verifikasi penugasan assessment telah dialihkan ke Asesor 2
        $this->assessment->refresh();
        $this->assertEquals($this->assessor2->id, $this->assessment->assessor_id);

        // Status deklarasi benturan lama berubah menjadi TRANSFERRED
        $declaration->refresh();
        $this->assertEquals('TRANSFERRED', $declaration->status);

        // Asesor 2 (pengganti yang bersih) kini dapat melakukan review APL tanpa blokir
        $this->actingAs($this->assessor2);
        $responseDecide = $this->post(route('asesor.apl.decide', $this->assessment->id), [
            'apl_status' => 'APPROVE',
        ]);
        $responseDecide->assertSessionHasNoErrors();

        $this->assessment->refresh();
        $this->assertEquals('APPROVED', $this->assessment->apl_status);
    }

    public function test_unauthorized_user_cannot_replace_assessor(): void
    {
        $this->actingAs($this->asesi);

        $response = $this->post(route('admin.assessments.replace-assessor', $this->assessment->id), [
            'replacement_assessor_id' => $this->assessor2->id,
            'reason' => 'Asesi mencoba mengganti asesor sendiri.',
        ]);

        // Dialihkan/ditolak oleh role protection
        $response->assertRedirect();
        $this->assessment->refresh();
        $this->assertEquals($this->assessor1->id, $this->assessment->assessor_id);
    }
}
