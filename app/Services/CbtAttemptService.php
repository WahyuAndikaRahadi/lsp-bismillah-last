<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AssessmentIaResponse;
use App\Models\AuditEvent;
use App\Models\QuestionBank;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class CbtAttemptService
{
    /**
     * Start or resume a CBT attempt with server-authoritative timer.
     */
    public function startOrResumeAttempt(Assessment $assessment): array
    {
        $user = auth()->user();
        if ($user && $user->id !== $assessment->asesi_id && ! $user->isAdmin()) {
            throw new InvalidArgumentException('Akses tidak terotorisasi untuk ujian CBT asesi lain.');
        }

        $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
        if (! $scheme) {
            throw new RuntimeException('Skema sertifikasi tidak ditemukan pada asesmen ini.');
        }

        return DB::transaction(function () use ($assessment, $scheme, $user) {
            $response = AssessmentIaResponse::where('assessment_id', $assessment->id)
                ->where('instrument_code', 'IA05')
                ->lockForUpdate()
                ->first();

            $baseDuration = 3600; // 60 menit baku

            // Periksa akomodasi penyesuaian waktu pada AK-07
            $ak07TimeAdjustment = AssessmentAk07Item::where('assessment_id', $assessment->id)
                ->where('decision', 'ya')
                ->where(function ($q) {
                    $q->where('category_number', 4)
                        ->orWhere('adjustment_category', 'like', '%fleksibel%')
                        ->orWhere('adjustment_category', 'like', '%keletihan%');
                })
                ->first();

            if ($ak07TimeAdjustment) {
                // Tambahan waktu 50% (30 menit) untuk penyesuaian wajar
                $baseDuration = 5400;
            }

            if (! $response) {
                $now = now();
                $response = AssessmentIaResponse::create([
                    'assessment_id' => $assessment->id,
                    'instrument_code' => 'IA05',
                    'started_at' => $now,
                    'duration_seconds' => $baseDuration,
                    'time_remaining_seconds' => $baseDuration,
                    'attempt_version' => 1,
                    'data_json' => [],
                    'is_expired' => false,
                ]);

                AuditEvent::record(
                    action: 'cbt.attempt_started',
                    subject: $assessment,
                    reason: "Ujian CBT (FR.IA.05) dimulai oleh asesi {$assessment->asesi?->name}.",
                    details: [
                        'assessment_id' => $assessment->id,
                        'duration_seconds' => $baseDuration,
                        'has_time_accommodation' => (bool) $ak07TimeAdjustment,
                    ],
                    userId: $user?->id ?? $assessment->asesi_id
                );
            }

            // Hitung sisa waktu yang mengikat (Server Authoritative)
            $now = Carbon::now();
            if (! $response->started_at) {
                $response->update(['started_at' => $now]);
                $response->started_at = $now;
            }
            $startedAt = Carbon::parse($response->started_at);
            $elapsed = (int) abs($now->diffInSeconds($startedAt));

            $remainingSeconds = max(0, (int) ($response->duration_seconds - $elapsed));

            if ($remainingSeconds <= 0 && ! $response->isSubmitted()) {
                $response->update([
                    'is_expired' => true,
                    'time_remaining_seconds' => 0,
                ]);
            } else {
                $response->update([
                    'time_remaining_seconds' => $remainingSeconds,
                ]);
            }

            // Ambil bank soal dengan strict concealment of correct_answer
            $questions = QuestionBank::where('scheme_id', $scheme->id)
                ->select([
                    'id',
                    'scheme_id',
                    'competency_unit_id',
                    'question_text',
                    'question_image',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                ])
                ->get();

            // Sembunyikan correct_answer secara definitif
            $questions->makeHidden(['correct_answer']);

            return [
                'response_id' => $response->id,
                'assessment_id' => $assessment->id,
                'started_at' => $response->started_at ? Carbon::parse($response->started_at)->toIso8601String() : $now->toIso8601String(),
                'duration_seconds' => $response->duration_seconds,
                'remaining_seconds' => $remainingSeconds,
                'is_expired' => $remainingSeconds <= 0,
                'is_completed' => $response->isSubmitted(),
                'score' => $response->score,
                'draft_answers' => $response->data_json ?? [],
                'questions' => $questions,
                'total_questions' => $questions->count(),
            ];
        });
    }

    /**
     * Save draft answers with optimistic versioning and idempotency.
     */
    public function saveDraft(Assessment $assessment, array $answers, int $version = 1): array
    {
        $user = auth()->user();
        if ($user && $user->id !== $assessment->asesi_id && ! $user->isAdmin()) {
            throw new InvalidArgumentException('Akses tidak terotorisasi.');
        }

        return DB::transaction(function () use ($assessment, $answers, $version) {
            $response = AssessmentIaResponse::where('assessment_id', $assessment->id)
                ->where('instrument_code', 'IA05')
                ->lockForUpdate()
                ->first();

            if (! $response) {
                $this->startOrResumeAttempt($assessment);
                $response = AssessmentIaResponse::where('assessment_id', $assessment->id)
                    ->where('instrument_code', 'IA05')
                    ->lockForUpdate()
                    ->first();
            }

            if ($response->isSubmitted()) {
                throw new RuntimeException('Ujian telah selesai dikirim dan tidak dapat diubah lagi.');
            }

            // Check timer expiry
            $startedAt = Carbon::parse($response->started_at);
            $elapsed = (int) abs(Carbon::now()->diffInSeconds($startedAt));
            $remaining = max(0, (int) ($response->duration_seconds - $elapsed));

            if ($remaining <= 0) {
                $response->update(['is_expired' => true, 'time_remaining_seconds' => 0]);
                throw new RuntimeException('Waktu pengerjaan ujian telah habis.');
            }

            $currentAnswers = is_array($response->data_json) ? $response->data_json : [];
            foreach ($answers as $qId => $ans) {
                $currentAnswers[(string) $qId] = $ans;
            }

            $response->update([
                'data_json' => $currentAnswers,
                'attempt_version' => $response->attempt_version + 1,
                'time_remaining_seconds' => $remaining,
            ]);

            return [
                'status' => 'success',
                'saved_at' => now()->toIso8601String(),
                'remaining_seconds' => $remaining,
                'count' => count($currentAnswers),
            ];
        });
    }

    /**
     * Submit final CBT examination, compute score without auto-determining K/BK.
     */
    public function submitFinal(Assessment $assessment, array $answers, ?string $signature = null): AssessmentIaResponse
    {
        $user = auth()->user();
        if ($user && $user->id !== $assessment->asesi_id && ! $user->isAdmin()) {
            throw new InvalidArgumentException('Akses tidak terotorisasi.');
        }

        $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;

        return DB::transaction(function () use ($assessment, $scheme, $answers, $signature, $user) {
            $response = AssessmentIaResponse::where('assessment_id', $assessment->id)
                ->where('instrument_code', 'IA05')
                ->lockForUpdate()
                ->first();

            if ($response && $response->isSubmitted()) {
                // Idempotent: return existing submitted record without recalculating
                return $response;
            }

            $questions = QuestionBank::where('scheme_id', $scheme->id)->get();
            $totalQuestions = $questions->count();
            $correctCount = 0;

            foreach ($questions as $q) {
                $answerGiven = strtoupper(trim((string) ($answers[$q->id] ?? '')));
                $expectedKey = strtoupper(trim((string) $q->correct_answer));

                if ($answerGiven !== '' && $answerGiven === $expectedKey) {
                    $correctCount++;
                }
            }

            $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0.00;
            $asesiSig = $signature ?? $assessment->asesi_signature ?? $user?->profile?->signature_data;
            $now = now();

            if (! $response) {
                $response = new AssessmentIaResponse([
                    'assessment_id' => $assessment->id,
                    'instrument_code' => 'IA05',
                    'started_at' => $now,
                    'duration_seconds' => 3600,
                ]);
            }

            $response->data_json = $answers;
            $response->score = $score;
            $response->asesi_signature = $asesiSig;
            $response->asesi_signed_at = $now;
            $response->submitted_at = $now;
            $response->time_remaining_seconds = 0;
            $response->save();

            $assessment->update(['last_active_step' => 'ruang-uji']);

            AuditEvent::record(
                action: 'cbt.submitted',
                subject: $assessment,
                reason: "Ujian CBT FR.IA.05 diselesaikan oleh asesi dengan skor {$score} ({$correctCount}/{$totalQuestions} benar).",
                details: [
                    'assessment_id' => $assessment->id,
                    'total_questions' => $totalQuestions,
                    'correct_count' => $correctCount,
                    'score' => $score,
                ],
                userId: $user?->id ?? $assessment->asesi_id
            );

            return $response;
        });
    }
}
