<?php

namespace Tests\Feature;

use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\User;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSchemeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Scheme $scheme;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProindoSchemesAndMukSeeder::class);
        $this->seed(ProindoUserSeeder::class);

        $this->admin = User::where('email', 'admin@lsp-proindo.id')->first()
            ?? User::factory()->create(['role' => 'ADMIN_LSP', 'email' => 'admin@test.com']);

        $this->scheme = Scheme::firstOrFail();
    }

    public function test_admin_can_view_scheme_detail_with_units(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.schemes.show', $this->scheme->id));
        $response->assertOk();
        $response->assertSee($this->scheme->name);
        $response->assertSee('+ Tambah Unit Kompetensi');
    }

    public function test_admin_can_crud_competency_unit(): void
    {
        // 1. Create Unit
        $createResponse = $this->actingAs($this->admin)
            ->post(route('admin.schemes.units.store', $this->scheme->id), [
                'kode_unit' => 'TEST.UNIT.001',
                'judul_unit' => 'Unit Kompetensi Uji Coba CRUD',
                'jenis_standar' => 'SKKNI 2026',
            ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('m_competency_units', [
            'scheme_id' => $this->scheme->id,
            'code' => 'TEST.UNIT.001',
            'title' => 'Unit Kompetensi Uji Coba CRUD',
        ]);

        $unit = CompetencyUnit::where('code', 'TEST.UNIT.001')->firstOrFail();

        // 2. Update Unit
        $updateResponse = $this->actingAs($this->admin)
            ->put(route('admin.schemes.units.update', $unit->id), [
                'kode_unit' => 'TEST.UNIT.001.REV',
                'judul_unit' => 'Unit Kompetensi Diperbarui',
                'jenis_standar' => 'SKKNI 2026 Revisi',
            ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('m_competency_units', [
            'id' => $unit->id,
            'code' => 'TEST.UNIT.001.REV',
            'title' => 'Unit Kompetensi Diperbarui',
        ]);

        // 3. Delete Unit
        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('admin.schemes.units.destroy', $unit->id));

        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('m_competency_units', [
            'id' => $unit->id,
        ]);
    }

    public function test_admin_can_crud_element_and_kuk(): void
    {
        $unit = CompetencyUnit::firstOrFail();

        // 1. Create Element
        $createElemResponse = $this->actingAs($this->admin)
            ->post(route('admin.schemes.elements.store', $unit->id), [
                'nama_elemen' => 'Elemen Baru Uji Coba',
            ]);

        $createElemResponse->assertRedirect();
        $this->assertDatabaseHas('m_unit_elements', [
            'competency_unit_id' => $unit->id,
            'title' => 'Elemen Baru Uji Coba',
        ]);

        $element = UnitElement::where('title', 'Elemen Baru Uji Coba')->firstOrFail();

        // 2. Update Element
        $updateElemResponse = $this->actingAs($this->admin)
            ->put(route('admin.schemes.elements.update', $element->id), [
                'nama_elemen' => 'Elemen Baru Diperbarui',
            ]);

        $updateElemResponse->assertRedirect();
        $this->assertDatabaseHas('m_unit_elements', [
            'id' => $element->id,
            'title' => 'Elemen Baru Diperbarui',
        ]);

        // 3. Create KUK under element
        $createKukResponse = $this->actingAs($this->admin)
            ->post(route('admin.schemes.criterias.store', $element->id), [
                'nomor_kuk' => '9.9',
                'pernyataan' => 'KUK Percobaan Berhasil Diverifikasi',
            ]);

        $createKukResponse->assertRedirect();
        $this->assertDatabaseHas('m_kuks', [
            'unit_element_id' => $element->id,
            'code_number' => '9.9',
            'statement' => 'KUK Percobaan Berhasil Diverifikasi',
        ]);

        $kuk = Kuk::where('code_number', '9.9')->where('unit_element_id', $element->id)->firstOrFail();

        // 4. Update KUK
        $updateKukResponse = $this->actingAs($this->admin)
            ->put(route('admin.schemes.criterias.update', $kuk->id), [
                'nomor_kuk' => '9.9.1',
                'pernyataan' => 'KUK Percobaan Diperbarui',
            ]);

        $updateKukResponse->assertRedirect();
        $this->assertDatabaseHas('m_kuks', [
            'id' => $kuk->id,
            'code_number' => '9.9.1',
            'statement' => 'KUK Percobaan Diperbarui',
        ]);

        // 5. Delete KUK
        $deleteKukResponse = $this->actingAs($this->admin)
            ->delete(route('admin.schemes.criterias.destroy', $kuk->id));

        $deleteKukResponse->assertRedirect();
        $this->assertDatabaseMissing('m_kuks', ['id' => $kuk->id]);

        // 6. Delete Element
        $deleteElemResponse = $this->actingAs($this->admin)
            ->delete(route('admin.schemes.elements.destroy', $element->id));

        $deleteElemResponse->assertRedirect();
        $this->assertDatabaseMissing('m_unit_elements', ['id' => $element->id]);
    }
}
