<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;

        $schedules = AssessmentSchedule::with(['scheme', 'tuk', 'event', 'applications.asesi.user'])
            ->whereHas('asesors', function ($query) use ($request) {
                $query->where('asesor_id', $request->user()->id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest('assessment_date')
            ->paginate(15);

        return view('asesor.schedules.index', [
            'schedules' => $schedules,
            'asesor' => $asesor,
        ]);
    }

    public function show(AssessmentSchedule $schedule): View
    {
        $schedule->load([
            'scheme.units.elements.criterias',
            'tuk',
            'event',
            'asesors.user',
            'applications.asesi.user',
            'applications.documents',
            'applications.apl02Assessment',
            'applications.ak01Agreement',
            'applications.ak02Assessment',
            'applications.ak03Feedback',
        ]);

        return view('asesor.schedules.show', [
            'schedule' => $schedule,
        ]);
    }
}
