<?php

namespace Database\Seeders;

use App\Models\LspProfile;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LspReferenceSeeder extends Seeder
{
    /**
     * Seed official LSP identity & core settings idempotently without creating demo users or demo passwords.
     */
    public function run(): void
    {
        // 1. Official LSP Profile from authentic data
        if (Schema::hasTable('lsp_profiles')) {
            LspProfile::firstOrCreate(
                ['kode_lsp' => 'LSP-026-ID'],
                [
                    'nama_lsp' => 'LSP PRO INDO',
                    'no_lisensi_bnsp' => 'BNSP-LSP-026-ID',
                    'alamat' => 'Cikarang, Bekasi, Jawa Barat',
                    'telepon' => '021-8901234',
                    'email' => 'sekretariat@lspproindo.id',
                    'website' => 'https://lspproindo.id',
                    'nama_ketua' => 'Ir. Bambang S., M.T.',
                    'no_sk_lisensi' => 'KEP.026/BNSP/2022',
                    'masa_berlaku_lisensi' => '2027-12-31',
                ]
            );
        }

        // 2. Official Core System Settings
        if (Schema::hasTable('m_settings')) {
            Setting::firstOrCreate(
                ['key' => 'appeal_window_days'],
                ['value' => '3', 'description' => 'Batas waktu pengajuan banding dalam hari kerja']
            );

            Setting::firstOrCreate(
                ['key' => 'cbt_passing_grade'],
                ['value' => '75.0', 'description' => 'Nilai passing grade ujian teori CBT FR.IA.05']
            );

            Setting::firstOrCreate(
                ['key' => 'surveillance_interval_months'],
                ['value' => '12', 'description' => 'Interval surveilans pemegang sertifikat dalam bulan']
            );
        }
    }
}
