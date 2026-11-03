<?php

namespace App\Policies;

use App\Models\AssessmentAppeal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppealPolicy
{
    use HandlesAuthorization;

    public function view(User $user, AssessmentAppeal $appeal): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $assessment = $appeal->assessment;
        if (! $assessment) {
            return false;
        }

        // Asesi hanya berhak melihat pengajuan banding miliknya sendiri (kerahasiaan)
        if ($user->isAsesi()) {
            return $assessment->asesi_id === $user->id;
        }

        // Investigator independen yang ditugaskan dapat melihat berkas
        if ($appeal->investigations()->where('investigator_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    public function investigate(User $user, AssessmentAppeal $appeal): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $appeal->investigations()->where('investigator_id', $user->id)->exists();
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
