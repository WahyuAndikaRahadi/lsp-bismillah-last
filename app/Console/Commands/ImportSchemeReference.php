<?php

namespace App\Console\Commands;

use App\Services\SchemeReferenceService;
use Illuminate\Console\Command;
use InvalidArgumentException;

class ImportSchemeReference extends Command
{
    protected $signature = 'scheme:import-reference 
                            {path : Path berkas JSON referensi skema} 
                            {--dry-run : Uji validasi skema tanpa menulis perubahan ke basis data}
                            {--approve : Langsung publish skema setelah validasi lolos jika bukti tersedia}';

    protected $description = 'Import dan validasi referensi skema sertifikasi dengan versioning immutable';

    public function handle(SchemeReferenceService $service): int
    {
        $path = $this->argument('path');
        if (! file_exists($path)) {
            $this->error("Berkas referensi tidak ditemukan di path: {$path}");

            return 1;
        }

        $content = file_get_contents($path);
        $data = json_decode($content, true);

        if (! is_array($data)) {
            $this->error("Berkas bukan format JSON valid: {$path}");

            return 1;
        }

        $dryRun = $this->option('dry-run');
        $this->info("Menjalankan import referensi skema: ".($dryRun ? 'MODE DRY-RUN' : 'MODE PENYIMPANAN AKTIF'));

        $result = $service->import($data, dryRun: $dryRun);

        if (! $result['success']) {
            $this->error("Validasi referensi gagal dengan ".count($result['errors'])." kesalahan:");
            foreach ($result['errors'] as $err) {
                $this->line(" - <fg=red>{$err}</>");
            }

            return 1;
        }

        if ($dryRun) {
            $this->info("Validasi berhasil! Skema: {$result['scheme_code']} ({$result['units_count']} unit). Hash: {$result['source_hash']}");

            return 0;
        }

        $this->info("Aksi: {$result['action']} | Skema ID: {$result['scheme_id']} | Versi: {$result['version']} | Status: {$result['status']}");

        return 0;
    }
}
