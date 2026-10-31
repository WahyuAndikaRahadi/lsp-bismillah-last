<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSchedule;
use App\Models\InstrumentReview;
use App\Models\TukVerification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;

        $activeSchedulesCount = 0;
        $activeSchedules = collect();
        $tukVerificationsCount = 0;
        $instrumentReviewsCount = 0;
        $schemesCount = 0;

        if ($asesor) {
            $activeSchedulesCount = AssessmentSchedule::whereHas('asesors', function ($q) use ($request) {
                $q->where('asesor_id', $request->user()->id);
            })->count();

            $activeSchedules = AssessmentSchedule::with(['scheme', 'tuk', 'event'])
                ->whereHas('asesors', function ($q) use ($request) {
                    $q->where('asesor_id', $request->user()->id);
                })
                ->latest('tgl_asesmen')
                ->take(5)
                ->get();

            $tukVerificationsCount = TukVerification::where('asesor_id', $asesor->id)->count();
            $instrumentReviewsCount = InstrumentReview::where('asesor_id', $asesor->id)->count();
            $schemesCount = $asesor->schemes()->count();
        }

        $daysRemaining = 0;
        if ($asesor && $asesor->license_valid_until) {
            $daysRemaining = max(0, (int) now()->diffInDays($asesor->license_valid_until, false));
        }

        return view('asesor.dashboard', [
            'asesor' => $request->user(),
            'asesorProfile' => $asesor,
            'activeSchedulesCount' => $activeSchedulesCount,
            'activeSchedules' => $activeSchedules,
            'tukVerificationsCount' => $tukVerificationsCount,
            'instrumentReviewsCount' => $instrumentReviewsCount,
            'schemesCount' => $schemesCount,
            'daysRemaining' => $daysRemaining,
        ]);
    }
}
