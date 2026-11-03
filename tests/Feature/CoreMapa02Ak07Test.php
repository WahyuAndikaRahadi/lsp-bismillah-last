<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Requests\Ak07Request;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AssessmentPlan;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Services\SchemeInstrumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreMapa02Ak07Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi;
    private Tuk $tuk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin User',
            'email' => 'admin@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Asesor Testing',
            'email' => 'asesor@proindo.id',
            'status' => 'ACTIVE',
        ]);
        AsesorProfile::create([
            'user_id' => $this->assessor->id,
            'no_met' => 'MET.99988.2026',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Asesi Testing',
            'email' => 'asesi@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->tuk = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-MAPA02'],
            ['nama_tuk' => 'TUK Otomotif Uji', 'jenis_tuk' => 'Sewaktu', 'is_active' => true]
        );
    }

    private function createScheduleForScheme(string $code, string $name): array
    {
        $scheme = Scheme::firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'category' => 'Automotive', 'is_active' => true]
        );

        $version = SchemeVersion::firstOrCreate(
            ['scheme_id' => $scheme->id, 'version' => 1],
            ['code' => $code . '-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );

        $schedule = Schedule::create([
            'scheme_id' => $scheme->id,
            'scheme_version_id' => $version->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-' . $code . '-' . rand(100, 999),
            'name' => 'Jadwal ' . $name,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
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
            'status' => 'scheduled',
        ]);

        return [$scheme, $schedule, $assessment];
    }

    public function test_mapa02_template_and_store_for_all_seven_schemes(): void
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

        foreach ($schemes as $code => $name) {
            [$scheme, $schedule, $assessment] = $this->createScheduleForScheme($code, $name);

            $this->actingAs($this->assessor);
            $response = $this->get(route('asesor.mapa02.edit', $schedule->id));
            $response->assertStatus(200);

            $plan = AssessmentPlan::where('schedule_id', $schedule->id)->first();
            $this->assertNotNull($plan);
            $this->assertNotEmpty($plan->mapa02_data);

            // Update MAPA-02 with valid active instruments without reference leaks
            $templateData = array_map(function ($grp) {
                $grp['instruments'] = array_map(function ($inst) {
                    if (in_array($inst['code'], ['FR.IA.01', 'FR.IA.02', 'FR.IA.03', 'FR.IA.05'])) {
                        $inst['is_active'] = 1;
                        $inst['potensi'] = [1, 2];
                    } else {
                        $inst['is_active'] = 0;
                    }
                    return $inst;
                }, $grp['instruments'] ?? []);
                return $grp;
            }, $plan->mapa02_data);

            $postResponse = $this->put(route('asesor.mapa02.update', $plan->id), [
                'schedule_id' => $schedule->id,
                'lock_version' => $plan->lock_version,
                'mapa02_data' => $templateData,
            ]);

            $postResponse->assertRedirect(route('asesor.mapa02.edit', ['schedule' => $schedule->id]));

            $plan->refresh();
            $this->assertEquals($templateData, $plan->mapa02_data);
        }
    }

    public function test_mapa02_missing_source_instruments_cannot_be_marked_active(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);
        $this->get(route('asesor.mapa02.edit', $schedule->id));
        $plan = AssessmentPlan::where('schedule_id', $schedule->id)->first();

        $invalidData = [
            [
                'group_name' => 'Kelompok 1',
                'instruments' => [
                    [
                        'code' => 'FR.IA.04A',
                        'name' => 'DIT Proyek',
                        'is_active' => 1, // Invalid! Missing source original
                        'potensi' => [1],
                    ],
                ],
            ],
        ];

        $response = $this->put(route('asesor.mapa02.update', $plan->id), [
            'schedule_id' => $schedule->id,
            'lock_version' => $plan->lock_version,
            'mapa02_data' => $invalidData,
        ]);

        $response->assertSessionHasErrors(['mapa02_data']);
    }

    public function test_ak07_all_categories_and_options_round_trip(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);

        $items = [];
        for ($i = 1; $i <= 8; $i++) {
            $catOptions = Ak07Request::$CATEGORY_OPTIONS[$i] ?? [];
            if ($i === 1) {
                $items[$i] = [
                    'category_number' => 1,
                    'title' => 'Keterbatasan bahasa',
                    'decision' => 'ya',
                    'options' => [$catOptions[0]],
                    'detail' => 'Dukungan penerjemah disediakan',
                ];
            } else {
                $items[$i] = [
                    'category_number' => $i,
                    'title' => "Kategori #{$i}",
                    'decision' => 'tidak',
                    'options' => [],
                    'detail' => '',
                ];
            }
        }

        $response = $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'sebelum_pra_asesmen',
            'items' => $items,
            'is_agreed' => true,
            'agreed_benchmark' => 'SKKNI No. 097/2018',
            'agreed_method' => 'Demonstrasi',
            'agreed_instrument' => 'FR.IA.01',
        ]);

        $response->assertRedirect(route('asesor.schedule.ak01', $schedule->id));

        $savedItems = AssessmentAk07Item::where('assessment_id', $assessment->id)->get();
        $this->assertCount(8, $savedItems);

        $cat1 = $savedItems->where('category_number', 1)->first();
        $this->assertEquals('ya', $cat1->decision);
        $this->assertContains(Ak07Request::$CATEGORY_OPTIONS[1][0], $cat1->selected_options);
        $this->assertEquals('sebelum_pra_asesmen', $cat1->timing);
        $this->assertTrue($cat1->is_agreed);
        $this->assertNotNull($cat1->asesor_signed_at);
        $this->assertNull($cat1->asesi_signed_at);
    }

    public function test_ak07_inconsistent_ya_without_detail_or_options_is_rejected(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);

        $items = [
            1 => [
                'category_number' => 1,
                'title' => 'Keterbatasan bahasa',
                'decision' => 'ya',
                'options' => [],
                'detail' => '', // Inconsistent!
            ],
        ];

        $response = $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'saat_pra_asesmen',
            'items' => $items,
        ]);

        $response->assertSessionHasErrors(['items.1.decision']);
    }

    public function test_ak07_unknown_option_is_rejected(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);

        $items = [
            1 => [
                'category_number' => 1,
                'title' => 'Keterbatasan bahasa',
                'decision' => 'ya',
                'options' => ['Pilihan Halusinasi Tidak Ada Di SOP'], // Unknown!
                'detail' => 'Catatan',
            ],
        ];

        $response = $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'saat_pra_asesmen',
            'items' => $items,
        ]);

        $response->assertSessionHasErrors(['items.1.options']);
    }

    public function test_ak07_explicit_tidak_stored_without_bulk_assumptions(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);

        $items = [];
        for ($i = 1; $i <= 8; $i++) {
            $items[$i] = [
                'category_number' => $i,
                'title' => "Kategori #{$i}",
                'decision' => 'tidak',
                'options' => [],
                'detail' => '',
            ];
        }

        $response = $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'sebelum_pra_asesmen',
            'items' => $items,
            'is_agreed' => false,
        ]);

        $response->assertRedirect(route('asesor.schedule.ak01', $schedule->id));

        $savedItems = AssessmentAk07Item::where('assessment_id', $assessment->id)->get();
        $this->assertCount(8, $savedItems);

        foreach ($savedItems as $it) {
            $this->assertEquals('tidak', $it->decision);
            $this->assertEmpty($it->selected_options);
        }
    }

    public function test_ak07_consent_isolation_and_signature(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        $this->actingAs($this->assessor);

        $items = [
            1 => [
                'category_number' => 1,
                'title' => 'Karakteristik 1',
                'decision' => 'ya',
                'options' => [Ak07Request::$CATEGORY_OPTIONS[1][0]],
                'detail' => 'Penerjemah',
            ],
        ];

        $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'sebelum_pra_asesmen',
            'items' => $items,
        ]);

        $item = AssessmentAk07Item::where('assessment_id', $assessment->id)->first();
        $this->assertNotNull($item->asesor_signed_at);
        $this->assertNull($item->asesi_signed_at, 'Assessor cannot forge asesi signature');

        // Other user attempts to sign asesi agreement -> Forbidden
        $otherUser = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'status' => 'ACTIVE',
        ]);
        $this->actingAs($otherUser);

        $failResponse = $this->post(route('asesi.assessment.ak07.acknowledge', $assessment->id));
        $failResponse->assertStatus(403);

        // Real asesi signs agreement
        $this->actingAs($this->asesi);
        $okResponse = $this->post(route('asesi.assessment.ak07.acknowledge', $assessment->id));
        $okResponse->assertSessionHas('success');

        $item->refresh();
        $this->assertNotNull($item->asesi_signed_at);
    }

    public function test_ak07_accommodation_change_invalidates_ak01_without_altering_competence(): void
    {
        [$scheme, $schedule, $assessment] = $this->createScheduleForScheme('SS-01', 'Motor Karbu');

        // Pre-set existing ak01 agreement and competence status
        $assessment->update([
            'ak01_agreed_at' => now()->subDay(),
            'status' => 'completed',
        ]);

        $this->actingAs($this->assessor);

        $items = [
            1 => [
                'category_number' => 1,
                'title' => 'Karakteristik 1',
                'decision' => 'ya',
                'options' => [Ak07Request::$CATEGORY_OPTIONS[1][0]],
                'detail' => 'Perubahan akomodasi baru',
            ],
        ];

        $this->post(route('asesor.schedule.ak07.store', $schedule->id), [
            'assessment_id' => $assessment->id,
            'timing' => 'setelah_pra_asesmen',
            'items' => $items,
        ]);

        $assessment->refresh();

        // AK01 agreement is invalidated
        $this->assertNull($assessment->ak01_agreed_at);

        // Competency status is unaltered
        $this->assertEquals('completed', $assessment->status);
    }
}
