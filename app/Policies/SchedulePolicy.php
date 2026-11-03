<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Schedule $schedule): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAsesor()) {
            return $schedule->assessors()->where('users.id', $user->id)->exists();
        }

        if ($user->isAsesi()) {
            return $schedule->assessments()->where('asesi_id', $user->id)->exists()
                || in_array($schedule->status, ['ACTIVE', 'OPEN'], true);
        }

        return false;
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin();
    }
}
