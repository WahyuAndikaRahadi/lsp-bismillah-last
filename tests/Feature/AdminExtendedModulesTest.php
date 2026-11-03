<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminExtendedModulesTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin LSP Utama',
            'email' => 'admin@lsp.localhost',
        ]);
    }

    public function test_admin_can_access_appeals_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.appeals.index'));
        $response->assertOk();
        $response->assertSee('Banding Asesmen');
    }

    public function test_admin_can_access_reports_and_submodules(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.reports.general'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.reports.monthly'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.reports.annual'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.reports.asesor-performance'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.reports.tuk-performance'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.reports.certificates'))->assertOk();
    }

    public function test_retired_quality_documents_and_audits_are_unavailable(): void
    {
        $this->actingAs($this->adminUser)->get('/admin/quality')->assertNotFound();
        $this->actingAs($this->adminUser)->get('/admin/quality/documents')->assertNotFound();
        $this->actingAs($this->adminUser)->get('/admin/quality/audits')->assertNotFound();
    }

    public function test_retired_finance_and_bank_accounts_are_unavailable(): void
    {
        $this->actingAs($this->adminUser)->get('/admin/finance')->assertNotFound();
        $this->actingAs($this->adminUser)->get('/admin/finance/transactions')->assertNotFound();
        $this->actingAs($this->adminUser)->get('/admin/finance/bank-accounts')->assertNotFound();
    }

    public function test_retired_letters_and_messages_are_unavailable(): void
    {
        $this->actingAs($this->adminUser)->get('/admin/letters')->assertNotFound();
        $this->actingAs($this->adminUser)->get('/admin/messages')->assertNotFound();
    }

    public function test_admin_can_access_asesi_submodules(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.asesi.unscheduled'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.scheduled'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.competent'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.incompetent'))->assertOk();
    }
}
