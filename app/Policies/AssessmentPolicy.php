<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Assessment $assessment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAsesi()) {
            return $assessment->asesi_id === $user->id;
        }

        if ($user->isAsesor()) {
            if ($assessment->assessor_id === $user->id) {
                return true;
            }

            if ($assessment->schedule && $assessment->schedule->assessors()->where('users.id', $user->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function update(User $user, Assessment $assessment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAsesor()) {
            // Strictly the assigned assessor only; other assessors in same schedule cannot edit
            return $assessment->assessor_id === $user->id;
        }

        return false;
    }

    public function viewAnswerKey(User $user, ?Assessment $assessment = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAsesor()) {
            if (! $assessment) {
                return true;
            }

            return $assessment->assessor_id === $user->id
                || ($assessment->schedule && $assessment->schedule->assessors()->where('users.id', $user->id)->exists());
        }

        // Asesi is strictly prohibited from viewing answer keys
        return false;
    }
}
