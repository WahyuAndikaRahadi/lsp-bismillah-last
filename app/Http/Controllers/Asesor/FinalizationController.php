<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\Schedule;
use App\Services\AkWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FinalizationController extends Controller
{
    use AuthorizesAssessor;

    public function __construct(
        protected AkWorkflowService $workflowService
    ) {}

    /**
     * Complete individual candidate assessment & auto-generate AK.02.
     * Note: Syncs instrument data to AK.02 without mutating status to COMPLETED;
     * redirects assessor to AK.02 form to determine final result.
     */
    public function completeAssessment(Assessment $assessment): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        if ($assessment->apl_status !== 'APPROVED') {
            return back()->with('error', 'Asesmen belum dapat diselesaikan karena APL belum disetujui.');
        }

        $this->workflowService->syncAk02($assessment);

        return redirect()->route('asesor.assessment.ak02', $assessment)
            ->with('success', "Data instrumen asesi {$assessment->asesi->full_name} berhasil disinkronkan ke FR.AK.02. Silakan tentukan keputusan final asesmen.");
    }

    /**
     * View FR.AK.02 Rekaman Asesmen for review and editing.
     */
    public function showAk02(Assessment $assessment): View
    {
        $this->authorizeAssessorAssessment($assessment);

        $assessment->load([
            'schedule.scheme.competencyUnits',
            'asesi.profile',
            'assessor',
            'ak02Items.competencyUnit',
            'iaResponses',
        ]);

        $units = $assessment->schedule->scheme->competencyUnits;
        $ak02Items = $assessment->ak02Items->keyBy('competency_unit_id');

        return view('asesor.ak02', compact('assessment', 'units', 'ak02Items'));
    }

    /**
     * Update FR.AK.02 Rekaman Asesmen.
     */
    public function updateAk02(Assessment $assessment, Request $request): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        $readinessService = app(\App\Services\AssessmentReadinessService::class);
        if (! $readinessService->validateAssessorAuthorization($assessment, auth()->user())) {
            abort(403, 'Hanya asesor yang ditugaskan pada asesmen ini yang berwenang menetapkan rekomendasi.');
        }

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah ditutup dan dikunci.');
        }

        if ($assessment->apl_status !== 'APPROVED') {
            return back()->with('error', 'FR.AK.02 hanya dapat direkam untuk asesi yang telah disetujui APL-nya.');
        }

        // Stale save / Version conflict check
        if ($request->filled('lock_version')) {
            if ((int) $request->input('lock_version') !== (int) ($assessment->lock_version ?? 1)) {
                abort(409, 'Konflik versi: rekomendasi asesmen telah diperbarui pada sesi lain. Muat ulang halaman.');
            }
        }

        $unitsInput = $request->input('units', []);

        $validated = $request->validate([
            'final_result' => 'required|in:K,BK,KOMPETEN,BELUM_KOMPETEN',
            'final_notes' => 'nullable|string|max:1000',
            'follow_up_action' => 'nullable|string|max:1000',
            'assessor_signature' => ['nullable', 'string', new \App\Rules\ValidSignature],
            'lock_version' => 'nullable|integer',
        ], [
            'final_result.required' => 'Rekomendasi hasil asesmen akhir (Kompeten atau Belum Kompeten) wajib dipilih secara eksplisit oleh asesor.',
        ]);

        // Explicit unit findings completeness check
        $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
        $schemeUnits = $scheme ? $scheme->competencyUnits : collect();

        $missingUnits = [];
        foreach ($schemeUnits as $u) {
            if (! isset($unitsInput[$u->id]) || ! isset($unitsInput[$u->id]['is_competent']) || $unitsInput[$u->id]['is_competent'] === '') {
                $missingUnits[] = $u->code ?? $u->kode_unit ?? "Unit #{$u->id}";
            }
        }

        if (! empty($missingUnits)) {
            return back()->withErrors([
                'units' => 'Seluruh unit kompetensi wajib memiliki temuan asesmen (K/BK) eksplisit. Unit belum dinilai: ' . implode(', ', $missingUnits),
            ])->withInput();
        }

        // Recompute recommendation server-side
        $computed = $readinessService->computeFinalRecommendation($assessment, $unitsInput);

        if (in_array(strtoupper((string) $validated['final_result']), ['K', 'KOMPETEN'], true) && ! $computed['all_competent']) {
            return back()->withErrors([
                'final_result' => 'Rekomendasi akhir tidak dapat dinyatakan KOMPETEN (K) karena terdapat unit kompetensi yang dinilai Belum Kompeten (BK).',
            ])->withInput();
        }

        // Observation proof required for Competent
        $iaResponses = $assessment->iaResponses->keyBy('instrument_code');
        $hasDemonstration = $iaResponses->has('IA01') || $iaResponses->has('IA01B') || $iaResponses->has('IA02');
        if ($computed['final_result'] === 'KOMPETEN' && ! $hasDemonstration) {
            return back()->withErrors([
                'final_result' => 'Rekomendasi KOMPETEN memerlukan rekaman bukti observasi demonstrasi praktik (FR.IA.01/IA.02). Skor CBT tertulis tanpa observasi tidak memenuhi syarat.',
            ])->withInput();
        }

        $finalResult = $computed['final_result'];

        Log::info('Asesor ID '.auth()->id()." memperbarui FR.AK.02 asesmen #{$assessment->id} dengan hasil {$finalResult}");

        $now = now();

        DB::transaction(function () use ($assessment, $unitsInput, $validated, $finalResult, $computed, $request, $now) {
            foreach ($unitsInput as $unitId => $data) {
                $unitComputed = $computed['units'][$unitId] ?? null;
                $methods = $unitComputed['methods'] ?? [
                    'observasi_demonstrasi' => ! empty($data['observasi_demonstrasi']),
                    'portofolio' => ! empty($data['portofolio']),
                    'pihak_ketiga' => ! empty($data['pihak_ketiga']),
                    'lisan' => ! empty($data['lisan']),
                    'tulis' => ! empty($data['tulis']),
                    'proyek' => ! empty($data['proyek']),
                    'lainnya' => ! empty($data['lainnya']),
                ];

                AssessmentAk02Item::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'competency_unit_id' => $unitId,
                    ],
                    [
                        'methods_json' => $methods,
                        'is_competent' => $unitComputed['is_competent'] ?? (! empty($data['is_competent'])),
                        'finding_notes' => $data['finding_notes'] ?? $data['notes'] ?? null,
                        'assessor_id' => auth()->id(),
                        'recorded_at' => $now,
                    ]
                );
            }

            $newAssessorSig = $request->input('assessor_signature') ?? $assessment->assessor_signature;
            if ($assessment->assessor_signature && $newAssessorSig !== $assessment->assessor_signature) {
                Log::info("Signature updated for assessment #{$assessment->id} by assessor #".auth()->id());
            }

            $this->workflowService->finalizeAssessment(
                $assessment,
                $finalResult,
                $validated['final_notes'] ?? null,
                auth()->user(),
                $validated['follow_up_action'] ?? null,
                $newAssessorSig
            );

            $assessment->rekomendasi_asesor = $computed['rekomendasi_asesor'];
            $assessment->lock_version = ($assessment->lock_version ?? 1) + 1;
            $assessment->save();

            \App\Models\AuditEvent::record(
                action: 'ak02.finalized',
                subject: $assessment,
                reason: "Asesor menetapkan rekomendasi final asesmen {$finalResult}",
                details: [
                    'final_result' => $finalResult,
                    'rekomendasi_asesor' => $computed['rekomendasi_asesor'],
                    'units_count' => count($unitsInput),
                    'assessor_id' => auth()->id(),
                ],
                userId: auth()->id()
            );
        });

        return redirect()->route('asesor.jadwal.asesi', $assessment->schedule_id)
            ->with('success', 'FR.AK.02 Rekaman Asesmen berhasil diperbarui.');
    }

    /**
     * View FR.AK.05 (Laporan Asesmen) & FR.AK.06 (Tinjauan Proses).
     */
    public function showReports(Schedule $schedule): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $schedule->load('scheme', 'assessors', 'assessments.asesi');

        return view('asesor.reports-ak05-ak06', compact('schedule'));
    }

    /**
     * Submit FR.AK.05 & FR.AK.06 and optionally close schedule.
     */
    public function submitReports(Schedule $schedule, Request $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $validated = $request->validate([
            'ak05_aspek_positif' => 'required|string|min:10',
            'ak05_aspek_negatif' => 'required|string|min:10',
            'ak05_rekomendasi' => 'required|string|min:10',
            'ak06_valid' => 'required|in:YA,TIDAK',
            'ak06_valid_notes' => 'required|string|min:5',
            'ak06_reliabel' => 'required|in:YA,TIDAK',
            'ak06_reliabel_notes' => 'required|string|min:5',
            'ak06_fleksibel' => 'required|in:YA,TIDAK',
            'ak06_fleksibel_notes' => 'required|string|min:5',
            'ak06_adil' => 'required|in:YA,TIDAK',
            'ak06_adil_notes' => 'required|string|min:5',
            'ak06_catatan_peninjau' => 'required|string|min:10',
            'confirm_draft_review' => 'accepted',
        ], [
            'ak05_aspek_positif.required' => 'Aspek positif laporan asesmen (FR.AK.05) wajib diisi.',
            'ak05_aspek_negatif.required' => 'Aspek negatif laporan asesmen (FR.AK.05) wajib diisi.',
            'ak05_rekomendasi.required' => 'Rekomendasi peningkatan mutu (FR.AK.05) wajib diisi.',
            'ak06_catatan_peninjau.required' => 'Catatan keseluruhan peninjau proses (FR.AK.06) wajib diisi.',
            'confirm_draft_review.accepted' => 'Anda wajib mencentang konfirmasi peninjauan draf laporan sebelum menyimpan dan menutup jadwal.',
        ]);

        $ak05 = [
            'aspek_positif' => $validated['ak05_aspek_positif'],
            'aspek_negatif' => $validated['ak05_aspek_negatif'],
            'rekomendasi' => $validated['ak05_rekomendasi'],
        ];

        $ak06 = [
            'valid' => $validated['ak06_valid'],
            'valid_notes' => $validated['ak06_valid_notes'],
            'reliabel' => $validated['ak06_reliabel'],
            'reliabel_notes' => $validated['ak06_reliabel_notes'],
            'fleksibel' => $validated['ak06_fleksibel'],
            'fleksibel_notes' => $validated['ak06_fleksibel_notes'],
            'adil' => $validated['ak06_adil'],
            'adil_notes' => $validated['ak06_adil_notes'],
            'catatan_peninjau' => $validated['ak06_catatan_peninjau'],
        ];

        if ($schedule->is_closed) {
            return redirect()->route('asesor.jadwal.index')
                ->with('info', 'Jadwal asesmen ini sudah resmi ditutup sebelumnya.');
        }

        $readiness = $this->workflowService->isScheduleReadyForClosure($schedule);

        DB::transaction(function () use ($schedule, $ak05, $ak06, $readiness) {
            $this->workflowService->saveScheduleReports($schedule, $ak05, $ak06, $readiness['is_ready']);
        });

        if (! $readiness['is_ready']) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('warning', "Laporan tersimpan. Jadwal belum dapat ditutup: {$readiness['uncompleted_count']} asesi belum selesai.");
        }

        return redirect()->route('asesor.jadwal.index')
            ->with('success', 'FR.AK.05 Laporan Asesmen dan FR.AK.06 Tinjauan Proses berhasil disimpan. Jadwal resmi ditutup.');
    }

    /**
     * Mark candidate as absent (TIDAK_HADIR).
     */
    public function markAbsent(Assessment $assessment, Request $request): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah ditutup dan dikunci.');
        }

        $notes = $request->input('notes') ?? $request->input('final_notes');
        $this->workflowService->markCandidateAbsent($assessment, $notes, auth()->id());

        return back()->with('success', "Asesi {$assessment->asesi->full_name} berhasil ditandai TIDAK HADIR.");
    }

    /**
     * Finalize & close schedule.
     */
    public function finalize(Schedule $schedule): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        if ($schedule->is_closed) {
            return redirect()->route('asesor.jadwal.index')
                ->with('info', 'Jadwal asesmen ini sudah resmi ditutup sebelumnya.');
        }

        $readiness = $this->workflowService->isScheduleReadyForClosure($schedule);

        if (! $readiness['is_ready']) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', "Jadwal belum dapat ditutup. Masih ada {$readiness['uncompleted_count']} asesi yang belum diselesaikan asesmennya.");
        }

        if (! $readiness['has_ak05'] || ! $readiness['has_ak06']) {
            return redirect()->route('asesor.schedule.reports', $schedule->id)
                ->with('info', 'Harap melengkapi FR.AK.05 (Laporan Asesmen) dan FR.AK.06 (Tinjauan Proses Asesmen) sebelum menutup jadwal asesmen.');
        }

        DB::transaction(function () use ($schedule) {
            $schedule->update([
                'is_closed' => true,
                'closed_at' => now(),
                'status' => 'FINISHED',
            ]);
        });

        return redirect()->route('asesor.jadwal.index')
            ->with('success', 'Jadwal asesmen telah resmi ditutup dan dikunci.');
    }
}
