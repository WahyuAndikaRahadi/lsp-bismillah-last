<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AssessmentPlan;
use App\Models\CertificationScheme;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\TukVerification;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\SchemeInstrumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreMapa01Test extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP',
            'status' => 'ACTIVE',
        ]);
    }

    private function createAsesorUser(array $profileAttributes = []): User
    {
        $asesor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Budi Asesor ' . rand(100, 999),
            'phone' => '+628111222333',
            'status' => 'ACTIVE',
        ]);

        AsesorProfile::create(array_merge([
            'user_id' => $asesor->id,
            'no_met' => 'MET.' . rand(10000, 99999) . ' 2026',
            'masa_berlaku' => now()->addYear(),
            'bidang_keahlian' => 'Otomotif Kendaraan Ringan',
            'is_active' => true,
        ], $profileAttributes));

        return $asesor;
    }

    private function createVerifiedTuk(): Tuk
    {
        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-' . rand(100, 999),
            'nama_tuk' => 'TUK Otomotif Mandiri',
            'jenis_tuk' => 'Mandiri',
            'alamat' => 'Jl. Industri No. 10 Bekasi',
            'is_active' => true,
        ]);

        $asesor = $this->createAsesorUser();

        TukVerification::create([
            'tuk_id' => $tuk->id,
            'asesor_id' => $asesor->asesorProfile->id,
            'spt_number' => 'ST/01/TUK/LSP/2026',
            'verification_date' => now()->subDays(5)->toDateString(),
            'verification_type' => 'mandiri',
            'recommendation' => 'layak',
            'status' => 'diverifikasi',
        ]);

        return $tuk;
    }

    private function createApprovedScheme(string $code = 'SKM-01-2026'): Scheme
    {
        CertificationScheme::firstOrCreate(
            ['kode_skema' => $code],
            [
                'nama_skema' => 'Skema ' . $code,
                'jenis_skema' => 'Klaster',
                'status' => 'active',
            ]
        );

        $scheme = Scheme::firstOrCreate(
            ['code' => $code],
            [
                'name' => 'Skema ' . $code,
                'type' => 'KLASTER',
                'status' => 'ACTIVE',
            ]
        );

        SchemeVersion::firstOrCreate(
            ['scheme_id' => $scheme->id, 'version' => 1],
            [
                'code' => $code,
                'title' => 'Skema ' . $code,
                'status' => 'approved',
                'source_hash' => hash('sha256', 'test-content-' . $code),
            ]
        );

        return $scheme;
    }

    private function createTestSchedule(array $attributes = []): Schedule
    {
        $admin = $this->createAdminUser();
        $scheme = $attributes['scheme'] ?? $this->createApprovedScheme('SKM-01-2026');
        $tuk = $attributes['tuk'] ?? $this->createVerifiedTuk();

        return Schedule::create(array_merge([
            'code' => 'SCH-' . uniqid(),
            'name' => 'Jadwal Tes MAPA',
            'scheme_id' => $scheme->id,
            'tuk_id' => $tuk->id,
            'tuk_name' => $tuk->nama_tuk,
            'pic_name' => 'Drs. Koordinator TUK',
            'pic_position' => 'Ketua TUK',
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'capacity' => 20,
            'status' => 'ACTIVE',
            'created_by' => $admin->id,
        ], collect($attributes)->except(['scheme', 'tuk'])->toArray()));
    }

    public function test_per_scheme_group_loads_different_units_for_different_schemes(): void
    {
        $service = app(SchemeInstrumentService::class);

        $scheme1 = $this->createApprovedScheme('SKM-01-2026');
        $scheme2 = $this->createApprovedScheme('SKM-02-2026');

        $groups1 = $service->getMapa01Groups($scheme1);
        $groups2 = $service->getMapa01Groups($scheme2);

        $this->assertNotEmpty($groups1);
        $this->assertNotEmpty($groups2);

        $units1 = $groups1[0]['units'];
        $units2 = $groups2[0]['units'];

        $this->assertCount(18, $units1, 'Scheme 1 must have 18 units');
        $this->assertCount(9, $units2, 'Scheme 2 must have 9 units');

        $this->assertNotEquals(
            collect($units1)->pluck('code')->toArray(),
            collect($units2)->pluck('code')->toArray()
        );
        $this->assertNotEquals($groups1[0]['name'], $groups2[0]['name']);
    }

    public function test_all_sections_round_trip_and_persisted(): void
    {
        $asesor = $this->createAsesorUser();
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $schedule = $this->createTestSchedule(['scheme' => $scheme]);
        $schedule->assessors()->attach($asesor->id);

        $payload = [
            'schedule_id' => $schedule->id,
            'lock_version' => 1,
            'status' => 'draft',
            'approach_data' => [
                'asesi_type' => 'hasil_pelatihan_telusur',
                'purpose' => 'sertifikasi',
                'context_environment' => 'tempat_kerja_simulasi',
                'context_opportunities' => 'tersedia',
                'context_relationship' => 'langsung',
                'context_conducted_by' => 'lembaga_sertifikasi',
                'benchmarks' => ['standar_kompetensi'],
            ],
            'work_groups' => [
                [
                    'name' => 'General Service & Engine',
                    'units' => [
                        [
                            'code' => 'G.45OTO01.001.2',
                            'title' => 'Melaksanakan Keselamatan dan Kesehatan Kerja',
                            'evidence_text' => 'Hasil observasi langsung dan uji tulis K3',
                            'evidence_types' => ['L', 'T'],
                            'methods' => ['CL', 'DPT'],
                        ],
                    ],
                ],
            ],
            'modifications' => [
                'has_special_characteristics' => 'ada',
                'special_characteristics_notes' => 'Asesi membutuhkan pencahayaan tambahan pada area uji.',
                'has_workplace_context' => 'tidak_ada',
                'has_training_package_suggestions' => 'tidak_ada',
                'has_instrument_adjustments' => 'tidak_ada',
                'has_integrated_assessment_opportunities' => 'tidak_ada',
            ],
            'confirmation_data' => [
                'compilers' => [
                    ['name' => $asesor->full_name, 'met' => 'MET.001', 'date' => now()->toDateString()],
                ],
            ],
        ];

        $response = $this->actingAs($asesor)->post(route('asesor.mapa01.store', $schedule->id), $payload);
        $response->assertRedirect();

        $plan = AssessmentPlan::where('schedule_id', $schedule->id)->where('assessor_id', $asesor->id)->first();
        $this->assertNotNull($plan);
        $this->assertEquals('hasil_pelatihan_telusur', $plan->approach_data['asesi_type']);
        $this->assertEquals('G.45OTO01.001.2', $plan->work_groups[0]['units'][0]['code']);
        $this->assertEquals('ada', $plan->modifications['has_special_characteristics']);
        $this->assertEquals('Asesi membutuhkan pencahayaan tambahan pada area uji.', $plan->modifications['special_characteristics_notes']);
    }

    public function test_foreign_units_rejected_by_request_validation(): void
    {
        $asesor = $this->createAsesorUser();
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $schedule = $this->createTestSchedule(['scheme' => $scheme]);
        $schedule->assessors()->attach($asesor->id);

        $payload = [
            'schedule_id' => $schedule->id,
            'work_groups' => [
                [
                    'name' => 'Kelompok Salah',
                    'units' => [
                        [
                            'code' => 'UNIT-ASING-999.001', // Foreign unit not in SKM-01
                            'title' => 'Unit Fiktif Luar Skema',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($asesor)->post(route('asesor.mapa01.store', $schedule->id), $payload);
        $response->assertSessionHasErrors('work_groups');
        $this->assertDatabaseMissing('assessment_plans', ['schedule_id' => $schedule->id]);
    }

    public function test_empty_modification_decision_is_not_automatically_tidak_and_ada_requires_notes(): void
    {
        $asesor = $this->createAsesorUser();
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $schedule = $this->createTestSchedule(['scheme' => $scheme]);
        $schedule->assessors()->attach($asesor->id);

        // When 'ada' is chosen, notes is required
        $payloadMissingNotes = [
            'schedule_id' => $schedule->id,
            'work_groups' => [
                [
                    'name' => 'General Service & Engine',
                    'units' => [
                        [
                            'code' => 'G.45OTO01.001.2',
                            'title' => 'Melaksanakan Keselamatan dan Kesehatan Kerja',
                        ],
                    ],
                ],
            ],
            'modifications' => [
                'has_special_characteristics' => 'ada',
                'special_characteristics_notes' => null, // Missing notes when Ada!
            ],
        ];

        $res1 = $this->actingAs($asesor)->post(route('asesor.mapa01.store', $schedule->id), $payloadMissingNotes);
        $res1->assertSessionHasErrors('modifications.special_characteristics_notes');

        // When left null, it is accepted as draft without automatically defaulting to 'tidak_ada'
        $payloadNull = [
            'schedule_id' => $schedule->id,
            'work_groups' => [
                [
                    'name' => 'General Service & Engine',
                    'units' => [
                        [
                            'code' => 'G.45OTO01.001.2',
                            'title' => 'Melaksanakan Keselamatan dan Kesehatan Kerja',
                        ],
                    ],
                ],
            ],
            'modifications' => [
                'has_special_characteristics' => null,
            ],
        ];

        $res2 = $this->actingAs($asesor)->post(route('asesor.mapa01.store', $schedule->id), $payloadNull);
        $res2->assertRedirect();

        $plan = AssessmentPlan::where('schedule_id', $schedule->id)->first();
        $this->assertNull($plan->modifications['has_special_characteristics']);
    }

    public function test_unauthorized_asesor_cannot_edit_schedule_plan(): void
    {
        $assignedAsesor = $this->createAsesorUser();
        $otherAsesor = $this->createAsesorUser();
        $schedule = $this->createTestSchedule();
        $schedule->assessors()->attach($assignedAsesor->id); // only assignedAsesor attached

        $payload = [
            'schedule_id' => $schedule->id,
            'work_groups' => [
                [
                    'name' => 'Group 1',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ];

        // otherAsesor tries to store plan for this schedule
        $response = $this->actingAs($otherAsesor)->post(route('asesor.mapa01.store', $schedule->id), $payload);
        $response->assertStatus(403);
    }

    public function test_stale_revision_rejected_with_409_conflict(): void
    {
        $asesor = $this->createAsesorUser();
        $schedule = $this->createTestSchedule();
        $schedule->assessors()->attach($asesor->id);

        $plan = AssessmentPlan::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $asesor->id,
            'scheme_id' => $schedule->scheme_id,
            'revision' => 1,
            'lock_version' => 2, // Current version in DB is 2
            'status' => 'draft',
            'work_groups' => [
                [
                    'name' => 'Group 1',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ]);

        // Client submits with stale lock_version = 1
        $payload = [
            'schedule_id' => $schedule->id,
            'lock_version' => 1, // Stale!
            'work_groups' => [
                [
                    'name' => 'Group 1',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($asesor)->put(route('asesor.mapa01.update', $plan->id), $payload);
        $response->assertStatus(409);
    }

    public function test_locked_plan_cannot_be_updated(): void
    {
        $asesor = $this->createAsesorUser();
        $schedule = $this->createTestSchedule();
        $schedule->assessors()->attach($asesor->id);

        $plan = AssessmentPlan::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $asesor->id,
            'scheme_id' => $schedule->scheme_id,
            'revision' => 1,
            'lock_version' => 1,
            'status' => 'locked',
            'is_locked' => true, // Locked!
            'locked_at' => now(),
            'work_groups' => [
                [
                    'name' => 'Group 1',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ]);

        $payload = [
            'schedule_id' => $schedule->id,
            'lock_version' => 1,
            'work_groups' => [
                [
                    'name' => 'Group 1',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($asesor)->put(route('asesor.mapa01.update', $plan->id), $payload);
        $response->assertStatus(403);
    }

    public function test_substantive_plan_change_invalidates_previously_agreed_ak01(): void
    {
        $asesor = $this->createAsesorUser();
        $schedule = $this->createTestSchedule();
        $schedule->assessors()->attach($asesor->id);

        $plan = AssessmentPlan::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $asesor->id,
            'scheme_id' => $schedule->scheme_id,
            'revision' => 1,
            'lock_version' => 1,
            'status' => 'draft',
            'work_groups' => [
                [
                    'name' => 'General Service & Engine',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
        ]);

        // Candidate has accepted AK-01
        $candidateUser = User::factory()->create(['role' => 'ASESI']);
        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $candidateUser->id,
            'apl_status' => 'APPROVED',
            'admin_status' => 'APPROVED',
            'ak01_agreed_at' => now()->subDay(), // Agreement active
            'registered_at' => now(),
            'last_active_step' => 'ruang-uji',
        ]);

        $this->assertNotNull($assessment->fresh()->ak01_agreed_at);

        // Assessor modifies plan
        $payload = [
            'schedule_id' => $schedule->id,
            'lock_version' => 1,
            'work_groups' => [
                [
                    'name' => 'General Service & Engine',
                    'units' => [
                        ['code' => 'G.45OTO01.001.2', 'title' => 'K3'],
                    ],
                ],
            ],
            'modifications' => [
                'has_special_characteristics' => 'ada',
                'special_characteristics_notes' => 'Perubahan signifikan pada metode asesmen',
            ],
        ];

        $this->actingAs($asesor)->put(route('asesor.mapa01.update', $plan->id), $payload)->assertRedirect();

        // Assessment AK-01 agreement must be invalidated
        $assessment->refresh();
        $this->assertNull($assessment->ak01_agreed_at, 'Substantive plan update must invalidate candidate AK-01 agreement');
    }
}
