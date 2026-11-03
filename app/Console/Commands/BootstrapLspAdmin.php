<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapLspAdmin extends Command
{
    protected $signature = 'lsp:bootstrap-admin 
                            {--name= : Nama lengkap administrator}
                            {--email= : Alamat email login administrator}
                            {--password= : Password akun administrator (opsional, akan diminta secara rahasia jika kosong)}
                            {--nik= : NIK KTP administrator (16 digit)}
                            {--phone= : Nomor telepon/WhatsApp aktif}';

    protected $description = 'Bootstrap akun administrator resmi LSP tanpa kata sandi demo hardcoded';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama Lengkap Administrator');
        $email = strtolower(trim($this->option('email') ?: $this->ask('Email Administrator')));

        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret('Password Administrator (input rahasia)');
            $confirmation = $this->secret('Konfirmasi Password Administrator');

            if ($password !== $confirmation) {
                $this->error('Password dan konfirmasi password tidak cocok.');

                return 1;
            }
        }

        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return 1;
        }

        $nik = $this->option('nik') ?: $this->ask('NIK (16 digit)');
        $phone = $this->option('phone') ?: $this->ask('Nomor Telepon/HP');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'full_name' => $name,
                'role' => UserRole::Admin,
                'nik' => $nik,
                'phone' => $phone,
                'password' => Hash::make($password),
                'is_active' => true,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => $nik,
                'no_telepon_rumah' => $phone,
                'kebangsaan' => 'Indonesia',
            ]
        );

        $this->info("Akun administrator resmi LSP berhasil dibootstrap: {$user->email} (ID: {$user->id})");

        return 0;
    }
}
