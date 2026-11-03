<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificatePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Certificate $certificate): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $assessment = $certificate->assessment;
        if (! $assessment) {
            return false;
        }

        if ($user->isAsesi()) {
            return $assessment->asesi_id === $user->id;
        }

        if ($user->isAsesor()) {
            return $assessment->assessor_id === $user->id;
        }

        return false;
    }
}
