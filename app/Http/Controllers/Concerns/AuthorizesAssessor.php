<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Assessment;
use App\Models\Schedule;

trait AuthorizesAssessor
{
    /**
     * Ensure the authenticated user is an ASESOR assigned to the given schedule.
     */
    protected function authorizeAssessorSchedule(Schedule $schedule): void
    {
        $user = auth()->user();
        $roleStr = is_object($user?->role) ? $user->role->value : (string) $user?->role;
        $isAssessorOrAdmin = $user && ($user->isRole('asesor') || $user->isRole('admin') || in_array($roleStr, ['ASESOR', 'SUPER_ADMIN', 'ADMIN_LSP'], true));
        if (! $isAssessorOrAdmin) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->isRole('asesor') || $roleStr === 'ASESOR') {
            $isAssigned = $schedule->assessors()->where('users.id', $user->id)->exists();
            if (! $isAssigned) {
                abort(403, 'Anda tidak ditugaskan pada jadwal asesmen ini.');
            }
        }
    }

    /**
     * Ensure the authenticated user is an ASESOR assigned to the given assessment's schedule
     * and not assigned to another assessor.
     */
    protected function authorizeAssessorAssessment(Assessment $assessment): void
    {
        $user = auth()->user();
        $roleStr = is_object($user?->role) ? $user->role->value : (string) $user?->role;
        $isAssessorOrAdmin = $user && ($user->isRole('asesor') || $user->isRole('admin') || in_array($roleStr, ['ASESOR', 'SUPER_ADMIN', 'ADMIN_LSP'], true));
        if (! $isAssessorOrAdmin) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->isRole('asesor') || $roleStr === 'ASESOR') {
            $schedule = $assessment->schedule;
            if (! $schedule) {
                abort(404, 'Jadwal asesmen tidak ditemukan.');
            }

            $isDirectAssessor = (int) $assessment->assessor_id === (int) $user->id;
            $isScheduleAssessor = $schedule->assessors()->where('users.id', $user->id)->exists();

            if (! $isDirectAssessor && ! $isScheduleAssessor) {
                abort(403, 'Anda tidak ditugaskan pada jadwal asesmen ini.');
            }

            if ($assessment->assessor_id && (int) $assessment->assessor_id !== (int) $user->id) {
                abort(403, 'Asesi ini telah ditugaskan kepada asesor lain.');
            }
        }
    }
}
