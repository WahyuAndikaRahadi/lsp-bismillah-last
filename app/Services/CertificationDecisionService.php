<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AuditEvent;
use App\Models\CertificationDecision;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CertificationDecisionService
{
    /**
     * Validate whether an actor has authority to decide on this assessment.
     * Rule: The assessor who assessed the candidate CANNOT be the decision-maker for that candidate.
     */
    public function validateDecisionMakerImpartiality(Assessment $assessment, User $actor): void
    {
        // 1. Candidate cannot decide their own assessment
        if ((int) $actor->id === (int) $assessment->asesi_id) {
            throw ValidationException::withMessages([
                'decided_by' => 'Peserta dilarang mengambil keputusan sertifikasi untuk dirinya sendiri.',
            ]);
        }

        // 2. The assigned assessor who assessed this candidate CANNOT be the decision maker
        if ($assessment->assessor_id && (int) $actor->id === (int) $assessment->assessor_id) {
            throw ValidationException::withMessages([
                'decided_by' => 'Asesor penguji dilarang menjadi pengambil keputusan sertifikasi untuk asesi yang diujinya (conflict of interest). Pengambilan keputusan harus dilakukan oleh tim/komite independen.',
            ]);
        }
    }

    /**
     * Record an explicit certification decision (FR-SOP-10.1 / SOP 04 §3.4).
     */
    public function recordDecision(
        Assessment $assessment,
        string $decision,
        User $actor,
        string $authorityRole,
        ?string $decisionNotes = null,
        ?string $correctiveAction = null,
        ?string $validationNotes = null,
        ?int $plenoMeetingId = null
    ): CertificationDecision {
        $this->validateDecisionMakerImpartiality($assessment, $actor);

        $normalizedDecision = strtoupper(trim($decision));
        if (! in_array($normalizedDecision, ['KOMPETEN', 'BELUM_KOMPETEN', 'CORRECTION_REQUESTED'], true)) {
            throw new \InvalidArgumentException("Keputusan sertifikasi tidak valid: {$decision}");
        }

        // AK-02 recommendation must exist before certification decision
        if (! in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true) && ! $assessment->rekomendasi_asesor) {
            throw ValidationException::withMessages([
                'decision' => 'Keputusan sertifikasi hanya dapat ditetapkan setelah rekomendasi asesor (FR.AK.02) telah selesai direkam.',
            ]);
        }

        return DB::transaction(function () use ($assessment, $normalizedDecision, $actor, $authorityRole, $decisionNotes, $correctiveAction, $validationNotes, $plenoMeetingId) {
            $lockedAssessment = Assessment::where('id', $assessment->id)->lockForUpdate()->first();

            // Supersede existing active decision if any (preserving immutable history)
            $existingActive = CertificationDecision::where('assessment_id', $lockedAssessment->id)
                ->where('is_superseded', false)
                ->lockForUpdate()
                ->first();

            if ($existingActive) {
                $existingActive->update([
                    'is_superseded' => true,
                    'status' => 'SUPERSEDED',
                    'superseded_by' => $actor->id,
                    'superseded_at' => now(),
                    'supersede_reason' => "Keputusan baru ditetapkan oleh {$actor->name} ({$authorityRole})",
                ]);
            }

            $newDecision = CertificationDecision::create([
                'assessment_id' => $lockedAssessment->id,
                'schedule_id' => $lockedAssessment->schedule_id,
                'pleno_meeting_id' => $plenoMeetingId,
                'decision' => $normalizedDecision,
                'decision_notes' => $decisionNotes,
                'corrective_action' => $correctiveAction,
                'validation_notes' => $validationNotes,
                'decided_by' => $actor->id,
                'authority_role' => $authorityRole,
                'decided_at' => now(),
                'evidence_verified' => true,
                'is_superseded' => false,
                'status' => 'FINAL',
            ]);

            // Update assessment status according to decision
            if ($normalizedDecision === 'CORRECTION_REQUESTED') {
                $lockedAssessment->update([
                    'status' => 'REVISION_REQUESTED',
                    'pleno_decision' => null,
                    'pleno_notes' => $correctiveAction,
                ]);
            } else {
                $lockedAssessment->update([
                    'pleno_decision' => $normalizedDecision,
                    'pleno_notes' => $decisionNotes,
                ]);
            }

            AuditEvent::record(
                action: 'certification.decision_recorded',
                subject: $newDecision,
                reason: "Keputusan komite sertifikasi ditetapkan: {$normalizedDecision}",
                details: [
                    'assessment_id' => $lockedAssessment->id,
                    'decision' => $normalizedDecision,
                    'authority_role' => $authorityRole,
                    'decided_by_id' => $actor->id,
                ],
                userId: $actor->id
            );

            return $newDecision;
        });
    }
}
