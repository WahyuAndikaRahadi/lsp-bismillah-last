<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AssessmentAppeal;
use App\Models\AssessmentIaResponse;
use App\Models\QuestionBank;
use App\Rules\ValidSignature;
use App\Services\AkWorkflowService;
use App\Services\AutoGradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RuangUjiController extends Controller
{
    public function __construct(
        protected AkWorkflowService $workflowService
    ) {}

    public function ak01(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->ak01_agreed_at) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment);
        }
        if ($assessment->apl_status !== 'APPROVED') {
            return redirect()->route('asesi.assessment.apl', $assessment)
                ->with('error', 'FR.AK.01 hanya dapat diakses setelah permohonan APL Anda disetujui asesor.');
        }

        $assessment->load(['schedule.scheme', 'iaResponses']);

        return view('asesi.ruang-uji.ak01', compact('assessment'));
    }

    public function signAk01(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->schedule?->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }
        if ($assessment->apl_status !== 'APPROVED') {
            return back()->with('error', 'FR.AK.01 hanya dapat disetujui setelah permohonan APL Anda disetujui asesor.');
        }

        $request->validate([
            'esign_consent' => 'required|accepted',
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'asesi_signature' => ['nullable', 'string', new ValidSignature],
        ], [
            'esign_consent.required' => 'Persetujuan penggunaan tanda tangan elektronik wajib dicentang.',
            'esign_consent.accepted' => 'Persetujuan penggunaan tanda tangan elektronik wajib disetujui.',
        ]);

        $now = now();
        $signature = $request->input('signature_data') ?? $request->input('asesi_signature') ?? $assessment->asesi_signature;
        if ($assessment->asesi_signature && $signature !== $assessment->asesi_signature) {
            Log::info("Signature updated for assessment #{$assessment->id} by asesi #{$assessment->asesi_id}");
        }

        DB::transaction(function () use ($assessment, $now, $signature, $request) {
            $assessment->update([
                'ak01_agreed_at' => $assessment->ak01_agreed_at ?? $now,
                'esign_consent_at' => $assessment->esign_consent_at ?? $now,
                'asesi_signature' => $signature,
                'asesi_signed_at' => $assessment->asesi_signed_at ?? $now,
                'last_active_step' => 'ruang-uji',
            ]);

            // Record explicit consent
            if ($assessment->schedule) {
                app(\App\Services\ScheduleConsentService::class)->recordConsent(
                    $assessment->schedule,
                    auth()->user(),
                    'ak01_agreement',
                    "Persetujuan FR.AK.01 untuk Jadwal {$assessment->schedule->code}",
                    $signature,
                    $request
                );
            }
        });

        return redirect()->route('asesi.assessment.ruang-uji', $assessment)
            ->with('success', 'FR.AK01 berhasil disetujui dan ditandatangani.');
    }

    public function enterExam(Assessment $assessment, Request $request, \App\Services\AttendanceService $attendanceService): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        try {
            $attendanceService->recordAsesiEnterExam($assessment, $request);
        } catch (\RuntimeException $e) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('asesi.assessment.ruang-uji', $assessment)
            ->with('success', 'Anda resmi hadir dan memasuki ruang uji asesmen.');
    }

    public function consentEsign(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        $request->validate([
            'esign_consent' => 'required|accepted',
        ], [
            'esign_consent.required' => 'Persetujuan penggunaan tanda tangan elektronik wajib dicentang.',
            'esign_consent.accepted' => 'Persetujuan penggunaan tanda tangan elektronik wajib disetujui.',
        ]);

        $assessment->update([
            'esign_consent_at' => now(),
        ]);

        return redirect()->route('asesi.assessment.ruang-uji', $assessment)
            ->with('success', 'Persetujuan penggunaan tanda tangan elektronik berhasil disimpan.');
    }

    public function hub(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->apl_status !== 'APPROVED') {
            return redirect()->route('asesi.assessment.apl', $assessment)
                ->with('warning', 'Ruang Uji baru dapat diakses setelah permohonan APL Anda disetujui.');
        }

        if (! $assessment->ak01_agreed_at) {
            return redirect()->route('asesi.assessment.ak01', $assessment)
                ->with('info', 'Silakan setujui Formulir FR.AK.01 (Persetujuan Asesmen & Kerahasiaan) terlebih dahulu sebelum memasuki Ruang Uji.');
        }

        $assessment->load('schedule.scheme', 'schedule.assessors', 'iaResponses', 'assessor');
        $selectedInstruments = $assessment->getUnlockedInstruments();
        $responses = $assessment->iaResponses->keyBy('instrument_code');

        return view('asesi.ruang-uji.hub', compact('assessment', 'selectedInstruments', 'responses'));
    }

    public function ia05(Assessment $assessment): View
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        $assessment->load(['schedule.scheme', 'iaResponses']);
        $questions = QuestionBank::where('scheme_id', $assessment->schedule->scheme_id)->get();

        return view('asesi.ruang-uji.ia05', compact('assessment', 'questions'));
    }

    public function submitIa05(Assessment $assessment, Request $request, AutoGradeService $grader): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        if ($assessment->apl_status !== 'APPROVED') {
            return back()->with('error', 'Ujian CBT hanya dapat diikuti setelah permohonan APL Anda disetujui.');
        }

        $existing = AssessmentIaResponse::where('assessment_id', $assessment->id)
            ->where('instrument_code', 'IA05')
            ->whereNotNull('asesi_signed_at')
            ->first();

        if ($existing) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('info', "Jawaban ujian CBT (IA.05) Anda telah tersimpan sebelumnya dengan skor: {$existing->score}.");
        }

        $rawAnswers = $request->input('answers', []);
        $answers = is_array($rawAnswers) ? $rawAnswers : (json_decode($rawAnswers, true) ?: []);

        $result = DB::transaction(function () use ($assessment, $answers, $grader) {
            $graded = $grader->grade($assessment, $answers);
            $assessment->update(['last_active_step' => 'ruang-uji']);

            return $graded;
        });

        return redirect()->route('asesi.assessment.ruang-uji', $assessment)
            ->with('success', "Jawaban tersimpan. Skor: {$result->score}");
    }

    public function saveIa05Draft(Assessment $assessment, Request $request): JsonResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return response()->json(['status' => 'error', 'message' => 'Jadwal asesmen sudah resmi ditutup.'], 403);
        }

        $existing = AssessmentIaResponse::where('assessment_id', $assessment->id)
            ->where('instrument_code', 'IA05')
            ->first();

        if ($existing && $existing->asesi_signed_at) {
            return response()->json(['status' => 'error', 'message' => 'Ujian CBT telah selesai dikerjakan dan tidak dapat diubah.'], 400);
        }

        $rawAnswers = $request->input('answers', []);
        $answers = is_array($rawAnswers) ? $rawAnswers : (json_decode($rawAnswers, true) ?: []);

        $response = $existing ?? new AssessmentIaResponse([
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA05',
        ]);

        $response->data_json = $answers;
        $response->save();

        return response()->json([
            'status' => 'success',
            'saved_at' => now()->toIso8601String(),
            'count' => count($answers),
        ]);
    }

    public function uploadEvidence(Assessment $assessment, $code, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        $request->validate(['evidence_image' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:10240']);

        $codeUpper = strtoupper($code);
        $path = $request->file('evidence_image')->store('ia02_evidence', 'local');

        $response = AssessmentIaResponse::firstOrNew(['assessment_id' => $assessment->id, 'instrument_code' => $codeUpper]);
        $dataJson = is_array($response->data_json) ? $response->data_json : (json_decode($response->data_json ?? '[]', true) ?: []);
        $dataJson['evidence_image_path'] = $path;
        $dataJson['evidence_uploaded_at'] = now()->toIso8601String();
        $dataJson['evidence_original_name'] = $request->file('evidence_image')->getClientOriginalName();

        $response->data_json = $dataJson;
        $response->save();

        return back()->with('success', 'Foto bukti lembar kerja fisik berhasil diunggah.');
    }

    public function showAk03(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);
        if (! $assessment->canAccessFeedback()) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.03 (Umpan Balik Asesi) hanya dapat diisi setelah rangkaian uji selesai dan hasil rekomendasi asesmen telah ditetapkan oleh asesor.');
        }

        $assessment->load('schedule.scheme', 'assessor', 'ak03Item');

        return view('asesi.ruang-uji.ak03', ['assessment' => $assessment, 'ak03' => $assessment->ak03Item]);
    }

    public function submitAk03(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);
        if (! $assessment->canAccessFeedback()) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.03 (Umpan Balik Asesi) hanya dapat diisi setelah rangkaian uji selesai dan hasil rekomendasi asesmen telah ditetapkan oleh asesor.');
        }
        if ($assessment->schedule?->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        $validated = $request->validate([
            'ratings' => 'required|array|min:10',
            'ratings.*.value' => 'required|in:YA,TIDAK',
            'ratings.*.comment' => 'nullable|string|max:500',
            'general_notes' => 'nullable|string|max:1000',
        ], [
            'ratings.required' => 'Seluruh butir evaluasi FR.AK.03 wajib diisi.',
            'ratings.min' => 'Seluruh 10 butir pernyataan evaluasi wajib dipilih.',
            'ratings.*.value.required' => 'Setiap butir pertanyaan evaluasi wajib dipilih (Ya atau Tidak).',
        ]);

        DB::transaction(fn () => $this->workflowService->submitAk03Feedback($assessment, $validated['ratings'], $validated['general_notes'] ?? null));

        return redirect()->route('asesi.jadwal.index')->with('success', 'Formulir FR.AK.03 Umpan Balik Asesi berhasil dikirim.');
    }

    public function showAppeal(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);
        if (! $assessment->canAccessAppeal()) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.04 (Banding Asesmen) hanya dapat diakses setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        $assessment->load('schedule.scheme.competencyUnits', 'assessor', 'appeals');
        $units = $assessment->schedule->scheme->competencyUnits;
        $appeal = $assessment->latestAppeal;

        $appealWindowDays = $this->workflowService->getAppealWindowDays();
        $baseDate = $this->workflowService->getAppealBaseDate($assessment);
        $deadline = $this->workflowService->calculateAppealDeadline($baseDate);
        $isWithinDeadline = $this->workflowService->isWithinAppealDeadline($assessment);

        return view('asesi.ruang-uji.ak04-appeal', compact('assessment', 'units', 'appeal', 'appealWindowDays', 'deadline', 'isWithinDeadline'));
    }

    public function submitAppeal(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);
        if (! $assessment->canAccessAppeal()) {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('error', 'Formulir FR.AK.04 (Banding Asesmen) hanya dapat diakses setelah rekomendasi hasil asesmen telah resmi ditetapkan oleh asesor.');
        }

        if (AssessmentAppeal::where('assessment_id', $assessment->id)->where('status', 'SUBMITTED')->exists()) {
            return redirect()->route('asesi.jadwal.index')
                ->with('info', 'Permohonan banding asesmen Anda sudah tercatat dan sedang dalam peninjauan Tim Banding LSP.');
        }

        $request->validate([
            'appeal_reasons' => 'required|string|min:10',
            'contested_units' => 'nullable|array',
        ]);

        DB::transaction(fn () => $this->workflowService->submitAppeal($assessment, $request->input('appeal_reasons'), $request->input('contested_units', [])));

        return redirect()->route('asesi.jadwal.index')
            ->with('success', 'Pengajuan Banding Asesmen (FR.AK.04) berhasil dikirimkan ke Admin LSP.');
    }
}
