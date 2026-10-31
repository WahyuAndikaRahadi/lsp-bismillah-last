<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $asesorCount = User::where('role', UserRole::Asesor)->count();
        $asesiCount = User::where('role', UserRole::Asesi)->count();

        // Skema aktif
        $schemes = CertificationScheme::where('is_active', true)
            ->withCount('units')
            ->take(6)
            ->get();

        // TUK aktif
        $tuks = Tuk::where('is_active', true)->take(6)->get();

        // Jadwal Asesmen aktif / buka pendaftaran
        $upcomingSchedules = AssessmentSchedule::whereIn('status', [ScheduleStatus::Pendaftaran, ScheduleStatus::Direncanakan])
            ->where('tgl_asesmen', '>=', now()->toDateString())
            ->with(['scheme', 'tuk', 'event'])
            ->orderBy('tgl_asesmen')
            ->take(6)
            ->get();

        return view('public.home', compact('asesorCount', 'asesiCount', 'schemes', 'tuks', 'upcomingSchedules'));
    }
}
