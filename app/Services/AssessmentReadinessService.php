<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\User;

class AssessmentReadinessService
{
    /**
     * Verify whether an assessment has all required evidence, attendance,
     * and explicit unit findings before it can be finalized.
     */
    public function checkAssessmentReadiness(Assessment $assessment): array
    {
        $errors = [];
        $missingUnits = [];
        $missingEvidence = [];

        // 1. APL status must be approved
        if ($assessment->apl_status !== 'APPROVED') {
            $errors[] = 'Permohonan APL belum disetujui.';
        }

        // 2. FR.AK.01 agreement must be recorded
        if (! $assessment->ak01_agreed_at && ! $assessment->esign_consent_at) {
            $errors[] = 'Persetujuan asesmen (FR.AK.01) belum disetujui oleh asesi.';
        }

        // 3. Attendance check (BK is an explicit finding, not an absence)
        $hasAttendance = $assessment->schedule
            && \App\Models\AttendanceEvent::where('schedule_id', $assessment->schedule_id)
                ->where('user_id', $assessment->asesi_id)
                ->where('event_type', 'asesi_hadir')
                ->exists();

        if (! $hasAttendance && ! $assessment->registered_at && ! $assessment->ak01_agreed_at) {
            $errors[] = 'Kehadiran asesi belum terverifikasi di Tempat Uji Kompetensi (TUK).';
        }

        // 4. Scheme competency units check
        $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
        $units = $scheme ? $scheme->competencyUnits : collect();

        $ak02Items = AssessmentAk02Item::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('competency_unit_id');

        foreach ($units as $unit) {
            $item = $ak02Items->get($unit->id);
            if (! $item || $item->is_competent === null) {
                $missingUnits[] = [
                    'id' => $unit->id,
                    'code' => $unit->code ?? $unit->kode_unit,
                    'title' => $unit->title ?? $unit->judul_unit,
                ];
            }
        }

        if (! empty($missingUnits)) {
            $errors[] = 'Terdapat ' . count($missingUnits) . ' unit kompetensi yang belum memiliki rekaman temuan asesmen (K/BK) eksplisit.';
        }

        // 5. Evidence check: CBT alone is never sufficient without practical observation
        $iaResponses = $assessment->iaResponses->keyBy('instrument_code');
        $hasDemonstration = $iaResponses->has('IA01') || $iaResponses->has('IA01B') || $iaResponses->has('IA02');

        if (! $hasDemonstration) {
            $missingEvidence[] = 'FR.IA.01/IA.02 (Bukti Observasi / Demonstrasi Praktik)';
            $errors[] = 'Bukti observasi unjuk kerja praktik belum direkam. Skor CBT tertulis tanpa observasi tidak dapat menetapkan kompeten.';
        }

        return [
            'is_ready' => empty($errors),
            'missing_units' => $missingUnits,
            'missing_evidence' => $missingEvidence,
            'errors' => $errors,
        ];
    }

    /**
     * Validate that the actor is the authorized assigned assessor for this assessment.
     */
    public function validateAssessorAuthorization(Assessment $assessment, User $user): bool
    {
        if ($assessment->assessor_id && (int) $assessment->assessor_id === (int) $user->id) {
            return true;
        }

        if ($assessment->schedule && $assessment->schedule->assessors()->where('users.id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Compute explicit final recommendation server-side from verified unit findings.
     * If ANY unit is not competent, the overall recommendation cannot be KOMPETEN.
     */
    public function computeFinalRecommendation(Assessment $assessment, array $unitsInput): array
    {
        $allCompetent = true;
        $processedUnits = [];

        foreach ($unitsInput as $unitId => $data) {
            $isComp = ! empty($data['is_competent']) && in_array(strtoupper((string) $data['is_competent']), ['1', 'TRUE', 'K', 'KOMPETEN'], true);
            if (! $isComp) {
                $allCompetent = false;
            }

            $processedUnits[$unitId] = [
                'is_competent' => $isComp,
                'finding_notes' => $data['finding_notes'] ?? $data['notes'] ?? null,
                'methods' => [
                    'observasi_demonstrasi' => ! empty($data['observasi_demonstrasi']),
                    'portofolio' => ! empty($data['portofolio']),
                    'pihak_ketiga' => ! empty($data['pihak_ketiga']),
                    'lisan' => ! empty($data['lisan']),
                    'tulis' => ! empty($data['tulis']),
                    'proyek' => ! empty($data['proyek']),
                    'lainnya' => ! empty($data['lainnya']),
                ],
            ];
        }

        return [
            'final_result' => $allCompetent ? 'KOMPETEN' : 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => $allCompetent ? 'K' : 'BK',
            'all_competent' => $allCompetent,
            'units' => $processedUnits,
        ];
    }
}
