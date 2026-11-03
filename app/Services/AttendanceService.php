<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AttendanceEvent;
use App\Models\AuditEvent;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class AttendanceService
{
    public function __construct(
        protected ScheduleConsentService $consentService
    ) {}

    /**
     * Record asesi entering exam at the local schedule window (idempotent, genuine event).
     */
    public function recordAsesiEnterExam(Assessment $assessment, ?Request $request = null): AttendanceEvent
    {
        $schedule = $assessment->schedule;
        if (! $schedule) {
            throw new RuntimeException('Jadwal asesmen tidak ditemukan.');
        }

        // 1. Eligibility gate
        if ($assessment->apl_status !== 'APPROVED') {
            throw new RuntimeException('Kehadiran tidak dapat dicatat: Permohonan APL belum disetujui.');
        }

        // 2. Agreement gate (AK-01)
        if ($assessment->ak01_agreed_at === null) {
            throw new RuntimeException('Kehadiran tidak dapat dicatat: Persetujuan FR.AK.01 belum ditandatangani.');
        }

        // 3. Active consent gate
        $asesi = $assessment->asesi;
        if (! $asesi) {
            throw new RuntimeException('Data asesi tidak ditemukan.');
        }

        $hasConsent = $this->consentService->hasActiveConsent($schedule, $asesi, 'join')
            || $this->consentService->hasActiveConsent($schedule, $asesi, 'exam_entry')
            || $this->consentService->hasActiveConsent($schedule, $asesi, 'ak01_agreement');

        if (! $hasConsent) {
            throw new RuntimeException('Kehadiran tidak dapat dicatat: Persetujuan integritas/consent jadwal belum diberikan atau telah dicabut.');
        }

        // 4. Time window / date validation (local schedule date)
        $today = Carbon::now()->toDateString();
        $startDate = $schedule->start_date ? Carbon::parse($schedule->start_date)->toDateString() : ($schedule->date ? Carbon::parse($schedule->date)->toDateString() : $today);
        $endDate = $schedule->end_date ? Carbon::parse($schedule->end_date)->toDateString() : $startDate;

        if ($today < $startDate) {
            throw new RuntimeException("Sesi asesmen belum dimulai. Jadwal dibuka pada tanggal {$startDate}.");
        }

        if ($today > $endDate) {
            throw new RuntimeException("Sesi asesmen telah berakhir pada tanggal {$endDate}.");
        }

        // 5. Idempotent attendance check (thread-safe transaction)
        return \Illuminate\Support\Facades\DB::transaction(function () use ($schedule, $assessment, $asesi, $request) {
            $existing = AttendanceEvent::where('schedule_id', $schedule->id)
                ->where('user_id', $asesi->id)
                ->where('event_type', 'asesi_enter_exam')
                ->where('is_reversed', false)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $now = now();

            $event = AttendanceEvent::create([
                'schedule_id' => $schedule->id,
                'user_id' => $asesi->id,
                'session_code' => $schedule->code,
                'event_type' => 'asesi_enter_exam',
                'source_record' => "assessment:{$assessment->id}",
                'occurred_at' => $now,
                'recorded_by' => auth()->id() ?? $asesi->id,
                'is_reversed' => false,
            ]);

            $assessment->update(['attendance_at' => $now]);

            AuditEvent::record(
                action: 'attendance.recorded',
                subject: $schedule,
                reason: "Asesi {$asesi->name} resmi hadir pada sesi asesmen (Ruang Uji).",
                details: [
                    'assessment_id' => $assessment->id,
                    'user_id' => $asesi->id,
                    'event_type' => 'asesi_enter_exam',
                    'occurred_at' => $now->toDateTimeString(),
                ],
                userId: auth()->id() ?? $asesi->id
            );

            return $event;
        });
    }

    /**
     * Record assessor active attendance on genuine session start or instrument save.
     */
    public function recordAssessorActive(Schedule $schedule, User $assessor, string $sourceRecord): AttendanceEvent
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($schedule, $assessor, $sourceRecord) {
            $existing = AttendanceEvent::where('schedule_id', $schedule->id)
                ->where('user_id', $assessor->id)
                ->where('event_type', 'assessor_session_active')
                ->where('is_reversed', false)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $now = now();

            $event = AttendanceEvent::create([
                'schedule_id' => $schedule->id,
                'user_id' => $assessor->id,
                'session_code' => $schedule->code,
                'event_type' => 'assessor_session_active',
                'source_record' => $sourceRecord,
                'occurred_at' => $now,
                'recorded_by' => $assessor->id,
                'is_reversed' => false,
            ]);

            AuditEvent::record(
                action: 'attendance.assessor_active',
                subject: $schedule,
                reason: "Asesor {$assessor->name} aktif mengawasi sesi asesmen ({$sourceRecord}).",
                details: [
                    'schedule_id' => $schedule->id,
                    'user_id' => $assessor->id,
                    'source_record' => $sourceRecord,
                ],
                userId: $assessor->id
            );

            return $event;
        });
    }

    /**
     * Admin reverses an erroneous attendance event with mandatory reason.
     */
    public function reverseAttendance(AttendanceEvent $event, User $admin, string $reason): void
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pembatalan kehadiran wajib disertakan.');
        }

        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('Hanya admin yang memiliki kewenangan membatalkan catatan kehadiran.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($event, $admin, $reason) {
            $event->update([
                'is_reversed' => true,
                'reversed_at' => now(),
                'reversed_by' => $admin->id,
                'reversal_reason' => $reason,
            ]);

            if (str_starts_with($event->source_record ?? '', 'assessment:')) {
                $assessmentId = (int) str_replace('assessment:', '', $event->source_record);
                Assessment::where('id', $assessmentId)->update(['attendance_at' => null]);
            }

            AuditEvent::record(
                action: 'attendance.reversed',
                subject: $event->schedule,
                reason: "Catatan kehadiran untuk user #{$event->user_id} dibatalkan oleh Admin {$admin->name}: {$reason}",
                details: [
                    'event_id' => $event->id,
                    'user_id' => $event->user_id,
                    'reversed_by' => $admin->id,
                    'reason' => $reason,
                ],
                userId: $admin->id
            );
        });
    }
}
