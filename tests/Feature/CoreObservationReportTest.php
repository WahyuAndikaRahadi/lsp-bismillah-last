<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\UnitElement;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\SchemeInstrumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreObservationReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP',
            'email' => 'admin@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Asesor Praktik',
            'email' => 'asesor@proindo.id',
            'status' => 'ACTIVE',
        ]);
        AsesorProfile::create([
            'user_id' => $this->assessor->id,
            'no_met' => 'MET.33344.2026',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Candidate Praktik',
            'email' => 'candidate@proindo.id',
            'status' => 'ACTIVE',
        ]);
        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201019901010077',
            'signature_data' => 'SIG-CANDIDATE',
        ]);

        $this->tuk = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-OBS-01'],
            ['nama_tuk' => 'TUK Bengkel Otomotif', 'jenis_tuk' => 'Sewaktu', 'is_active' => true]
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

    private function createScheduleWithKuks(int $kukCount = 3): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-OBS-' . rand(100, 999),
            'name' => 'Jadwal Observasi Otomotif',
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
            'esign_consent_at' => now(),
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
        ]);

        $unit = CompetencyUnit::firstOrCreate(
            ['code' => 'G.45OTO01.001.2'],
            ['scheme_id' => $this->scheme->id, 'title' => 'Melaksanakan K3', 'unit_type' => 'core']
        );

        $element = UnitElement::firstOrCreate(
            ['competency_unit_id' => $unit->id, 'sequence' => 1],
            ['title' => 'Standar Keselamatan']
        );

        $kuks = [];
        for ($i = 1; $i <= $kukCount; $i++) {
            $kuks[] = Kuk::firstOrCreate(
                ['unit_element_id' => $element->id, 'code_number' => '1.' . $i],
                ['statement' => "KUK observasi ke-{$i}", 'sequence' => $i]
            );
        }

        return [$schedule, $assessment, $kuks];
    }

    public function test_ia01_observation_stored_with_kuk_results_and_unobserved_remains_null(): void
    {
        [$schedule, $assessment, $kuks] = $this->createScheduleWithKuks(3);

        $this->actingAs($this->assessor);

        // KUK 0: K, KUK 1: BK, KUK 2: left unobserved
        $postData = [
            'kuks' => [
                $kuks[0]->id => 'K',
                $kuks[1]->id => 'BK',
            ],
            'general_notes' => 'Observasi praktik sistem K3 selesai dilaksanakan.',
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment->id, 'ia01']), $postData);
        $response->assertSessionHas('success');

        $iaResponse = AssessmentIaResponse::where('assessment_id', $assessment->id)->where('instrument_code', 'IA01')->first();
        $this->assertNotNull($iaResponse);

        $savedKuks = $iaResponse->data_json['kuks'];
        $this->assertEquals('K', $savedKuks[$kuks[0]->id]);
        $this->assertEquals('BK', $savedKuks[$kuks[1]->id]);
        $this->assertArrayNotHasKey($kuks[2]->id, $savedKuks);

        // Unobserved KUK must NOT automatically default to K or BK
        $assessment->refresh();
        $this->assertNotEquals('K', $assessment->final_result);
        $this->assertNull($assessment->rekomendasi_asesor);
    }

    public function test_ia01_foreign_kuk_rejected(): void
    {
        [$schedule, $assessment, $kuks] = $this->createScheduleWithKuks(2);

        $this->actingAs($this->assessor);

        $postData = [
            'kuks' => [
                $kuks[0]->id => 'K',
                999999 => 'K', // Foreign/unknown KUK ID
            ],
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment->id, 'ia01']), $postData);
        $response->assertSessionHasErrors(['kuks.999999']);
    }

    public function test_ia01b_report_sheet_stored_preserving_zero_and_decimals(): void
    {
        [$schedule, $assessment, $kuks] = $this->createScheduleWithKuks(1);

        $this->actingAs($this->assessor);

        $items = [
            1 => [
                'item' => 'Runout Cakram Rem',
                'standard' => 'Maks 0.10 mm',
                'measurement_value' => 0, // Valid 0
                'unit' => 'mm',
                'condition' => 'Baik',
                'result' => 'SESUAI',
            ],
            2 => [
                'item' => 'Celah Renggang Klep Masuk',
                'standard' => '0.10 - 0.15 mm',
                'measurement_value' => 0.12, // Valid decimal
                'unit' => 'mm',
                'condition' => 'Baik',
                'result' => 'SESUAI',
            ],
            3 => [
                'item' => 'Tahanan Koil Pengapian',
                'standard' => '0.5 - 1.0 Ohm',
                'measurement_value' => '0.8',
                'unit' => 'Ohm',
                'condition' => 'Baik',
                'result' => 'SESUAI',
            ],
        ];

        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment->id, 'ia01b']), [
            'items' => $items,
            'notes' => 'Pengukuran sesuai spesifikasi workshop manual.',
        ]);

        $response->assertSessionHas('success');

        $iaResponse = AssessmentIaResponse::where('assessment_id', $assessment->id)->where('instrument_code', 'IA01B')->first();
        $this->assertNotNull($iaResponse);

        $savedItems = $iaResponse->data_json['items'];

        // Crucial: 0 must be preserved as 0, not treated as empty or null
        $this->assertSame(0, $savedItems[1]['measurement_value']);
        $this->assertSame(0.12, $savedItems[2]['measurement_value']);
        $this->assertEquals('SESUAI', $savedItems[1]['result']);
    }

    public function test_ia01b_unauthorized_role_column_rejected(): void
    {
        [$schedule, $assessment, $kuks] = $this->createScheduleWithKuks(1);

        // Asesi attempts to forge assessor result column
        $this->actingAs($this->asesi);

        $items = [
            1 => [
                'item' => 'Item 1',
                'measurement_value' => 10,
                'result' => 'SESUAI', // Asesi not allowed to fill result!
            ],
        ];

        $response = $this->post(route('asesi.assessment.instrument.store', [$assessment->id, 'ia01b']), [
            'items' => $items,
        ]);

        $response->assertSessionHasErrors(['items.1.result']);
    }

    public function test_stale_save_conflict_detection(): void
    {
        [$schedule, $assessment, $kuks] = $this->createScheduleWithKuks(2);

        // Pre-create existing response with attempt_version = 2
        AssessmentIaResponse::create([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA01',
            'attempt_version' => 2,
            'data_json' => ['kuks' => [$kuks[0]->id => 'K']],
        ]);

        $this->actingAs($this->assessor);

        // Submitting with stale lock_version = 1 -> Conflict 409
        $response = $this->post(route('asesor.assessment.instrument.store', [$assessment->id, 'ia01']), [
            'lock_version' => 1,
            'kuks' => [$kuks[0]->id => 'K', $kuks[1]->id => 'K'],
        ]);

        $response->assertStatus(409);
    }

    public function test_seven_schemes_report_sheet_compatibility(): void
    {
        $schemes = [
            'SKM-01-2026' => 'Sepeda Motor Karburator',
            'SKM-02-2026' => 'Sepeda Motor Injeksi',
            'SKM-03-2026' => 'Sepeda Motor CVT',
            'SKM-04-2026' => 'Sepeda Motor Sistem Rem',
            'SKM-05-2026' => 'Sepeda Motor Kelistrikan',
            'SKM-06-2026' => 'Sepeda Motor Tune Up',
            'SKM-07-2026' => 'Sepeda Motor Rangka & Kemudi',
        ];

        $service = app(SchemeInstrumentService::class);

        foreach ($schemes as $code => $name) {
            $scheme = Scheme::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'category' => 'Automotive', 'is_active' => true]
            );

            $version = SchemeVersion::firstOrCreate(
                ['scheme_id' => $scheme->id, 'version' => 1],
                ['code' => $code . '-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
            );

            $items = $service->getReportSheetItems($scheme);
            $this->assertNotEmpty($items, "Scheme {$code} must have report sheet items");

            $schedule = Schedule::create([
                'scheme_id' => $scheme->id,
                'scheme_version_id' => $version->id,
                'tuk_id' => $this->tuk->id,
                'code' => 'SCH-S-' . $code . '-' . rand(10, 99),
                'name' => 'Jadwal ' . $name,
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
                'scheme_id' => $scheme->id,
                'apl_status' => 'APPROVED',
                'esign_consent_at' => now(),
                'ak01_agreed_at' => now(),
                'status' => 'scheduled',
            ]);

            $this->actingAs($this->assessor);
            $res = $this->post(route('asesor.assessment.instrument.store', [$assessment->id, 'ia01b']), [
                'items' => [
                    1 => [
                        'item' => $items[0]['item'] ?? 'Item 1',
                        'standard' => $items[0]['standard'] ?? 'Standar SOP',
                        'measurement_value' => 0,
                        'result' => 'SESUAI',
                    ],
                ],
            ]);

            $res->assertSessionHas('success');
            $this->assertTrue(
                AssessmentIaResponse::where('assessment_id', $assessment->id)->where('instrument_code', 'IA01B')->exists()
            );
        }
    }
}
