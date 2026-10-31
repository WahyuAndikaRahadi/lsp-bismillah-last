<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoAuthAndLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_url_redirects_guest_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_root_url_redirects_authenticated_admin_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin@proindo.test',
            'password' => 'secret123',
            'role' => 'ADMIN_LSP',
        ]);

        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_root_url_redirects_authenticated_asesi_to_asesi_dashboard(): void
    {
        $asesi = User::create([
            'name' => 'Asesi Baru',
            'email' => 'asesi@proindo.test',
            'password' => 'secret123',
            'role' => 'ASESI',
        ]);

        $response = $this->actingAs($asesi)->get('/');
        $response->assertRedirect(route('asesi.dashboard'));
    }

    public function test_login_page_renders_with_proindo_branding(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        $response->assertSee('LSP PrO Indo');
        $response->assertSee('PORTAL PENGGUNA');
    }

    public function test_user_can_login_with_seeded_proindo_credentials(): void
    {
        $user = User::create([
            'name' => 'Super Admin LSP',
            'email' => 'lsp.proindo@gmail.com',
            'password' => bcrypt('@Kompeten86'),
            'role' => 'SUPER_ADMIN',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'lsp.proindo@gmail.com',
            'password' => '@Kompeten86',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
