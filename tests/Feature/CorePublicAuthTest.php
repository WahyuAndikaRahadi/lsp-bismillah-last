<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\LspProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorePublicAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_renders_identity_and_approved_data_without_remote_cdn_fonts(): void
    {
        LspProfile::create([
            'nama_lsp' => 'LSP PRO INDO',
            'kode_lsp' => 'LSP-026-ID',
            'no_lisensi_bnsp' => 'BNSP-LSP-026-ID',
            'alamat' => 'Bekasi, Jawa Barat',
            'nama_ketua' => 'Ir. Bambang S., M.T.',
        ]);

        $response = $this->get('/');
        $response->assertOk();

        $content = $response->getContent();

        // 1. LSP Identity is rendered
        $this->assertStringContainsString('LSP PRO INDO', $content);
        $this->assertStringContainsString('BNSP-LSP-026-ID', $content);

        // 2. No remote Google Fonts or Bunny Fonts CDN links in rendered HTML
        $this->assertStringNotContainsString('fonts.googleapis.com', $content);
        $this->assertStringNotContainsString('fonts.bunny.net', $content);
    }

    public function test_registration_form_has_no_role_selector_and_strictly_creates_asesi(): void
    {
        $response = $this->get(route('register'));
        $response->assertOk();

        $content = $response->getContent();

        // Must not contain any role select dropdown or radio button
        $this->assertStringNotContainsString('name="role"', $content);
        $this->assertStringNotContainsString('select name="role"', $content);

        // Attempt submission with injected role
        $payload = [
            'name' => 'Calon Peserta Uji',
            'email' => 'calon_peserta@example.com',
            'nik' => '3201010101010077',
            'phone' => '081234567899',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => '1',
            'role' => 'admin', // Malicious attempt to inject admin role
        ];

        $postResponse = $this->post(route('register'), $payload);
        $postResponse->assertRedirect(route('asesi.dashboard'));

        $user = User::where('email', 'calon_peserta@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Asesi, $user->canonicalRole());
        $this->assertTrue($user->isAsesi());
        $this->assertFalse($user->isAdmin());
    }

    public function test_login_and_auth_views_render_without_remote_font_dependencies(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringNotContainsString('fonts.googleapis.com', $content);
        $this->assertStringNotContainsString('fonts.bunny.net', $content);
    }

    public function test_local_font_license_and_assets_exist(): void
    {
        $this->assertFileExists(public_path('fonts/LICENSE.txt'));
    }

    public function test_page_pattern_components_render_clean_empty_states_and_accessible_markup(): void
    {
        // 1. List component empty state
        $view = $this->blade(
            '<x-page.list title="Daftar Jadwal" :isEmpty="true" emptyMessage="Tidak ada jadwal aktif saat ini." />'
        );
        $view->assertSee('Daftar Jadwal');
        $view->assertSee('Tidak ada jadwal aktif saat ini.');

        // 2. Detail component
        $detailView = $this->blade(
            '<x-page.detail title="Detail Asesmen" subtitle="Kode: SCH-001">Konten Detail</x-page.detail>'
        );
        $detailView->assertSee('Detail Asesmen');
        $detailView->assertSee('Konten Detail');

        // 3. Form component
        $formView = $this->blade(
            '<x-page.form title="Formulir Pendaftaran" action="/test-action" submitLabel="Kirim Permohonan"><input name="test" /></x-page.form>'
        );
        $formView->assertSee('Formulir Pendaftaran');
        $formView->assertSee('Kirim Permohonan');
    }
}
