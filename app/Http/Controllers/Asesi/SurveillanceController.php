<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\SurveillancePlan;
use App\Models\SurveillanceReport;
use App\Services\PrivateFileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    public function __construct(
        protected PrivateFileService $privateFileService
    ) {}

    /**
     * Tampilkan daftar rencana surveilans dan riwayat laporan pemegang sertifikat.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Rencana surveilans untuk seluruh sertifikat yang dimiliki asesi login
        $plans = SurveillancePlan::with([
            'certificate.assessment.scheme',
            'reports',
        ])
        ->whereHas('certificate.assessment', function ($q) use ($user) {
            $q->where('asesi_id', $user->id);
        })
        ->orderBy('due_date')
        ->get();

        return view('asesi.surveillance.index', compact('plans'));
    }

    /**
     * Tampilkan formulir pengunggahan bukti pemeliharaan kompetensi (FR.SP.01).
     */
    public function show(SurveillancePlan $plan): View
    {
        Gate::authorize('view', $plan);

        $plan->load([
            'certificate.assessment.scheme',
            'reports.evaluator',
            'reports.decider',
        ]);

        return view('asesi.surveillance.show', compact('plan'));
    }

    /**
     * Simpan pengajuan laporan surveilans berkala oleh pemegang sertifikat (FR.SP.01).
     */
    public function submitReport(Request $request, SurveillancePlan $plan): RedirectResponse
    {
        Gate::authorize('submitReport', $plan);

        $validated = $request->validate([
            'activity_summary' => ['required', 'string', 'min:10'],
            'evidence_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'activity_summary.required' => 'Uraian ringkas kegiatan praktik/pekerjaan pemeliharaan kompetensi wajib diisi.',
            'evidence_file.mimes' => 'Berkas bukti harus berformat PDF, JPG, atau PNG.',
        ]);

        $user = Auth::user();
        $filePath = null;

        if ($request->hasFile('evidence_file')) {
            $privateFile = $this->privateFileService->storeUploadedFile(
                file: $request->file('evidence_file'),
                category: 'surveillance',
                ownerType: SurveillancePlan::class,
                ownerId: $plan->id,
                uploadedBy: $user->id
            );
            $filePath = $privateFile->path;
        }

        SurveillanceReport::create([
            'surveillance_plan_id' => $plan->id,
            'certificate_id' => $plan->certificate_id,
            'asesi_id' => $user->id,
            'activity_summary' => $validated['activity_summary'],
            'evidence_file_path' => $filePath,
            'submitted_at' => now(),
            'status' => 'SUBMITTED',
        ]);

        $plan->update([
            'status' => 'REPORTED',
        ]);

        return redirect()->route('asesi.surveillance.show', $plan->id)
            ->with('success', 'Laporan surveilans pemeliharaan kompetensi (FR.SP.01) berhasil dikirimkan ke LSP.');
    }
}
