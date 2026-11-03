<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_archived_tables_can_be_persisted_via_db(): void
    {
        DB::table('bank_accounts')->insert([
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1234567890',
            'account_holder' => 'LSP Sertifikasi Profesi',
            'branch' => 'Jakarta Pusat',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('bank_accounts', ['account_number' => '1234567890']);

        $finAccId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '4-1000',
            'account_name' => 'Pendapatan Uji Sertifikasi',
            'account_type' => 'pendapatan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('financial_accounts', ['account_code' => '4-1000']);

        DB::table('financial_transactions')->insert([
            'account_id' => $finAccId,
            'transaction_date' => now()->toDateString(),
            'reference_type' => 'pembayaran_asesi',
            'description' => 'Pembayaran Uji Mandiri Asesi',
            'type' => 'pemasukan',
            'amount' => 1500000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('financial_transactions', ['amount' => 1500000]);

        DB::table('letters')->insert([
            'reference_number' => '001/ST-LSP/X/2026',
            'category' => 'surat_tugas',
            'subject' => 'Penugasan Asesor Uji Kompetensi TKJ',
            'recipient' => 'Abu, S.T.',
            'letter_date' => now()->toDateString(),
            'status' => 'terbit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('letters', ['reference_number' => '001/ST-LSP/X/2026']);

        DB::table('system_messages')->insert([
            'direction' => 'outgoing',
            'channel' => 'sms',
            'recipient_or_sender' => '08123456789',
            'body' => 'Kode OTP login Anda: 123456',
            'status' => 'terkirim',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('system_messages', ['recipient_or_sender' => '08123456789']);
    }
}
