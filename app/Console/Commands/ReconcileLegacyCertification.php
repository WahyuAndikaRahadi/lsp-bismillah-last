<?php

namespace App\Console\Commands;

use App\Services\LegacyReconciliationService;
use Illuminate\Console\Command;

class ReconcileLegacyCertification extends Command
{
    protected $signature = 'lsp:reconcile-legacy {--dry-run : Run simulation without writing to canonical tables} {--batch=100 : Batch size}';
    protected $description = 'Reconcile legacy certification applications into canonical assessments without numeric ID collision';

    public function handle(LegacyReconciliationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $batch = (int) $this->option('batch');

        $this->info($dryRun ? 'Running reconciliation in DRY-RUN mode...' : 'Applying reconciliation to canonical domain...');

        $report = $service->reconcile($dryRun, $batch);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Legacy Records', $report['total_legacy']],
                ['Mapped', $report['mapped']],
                ['Skipped (Already mapped)', $report['skipped']],
                ['Quarantined (Orphans)', $report['quarantined']],
                ['Conflicts', $report['conflicts']],
                ['Mode', $report['dry_run'] ? 'DRY-RUN' : 'APPLIED'],
            ]
        );

        if (! empty($report['details'])) {
            $this->warn('Issues / Details:');
            foreach ($report['details'] as $detail) {
                $this->line("- Legacy ID {$detail['legacy_id']}: {$detail['status']}" . (! empty($detail['reason']) ? " ({$detail['reason']})" : ''));
            }
        }

        return 0;
    }
}
