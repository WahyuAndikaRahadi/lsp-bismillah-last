<?php

namespace App\Services;

use App\Models\AttendanceEvent;
use App\Models\Schedule;
use App\Models\ScheduleMinutes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleMinutesService
{
    /**
     * Compute actual real-world facts for a schedule without any mutation.
     */
    public function aggregateScheduleFacts(Schedule $schedule): array
    {
        $assessments = $schedule->assessments()
            ->with(['asesi.profile', 'assessor'])
            ->get();

        $enrolledCount = $assessments->count();

        // Check active attendance events
        $presentAsesiIds = AttendanceEvent::where('schedule_id', $schedule->id)
            ->where('event_type', 'asesi_hadir')
            ->where(function ($q) {
                $q->whereNull('is_reversed')
                  ->orWhere('is_reversed', false);
            })
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $presentCount = 0;
        $absentCount = 0;
        $competentCount = 0;
        $notCompetentCount = 0;
        $pendingCount = 0;

        $rosterSummary = [];

        foreach ($assessments as $a) {
            $isPresent = in_array($a->asesi_id, $presentAsesiIds, true);
            if ($isPresent) {
                $presentCount++;
            } else {
                $absentCount++;
            }

            $finalResult = strtoupper((string) ($a->final_result ?? ''));
            if (in_array($finalResult, ['KOMPETEN', 'K'], true)) {
                $competentCount++;
            } elseif (in_array($finalResult, ['BELUM_KOMPETEN', 'BK'], true)) {
                $notCompetentCount++;
            } else {
                // Not assessed yet; must NOT be counted as BK
                $pendingCount++;
            }

            $rosterSummary[] = [
                'assessment_id' => $a->id,
                'asesi_id' => $a->asesi_id,
                'name' => $a->asesi?->name,
                'is_present' => $isPresent,
                'final_result' => $a->final_result,
                'rekomendasi_asesor' => $a->rekomendasi_asesor,
                'follow_up_action' => $a->follow_up_action,
                'notes' => $a->final_notes,
            ];
        }

        // Exact invariant check: Enrolled must equal Present + Absent
        assert($enrolledCount === ($presentCount + $absentCount));

        return [
            'enrolled_count' => $enrolledCount,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'competent_count' => $competentCount,
            'not_competent_count' => $notCompetentCount,
            'pending_count' => $pendingCount,
            'roster' => $rosterSummary,
        ];
    }

    /**
     * Validate minutes readiness before finalizing.
     */
    public function validateMinutesReadiness(Schedule $schedule, array $facts, ?int $coordinatorId, ?string $startTime, ?string $endTime, bool $isFinal = false): array
    {
        $errors = [];

        // 1. Explicit coordinator identity is required
        if (empty($coordinatorId)) {
            $errors[] = 'Koordinator TUK/Asesmen wajib ditunjuk secara eksplisit dari orang nyata yang berwenang.';
        } else {
            $coord = User::find($coordinatorId);
            if (! $coord) {
                $errors[] = 'Identitas koordinator tidak valid atau tidak terdaftar.';
            }
        }

        // 2. Actual start and end time check
        if (empty($startTime) || empty($endTime)) {
            $errors[] = 'Waktu mulai dan selesai pelaksanaan aktual wajib diisi.';
        } elseif (strtotime($startTime) >= strtotime($endTime)) {
            $errors[] = 'Waktu mulai pelaksanaan aktual harus lebih awal dari waktu selesai.';
        }

        // 3. Finalization completeness check
        if ($isFinal) {
            if ($facts['pending_count'] > 0) {
                $errors[] = "Berita Acara tidak dapat difinalisasi karena masih ada {$facts['pending_count']} asesi yang belum memiliki rekomendasi hasil asesmen (pending).";
            }
        }

        return [
            'is_ready' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Save Schedule Minutes (Berita Acara) record.
     */
    public function recordMinutes(Schedule $schedule, array $data, User $actor): ScheduleMinutes
    {
        $facts = $this->aggregateScheduleFacts($schedule);

        $coordinatorId = $data['coordinator_id'] ?? null;
        $startTime = $data['actual_start_time'] ?? null;
        $endTime = $data['actual_end_time'] ?? null;
        $isFinal = ($data['status'] ?? 'DRAFT') === 'FINAL';

        $readiness = $this->validateMinutesReadiness($schedule, $facts, $coordinatorId, $startTime, $endTime, $isFinal);
        if (! $readiness['is_ready']) {
            throw ValidationException::withMessages([
                'minutes' => $readiness['errors'],
            ]);
        }

        $coordinator = User::find($coordinatorId);

        return DB::transaction(function () use ($schedule, $data, $facts, $coordinator, $isFinal, $actor) {
            $existing = ScheduleMinutes::where('schedule_id', $schedule->id)
                ->latest('revision')
                ->lockForUpdate()
                ->first();

            if ($existing && ! empty($data['lock_version'])) {
                if ((int) $data['lock_version'] !== (int) ($existing->lock_version ?? 1)) {
                    abort(409, 'Konflik versi: Berita Acara telah diperbarui pada sesi lain. Muat ulang halaman.');
                }
            }

            $revision = $existing ? ($existing->revision + 1) : 1;
            $lockVersion = $existing ? ($existing->lock_version + 1) : 1;

            $minutes = ScheduleMinutes::create([
                'schedule_id' => $schedule->id,
                'document_code' => $data['document_code'] ?? 'FR-SOP-04.4',
                'revision' => $revision,
                'actual_date' => $data['actual_date'] ?? $schedule->date,
                'actual_start_time' => $data['actual_start_time'],
                'actual_end_time' => $data['actual_end_time'],
                'location' => $data['location'] ?? $schedule->tuk_name,
                'enrolled_count' => $facts['enrolled_count'],
                'present_count' => $facts['present_count'],
                'absent_count' => $facts['absent_count'],
                'competent_count' => $facts['competent_count'],
                'not_competent_count' => $facts['not_competent_count'],
                'pending_count' => $facts['pending_count'],
                'roster_summary_json' => $facts['roster'],
                'coordinator_id' => $coordinator?->id,
                'coordinator_name' => $coordinator?->name ?? $data['coordinator_name'] ?? null,
                'coordinator_signature' => $data['coordinator_signature'] ?? null,
                'coordinator_signed_at' => ! empty($data['coordinator_signature']) ? now() : null,
                'lead_assessor_id' => $data['lead_assessor_id'] ?? $schedule->assessors()->first()?->id,
                'lead_assessor_signature' => $data['lead_assessor_signature'] ?? null,
                'lead_assessor_signed_at' => ! empty($data['lead_assessor_signature']) ? now() : null,
                'notes' => $data['notes'] ?? null,
                'status' => $isFinal ? 'FINAL' : 'DRAFT',
                'lock_version' => $lockVersion,
            ]);

            \App\Models\AuditEvent::record(
                action: 'schedule.minutes_recorded',
                subject: $minutes,
                reason: "Berita Acara asesmen revisi {$revision} disimpan dengan status {$minutes->status}",
                details: [
                    'schedule_id' => $schedule->id,
                    'revision' => $revision,
                    'status' => $minutes->status,
                    'enrolled' => $facts['enrolled_count'],
                    'present' => $facts['present_count'],
                    'competent' => $facts['competent_count'],
                ],
                userId: $actor->id
            );

            return $minutes;
        });
    }
}
