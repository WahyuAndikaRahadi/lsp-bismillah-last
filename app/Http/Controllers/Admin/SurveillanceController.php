<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\SurveillancePlan;
use App\Models\SurveillanceReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    /**
     * Tampilkan monitoring rencana dan laporan surveilans berkala (SOP 04 §4 / SOP 19).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = SurveillancePlan::with([
            'certificate.assessment.asesi.profile',
            'certificate.assessment.scheme',
            'reports.evaluator',
            'reports.decider',
            'creator',
        ])->orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        $plans = $query->paginate(15);

        // Sertifikat aktif yang belum dibuatkan rencana surveilans tahun berjalan
        $activeCertificates = Certificate::with(['assessment.asesi', 'assessment.scheme'])
            ->where('status', 'ACTIVE')
            ->whereDoesntHave('surveillancePlans', function ($q) {
                $q->whereIn('status', ['SCHEDULED', 'REPORTED', 'UNDER_REVIEW']);
            })
            ->latest('id')
            ->take(10)
            ->get();

        return view('admin.surveillance.index', compact('plans', 'activeCertificates', 'status'));
    }

    /**
     * Form pembuatan rencana surveilans untuk sertifikat aktif.
     */
    public function createPlan(Certificate $certificate): View|RedirectResponse
    {
        if ($certificate->status !== 'ACTIVE') {
            return redirect()->route('admin.surveillance.index')
                ->with('error', 'Rencana surveilans hanya dapat dibuat untuk sertifikat yang berstatus AKTIF.');
        }

        $certificate->load(['assessment.asesi.profile', 'assessment.scheme']);

        return view('admin.surveillance.create-plan', compact('certificate'));
    }

    /**
     * Simpan rencana surveilans berkala baru (FR.SPS.01 Rencana).
     */
    public function storePlan(Request $request, Certificate $certificate): RedirectResponse
    {
        if ($certificate->status !== 'ACTIVE') {
            return redirect()->back()
                ->with('error', 'Rencana surveilans hanya dapat dibuat untuk sertifikat yang berstatus AKTIF.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'surveillance_year' => ['required', 'integer', 'min:1', 'max:5'],
            'due_date' => ['required', 'date', 'after:today'],
            'required_evidence_description' => ['required', 'string', 'min:10'],
        ], [
            'title.required' => 'Judul rencana surveilans wajib diisi.',
            'due_date.after' => 'Batas waktu surveilans harus di masa mendatang.',
            'required_evidence_description.required' => 'Persyaratan bukti pemeliharaan kompetensi wajib diuraikan.',
        ]);

        SurveillancePlan::create([
            'certificate_id' => $certificate->id,
            'title' => $validated['title'],
            'surveillance_year' => $validated['surveillance_year'],
            'due_date' => $validated['due_date'],
            'required_evidence_description' => $validated['required_evidence_description'],
            'status' => 'SCHEDULED',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.surveillance.index')
            ->with('success', "Rencana surveilans untuk sertifikat {$certificate->certificate_number} berhasil diterbitkan.");
    }

    /**
     * Evaluasi laporan pemeliharaan kompetensi dan tetapkan keputusan resmi (FR.SPS.01 / FR.SK.01).
     */
    public function evaluateReport(Request $request, SurveillanceReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'evaluator_notes' => ['required', 'string', 'min:10'],
            'decision' => ['required', 'in:COMPLIANT,ACTION_REQUIRED,SUSPENDED'],
            'decision_notes' => ['nullable', 'string'],
        ], [
            'evaluator_notes.required' => 'Catatan temuan verifikasi bukti (FR.SPS.01) wajib diisi minimal 10 karakter.',
            'decision.required' => 'Keputusan resmi hasil surveilans (FR.SK.01) wajib ditetapkan.',
        ]);

        DB::beginTransaction();
        try {
            $actor = Auth::user();

            $report->update([
                'evaluator_id' => $actor->id,
                'evaluator_notes' => $validated['evaluator_notes'],
                'evaluated_at' => now(),
                'decision' => $validated['decision'],
                'decision_notes' => $validated['decision_notes'] ?? null,
                'decided_by' => $actor->id,
                'decided_at' => now(),
                'status' => 'VERIFIED',
            ]);

            // Perbarui status pada rencana surveilans
            $report->plan?->update([
                'status' => 'COMPLETED',
            ]);

            // Jika diputuskan pembekuan sertifikat, update status sertifikat
            if ($validated['decision'] === 'SUSPENDED') {
                $report->certificate?->update([
                    'status' => 'SUSPENDED',
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Keputusan surveilans resmi (FR.SK.01) berhasil disahkan dengan status {$validated['decision']}.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses evaluasi surveilans: ' . $e->getMessage());
        }
    }
}
