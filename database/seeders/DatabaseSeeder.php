<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\LspProfile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Official LSP Reference & Master Data
        $this->call([
            LspReferenceSeeder::class,
            TukSeeder::class,
            UserSeeder::class,
            SchemeSeeder::class,
        ]);

        // 2. Default System Settings
        if (Schema::hasTable('m_settings')) {
            Setting::firstOrCreate(
                ['key' => 'appeal_window_days'],
                ['value' => '3', 'description' => 'Batas waktu pengajuan banding dalam hari kerja']
            );

            Setting::firstOrCreate(
                ['key' => 'cbt_passing_grade'],
                ['value' => '75.0', 'description' => 'Nilai passing grade ujian teori pilihan ganda CBT (FR.IA.05)']
            );
        }

        // 3. Official LSP Profile
        if (Schema::hasTable('lsp_profiles')) {
            LspProfile::updateOrCreate(
                ['kode_lsp' => 'LSP-026-ID'],
                [
                    'nama_lsp' => 'LSP PrO Indo',
                    'no_lisensi_bnsp' => 'BNSP-LSP-026-ID',
                    'alamat' => 'Cikarang, Bekasi, Jawa Barat',
                    'telepon' => '021-8901234',
                    'email' => 'lsp.proindo@gmail.com',
                    'website' => 'https://demo.aplikasilsp.web.id',
                    'nama_ketua' => 'Ir. Bambang S., M.T.',
                ]
            );
        }

        // 4. Legacy Demo Credentials for convenience (optional)
        User::firstOrCreate(
            ['email' => 'admin@lsp.localhost'],
            [
                'name' => 'Administrator LSP',
                'full_name' => 'Administrator LSP',
                'role' => UserRole::Admin,
                'nik' => '3171010101900001',
                'phone' => '08110000001',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'asesor@lsp.localhost'],
            [
                'name' => 'Abu, S.T. (Asesor)',
                'full_name' => 'Abu, S.T. (Asesor)',
                'role' => UserRole::Asesor,
                'nik' => '3504010710670002',
                'phone' => '081234567890',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'asesi@lsp.localhost'],
            [
                'name' => 'Aditya Dwi Kurniawan',
                'full_name' => 'Aditya Dwi Kurniawan',
                'role' => UserRole::Asesi,
                'nik' => '3517060507860006',
                'phone' => '081234567891',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
