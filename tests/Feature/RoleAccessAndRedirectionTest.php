<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessAndRedirectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_accessing_protected_route_is_redirected_to_intended_after_login(): void
    {
        $asesi = User::create([
            'name' => 'Asesi Budi',
            'email' => 'budi@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        // Attempt to access protected asesi page while unauthenticated
        $response = $this->get('/asesi/jadwal');
        $response->assertRedirect('/login');

        // Now login as asesi
        $loginResponse = $this->post('/login', [
            'login' => 'budi@proindo.test',
            'password' => 'secret123',
        ]);

        // Should redirect to intended URL (/asesi/jadwal)
        $loginResponse->assertRedirect('/asesi/jadwal');
    }

    public function test_asesi_accessing_admin_route_is_redirected_to_asesi_dashboard_with_warning(): void
    {
        $asesi = User::create([
            'name' => 'Asesi Budi',
            'email' => 'budi2@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        $response = $this->actingAs($asesi)->get('/admin/dashboard');

        $response->assertRedirect(route('asesi.dashboard'));
        $response->assertSessionHas('warning', 'Anda dialihkan ke dashboard Anda karena tidak memiliki akses ke halaman tersebut.');
    }

    public function test_asesor_accessing_admin_route_is_redirected_to_asesor_dashboard_with_warning(): void
    {
        $asesor = User::create([
            'name' => 'Asesor Andi',
            'email' => 'andi@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $response = $this->actingAs($asesor)->get('/admin/jadwal');

        $response->assertRedirect(route('asesor.dashboard'));
        $response->assertSessionHas('warning', 'Anda dialihkan ke dashboard Anda karena tidak memiliki akses ke halaman tersebut.');
    }

    public function test_admin_accessing_asesi_route_is_redirected_to_admin_dashboard_with_warning(): void
    {
        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/asesi/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('warning', 'Anda dialihkan ke dashboard Anda karena tidak memiliki akses ke halaman tersebut.');
    }
}
