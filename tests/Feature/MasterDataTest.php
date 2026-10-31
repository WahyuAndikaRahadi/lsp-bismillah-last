<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\LspProfile;
use App\Models\Standard;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $asesor;
    protected User $asesi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->asesor = User::factory()->create([
            'role' => UserRole::Asesor,
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
        ]);
    }

    public function test_admin_can_view_and_update_lsp_profile(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/lsp-profile');
        $response->assertStatus(200);
        $response->assertSee('Profil Lembaga Sertifikasi Profesi (LSP)');

        $updateResponse = $this->actingAs($this->admin)->put('/admin/lsp-profile', [
            'nama_lsp' => 'LSP Digital Nusantara',
            'no_lisensi_bnsp' => 'BNSP-LSP-999-ID',
            'nama_ketua' => 'Bambang Sudibyo, M.Kom.',
            'telepon' => '021-99887766',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('lsp_profiles', [
            'nama_lsp' => 'LSP Digital Nusantara',
            'no_lisensi_bnsp' => 'BNSP-LSP-999-ID',
        ]);
    }

    public function test_admin_can_create_and_manage_standards(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/standards', [
            'kode_standar' => 'SKKNI No. 100 Tahun 2025',
            'judul_standar' => 'Standar Pemrograman Cloud Native',
            'jenis_standar' => 'SKKNI',
            'nomor_sk' => 'KEP.100/MEN/2025',
            'tahun' => 2025,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.standards.index'));
        $this->assertDatabaseHas('standards', [
            'kode_standar' => 'SKKNI No. 100 Tahun 2025',
        ]);

        $standard = Standard::where('kode_standar', 'SKKNI No. 100 Tahun 2025')->first();

        // Update
        $this->actingAs($this->admin)->put("/admin/standards/{$standard->id}", [
            'kode_standar' => 'SKKNI No. 100 Tahun 2025 Rev',
            'judul_standar' => 'Standar Pemrograman Cloud Native Diperbarui',
            'jenis_standar' => 'SKKNI',
            'tahun' => 2025,
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('standards', [
            'kode_standar' => 'SKKNI No. 100 Tahun 2025 Rev',
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/standards/{$standard->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('standards', ['id' => $standard->id]);
    }

    public function test_admin_can_create_scheme_with_requirements_and_units(): void
    {
        $standard = Standard::create([
            'kode_standar' => 'SKKNI Test',
            'judul_standar' => 'Standar Uji',
            'jenis_standar' => 'SKKNI',
        ]);

        // Create scheme
        $schemeResponse = $this->actingAs($this->admin)->post('/admin/schemes', [
            'kode_skema' => 'SKM-TEST-01',
            'nama_skema' => 'DevOps Specialist',
            'standard_id' => $standard->id,
            'jenis_skema' => 'Okupasi',
            'biaya' => 1250000,
            'is_active' => '1',
        ]);

        $scheme = CertificationScheme::where('kode_skema', 'SKM-TEST-01')->first();
        $this->assertNotNull($scheme);
        $schemeResponse->assertRedirect(route('admin.schemes.show', $scheme));

        // Add requirement
        $this->actingAs($this->admin)->post("/admin/schemes/{$scheme->id}/requirements", [
            'nama_dokumen' => 'Sertifikat Docker & Kubernetes',
            'is_required' => '1',
            'keterangan' => 'Scan sertifikat resmi',
        ]);

        $this->assertDatabaseHas('scheme_requirements', [
            'scheme_id' => $scheme->id,
            'nama_dokumen' => 'Sertifikat Docker & Kubernetes',
        ]);

        // Add competency unit
        $this->actingAs($this->admin)->post("/admin/schemes/{$scheme->id}/units", [
            'kode_unit' => 'J.620900.001.01',
            'judul_unit' => 'Mengoperasikan Container Orchestration',
            'jenis_standar' => 'SKKNI',
        ]);

        $unit = CompetencyUnit::where('kode_unit', 'J.620900.001.01')->first();
        $this->assertNotNull($unit);

        // Add element
        $this->actingAs($this->admin)->post("/admin/units/{$unit->id}/elements", [
            'nama_elemen' => 'Mengonfigurasi Cluster Container',
        ]);

        $element = CompetencyElement::where('unit_id', $unit->id)->first();
        $this->assertNotNull($element);

        // Add KUK
        $this->actingAs($this->admin)->post("/admin/elements/{$element->id}/criterias", [
            'nomor_kuk' => '1.1',
            'pernyataan' => 'Simpul node dan control plane dikonfigurasi sesuai kebutuhan beban kerja.',
            'pertanyaan_observasi' => 'Bagaimana cara menambahkan worker node baru?',
        ]);

        $this->assertDatabaseHas('work_key_criterias', [
            'element_id' => $element->id,
            'nomor_kuk' => '1.1',
        ]);
    }

    public function test_admin_can_manage_tuks(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/tuks', [
            'kode_tuk' => 'TUK-JKT-01',
            'nama_tuk' => 'TUK Lab Siber Jakarta',
            'jenis_tuk' => 'Mandiri',
            'alamat' => 'Jl. Gatot Subroto No. 10',
            'penanggung_jawab' => 'Budi Sudarsono',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.tuks.index'));
        $this->assertDatabaseHas('tuks', [
            'kode_tuk' => 'TUK-JKT-01',
            'nama_tuk' => 'TUK Lab Siber Jakarta',
        ]);

        $tuk = Tuk::where('kode_tuk', 'TUK-JKT-01')->first();

        // Toggle status
        $this->actingAs($this->admin)->patch("/admin/tuks/{$tuk->id}/toggle-status");
        $this->assertFalse($tuk->fresh()->is_active);

        // Delete
        $this->actingAs($this->admin)->delete("/admin/tuks/{$tuk->id}");
        $this->assertDatabaseMissing('tuks', ['id' => $tuk->id]);
    }

    public function test_admin_can_register_asesor_with_user_account(): void
    {
        $scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-SEC-01',
            'nama_skema' => 'Cyber Security Analyst',
            'jenis_skema' => 'Okupasi',
            'biaya' => 1500000,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/asesors', [
            'name' => 'Dr. Rudianto, M.T.',
            'email' => 'rudianto.asesor@example.com',
            'nik' => '3171010101850002',
            'phone' => '081299887766',
            'password' => 'secret12345',
            'no_met' => 'MET.000.009999 2024',
            'no_blangko' => 'B-112233',
            'bidang_keahlian' => 'Keamanan Jaringan & Cyber Defense',
            'scheme_ids' => [$scheme->id],
        ]);

        $response->assertRedirect(route('admin.asesors.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'rudianto.asesor@example.com',
            'role' => 'asesor',
        ]);

        $asesorProfile = AsesorProfile::where('no_met', 'MET.000.009999 2024')->first();
        $this->assertNotNull($asesorProfile);
        $this->assertTrue($asesorProfile->schemes->contains($scheme));
    }

    public function test_non_admin_cannot_access_master_data(): void
    {
        // Asesi attempts access
        $responseAsesi = $this->actingAs($this->asesi)->get('/admin/schemes');
        $responseAsesi->assertRedirect(route('asesi.dashboard'));

        // Asesor attempts access
        $responseAsesor = $this->actingAs($this->asesor)->get('/admin/lsp-profile');
        $responseAsesor->assertRedirect(route('asesor.dashboard'));
    }
}
