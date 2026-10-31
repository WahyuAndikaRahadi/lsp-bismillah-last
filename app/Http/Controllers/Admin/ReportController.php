<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsesorProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Tuk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function general(): View
    {
        $schemes = CertificationScheme::withCount('applications')->get();
        $totalApplications = AssessmentApplication::count();
        $competentCount = AssessmentApplication::where('status', 'diterima')->count();

        return view('admin.reports.general', [
            'schemes' => $schemes,
            'totalApplications' => $totalApplications,
            'competentCount' => $competentCount,
        ]);
    }

    public function monthly(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));

        $schedules = AssessmentSchedule::with(['scheme', 'tuk'])
            ->whereMonth('tgl_asesmen', $selectedMonth)
            ->whereYear('tgl_asesmen', $selectedYear)
            ->get();

        return view('admin.reports.monthly', [
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'schedules' => $schedules,
        ]);
    }

    public function annual(Request $request): View
    {
        $year = $request->input('year', now()->format('Y'));

        $schemes = CertificationScheme::withCount(['applications' => function ($q) use ($year) {
            $q->whereYear('created_at', $year);
        }])->get();

        return view('admin.reports.annual', [
            'year' => $year,
            'schemes' => $schemes,
        ]);
    }

    public function asesorPerformance(): View
    {
        $asesors = AsesorProfile::with(['user', 'schemes'])->get();

        return view('admin.reports.asesor_performance', [
            'asesors' => $asesors,
        ]);
    }

    public function tukPerformance(): View
    {
        $tuks = Tuk::all();

        return view('admin.reports.tuk_performance', [
            'tuks' => $tuks,
        ]);
    }

    public function certificates(): View
    {
        $certificates = AssessmentApplication::with(['asesi.user', 'scheme'])
            ->whereNotNull('nomor_sertifikat_bnsp')
            ->latest('tgl_terbit_sertifikat')
            ->paginate(20);

        return view('admin.reports.certificates', [
            'certificates' => $certificates,
        ]);
    }
}
