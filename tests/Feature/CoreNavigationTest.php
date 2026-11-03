<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\Tuk;
use App\Models\UnitElement;
use App\Models\User;
use App\Services\AssessmentNextAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesor;
    private User $asesi;
    private Scheme $scheme;
    private Tuk $tuk;
    private Schedule $schedule;
    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->asesor = User::factory()->create([
            'role' => UserRole::Asesor,
            'is_active' => true,
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'is_active' => true,
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-001',
            'name' => 'Skema Sertifikasi Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'code' => 'J.620100.004.01',
            'title' => 'Menggunakan Struktur Data',
            'standard_type' => 'SKKNI',
        ]);

        $elem = UnitElement::create([
            'competency_unit_id' => $unit->id,
            'title' => 'Menerapkan Tipe Data dan Struktur Data',
        ]);

        Kuk::create([
            'unit_element_id' => $elem->id,
            'code_number' => '1.1',
            'statement' => 'Tipe data diidentifikasi sesuai kebutuhan.',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-01',
            'nama_tuk' => 'TUK Utama',
            'jenis_tuk' => 'SEWAKTU',
            'is_active' => true,
        ]);

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $this->tuk->id,
            'tuk_name' => 'TUK Utama',
            'code' => 'JADWAL-2026-NAV',
            'name' => 'Uji Navigasi 2026',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'created_by' => $this->admin->id,
            'status' => 'ACTIVE',
            'is_closed' => false,
        ]);

        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'final_result' => 'BELUM_SELESAI',
        ]);
    }

    public function test_public_and_auth_routes_load_cleanly(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/forgot-password')->assertOk();
    }

    public function test_admin_navigation_routes_load_without_dead_ends(): void
    {
        $adminRoutes = [
            route('admin.dashboard'),
            route('admin.jadwal.index'),
            route('admin.asesors.index'),
            route('admin.pleno.index'),
            route('admin.certificates.index'),
            route('admin.appeals.index'),
            route('admin.impartiality.index'),
            route('admin.surveillance.index'),
            route('admin.recertification.index'),
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertOk();
        }
    }

    public function test_asesor_navigation_routes_load_without_dead_ends(): void
    {
        $asesorRoutes = [
            route('asesor.dashboard'),
            route('asesor.jadwal.index'),
            route('asesor.profile.edit'),
            route('asesor.tuks.index'),
            route('asesor.impartiality.commitment'),
            route('asesor.schedule.conflict-declaration', $this->schedule->id),
            route('asesor.mapa01.edit', $this->schedule->id),
            route('asesor.mapa02.edit', $this->schedule->id),
            route('asesor.schedule.instrument', ['schedule' => $this->schedule->id, 'code' => 'ak01']),
            route('asesor.schedule.ak05', $this->schedule->id),
            route('asesor.schedule.ak06', $this->schedule->id),
            route('asesor.schedule.ak07', $this->schedule->id),
            route('asesor.assessment.workspace', $this->assessment->id),
            route('asesor.apl.review', $this->assessment->id),
            route('asesor.assessment.ak02', $this->assessment->id),
        ];

        foreach ($asesorRoutes as $route) {
            $response = $this->actingAs($this->asesor)->get($route);
            $this->assertEquals(200, $response->status(), "Asesor failed on route {$route} with status {$response->status()} redirecting to " . $response->headers->get('Location'));
        }

        // Schedule AK-01 helper route redirects to instrument viewer
        $this->actingAs($this->asesor)->get(route('asesor.schedule.ak01', $this->schedule->id))
            ->assertRedirect(route('asesor.schedule.instrument', ['schedule' => $this->schedule->id, 'code' => 'ak01']));
    }

    public function test_asesi_navigation_routes_load_without_dead_ends(): void
    {
        // 1. Prior to signing AK-01, ak01 agreement page loads cleanly
        $this->assessment->update(['ak01_agreed_at' => null]);
        $this->actingAs($this->asesi)->get(route('asesi.assessment.ak01', $this->assessment->id))->assertOk();

        // 2. After signing AK-01, ak01 route redirects to ruang-uji, and ruang-uji is accessible
        $this->assessment->update(['ak01_agreed_at' => now()]);
        $this->actingAs($this->asesi)->get(route('asesi.assessment.ak01', $this->assessment->id))
            ->assertRedirect(route('asesi.assessment.ruang-uji', $this->assessment->id));

        $asesiRoutes = [
            route('asesi.dashboard'),
            route('asesi.jadwal.index'),
            route('asesi.profile.edit'),
            route('asesi.certificates.index'),
            route('asesi.surveillance.index'),
            route('asesi.assessment.apl', $this->assessment->id),
            route('asesi.assessment.ruang-uji', $this->assessment->id),
        ];

        foreach ($asesiRoutes as $route) {
            $response = $this->actingAs($this->asesi)->get($route);
            $this->assertEquals(200, $response->status(), "Asesi failed on route {$route} with status {$response->status()} redirecting to " . $response->headers->get('Location'));
        }

        // Before recommendation, AK-03 and appeal redirect gracefully to ruang-uji
        $this->actingAs($this->asesi)->get(route('asesi.assessment.ak03', $this->assessment->id))
            ->assertRedirect(route('asesi.assessment.ruang-uji', $this->assessment->id));
        $this->actingAs($this->asesi)->get(route('asesi.assessment.appeal', $this->assessment->id))
            ->assertRedirect(route('asesi.assessment.ruang-uji', $this->assessment->id));

        // After KOMPETEN recommendation is set, AK-03 feedback form opens cleanly
        $this->assessment->update(['final_result' => 'KOMPETEN']);
        $this->actingAs($this->asesi)->get(route('asesi.assessment.ak03', $this->assessment->id))
            ->assertOk();

        // After BELUM_KOMPETEN recommendation is set, AK-04 appeal form opens cleanly
        $this->assessment->update(['final_result' => 'BELUM_KOMPETEN']);
        $this->actingAs($this->asesi)->get(route('asesi.assessment.appeal', $this->assessment->id))
            ->assertOk();
    }

    public function test_assessment_next_action_resolver_yields_valid_actions_for_all_roles(): void
    {
        $service = app(AssessmentNextAction::class);

        // 1. Admin
        $adminAction = $service->determine($this->admin, $this->assessment);
        $this->assertNotEmpty($adminAction['action_url']);
        $this->assertNotEmpty($adminAction['action_label']);

        // 2. Asesor
        $asesorAction = $service->determine($this->asesor, $this->assessment);
        $this->assertNotEmpty($asesorAction['action_url']);
        $this->assertNotEmpty($asesorAction['action_label']);

        // 3. Asesi
        $asesiAction = $service->determine($this->asesi, $this->assessment);
        $this->assertNotEmpty($asesiAction['action_url']);
        $this->assertNotEmpty($asesiAction['action_label']);
    }

    public function test_check_apl_approved_middleware_redirects_unapproved_candidate(): void
    {
        $otherAsesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'is_active' => true,
        ]);

        $unapprovedAssessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $otherAsesi->id,
            'status' => 'REGISTERED',
            'apl_status' => 'DRAFT',
            'final_result' => 'BELUM_SELESAI',
        ]);

        $response = $this->actingAs($otherAsesi)->get(route('asesi.assessment.ruang-uji', $unapprovedAssessment->id));
        $response->assertRedirect(route('asesi.assessment.apl', $unapprovedAssessment->id));
        $response->assertSessionHas('error');
    }
}
