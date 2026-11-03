<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentApplication;
use App\Models\LegacyIdentityMap;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LegacyReconciliationService
{
    /**
     * Run reconciliation on legacy assessment applications into canonical assessments.
     *
     * @param bool $dryRun
     * @param int $batchSize
     * @return array Summary report
     */
    public function reconcile(bool $dryRun = true, int $batchSize = 100): array
    {
        $report = [
            'total_legacy' => 0,
            'mapped' => 0,
            'conflicts' => 0,
            'quarantined' => 0,
            'skipped' => 0,
            'dry_run' => $dryRun,
            'details' => [],
        ];

        if (! class_exists(AssessmentApplication::class)) {
            return $report;
        }

        $legacyApplications = AssessmentApplication::all();
        $report['total_legacy'] = $legacyApplications->count();

        foreach ($legacyApplications as $legacy) {
            $existingMap = LegacyIdentityMap::where('source_table', 'assessment_applications')
                ->where('legacy_id', $legacy->id)
                ->first();

            if ($existingMap && $existingMap->status === 'mapped') {
                $report['skipped']++;
                continue;
            }

            $checksum = hash('sha256', json_encode($legacy->toArray()));

            // 1. Check for orphaned candidate
            $candidateExists = User::where('id', $legacy->user_id)->exists();
            if (! $candidateExists) {
                $report['quarantined']++;
                $report['details'][] = [
                    'legacy_id' => $legacy->id,
                    'status' => 'quarantined',
                    'reason' => "Orphan candidate user_id {$legacy->user_id} not found in users",
                ];

                if (! $dryRun) {
                    LegacyIdentityMap::updateOrCreate(
                        ['source_table' => 'assessment_applications', 'legacy_id' => $legacy->id],
                        [
                            'target_table' => 't_assessments',
                            'target_id' => null,
                            'checksum' => $checksum,
                            'status' => 'quarantined',
                            'reason' => "Orphan candidate user_id {$legacy->user_id} not found in users",
                        ]
                    );
                }
                continue;
            }

            // 2. Identify target schedule
            $scheduleId = $legacy->schedule_id;
            $targetSchedule = Schedule::find($scheduleId);

            if (! $targetSchedule) {
                $report['quarantined']++;
                $report['details'][] = [
                    'legacy_id' => $legacy->id,
                    'status' => 'quarantined',
                    'reason' => "Orphan schedule_id {$scheduleId} not found in t_schedules",
                ];

                if (! $dryRun) {
                    LegacyIdentityMap::updateOrCreate(
                        ['source_table' => 'assessment_applications', 'legacy_id' => $legacy->id],
                        [
                            'target_table' => 't_assessments',
                            'target_id' => null,
                            'checksum' => $checksum,
                            'status' => 'quarantined',
                            'reason' => "Orphan schedule_id {$scheduleId} not found in t_schedules",
                        ]
                    );
                }
                continue;
            }

            // 3. Dry-run checkpoint: do not modify target domain
            if ($dryRun) {
                $report['mapped']++;
                $report['details'][] = [
                    'legacy_id' => $legacy->id,
                    'status' => 'mapped (dry-run)',
                    'candidate_id' => $legacy->user_id,
                    'schedule_id' => $scheduleId,
                ];
                continue;
            }

            // 4. Atomic application per batch with transaction
            try {
                DB::transaction(function () use ($legacy, $checksum, $scheduleId, &$report) {
                    // Do NOT merge purely by numeric ID! Find or create canonical assessment by candidate + schedule
                    $statusStr = $legacy->status instanceof \BackedEnum
                        ? $legacy->status->value
                        : (string) ($legacy->status ?? 'diajukan');

                    $canonical = Assessment::firstOrCreate(
                        [
                            'schedule_id' => $scheduleId,
                            'asesi_id' => $legacy->user_id,
                        ],
                        [
                            'assessor_id' => $legacy->asesor_id,
                            'status' => 'REGISTERED',
                            'apl_status' => strtoupper($statusStr) === 'DITERIMA' ? 'APPROVED' : 'SUBMITTED',
                            'final_result' => 'BELUM_SELESAI',
                        ]
                    );

                    LegacyIdentityMap::updateOrCreate(
                        ['source_table' => 'assessment_applications', 'legacy_id' => $legacy->id],
                        [
                            'target_table' => 't_assessments',
                            'target_id' => $canonical->id,
                            'checksum' => $checksum,
                            'status' => 'mapped',
                            'reason' => null,
                        ]
                    );

                    $report['mapped']++;
                });
            } catch (\Throwable $e) {
                Log::error("Failed to reconcile legacy assessment application #{$legacy->id}: " . $e->getMessage());
                $report['conflicts']++;
                $report['details'][] = [
                    'legacy_id' => $legacy->id,
                    'status' => 'conflict',
                    'reason' => $e->getMessage(),
                ];

                LegacyIdentityMap::updateOrCreate(
                    ['source_table' => 'assessment_applications', 'legacy_id' => $legacy->id],
                    [
                        'target_table' => 't_assessments',
                        'target_id' => null,
                        'checksum' => $checksum,
                        'status' => 'conflict',
                        'reason' => $e->getMessage(),
                    ]
                );
            }
        }

        return $report;
    }

    /**
     * Store large immutable snapshot payload (>64KB).
     */
    public function storeSnapshot(Assessment $assessment, array $payload): void
    {
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);
        $assessment->update([
            'snapshot_profile' => $encoded,
        ]);
    }

    /**
     * Retrieve snapshot payload.
     */
    public function getSnapshot(Assessment $assessment): ?array
    {
        if (empty($assessment->snapshot_profile)) {
            return null;
        }

        if (is_array($assessment->snapshot_profile)) {
            return $assessment->snapshot_profile;
        }

        return json_decode($assessment->snapshot_profile, true);
    }
}
