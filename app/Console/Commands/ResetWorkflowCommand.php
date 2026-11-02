<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetWorkflowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lsp:reset-workflow {--force : Paksa eksekusi tanpa konfirmasi dan izinkan di environment production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengosongkan seluruh data transaksi workflow asesmen (jadwal, asesmen, respon, bukti, laporan) tanpa menyentuh data master/akun';

    /**
     * Workflow tables to be emptied (ordered by reverse dependency).
     *
     * @var array<int, string>
     */
    protected array $workflowTables = [
        't_assessment_appeals',
        't_assessment_ak07_items',
        't_assessment_ak03_items',
        't_assessment_ak02_items',
        't_assessment_ia_responses',
        't_assessment_apl02_items',
        't_assessment_proofs',
        't_assessments',
        't_schedule_assessors',
        't_schedules',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Production environment guard
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('Perintah lsp:reset-workflow ditolak di environment production tanpa opsi --force.');

            return self::FAILURE;
        }

        // 2. Count rows per workflow table
        $tableRows = [];
        $totalRows = 0;

        foreach ($this->workflowTables as $table) {
            $count = Schema::hasTable($table) ? DB::table($table)->count() : 0;
            $tableRows[] = [$table, number_format($count)];
            $totalRows += $count;
        }

        $this->info('=== DAFTAR TABEL WORKFLOW YANG AKAN DIKOSONGKAN ===');
        $this->table(['Tabel Workflow', 'Jumlah Baris Data'], $tableRows);
        $this->line("Total data transaksi workflow: <comment>{$totalRows} baris</comment>");
        $this->line('<info>Catatan: Tabel master (users, profil, skema, unit, elemen, KUK, bank soal, TUK, settings) TIDAK disentuh.</info>');

        if ($totalRows === 0) {
            $this->info('Seluruh tabel workflow sudah dalam kondisi kosong. Tidak ada data yang dihapus.');

            return self::SUCCESS;
        }

        // 3. User confirmation
        if (! $this->option('force') && ! $this->confirm('Apakah Anda yakin ingin mengosongkan SELURUH data transaksi workflow di atas? Tindakan ini tidak dapat dibatalkan.')) {
            $this->warn('Operasi pengosongan workflow dibatalkan oleh pengguna.');

            return self::SUCCESS;
        }

        // 4. Execute deletion in transaction
        DB::transaction(function () {
            foreach ($this->workflowTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }
        });

        // 5. Delete uploaded files associated with workflow
        Storage::disk('public')->deleteDirectory('proofs');
        Storage::disk('public')->deleteDirectory('ia02_evidence');
        Storage::disk('local')->deleteDirectory('proofs');
        Storage::disk('local')->deleteDirectory('ia02_evidence');

        $this->info("Berhasil! Seluruh tabel workflow ({$totalRows} baris) dan berkas unggahan terkait telah dikosongkan.");

        return self::SUCCESS;
    }
}
