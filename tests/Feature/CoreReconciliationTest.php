<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\Assessment;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\LegacyIdentityMap;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\Tuk;
use App\Models\User;
use App\Services\LegacyReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoreReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private User $userB;
    private User $asesor;
    private Scheme $scheme;
    private Schedule $schedule;
    private CertificationScheme $legacyScheme;
    private AssessmentSchedule $legacySchedule;
    private LegacyReconciliationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LegacyReconciliationService::class);

        $this->userA = User::factory()->create([
            'name' => 'Kandidat Legacy A',
            'email' => 'kandidat_a@example.com',
            'role' => 'asesi',
        ]);

        $this->userB = User::factory()->create([
            'name' => 'Kandidat Canonical B',
            'email' => 'kandidat_b@example.com',
            'role' => 'asesi',
        ]);

        $this->asesor = User::factory()->create([
            'name' => 'Asesor Tim',
            'email' => 'asesor_tim@example.com',
            'role' => 'asesor',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-CORE-01',
            'name' => 'Skema Core Otomotif',
            'scheme_type' => 'KLASTER',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-CORE-01',
            'name' => 'Jadwal Core',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'TUK Utama',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['IA01', 'IA05'],
            'created_by' => $this->asesor->id,
            'status' => 'ACTIVE',
        ]);

        // Legacy master data for legacy foreign key constraints
        $this->legacyScheme = CertificationScheme::create([
            'kode_skema' => 'SKM-LEGACY-01',
            'nama_skema' => 'Skema Legacy',
            'jenis_skema' => 'Okupasi',
        ]);

        $legacyEvent = Event::create([
            'kode_event' => 'EVT-LEGACY-01',
            'nama_event' => 'Event Sertifikasi Legacy',
            'tahun_anggaran' => 2026,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->toDateString(),
        ]);

        $legacyTuk = Tuk::create([
            'kode_tuk' => 'TUK-LEGACY-01',
            'nama_tuk' => 'TUK Legacy',
            'jenis_tuk' => 'Sewaktu',
        ]);

        $this->legacySchedule = AssessmentSchedule::create([
            'kode_jadwal' => 'SCH-LEG-01',
            'nama_jadwal' => 'Jadwal Legacy',
            'scheme_id' => $this->legacyScheme->id,
            'event_id' => $legacyEvent->id,
            'tuk_id' => $legacyTuk->id,
            'tgl_asesmen' => now()->toDateString(),
        ]);
    }

    public function test_two_domains_with_same_numeric_id_produce_distinct_objects_without_collision(): void
    {
        // 1. Pre-create canonical Assessment with ID 1 for User B
        $canonicalAssessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->userB->id,
            'assessor_id' => $this->asesor->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
        ]);
        $this->assertEquals(1, $canonicalAssessment->id);

        // 2. Pre-create legacy AssessmentApplication with ID 1 for User A
        $legacyApp = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-LEGACY-001',
            'scheme_id' => $this->legacyScheme->id,
            'user_id' => $this->userA->id,
            'schedule_id' => $this->legacySchedule->id,
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi',
        ]);
        $this->assertEquals(1, $legacyApp->id);

        // 3. Run reconciliation in apply mode
        $report = $this->service->reconcile(dryRun: false);

        $this->assertEquals(1, $report['mapped']);
        $this->assertEquals(0, $report['conflicts']);

        // Verify LegacyIdentityMap recorded mapping correctly
        $map = LegacyIdentityMap::where('source_table', 'assessment_applications')
            ->where('legacy_id', $legacyApp->id)
            ->first();

        $this->assertNotNull($map);
        $this->assertEquals('mapped', $map->status);
        $this->assertEquals('t_assessments', $map->target_table);

        // Target canonical assessment ID must NOT overwrite User B's record (ID 1)!
        $this->assertNotEquals(1, $map->target_id);

        $reconciledAssessment = Assessment::find($map->target_id);
        $this->assertNotNull($reconciledAssessment);
        $this->assertEquals($this->userA->id, $reconciledAssessment->asesi_id);

        // Verify canonical Assessment ID 1 still belongs to User B untouched
        $canonicalAssessment->refresh();
        $this->assertEquals($this->userB->id, $canonicalAssessment->asesi_id);
    }

    public function test_dry_run_mode_does_not_mutate_canonical_domain(): void
    {
        $initialCount = Assessment::count();

        $legacyApp = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-LEGACY-002',
            'scheme_id' => $this->legacyScheme->id,
            'user_id' => $this->userA->id,
            'schedule_id' => $this->legacySchedule->id,
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi',
        ]);

        $report = $this->service->reconcile(dryRun: true);

        $this->assertTrue($report['dry_run']);
        $this->assertEquals(1, $report['mapped']);

        // Zero changes in canonical domain
        $this->assertEquals($initialCount, Assessment::count());
        $this->assertEquals(0, LegacyIdentityMap::count());
    }

    public function test_orphan_records_are_quarantined_and_retry_is_idempotent(): void
    {
        $legacyApp = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-LEGACY-003',
            'scheme_id' => $this->legacyScheme->id,
            'user_id' => $this->userA->id,
            'schedule_id' => null, // Orphan schedule: not associated to any valid schedule
            'asesor_id' => $this->asesor->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi',
        ]);

        // First run: quarantines orphan
        $report1 = $this->service->reconcile(dryRun: false);
        $this->assertEquals(1, $report1['quarantined']);

        $map = LegacyIdentityMap::where('source_table', 'assessment_applications')
            ->where('legacy_id', $legacyApp->id)
            ->first();
        $this->assertNotNull($map);
        $this->assertEquals('quarantined', $map->status);
        $this->assertNull($map->target_id);

        // Second run: idempotent, skips already processed
        $report2 = $this->service->reconcile(dryRun: false);
        $this->assertEquals(0, $report2['mapped']);
        $this->assertEquals(1, LegacyIdentityMap::count());
    }

    public function test_large_snapshot_payload_exceeding_64kb_is_preserved_without_truncation(): void
    {
        $assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->userA->id,
            'assessor_id' => $this->asesor->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
        ]);

        // Generate a payload guaranteed to exceed 75 KB
        $largeString = str_repeat('ABCDEF1234567890', 5000); // 80,000 bytes
        $payload = [
            'applicant_info' => [
                'name' => $this->userA->name,
                'email' => $this->userA->email,
                'raw_large_data' => $largeString,
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        $jsonLength = strlen(json_encode($payload));
        $this->assertGreaterThan(65536, $jsonLength, 'Payload must exceed 64KB MySQL TEXT limit');

        $this->service->storeSnapshot($assessment, $payload);

        $assessment->refresh();
        $retrieved = $this->service->getSnapshot($assessment);

        $this->assertNotNull($retrieved);
        $this->assertEquals($this->userA->name, $retrieved['applicant_info']['name']);
        $this->assertEquals($largeString, $retrieved['applicant_info']['raw_large_data']);
    }

    public function test_artisan_reconcile_command_runs_successfully(): void
    {
        $this->artisan('lsp:reconcile-legacy', ['--dry-run' => true])
            ->assertExitCode(0);
    }
}
