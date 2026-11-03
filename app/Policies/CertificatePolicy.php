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

        return false;
    }

    public function download(User $user, Certificate $certificate): bool
    {
        if ($certificate->status !== 'ACTIVE') {
            return false;
        }

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

        return false;
    }

    public function upload(User $user): bool
    {
        return $user->isAdmin();
    }

    public function acknowledge(User $user, Certificate $certificate): bool
    {
        if ($certificate->status !== 'ACTIVE') {
            return false;
        }

        $assessment = $certificate->assessment;
        if (! $assessment) {
            return false;
        }

        return $user->isAsesi() && $assessment->asesi_id === $user->id;
    }
}
