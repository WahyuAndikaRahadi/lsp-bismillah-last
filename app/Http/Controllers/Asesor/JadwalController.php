<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use Illuminate\View\View;

class JadwalController extends Controller
{
    use AuthorizesAssessor;

    public function index(): View
    {
        $user = auth()->user();
        $assignedScheduleIds = $user->assignedSchedules()->pluck('t_schedules.id');

        $schedules = $user->assignedSchedules()
            ->with(['scheme', 'assessments'])
            ->latest('date')
            ->get();

        $jadwals = $schedules;

        // Pending APL reviews across all assigned schedules
        $pendingAplAssessments = Assessment::with(['asesi.profile', 'schedule.scheme'])
            ->whereIn('schedule_id', $assignedScheduleIds)
            ->where('apl_status', 'SUBMITTED')
            ->latest('registered_at')
            ->get();

        // Candidates needing assessment/scoring (APL approved but not completed/scored)
        $pendingScoringAssessments = Assessment::with(['asesi.profile', 'schedule.scheme'])
            ->whereIn('schedule_id', $assignedScheduleIds)
            ->where('apl_status', 'APPROVED')
            ->whereNull('final_result')
            ->where('status', '!=', 'COMPLETED')
            ->latest('registered_at')
            ->take(6)
            ->get();

        return view('asesor.jadwal.index', compact(
            'schedules',
            'jadwals',
            'pendingAplAssessments',
            'pendingScoringAssessments'
        ));
    }

    public function asesiList(Schedule $schedule): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $schedule->load(['scheme', 'assessors']);
        $jadwal = $schedule;
        $assessments = $schedule->assessments()
            ->with(['asesi.profile', 'assessor', 'latestAppeal'])
            ->get();

        return view('asesor.jadwal.asesi', compact('schedule', 'jadwal', 'assessments'));
    }
}
