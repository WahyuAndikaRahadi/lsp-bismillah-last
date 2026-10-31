<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\AssessmentAk03Item;
use App\Models\AssessmentAppeal;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AkWorkflowService
{
    /**
     * Auto-populate or update FR.AK.02 Rekaman Asesmen per unit kompetensi.
     */
    public function syncAk02(Assessment $assessment): array
    {
        $assessment->load(['schedule.scheme.competencyUnits', 'iaResponses', 'proofs', 'ak02Items']);
        $units = $assessment->schedule->scheme->competencyUnits;

        // Idempotency rule: Do NOT overwrite valid manual assessor overrides
        if (! empty($assessment->assessor_signed_at) && in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true)) {
            return [
                'final_result' => $assessment->final_result,
                'total_units' => $units->count(),
                'all_competent' => in_array($assessment->final_result, ['KOMPETEN', 'K'], true),
            ];
        }

        $iaResponses = $assessment->iaResponses->keyBy('instrument_code');
        $hasProofs = $assessment->proofs()->count() > 0;

        $hasDemonstration = $iaResponses->has('IA01') || $iaResponses->has('IA01B') || $iaResponses->has('IA02');
        $hasOral = $iaResponses->has('IA03');
        $hasWritten = $iaResponses->has('IA05');

        $allUnitsCompetent = true;
        $finalResult = 'KOMPETEN';

        DB::transaction(function () use (
            $assessment,
            $units,
            $hasDemonstration,
            $hasProofs,
            $hasOral,
            $hasWritten,
            $iaResponses,
            &$allUnitsCompetent,
            &$finalResult
        ) {
            foreach ($units as $unit) {
                $methods = [
                    'observasi_demonstrasi' => $hasDemonstration,
                    'portofolio' => $hasProofs,
                    'pihak_ketiga' => false,
                    'lisan' => $hasOral,
                    'tulis' => $hasWritten,
                    'proyek' => false,
                    'lainnya' => false,
                ];

                // Determine competence: if IA05 CBT failed (< passing grade), competence is false
                $isUnitCompetent = true;
                if ($hasWritten && isset($iaResponses['IA05'])) {
                    $passingGrade = (float) ($assessment->schedule?->scheme?->passing_grade_theory ?? Setting::get('cbt_passing_grade', 75.0));
                    $score = (float) $iaResponses['IA05']->score;
                    if ($score < $passingGrade) {
                        $isUnitCompetent = false;
                    }
                }

                if (! $isUnitCompetent) {
                    $allUnitsCompetent = false;
                }

                AssessmentAk02Item::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'competency_unit_id' => $unit->id,
                    ],
                    [
                        'methods_json' => $methods,
                        'is_competent' => $isUnitCompetent,
                    ]
                );
            }

            $finalResult = $allUnitsCompetent ? 'KOMPETEN' : 'BELUM_KOMPETEN';
            $assessment->update([
                'final_result' => $finalResult,
                'final_notes' => $finalResult === 'KOMPETEN'
                    ? 'Seluruh unjuk kerja memenuhi Kriteria Unjuk Kerja (KUK) dan standar SKKNI.'
                    : 'Diperlukan tindak lanjut pengayaan dan asesmen ulang pada unit yang belum kompeten.',
                'follow_up_action' => $finalResult === 'KOMPETEN'
                    ? 'Diusulkan untuk penerbitan Sertifikat Kompetensi BNSP.'
                    : 'Mengikuti asesmen ulang pada unit kompetensi terkait.',
            ]);
        });

        return [
            'final_result' => $finalResult,
            'total_units' => $units->count(),
            'all_competent' => $allUnitsCompetent,
        ];
    }

    /**
     * Submit candidate registration approval by Admin LSP.
     */
    public function approveCandidateRegistration(Assessment $assessment, string $status, ?string $notes = null, ?int $adminId = null): Assessment
    {
        $statusUpper = strtoupper($status);
        $validStatuses = ['PENDING', 'APPROVED', 'REVISION', 'REJECTED'];

        if (! in_array($statusUpper, $validStatuses, true)) {
            $statusUpper = 'APPROVED';
        }

        $now = now();
        $reviewerId = $adminId ?? auth()->id();

        DB::transaction(function () use ($assessment, $statusUpper, $notes, $now, $reviewerId) {
            $assessment->update([
                'admin_status' => $statusUpper,
                'admin_notes' => $notes,
                'admin_approved_at' => $now,
                'admin_reviewed_at' => $now,
                'admin_id' => $reviewerId,
                'admin_reviewer_id' => $reviewerId,
            ]);
        });

        return $assessment;
    }

    /**
     * Check whether an asesi candidate has complete required documents.
     * Rule: minimum 1 basic proof (IJAZAH_SMK, SURAT_SISWA_AKTIF, IJAZAH_SMA_SERTIFIKAT, PENGALAMAN_KERJA) + KTP + PAS_FOTO.
     */
    public function isCandidateDocumentComplete(Assessment $assessment): bool
    {
        if (! $assessment->relationLoaded('proofs')) {
            $assessment->load('proofs');
        }

        $proofTypes = $assessment->proofs->pluck('proof_type')->map(fn ($t) => strtoupper((string) $t))->all();
        $hasKtp = in_array('KTP', $proofTypes, true);
        $hasPasFoto = in_array('PAS_FOTO', $proofTypes, true);
        $hasDasar = (bool) array_intersect(
            ['IJAZAH_SMK', 'SURAT_SISWA_AKTIF', 'IJAZAH_SMA_SERTIFIKAT', 'PENGALAMAN_KERJA'],
            $proofTypes
        );

        return $hasKtp && $hasPasFoto && $hasDasar;
    }

    /**
     * Submit FR.AK.03 Umpan Balik Asesi.
     */
    public function submitAk03Feedback(Assessment $assessment, array $ratings, ?string $generalNotes = null): AssessmentAk03Item
    {
        return AssessmentAk03Item::updateOrCreate(
            ['assessment_id' => $assessment->id],
            [
                'ratings_json' => $ratings,
                'general_notes' => $generalNotes,
            ]
        );
    }

    /**
     * Get appeal window in business days (default: 3 days).
     * Note: 3 days is standard BNSP practice, pending official confirmation by LSP PrO Indo SOP.
     */
    public function getAppealWindowDays(): int
    {
        return (int) Setting::get('appeal_window_days', 3);
    }

    /**
     * Calculate appeal deadline in Monday-Friday business days (skipping Saturday and Sunday).
     */
    public function calculateAppealDeadline(Carbon $startDate, ?int $windowDays = null): Carbon
    {
        $days = $windowDays ?? $this->getAppealWindowDays();
        $current = $startDate->copy()->startOfDay();
        $added = 0;

        while ($added < $days) {
            $current->addDay();
            if (! $current->isWeekend()) {
                $added++;
            }
        }

        return $current->endOfDay();
    }

    /**
     * Get the start date for appeal countdown.
     * Priority: final_result_at (when K/BK was determined/announced) -> completed_at -> schedule->date -> now().
     * Note: calculating from final_result_at is BNSP standard, pending LSP PrO Indo SOP confirmation.
     */
    public function getAppealBaseDate(Assessment $assessment): Carbon
    {
        if ($assessment->final_result_at) {
            return Carbon::parse($assessment->final_result_at);
        }

        if ($assessment->completed_at) {
            return Carbon::parse($assessment->completed_at);
        }

        if ($assessment->schedule?->date) {
            return Carbon::parse($assessment->schedule->date);
        }

        return now();
    }

    /**
     * Check if given assessment appeal date is within allowed business days deadline.
     */
    public function isWithinAppealDeadline(Assessment $assessment, ?Carbon $targetDate = null): bool
    {
        $target = $targetDate ? $targetDate->copy() : now();
        $baseDate = $this->getAppealBaseDate($assessment);

        $deadline = $this->calculateAppealDeadline($baseDate);

        return $target->lte($deadline);
    }

    /**
     * Submit FR.AK.04 Banding Asesmen by Asesi.
     */
    public function submitAppeal(Assessment $assessment, string $reasons, array $contestedUnits = [], ?Carbon $appealDate = null): AssessmentAppeal
    {
        $date = $appealDate ?? now();

        if (! $this->isWithinAppealDeadline($assessment, $date)) {
            $days = $this->getAppealWindowDays();
            $baseDate = $this->getAppealBaseDate($assessment);
            $deadline = $this->calculateAppealDeadline($baseDate);

            throw ValidationException::withMessages([
                'appeal_reasons' => "Batas waktu pengajuan banding ({$days} hari kerja) telah berakhir pada ".$deadline->translatedFormat('d F Y').'. Sesuai ketentuan, pengajuan banding tidak dapat diproses.',
            ]);
        }

        return DB::transaction(function () use ($assessment, $date, $reasons, $contestedUnits) {
            $lockedAssessment = Assessment::where('id', $assessment->id)->lockForUpdate()->first();

            $existingAppeal = AssessmentAppeal::where('assessment_id', $lockedAssessment->id)
                ->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])
                ->lockForUpdate()
                ->first();

            if ($existingAppeal) {
                throw ValidationException::withMessages([
                    'appeal_reasons' => 'Pengajuan banding untuk asesmen ini sedang aktif atau dalam proses peninjauan.',
                ]);
            }

            return AssessmentAppeal::create([
                'assessment_id' => $lockedAssessment->id,
                'appeal_date' => $date->toDateString(),
                'appeal_reasons' => $reasons,
                'contested_units' => $contestedUnits,
                'status' => 'SUBMITTED',
            ]);
        });
    }

    /**
     * Decide appeal by Admin LSP (optionally specifying Appeals Committee Chair).
     */
    public function decideAppeal(AssessmentAppeal $appeal, string $decision, ?string $decisionNotes = null, ?int $adminId = null, ?string $committeeChair = null): AssessmentAppeal
    {
        $status = strtoupper($decision) === 'ACCEPTED' ? 'ACCEPTED' : 'REJECTED';

        DB::transaction(function () use ($appeal, $status, $decisionNotes, $adminId, $committeeChair) {
            $appeal->update([
                'status' => $status,
                'decision_notes' => $decisionNotes,
                'decision_date' => now()->toDateString(),
                'decided_by' => $adminId ?? auth()->id(),
                'appeals_committee_chair' => $committeeChair ?: $appeal->appeals_committee_chair,
            ]);
        });

        return $appeal;
    }

    /**
     * Finalize assessment with a valid final decision (KOMPETEN, BELUM_KOMPETEN, or TIDAK_HADIR).
     * This is the single source of truth for transitioning an assessment to COMPLETED.
     */
    public function finalizeAssessment(
        Assessment $assessment,
        string $finalResult,
        ?string $notes = null,
        ?User $actor = null,
        ?string $followUpAction = null,
        ?string $assessorSignature = null
    ): Assessment {
        $normalizedResult = match (strtoupper($finalResult)) {
            'K', 'KOMPETEN' => 'KOMPETEN',
            'BK', 'BELUM_KOMPETEN' => 'BELUM_KOMPETEN',
            'TIDAK_HADIR', 'TH' => 'TIDAK_HADIR',
            default => throw new \InvalidArgumentException("Hasil akhir tidak valid: {$finalResult}"),
        };

        return DB::transaction(function () use ($assessment, $normalizedResult, $notes, $actor, $followUpAction, $assessorSignature) {
            $now = now();
            $data = [
                'final_result' => $normalizedResult,
                'final_result_at' => $assessment->final_result_at ?? $now,
                'status' => 'COMPLETED',
                'completed_at' => $assessment->completed_at ?? $now,
            ];

            if ($notes !== null) {
                $data['final_notes'] = $notes;
            }

            if ($followUpAction !== null) {
                $data['follow_up_action'] = $followUpAction;
            }

            if ($actor && in_array($actor->role, ['ASESOR', 'SUPER_ADMIN', 'ADMIN_LSP'], true)) {
                $data['assessor_id'] = $actor->id;
            }

            if ($assessorSignature) {
                $data['assessor_signature'] = $assessorSignature;
                $data['assessor_signed_at'] = $assessment->assessor_signed_at ?? $now;
            }

            $assessment->update($data);

            return $assessment;
        });
    }

    /**
     * Mark candidate as absent (TIDAK_HADIR).
     */
    public function markCandidateAbsent(Assessment $assessment, ?string $notes = null, ?int $userId = null): Assessment
    {
        $actor = $userId ? User::find($userId) : auth()->user();

        return $this->finalizeAssessment(
            $assessment,
            'TIDAK_HADIR',
            $notes ?: 'Peserta tidak hadir pada pelaksanaan uji kompetensi.',
            $actor
        );
    }

    /**
     * Save Schedule Report (FR.AK.05 & FR.AK.06), and close schedule only if closure conditions are met.
     */
    public function saveScheduleReports(Schedule $schedule, array $ak05Data, array $ak06Data, bool $closeIfReady = true): bool
    {
        return DB::transaction(function () use ($schedule, $ak05Data, $ak06Data, $closeIfReady) {
            $lockedSchedule = Schedule::where('id', $schedule->id)->lockForUpdate()->first();

            $lockedSchedule->update([
                'ak05_report' => array_merge($ak05Data, [
                    'assessor_id' => auth()->id(),
                    'submitted_at' => now()->toIso8601String(),
                ]),
                'ak06_review' => array_merge($ak06Data, [
                    'reviewer_id' => auth()->id(),
                    'reviewed_at' => now()->toIso8601String(),
                ]),
            ]);

            if ($closeIfReady) {
                $readiness = $this->isScheduleReadyForClosure($lockedSchedule);
                if ($readiness['is_ready']) {
                    $lockedSchedule->update([
                        'is_closed' => true,
                        'closed_at' => now(),
                        'status' => 'FINISHED',
                    ]);

                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Submit Schedule Report (FR.AK.05 & FR.AK.06) before closure.
     */
    public function submitScheduleReports(Schedule $schedule, array $ak05Data, array $ak06Data): Schedule
    {
        $this->saveScheduleReports($schedule, $ak05Data, $ak06Data, true);

        return $schedule->refresh();
    }

    /**
     * Check if schedule is ready to be closed.
     * Treats TIDAK_HADIR as resolved/completed for closure.
     */
    public function isScheduleReadyForClosure(Schedule $schedule): array
    {
        $uncompletedAssessments = $schedule->assessments()
            ->where(function ($query) {
                $query->where('status', '!=', 'COMPLETED')
                    ->where('final_result', '!=', 'TIDAK_HADIR');
            })
            ->count();

        $hasAk05 = ! empty($schedule->ak05_report);
        $hasAk06 = ! empty($schedule->ak06_review);

        return [
            'is_ready' => $uncompletedAssessments === 0,
            'uncompleted_count' => $uncompletedAssessments,
            'has_ak05' => $hasAk05,
            'has_ak06' => $hasAk06,
        ];
    }
}
