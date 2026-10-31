<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Administrator LSP
        User::firstOrCreate(
            ['email' => 'admin@lsp.localhost'],
            [
                'name' => 'Administrator LSP',
                'role' => UserRole::Admin,
                'nik' => '3171010101900001',
                'phone' => '08110000001',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Asesor Kompetensi
        User::firstOrCreate(
            ['email' => 'asesor@lsp.localhost'],
            [
                'name' => 'Abu, S.T. (Asesor)',
                'role' => UserRole::Asesor,
                'nik' => '3504010710670002',
                'phone' => '081234567890',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Asesi / Peserta Uji
        User::firstOrCreate(
            ['email' => 'asesi@lsp.localhost'],
            [
                'name' => 'Aditya Dwi Kurniawan',
                'role' => UserRole::Asesi,
                'nik' => '3517060507860006',
                'phone' => '081234567891',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Seed Master Data (Profil LSP, SKKNI, Skema, Unit, KUK, TUK, Asesor)
        $this->call([
            MasterDataSeeder::class,
            EventScheduleSeeder::class,
            Apl01ApplicationSeeder::class,
            ExtendedModulesSeeder::class,
            ProindoSchemesAndMukSeeder::class,
            ProindoUserSeeder::class,
        ]);

        // 5. Seed Event & Jadwal Asesmen (Fase 3)
        $this->call(EventScheduleSeeder::class);

        // 6. Seed Pendaftaran & Verifikasi APL-01 (Fase 4)
        $this->call(Apl01ApplicationSeeder::class);
    }
}
