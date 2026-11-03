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

        $response->assertRedirect();
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

    public function test_proxy_impersonation_is_unavailable(): void
    {
        $this->actingAs($this->admin)->get('/admin/proxy')->assertNotFound();
        $this->actingAs($this->admin)->post('/admin/proxy/' . $this->asesi->id)->assertNotFound();
        $this->post('/proxy/switch-back')->assertNotFound();
    }
}
