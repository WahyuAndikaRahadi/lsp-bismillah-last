<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAk03Item;
use App\Models\AssessmentAppeal;
use App\Rules\ValidSignature;
use App\Services\AkWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentFeedbackController extends Controller
{
    public function __construct(
        protected AkWorkflowService $workflowService
    ) {}

    /**
     * Tampilkan formulir FR.AK.03 Umpan Balik Asesi.
     */
    public function showAk03(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403, 'Akses ditolak.');

        if (! in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true) && ! $assessment->rekomendasi_asesor) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.03 (Umpan Balik Asesi) hanya dapat diisi setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        $assessment->load('schedule.scheme', 'assessor', 'ak03Item');

        return view('asesi.ruang-uji.ak03', [
            'assessment' => $assessment,
            'ak03' => $assessment->ak03Item,
        ]);
    }

    /**
     * Simpan FR.AK.03 Umpan Balik Asesi.
     * Catatan: Umpan balik asesi TIDAK mengubah hasil/rekomendasi kompetensi.
     */
    public function submitAk03(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403, 'Akses ditolak.');

        if (! in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true) && ! $assessment->rekomendasi_asesor) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.03 hanya dapat diisi setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        if ($assessment->schedule?->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        $validated = $request->validate([
            'ratings' => 'required|array|min:10',
            'ratings.*.value' => 'required|in:YA,TIDAK,ya,tidak',
            'ratings.*.comment' => 'nullable|string|max:500',
            'general_notes' => 'nullable|string|max:1000',
        ], [
            'ratings.required' => 'Seluruh 10 butir evaluasi FR.AK.03 wajib diisi.',
            'ratings.min' => 'Seluruh 10 butir pernyataan evaluasi wajib diisi.',
            'ratings.*.value.required' => 'Setiap butir pertanyaan evaluasi wajib dipilih (Ya atau Tidak).',
        ]);

        $resultBefore = $assessment->final_result;

        DB::transaction(fn () => $this->workflowService->submitAk03Feedback($assessment, $validated['ratings'], $validated['general_notes'] ?? null));

        // Strict verification: final result must remain unchanged
        $assessment->refresh();
        assert($assessment->final_result === $resultBefore);

        return redirect()->route('asesi.jadwal.index')->with('success', 'Formulir FR.AK.03 Umpan Balik Asesi berhasil disimpan.');
    }

    /**
     * Tampilkan formulir FR.AK.04 Banding Asesmen (Intake).
     */
    public function showAppeal(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403, 'Akses ditolak.');

        if (! in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true) && ! $assessment->rekomendasi_asesor) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.04 (Banding Asesmen) hanya dapat diakses setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        $assessment->load('schedule.scheme.competencyUnits', 'assessor', 'appeals');
        $units = $assessment->schedule?->scheme?->competencyUnits ?? collect();
        $appeal = $assessment->latestAppeal;

        $appealWindowDays = $this->workflowService->getAppealWindowDays();
        $baseDate = $this->workflowService->getAppealBaseDate($assessment);
        $deadline = $this->workflowService->calculateAppealDeadline($baseDate);
        $isWithinDeadline = $this->workflowService->isWithinAppealDeadline($assessment);

        return view('asesi.ruang-uji.ak04-appeal', compact('assessment', 'units', 'appeal', 'appealWindowDays', 'deadline', 'isWithinDeadline'));
    }

    /**
     * Simpan pengajuan FR.AK.04 Banding Asesmen oleh Asesi (Intake resmi untuk investigasi Task 21-01).
     * Catatan: Pengajuan banding TIDAK mengubah hasil kompetensi secara otomatis.
     */
    public function submitAppeal(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403, 'Akses ditolak.');

        if (! in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true) && ! $assessment->rekomendasi_asesor) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.04 (Banding Asesmen) hanya dapat diakses setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        if (AssessmentAppeal::where('assessment_id', $assessment->id)->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])->exists()) {
            return redirect()->route('asesi.jadwal.index')
                ->with('info', 'Permohonan banding asesmen Anda sudah tercatat dan sedang dalam peninjauan Tim Banding LSP.');
        }

        $request->validate([
            'prosedur_dijelaskan' => 'required|boolean',
            'diskusi_dengan_asesor' => 'required|boolean',
            'melibatkan_orang_lain' => 'required|boolean',
            'appeal_reasons' => 'required|string|min:10',
            'contested_units' => 'nullable|array',
            'signature_data' => ['nullable', 'string', new ValidSignature],
        ], [
            'prosedur_dijelaskan.required' => 'Pernyataan penjelasan prosedur banding wajib dijawab Ya atau Tidak.',
            'diskusi_dengan_asesor.required' => 'Pernyataan diskusi dengan asesor wajib dijawab Ya atau Tidak.',
            'melibatkan_orang_lain.required' => 'Pernyataan pelibatan orang lain wajib dijawab Ya atau Tidak.',
            'appeal_reasons.required' => 'Alasan pengajuan banding wajib diisi minimal 10 karakter.',
        ]);

        $resultBefore = $assessment->final_result;

        DB::transaction(fn () => $this->workflowService->submitAppeal(
            assessment: $assessment,
            reasons: $request->input('appeal_reasons'),
            contestedUnits: $request->input('contested_units', []),
            prosedurDijelaskan: (bool) $request->input('prosedur_dijelaskan'),
            diskusiDenganAsesor: (bool) $request->input('diskusi_dengan_asesor'),
            melibatkanOrangLain: (bool) $request->input('melibatkan_orang_lain'),
            signature: $request->input('signature_data')
        ));

        // Verification: Appeal intake does NOT change assessment final_result automatically
        $assessment->refresh();
        assert($assessment->final_result === $resultBefore);

        return redirect()->route('asesi.jadwal.index')
            ->with('success', 'Pengajuan Banding Asesmen (FR.AK.04) berhasil dikirimkan ke LSP dan tercatat sebagai berkas intake.');
    }

    /**
     * Tampilkan status dan pengumuman hasil investigasi banding untuk asesi (Kerahasiaan terjaga).
     */
    public function showAppealStatus(AssessmentAppeal $appeal): View
    {
        abort_if($appeal->assessment?->asesi_id !== auth()->id(), 403, 'Akses ditolak.');

        $appeal->load([
            'assessment.scheme',
            'assessment.assessor',
            'latestInvestigation',
        ]);

        return view('asesi.appeals.show', compact('appeal'));
    }
}
