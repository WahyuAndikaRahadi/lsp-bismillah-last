<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Portal Sertifikasi');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Calon Asesi');
    }

    public function test_new_asesi_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'nik' => '3201010101900001',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('asesi.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@example.com',
            'nik' => '3201010101900001',
            'role' => 'asesi',
            'is_active' => true,
        ]);
    }

    public function test_registration_fails_if_nik_is_not_16_digits(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'nik' => '12345', // Invalid length
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['nik']);
        $this->assertGuest();
    }

    public function test_user_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'testadmin@lsp.localhost',
            'role' => UserRole::Admin,
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'testadmin@lsp.localhost',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_can_authenticate_using_nik(): void
    {
        $user = User::factory()->create([
            'email' => 'asesor@lsp.localhost',
            'nik' => '3504010710670002',
            'role' => UserRole::Asesor,
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => '3504010710670002',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('asesor.dashboard'));
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'asesi@lsp.localhost',
            'password' => Hash::make('validpassword'),
        ]);

        $this->post('/login', [
            'login' => 'asesi@lsp.localhost',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    public function test_asesor_cannot_access_admin_dashboard(): void
    {
        $asesor = User::factory()->create([
            'role' => UserRole::Asesor,
        ]);

        $response = $this->actingAs($asesor)->get('/admin/dashboard');

        // Should be redirected to their own dashboard with warning
        $response->assertRedirect(route('asesor.dashboard'));
    }

    public function test_asesi_cannot_access_asesor_dashboard(): void
    {
        $asesi = User::factory()->create([
            'role' => UserRole::Asesi,
        ]);

        $response = $this->actingAs($asesi)->get('/asesor/dashboard');

        $response->assertRedirect(route('asesi.dashboard'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Operasional Admin');
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'orig@example.com',
            'nik' => '3171010101900001',
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'nik' => '3171010101900001',
            'phone' => '08999999999',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('currentpassword123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'currentpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
