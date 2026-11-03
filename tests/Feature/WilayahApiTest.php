<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_provinces_returns_data_successfully(): void
    {
        $response = $this->getJson('/api/wilayah/provinces');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('code', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
    }

    public function test_api_regencies_returns_data_by_province_code(): void
    {
        $response = $this->getJson('/api/wilayah/regencies?province_id=31');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('code', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);

        // Pastikan memuat salah satu kota di Jakarta
        $names = array_column($data, 'name');
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains(strtolower($n), 'jakarta')));
    }

    public function test_api_districts_and_villages_support_dotted_and_undotted_codes(): void
    {
        // Test with undotted and dotted
        $resDotted = $this->getJson('/api/wilayah/districts?regency_id=31.71');
        $resDotted->assertStatus(200);
        $this->assertNotEmpty($resDotted->json());

        $resUndotted = $this->getJson('/api/wilayah/districts?regency_id=3171');
        $resUndotted->assertStatus(200);
        $this->assertNotEmpty($resUndotted->json());

        $resVillage = $this->getJson('/api/wilayah/villages?district_id=317101');
        $resVillage->assertStatus(200);
        $this->assertNotEmpty($resVillage->json());
    }

    public function test_api_find_regency_matches_city_to_province(): void
    {
        $response = $this->getJson('/api/wilayah/find-regency?name=Bekasi');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertNotNull($data);
        $this->assertEquals('32', $data['province_code']);
        $this->assertNotEmpty($data['regencies']);
    }

    public function test_asesi_profile_page_renders_wilayah_selector(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Asesi,
        ]);
        UserProfile::create([
            'user_id' => $user->id,
            'tempat_lahir' => 'Kota Administrasi Jakarta Selatan',
            'kebangsaan' => 'Indonesia',
        ]);

        $response = $this->actingAs($user)->get('/asesi/profile');
        $response->assertStatus(200);
        $response->assertSee('Provinsi Tempat Lahir');
        $response->assertSee('Kota / Kabupaten Tempat Lahir');
        $response->assertSee('wilayahSelector', false);
    }
}
