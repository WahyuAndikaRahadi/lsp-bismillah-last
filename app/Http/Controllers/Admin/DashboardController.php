<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_admin' => User::where('role', UserRole::Admin)->count(),
            'total_asesor' => User::where('role', UserRole::Asesor)->count(),
            'total_asesi' => User::where('role', UserRole::Asesi)->count(),
            'total_schemes' => \App\Models\CertificationScheme::count(),
            'total_schedules' => \App\Models\AssessmentSchedule::count(),
            'total_tuks' => \App\Models\Tuk::count(),
            'total_certificates' => \App\Models\AssessmentApplication::whereNotNull('nomor_sertifikat_bnsp')->count(),
            'total_applications' => \App\Models\AssessmentApplication::count(),
            'pending_verifications' => \App\Models\AssessmentApplication::where('status', \App\Enums\ApplicationStatus::Diajukan)->count(),
            'accepted_applications' => \App\Models\AssessmentApplication::where('status', \App\Enums\ApplicationStatus::Diterima)->count(),
            'revision_applications' => \App\Models\AssessmentApplication::where('status', \App\Enums\ApplicationStatus::Revisi)->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentApplications = \App\Models\AssessmentApplication::with(['asesi.user', 'scheme'])->latest()->take(5)->get();
        $recentSchedules = \App\Models\AssessmentSchedule::with(['scheme', 'tuk'])->latest('tgl_asesmen')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentApplications', 'recentSchedules'));
    }
}
