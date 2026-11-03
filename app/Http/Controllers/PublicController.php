<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleStatus;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\LspProfile;
use App\Models\Tuk;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $lspProfile = LspProfile::current();

        // Skema sertifikasi resmi aktif
        $schemes = CertificationScheme::where('is_active', true)
            ->withCount('units')
            ->orderBy('kode_skema')
            ->take(6)
            ->get();

        // Tempat Uji Kompetensi (TUK) resmi terverifikasi
        $tuks = Tuk::where('is_active', true)
            ->orderBy('nama_tuk')
            ->take(6)
            ->get();

        // Jadwal Uji Kompetensi aktif (bukan sample data)
        $upcomingSchedules = AssessmentSchedule::whereIn('status', [ScheduleStatus::Pendaftaran, ScheduleStatus::Direncanakan])
            ->where('tgl_asesmen', '>=', now()->toDateString())
            ->with(['scheme', 'tuk', 'event'])
            ->orderBy('tgl_asesmen')
            ->take(6)
            ->get();

        return view('public.home', compact('lspProfile', 'schemes', 'tuks', 'upcomingSchedules'));
    }
}
