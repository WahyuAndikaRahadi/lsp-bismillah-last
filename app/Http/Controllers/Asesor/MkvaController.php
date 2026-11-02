<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AsesorProfile;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\MkvaAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MkvaController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;

        $assignments = MkvaAssignment::with(['scheme', 'schedule', 'asesor.user'])
            ->when($asesor, function ($q) use ($asesor) {
                $q->where('asesor_id', $asesor->id);
            })
            ->latest('assignment_date')
            ->paginate(15);

        $schemes = CertificationScheme::where('is_active', true)->orderBy('nama_skema')->get();
        $schedules = AssessmentSchedule::latest('tgl_asesmen')->take(20)->get();

        return view('asesor.mkva.index', [
            'assignments' => $assignments,
            'schemes' => $schemes,
            'schedules' => $schedules,
            'asesor' => $asesor,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $asesor = $request->user()->asesorProfile;
        if (! $asesor && $request->user()->isAsesor()) {
            $asesor = AsesorProfile::firstOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'no_met' => $request->user()->assessor_registration_number ?? $request->user()->profile?->assessor_registration_number ?? ('MET.000.00'.$request->user()->id.' 2026'),
                    'masa_berlaku' => now()->addYears(3),
                    'bidang_keahlian' => 'Otomotif Kendaraan Ringan & Sepeda Motor',
                    'is_active' => true,
                ]
            );
        }
        if (! $asesor) {
            abort(403, 'Akses khusus asesor terdaftar.');
        }

        $validated = $request->validate([
            'scheme_id' => ['required', 'exists:certification_schemes,id'],
            'schedule_id' => ['nullable', 'exists:assessment_schedules,id'],
            'assignment_date' => ['required', 'date'],
            'findings' => ['required', 'string'],
            'improvement_plan' => ['nullable', 'string'],
        ]);

        MkvaAssignment::create([
            'scheme_id' => $validated['scheme_id'],
            'asesor_id' => $asesor->id,
            'schedule_id' => $validated['schedule_id'] ?? null,
            'assignment_date' => $validated['assignment_date'],
            'findings' => $validated['findings'],
            'improvement_plan' => $validated['improvement_plan'] ?? null,
            'status' => 'selesai',
        ]);

        return redirect()->route('asesor.mkva.index')
            ->with('success', 'Laporan MKVA (Meninjau dan Mengembangkan Perangkat Asesmen) berhasil disimpan.');
    }
}
