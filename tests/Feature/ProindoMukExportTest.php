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

class ProindoMukExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $asesor;
    protected User $asesi;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProindoSchemesAndMukSeeder::class);
        $this->seed(ProindoUserSeeder::class);

        $this->admin = User::where('role', 'ADMIN_LSP')->first()
            ?? User::factory()->create(['role' => 'ADMIN_LSP', 'email' => 'admin@test.com']);

        $this->asesor = User::where('role', 'ASESOR')->first()
            ?? User::factory()->create(['role' => 'ASESOR', 'email' => 'asesor@test.com']);

        $this->asesi = User::factory()->create([
            'role' => 'ASESI',
            'email' => 'peserta.muk@test.com',
            'full_name' => 'Ahmad Fadhil',
        ]);

        $this->scheme = Scheme::first();

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Uji Sertifikasi Otomotif BNSP 2026',
            'code' => 'SCH-MUK-01',
            'tuk_name' => 'TUK Otomotif Sejahtera',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'selected_instruments' => ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
        ]);
    }

    public function test_retired_dokumen_muk_routes_are_unavailable(): void
    {
        $retiredRoutes = [
            '/admin/dokumen-muk',
            '/admin/dokumen-muk/export/1/ba',
            '/admin/dokumen-muk/export/1/hadir-asesor',
            '/admin/dokumen-muk/export/1/hadir-asesi',
            '/admin/dokumen-muk/export-asesi?assessment=1&type=apl01',
            '/admin/dokumen-muk/export-excel-biodata/1',
            '/admin/dokumen-muk/export-excel-hadir-asesi/1',
            '/admin/dokumen-muk/export-excel-hadir-asesor/1',
        ];

        foreach ($retiredRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertEquals(404, $response->status(), "Route {$route} should be retired/unavailable (404)");
        }
    }
}
