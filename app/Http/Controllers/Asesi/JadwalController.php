<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function dashboard(): View
    {
        $userId = auth()->id();
        $activeAssessmentsCount = Assessment::where('asesi_id', $userId)
            ->where('status', 'IN_PROGRESS')
            ->count();

        $assessments = Assessment::with(['schedule.scheme'])
            ->where('asesi_id', $userId)
            ->latest('registered_at')
            ->get();

        return view('asesi.dashboard', compact('activeAssessmentsCount', 'assessments'));
    }

    public function index(): View
    {
        $assessments = Assessment::with(['schedule.scheme', 'assessor'])
            ->where('asesi_id', auth()->id())
            ->latest('registered_at')
            ->get();

        return view('asesi.jadwal.index', compact('assessments'));
    }

    public function join(Request $request): RedirectResponse
    {
        if (! auth()->user()->isProfileComplete()) {
            return redirect()->route('asesi.profile.edit')
                ->with('error', 'Silakan lengkapi biodata dan tanda tangan profil Anda terlebih dahulu sebelum bergabung dengan jadwal asesmen.');
        }

        $request->validate([
            'code' => 'required|string',
        ]);

        $schedule = Schedule::where('code', $request->code)->first();

        if (! $schedule || $schedule->status !== 'ACTIVE' || $schedule->is_closed) {
            return back()->with('error', 'Kode jadwal tidak ditemukan atau jadwal sudah tidak aktif/ditutup.');
        }

        $userId = auth()->id();

        $assessment = DB::transaction(function () use ($schedule, $userId) {
            return Assessment::firstOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'asesi_id' => $userId,
                ],
                [
                    'apl_status' => 'DRAFT',
                    'admin_status' => 'APPROVED',
                    'registered_at' => now(),
                    'last_active_step' => 'apl',
                ]
            );
        });

        if (! $assessment->wasRecentlyCreated) {
            if ($assessment->last_active_step === 'ruang-uji' && $assessment->apl_status === 'APPROVED') {
                return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                    ->with('info', 'Anda sudah terdaftar pada jadwal ini dan telah disetujui untuk Ruang Uji.');
            }

            return redirect()->route('asesi.assessment.apl', $assessment)
                ->with('info', 'Anda sudah terdaftar pada jadwal ini.');
        }

        return redirect()->route('asesi.assessment.apl', $assessment)
            ->with('success', 'Berhasil bergabung dengan jadwal asesmen.');
    }
}
