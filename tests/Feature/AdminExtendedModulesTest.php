<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AuditInternal;
use App\Models\BankAccount;
use App\Models\FinancialAccount;
use App\Models\Letter;
use App\Models\MutuDocument;
use App\Models\SystemMessage;
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
        $response->assertSee('Layanan Banding Asesmen');
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

    public function test_admin_can_manage_quality_documents_and_audits(): void
    {
        MutuDocument::create([
            'document_code' => 'PM-LSP-01',
            'title' => 'Panduan Mutu LSP Berdasarkan BNSP 201',
            'category' => 'panduan_mutu',
            'version' => '1.0',
            'effective_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);

        $docResponse = $this->actingAs($this->adminUser)->get(route('admin.quality.documents.index'));
        $docResponse->assertOk();
        $docResponse->assertSee('PM-LSP-01');

        AuditInternal::create([
            'audit_code' => 'AUD-2026-001',
            'audit_date' => now()->toDateString(),
            'auditor_name' => 'Dr. Bambang Sutrisno',
            'scope' => 'Klausul 4 s/d 10 Pedoman BNSP 201',
            'status' => 'selesai',
        ]);

        $auditResponse = $this->actingAs($this->adminUser)->get(route('admin.quality.audits.index'));
        $auditResponse->assertOk();
        $auditResponse->assertSee('AUD-2026-001');
    }

    public function test_admin_can_manage_finance_and_bank_accounts(): void
    {
        BankAccount::create([
            'bank_name' => 'Bank Mandiri',
            'account_number' => '137001928374',
            'account_holder' => 'LSP Sertifikasi Profesi',
            'is_active' => true,
        ]);

        $bankResponse = $this->actingAs($this->adminUser)->get(route('admin.finance.bank-accounts.index'));
        $bankResponse->assertOk();
        $bankResponse->assertSee('137001928374');

        FinancialAccount::create([
            'account_code' => '1-1100',
            'account_name' => 'Kas Operasional LSP',
            'account_type' => 'aktiva',
        ]);

        $this->actingAs($this->adminUser)->get(route('admin.finance.transactions.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.finance.balance-sheet'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.finance.income-statement'))->assertOk();
    }

    public function test_admin_can_manage_letters_and_sent_messages(): void
    {
        Letter::create([
            'reference_number' => '045/ST-LSP/X/2026',
            'category' => 'surat_tugas',
            'subject' => 'Surat Tugas Asesor Uji TKJ',
            'recipient' => 'Abu, S.T.',
            'letter_date' => now()->toDateString(),
            'status' => 'terbit',
        ]);

        $letterResponse = $this->actingAs($this->adminUser)->get(route('admin.letters.index'));
        $letterResponse->assertOk();
        $letterResponse->assertSee('045/ST-LSP/X/2026');

        SystemMessage::create([
            'direction' => 'outgoing',
            'channel' => 'sms',
            'recipient_or_sender' => '081234567890',
            'body' => 'Kode OTP login Asesor LSP: 998877',
            'status' => 'terkirim',
        ]);

        $msgResponse = $this->actingAs($this->adminUser)->get(route('admin.messages.sent'));
        $msgResponse->assertOk();
        $msgResponse->assertSee('998877');
    }

    public function test_admin_can_access_asesi_submodules(): void
    {
        $this->actingAs($this->adminUser)->get(route('admin.asesi.unscheduled'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.scheduled'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.competent'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.asesi.incompetent'))->assertOk();
    }
}
