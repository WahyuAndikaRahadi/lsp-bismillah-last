<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\LspProfile;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\User;
use App\Services\SchemeReferenceService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreReferenceImportTest extends TestCase
{
    use RefreshDatabase;

    private SchemeReferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SchemeReferenceService::class);
    }

    private function sampleSchemePayload(): array
    {
        return [
            'code' => 'SKM-TEST-01',
            'name' => 'Skema Uji Validasi Referensi',
            'scheme_type' => 'KLASTER',
            'units' => [
                [
                    'code' => 'OTO.KR01.001.01',
                    'title' => 'Melakukan Perawatan Sistem Rem',
                    'elements' => [
                        [
                            'name' => 'Memeriksa keausan pad rem',
                            'kuks' => [
                                [
                                    'code' => '1.1',
                                    'criteria' => 'Ketebalan pad rem diukur sesuai SOP pabrik.',
                                ],
                                [
                                    'code' => '1.2',
                                    'criteria' => 'Kondisi piringan cakram diperiksa dari keolengan.',
                                ],
                            ],
                        ],
                    ],
                    'questions' => [
                        [
                            'question' => 'Berapa batas ketebalan minimum pad rem?',
                            'options' => ['1 mm', '5 mm', '10 mm'],
                            'answer_key' => '1 mm',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_dry_run_is_strictly_read_only_and_writes_no_records(): void
    {
        $payload = $this->sampleSchemePayload();

        $result = $this->service->import($payload, dryRun: true);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dry_run']);
        $this->assertEquals('validated_read_only', $result['action']);

        // Zero changes in database
        $this->assertEquals(0, Scheme::count());
        $this->assertEquals(0, SchemeVersion::count());
    }

    public function test_import_validation_pinpoints_exact_source_and_field_errors(): void
    {
        $corruptPayload = [
            'code' => 'SKM-ERR-01',
            'name' => 'Skema Cacat',
            'scheme_type' => 'KLASTER',
            'units' => [
                [
                    'code' => 'UNIT.ERR.01',
                    'title' => 'Unit Bermasalah',
                    'elements' => [
                        [
                            'name' => 'Elemen 1',
                            'kuks' => [
                                ['code' => '1.1', 'criteria' => 'Valid KUK'],
                                ['code' => '1.1', 'criteria' => 'Duplicate KUK code'], // Duplicate
                            ],
                        ],
                    ],
                    'questions' => [
                        [
                            'question' => 'Soal tanpa kunci?',
                            'options' => ['A', 'B'],
                            'answer_key' => '', // Empty answer key!
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->service->import($corruptPayload, dryRun: false);

        $this->assertFalse($result['success']);
        $errors = implode(' | ', $result['errors']);

        // Error pinpointing source and field
        $this->assertStringContainsString("Duplikasi KUK code '1.1'", $errors);
        $this->assertStringContainsString("UNIT.ERR.01", $errors);
        $this->assertStringContainsString("Kunci jawaban (answer_key) tidak boleh kosong", $errors);

        // Database stays untouched
        $this->assertEquals(0, Scheme::count());
    }

    public function test_reimport_is_idempotent_and_does_not_duplicate_approved_reference(): void
    {
        $payload = $this->sampleSchemePayload();

        // 1. Initial import creates draft
        $res1 = $this->service->import($payload, dryRun: false);
        $this->assertTrue($res1['success']);
        $this->assertEquals('created_draft', $res1['action']);
        $this->assertEquals(1, SchemeVersion::count());

        $version = SchemeVersion::first();

        // Setup LSP profile for approval
        LspProfile::create([
            'nama_lsp' => 'LSP PRO INDO',
            'kode_lsp' => 'LSP-026-ID',
            'no_lisensi_bnsp' => 'BNSP-LSP-026-ID',
            'nama_ketua' => 'Ir. Bambang S., M.T.',
        ]);

        // Publish version
        $published = $this->service->publishVersion($version->id, [
            'reviewer_name' => 'Validator Resmi BNSP',
            'review_date' => '2026-11-01',
        ]);
        $this->assertEquals('published', $published->status);

        // 2. Re-import identical payload: must be skipped_approved without modifying or creating new records
        $res2 = $this->service->import($payload, dryRun: false);
        $this->assertTrue($res2['success']);
        $this->assertEquals('skipped_approved', $res2['action']);
        $this->assertEquals(1, SchemeVersion::count());

        $published->refresh();
        $this->assertEquals('published', $published->status);
    }

    public function test_source_change_generates_new_draft_version(): void
    {
        $payload = $this->sampleSchemePayload();
        $this->service->import($payload, dryRun: false);

        // Alter payload (source changed)
        $payload['units'][0]['elements'][0]['kuks'][] = [
            'code' => '1.3',
            'criteria' => 'Tambahan kriteria baru pada revisi skema.',
        ];

        $res2 = $this->service->import($payload, dryRun: false);

        $this->assertTrue($res2['success']);
        $this->assertEquals('created_draft', $res2['action']);
        $this->assertEquals('v2.0', $res2['version']);
        $this->assertEquals(2, SchemeVersion::count());
    }

    public function test_missing_lsp_config_prevents_publishing(): void
    {
        $payload = $this->sampleSchemePayload();
        $res = $this->service->import($payload, dryRun: false);
        $version = SchemeVersion::first();

        // Without LSP Profile, publish must be rejected with DomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Konfigurasi resmi LSP (nama, no lisensi, ketua) belum lengkap');

        $this->service->publishVersion($version->id, [
            'reviewer_name' => 'Dr. Reviewer',
            'review_date' => '2026-11-01',
        ]);
    }

    public function test_retire_version_preserves_historical_assessment_snapshots(): void
    {
        $payload = $this->sampleSchemePayload();
        $this->service->import($payload, dryRun: false);
        $version = SchemeVersion::first();

        LspProfile::create([
            'nama_lsp' => 'LSP PRO INDO',
            'kode_lsp' => 'LSP-026-ID',
            'no_lisensi_bnsp' => 'BNSP-LSP-026-ID',
            'nama_ketua' => 'Ir. Bambang S., M.T.',
        ]);

        $this->service->publishVersion($version->id, [
            'reviewer_name' => 'Validator',
            'review_date' => '2026-11-01',
        ]);

        $user = User::factory()->create();
        $asesor = User::factory()->create();
        $scheme = Scheme::first();

        $schedule = Schedule::create([
            'code' => 'SCH-01',
            'name' => 'Jadwal Historis',
            'scheme_id' => $scheme->id,
            'tuk_name' => 'TUK Utama',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['IA01'],
            'created_by' => $asesor->id,
            'status' => 'ACTIVE',
        ]);

        // Assessment tied to the scheme version with a snapshot
        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $user->id,
            'assessor_id' => $asesor->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
            'snapshot_profile' => [
                'version' => $version->version,
                'scheme_code' => $version->code,
                'snapshot_time' => now()->toIso8601String(),
            ],
        ]);

        // Retire the version
        $retired = $this->service->retireVersion($version->id, 'Replaced by next standard edition');
        $this->assertEquals('retired', $retired->status);

        // Historical snapshot on assessment is completely preserved
        $assessment->refresh();
        $this->assertNotNull($assessment->snapshot_profile);
        $this->assertEquals($version->version, $assessment->snapshot_profile['version']);
    }

    public function test_bootstrap_admin_command_creates_admin_without_hardcoded_demo_password(): void
    {
        $this->artisan('lsp:bootstrap-admin', [
            '--name' => 'Administrator Utama',
            '--email' => 'admin_baru@lsp.test',
            '--password' => 'SecurePassw0rd!',
            '--nik' => '3201010101990001',
            '--phone' => '081234567890',
        ])->assertExitCode(0);

        $admin = User::where('email', 'admin_baru@lsp.test')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('SecurePassw0rd!', $admin->password));
    }
}
