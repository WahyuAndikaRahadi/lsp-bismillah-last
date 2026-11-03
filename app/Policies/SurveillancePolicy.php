<?php

namespace App\Policies;

use App\Models\SurveillancePlan;
use App\Models\SurveillanceReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurveillancePolicy
{
    use HandlesAuthorization;

    public function view(User $user, SurveillancePlan $plan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $assessment = $plan->certificate?->assessment;
        if (! $assessment) {
            return false;
        }

        return $user->isAsesi() && $assessment->asesi_id === $user->id;
    }

    public function submitReport(User $user, SurveillancePlan $plan): bool
    {
        if (! $user->isAsesi()) {
            return false;
        }

        $certificate = $plan->certificate;
        if (! $certificate || $certificate->status !== 'ACTIVE') {
            return false;
        }

        return $certificate->assessment?->asesi_id === $user->id;
    }

    public function evaluate(User $user, SurveillanceReport $report): bool
    {
        return $user->isAdmin();
    }
}
