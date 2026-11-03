<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AppealInvestigation;
use App\Models\AssessmentAppeal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppealController extends Controller
{
    /**
     * Tampilkan daftar pengajuan banding (FR-SOP-07.1 intake).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = AssessmentAppeal::with([
            'assessment.asesi.profile',
            'assessment.scheme',
            'assessment.assessor',
            'latestInvestigation.investigator',
        ])->orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        $appeals = $query->paginate(15);

        return view('admin.appeals.index', compact('appeals', 'status'));
    }

    /**
     * Tampilkan rincian banding, berita acara investigasi, dan formulir investigasi independen.
     */
    public function show(AssessmentAppeal $appeal): View
    {
        $appeal->load([
            'assessment.asesi.profile',
            'assessment.scheme',
            'assessment.assessor',
            'investigations.investigator',
            'investigations.decider',
        ]);

        $involvedAssessorId = $appeal->assessment?->assessor_id;

        // Daftar calon investigator independen (seluruh asesor kecuali yang menguji kandidat)
        $potentialInvestigators = User::where('role', UserRole::Asesor->value)
            ->where('id', '!=', $involvedAssessorId)
            ->orderBy('name')
            ->get();

        return view('admin.appeals.show', compact('appeal', 'potentialInvestigators', 'involvedAssessorId'));
    }

    /**
     * Catat berita acara investigasi banding independen (FR-SOP-07.2).
     */
    public function recordInvestigation(Request $request, AssessmentAppeal $appeal): RedirectResponse
    {
        $validated = $request->validate([
            'investigator_id' => ['required', 'exists:users,id'],
            'investigation_date' => ['required', 'date'],
            'investigation_place' => ['required', 'string', 'max:255'],
            'factual_findings' => ['required', 'string', 'min:10'],
            'recommendation' => ['required', 'in:PERTAHANKAN_KEPUTUSAN,ASESMEN_ULANG,BATALKAN_KEPUTUSAN,MAINTAIN_DECISION,REASSESSMENT_REQUIRED,REVOKE_DECISION'],
            'recommendation_notes' => ['nullable', 'string'],
        ], [
            'investigator_id.required' => 'Investigator banding independen wajib ditugaskan.',
            'factual_findings.required' => 'Temuan faktual investigasi wajib diisi minimal 10 karakter.',
            'recommendation.required' => 'Rekomendasi tindak lanjut investigasi wajib dipilih.',
        ]);

        // Pemeriksaan independensi: Asesor yang menguji kandidat dilarang keras menjadi investigator
        $involvedAssessorId = $appeal->assessment?->assessor_id;
        if ($involvedAssessorId && (int) $validated['investigator_id'] === (int) $involvedAssessorId) {
            throw ValidationException::withMessages([
                'investigator_id' => ['Investigator banding harus independen dan tidak boleh asesor yang menguji kandidat.'],
            ]);
        }

        DB::beginTransaction();
        try {
            $investigator = User::findOrFail($validated['investigator_id']);

            // 1. Simpan Berita Acara Investigasi (FR-SOP-07.2)
            $investigation = AppealInvestigation::create([
                'appeal_id' => $appeal->id,
                'investigator_id' => $investigator->id,
                'investigator_role' => 'Investigator Banding Independen',
                'investigation_date' => $validated['investigation_date'],
                'investigation_place' => $validated['investigation_place'],
                'factual_findings' => $validated['factual_findings'],
                'recommendation' => $validated['recommendation'],
                'recommendation_notes' => $validated['recommendation_notes'] ?? null,
                'decided_by' => Auth::id(),
                'decided_at' => now(),
                'status' => 'COMPLETED',
            ]);

            // 2. Pemetaan status banding resmi
            $isAccepted = in_array($validated['recommendation'], [
                'ASESMEN_ULANG',
                'BATALKAN_KEPUTUSAN',
                'REASSESSMENT_REQUIRED',
                'REVOKE_DECISION',
            ], true);

            $appeal->update([
                'status' => $isAccepted ? 'ACCEPTED' : 'REJECTED',
                'decision_notes' => $validated['factual_findings'] . "\nCatatan: " . ($validated['recommendation_notes'] ?? $validated['recommendation']),
                'decision_date' => now()->toDateString(),
                'decided_by' => Auth::id(),
                'appeals_committee_chair' => Auth::user()?->name ?? 'Ketua Komite Teknis',
            ]);

            // Sesuai prinsip SOP 07: tidak ada hasil kompetensi otomatis pada assessment
            DB::commit();

            return redirect()->route('admin.appeals.show', $appeal->id)
                ->with('success', 'Berita acara investigasi banding (FR-SOP-07.2) berhasil dicatat dan disahkan.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal mencatat investigasi banding: ' . $e->getMessage());
        }
    }
}
