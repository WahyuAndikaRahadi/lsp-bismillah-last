<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreRetiredSurfaceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesor;
    private User $asesi;

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
    }

    public function test_retired_surfaces_return_404_for_all_users(): void
    {
        $retiredRoutes = [
            '/proxy',
            '/admin/proxy',
            '/complaints',
            '/admin/complaints',
            '/admin/finance',
            '/admin/finance/transactions',
            '/admin/letters',
            '/admin/quality',
            '/admin/messages',
            '/staff',
            '/staff/dashboard',
            '/sertifikat/1',
            '/admin/exports/bnsp',
            '/surveillance', // legacy unauthenticated root surveillance route
        ];

        foreach ($retiredRoutes as $route) {
            // Guest check
            $guestResponse = $this->get($route);
            $this->assertEquals(404, $guestResponse->status(), "Guest access to {$route} should be 404");

            // Admin check
            $adminResponse = $this->actingAs($this->admin)->get($route);
            $this->assertEquals(404, $adminResponse->status(), "Admin access to {$route} should be 404");
        }
    }

    public function test_active_lean_and_core_surfaces_remain_functional(): void
    {
        // 1. Admin surfaces: appeals, impartiality, surveillance, recertification, certificates, pleno, jadwal
        $this->actingAs($this->admin)->get(route('admin.appeals.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.impartiality.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.surveillance.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.recertification.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.certificates.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pleno.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.jadwal.index'))->assertOk();

        // 2. Asesi surfaces: certificates, surveillance
        $this->actingAs($this->asesi)->get(route('asesi.certificates.index'))->assertOk();
        $this->actingAs($this->asesi)->get(route('asesi.surveillance.index'))->assertOk();

        // 3. Asesor surfaces: schedules, impartiality commitment
        $this->actingAs($this->asesor)->get(route('asesor.jadwal.index'))->assertOk();
        $this->actingAs($this->asesor)->get(route('asesor.impartiality.commitment'))->assertOk();
    }

    public function test_impersonation_proxy_is_not_present_in_session_or_user(): void
    {
        $this->assertFalse(method_exists($this->admin, 'canImpersonate'));
        $this->assertFalse(method_exists($this->admin, 'isImpersonating'));
    }
}
