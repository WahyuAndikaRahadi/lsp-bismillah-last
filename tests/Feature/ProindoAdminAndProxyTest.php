<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoAdminAndProxyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected Scheme $scheme;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProindoSchemesAndMukSeeder::class);
        $this->seed(ProindoUserSeeder::class);

        $this->admin = User::where('email', 'admin@lsp-proindo.id')->first()
            ?? User::factory()->create(['role' => 'ADMIN_LSP', 'email' => 'admin@test.com', 'full_name' => 'Admin Test']);

        $this->asesor = User::where('role', 'ASESOR')->first();
        $this->asesi = User::factory()->create([
            'role' => 'ASESI',
            'email' => 'asesi.candidate@test.com',
            'full_name' => 'Kandidat Asesi',
        ]);

        $this->scheme = Scheme::first();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Panel Administrasi LSP PrO Indo');
    }

    public function test_admin_can_create_and_manage_schedule(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'scheme_id' => $this->scheme->id,
            'name' => 'Uji Sertifikasi Mekanik Motor 2026',
            'code' => 'SCH-PRO-99',
            'tuk_name' => 'Bengkel Resmi Yamaha Cikarang',
            'date' => now()->addDays(5)->toDateString(),
            'start_time' => '08:30:00',
            'end_time' => '16:30:00',
            'selected_instruments' => ['IA01', 'IA02', 'IA05'],
            'assessor_ids' => [$this->asesor->id],
        ]);

        $response->assertRedirect(route('admin.jadwal.index'));
        $this->assertDatabaseHas('t_schedules', [
            'code' => 'SCH-PRO-99',
            'tuk_name' => 'Bengkel Resmi Yamaha Cikarang',
        ]);
    }

    public function test_admin_can_review_candidate_registration(): void
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Jadwal Review Test',
            'code' => 'SCH-REV-01',
            'tuk_name' => 'TUK Mandiri',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'selected_instruments' => ['IA01', 'IA05'],
            'status' => 'ACTIVE',
            'created_by' => $this->admin->id,
        ]);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'admin_status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.assessment.review-registration', $assessment), [
            'action' => 'APPROVE',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_assessments', [
            'id' => $assessment->id,
            'admin_status' => 'APPROVED',
        ]);
    }

    public function test_proxy_impersonation_and_switch_back(): void
    {
        // 1. Admin visits proxy list
        $response = $this->actingAs($this->admin)->get(route('admin.proxy.index'));
        $response->assertOk();

        // 2. Admin impersonates asesi
        $responseSwitch = $this->actingAs($this->admin)->post(route('admin.proxy.impersonate', $this->asesi));
        $responseSwitch->assertRedirect(route('asesi.dashboard'));

        $this->assertEquals($this->asesi->id, auth()->id());
        $this->assertEquals($this->admin->id, session('admin_impersonator_id'));

        // 3. Impersonated session switches back to admin
        $responseBack = $this->post(route('proxy.switch-back'));
        $responseBack->assertRedirect(route('admin.proxy.index'));

        $this->assertEquals($this->admin->id, auth()->id());
        $this->assertFalse(session()->has('admin_impersonator_id'));
    }

    public function test_cannot_impersonate_super_admin_or_self(): void
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first()
            ?? User::factory()->create(['role' => 'SUPER_ADMIN', 'email' => 'super@test.com']);

        // Self
        $resSelf = $this->actingAs($this->admin)->post(route('admin.proxy.impersonate', $this->admin));
        $resSelf->assertSessionHas('warning');

        // Super Admin target
        $resSuper = $this->actingAs($this->admin)->post(route('admin.proxy.impersonate', $superAdmin));
        $resSuper->assertForbidden();
    }
}
