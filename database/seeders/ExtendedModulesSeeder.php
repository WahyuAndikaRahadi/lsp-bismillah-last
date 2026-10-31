<?php

namespace Database\Seeders;

use App\Models\AuditInternal;
use App\Models\BankAccount;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Letter;
use App\Models\MutuDocument;
use App\Models\SystemMessage;
use Illuminate\Database\Seeder;

class ExtendedModulesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Rekening Bank
        BankAccount::firstOrCreate(
            ['account_number' => '1370019283741'],
            [
                'bank_name' => 'Bank Mandiri',
                'account_holder' => 'LSP Sertifikasi Profesi Mandiri',
                'branch' => 'KCP Jakarta Sudirman',
                'is_active' => true,
            ]
        );

        BankAccount::firstOrCreate(
            ['account_number' => '8012938475'],
            [
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_holder' => 'LSP Sertifikasi Profesi Mandiri',
                'branch' => 'KCU Thamrin',
                'is_active' => true,
            ]
        );

        // 2. Kode Akun Keuangan
        $kas = FinancialAccount::firstOrCreate(
            ['account_code' => '1-1100'],
            ['account_name' => 'Kas Operasional LSP', 'account_type' => 'aktiva']
        );

        $bank = FinancialAccount::firstOrCreate(
            ['account_code' => '1-1200'],
            ['account_name' => 'Bank Mandiri Giro', 'account_type' => 'aktiva']
        );

        $pendapatan = FinancialAccount::firstOrCreate(
            ['account_code' => '4-1000'],
            ['account_name' => 'Pendapatan Biaya Uji Sertifikasi', 'account_type' => 'pendapatan']
        );

        $honor = FinancialAccount::firstOrCreate(
            ['account_code' => '5-1100'],
            ['account_name' => 'Beban Honorarium Asesor Penguji', 'account_type' => 'beban']
        );

        // 3. Sample Transaksi
        FinancialTransaction::firstOrCreate(
            ['description' => 'Pembayaran Uji Mandiri Asesi TKJ - Batch 1'],
            [
                'account_id' => $pendapatan->id,
                'transaction_date' => now()->subDays(5)->toDateString(),
                'type' => 'pemasukan',
                'amount' => 7500000,
            ]
        );

        FinancialTransaction::firstOrCreate(
            ['description' => 'Honorarium Asesor Uji Praktik Demonstrasi'],
            [
                'account_id' => $honor->id,
                'transaction_date' => now()->subDays(2)->toDateString(),
                'type' => 'pengeluaran',
                'amount' => 2500000,
            ]
        );

        // 4. Dokumen Mutu
        MutuDocument::firstOrCreate(
            ['document_code' => 'PM-LSP-01'],
            [
                'title' => 'Panduan Mutu Pelaksanaan Asesmen Kompetensi Berdasarkan BNSP 201',
                'category' => 'panduan_mutu',
                'version' => '2.1',
                'effective_date' => '2026-01-01',
                'status' => 'aktif',
            ]
        );

        MutuDocument::firstOrCreate(
            ['document_code' => 'SOP-LSP-04'],
            [
                'title' => 'Standar Operasional Prosedur Verifikasi TUK dan Perangkat Asesmen',
                'category' => 'sop',
                'version' => '1.5',
                'effective_date' => '2026-01-15',
                'status' => 'aktif',
            ]
        );

        // 5. Audit Internal
        AuditInternal::firstOrCreate(
            ['audit_code' => 'AUD-2026-001'],
            [
                'audit_date' => '2026-03-15',
                'auditor_name' => 'Dr. Bambang Sutrisno, M.T.',
                'scope' => 'Klausul 4 s/d 10 Pedoman BNSP 201 (Manajemen & Operasional)',
                'findings' => 'Seluruh dokumen verifikasi TUK tersimpan digital dan rapi.',
                'recommendations' => 'Tingkatkan kecepatan verifikasi APL-01 secara realtime.',
                'status' => 'selesai',
            ]
        );

        // 6. Persuratan
        Letter::firstOrCreate(
            ['reference_number' => '001/ST-LSP/X/2026'],
            [
                'category' => 'surat_tugas',
                'subject' => 'Surat Tugas Asesor Uji Kompetensi TKJ',
                'recipient' => 'Abu, S.T. (No. MET: MET.000.00000.0020)',
                'letter_date' => now()->toDateString(),
                'signer_name' => 'Ketua LSP Sertifikasi Profesi',
                'signer_role' => 'Ketua LSP',
                'status' => 'terbit',
            ]
        );

        // 7. System Messages
        SystemMessage::firstOrCreate(
            ['body' => 'Kode OTP login Asesor LSP Demo Aplikasi SILSP: 544802. Berlaku 10 menit.'],
            [
                'direction' => 'outgoing',
                'channel' => 'sms',
                'recipient_or_sender' => '081234567890',
                'status' => 'terkirim',
            ]
        );

        SystemMessage::firstOrCreate(
            ['body' => 'Yth. Asesor Abu, Anda telah ditugaskan pada jadwal asesmen SCH-TKJ-2026 di TUK Lab TKJ.'],
            [
                'direction' => 'outgoing',
                'channel' => 'whatsapp',
                'recipient_or_sender' => '081234567890',
                'status' => 'terkirim',
            ]
        );
    }
}
