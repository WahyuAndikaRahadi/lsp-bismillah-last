<?php

namespace Tests\Feature;

use App\Models\AuditInternal;
use App\Models\BankAccount;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\InstrumentReview;
use App\Models\Letter;
use App\Models\MkvaAssignment;
use App\Models\MutuDocument;
use App\Models\SystemMessage;
use App\Models\TukVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExtendedSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_extended_tables_exist(): void
    {
        $expectedTables = [
            'audit_internals',
            'mutu_documents',
            'letters',
            'financial_accounts',
            'financial_transactions',
            'bank_accounts',
            'system_messages',
            'tuk_verifications',
            'instrument_reviews',
            'mkva_assignments',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} harus ada di database.");
        }
    }

    public function test_models_can_be_instantiated_and_persisted(): void
    {
        $bank = BankAccount::create([
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1234567890',
            'account_holder' => 'LSP Sertifikasi Profesi',
            'branch' => 'Jakarta Pusat',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('bank_accounts', ['account_number' => '1234567890']);

        $finAcc = FinancialAccount::create([
            'account_code' => '4-1000',
            'account_name' => 'Pendapatan Uji Sertifikasi',
            'account_type' => 'pendapatan',
        ]);
        $this->assertDatabaseHas('financial_accounts', ['account_code' => '4-1000']);

        $trans = FinancialTransaction::create([
            'account_id' => $finAcc->id,
            'transaction_date' => now()->toDateString(),
            'reference_type' => 'pembayaran_asesi',
            'description' => 'Pembayaran Uji Mandiri Asesi',
            'type' => 'pemasukan',
            'amount' => 1500000,
        ]);
        $this->assertDatabaseHas('financial_transactions', ['amount' => 1500000]);

        $letter = Letter::create([
            'reference_number' => '001/ST-LSP/X/2026',
            'category' => 'surat_tugas',
            'subject' => 'Penugasan Asesor Uji Kompetensi TKJ',
            'recipient' => 'Abu, S.T.',
            'letter_date' => now()->toDateString(),
            'status' => 'terbit',
        ]);
        $this->assertDatabaseHas('letters', ['reference_number' => '001/ST-LSP/X/2026']);

        $msg = SystemMessage::create([
            'direction' => 'outgoing',
            'channel' => 'sms',
            'recipient_or_sender' => '08123456789',
            'body' => 'Kode OTP login Anda: 123456',
            'status' => 'terkirim',
        ]);
        $this->assertDatabaseHas('system_messages', ['recipient_or_sender' => '08123456789']);
    }
}
