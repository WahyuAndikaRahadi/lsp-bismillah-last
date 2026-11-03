<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AuditEvent;
use App\Rules\ValidSignature;
use App\Services\ScheduleConsentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentAgreementController extends Controller
{
    public function __construct(
        protected ScheduleConsentService $consentService
    ) {}

    public function show(Assessment $assessment): View|RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->apl_status !== 'APPROVED') {
            return redirect()->route('asesi.assessment.apl', $assessment)
                ->with('error', 'FR.AK.01 hanya dapat diakses setelah permohonan APL Anda disetujui asesor.');
        }

        $assessment->load(['schedule.scheme', 'schedule.assessors', 'ak07Items']);

        return view('asesi.ruang-uji.ak01', compact('assessment'));
    }

    public function agree(Assessment $assessment, Request $request): RedirectResponse
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
        $user = auth()->user();
        $signature = $request->input('signature_data')
            ?? $request->input('asesi_signature')
            ?? $user->profile?->signature_data
            ?? $assessment->asesi_signature;

        DB::transaction(function () use ($assessment, $user, $signature, $now, $request) {
            $assessment->update([
                'ak01_agreed_at' => $now,
                'esign_consent_at' => $assessment->esign_consent_at ?? $now,
                'asesi_signature' => $signature,
                'asesi_signed_at' => $now,
                'last_active_step' => 'ruang-uji',
            ]);

            $consentText = "Persetujuan Kerahasiaan dan Rencana Asesmen (FR.AK.01) untuk Skema {$assessment->schedule->scheme->name} pada Jadwal {$assessment->schedule->code}";

            $this->consentService->recordConsent(
                $assessment->schedule,
                $user,
                'ak01_agreement',
                $consentText,
                $signature,
                $request
            );

            AuditEvent::record(
                action: 'ak01.agreed',
                subject: $assessment,
                reason: "Asesi {$user->name} menyetujui FR.AK.01.",
                details: [
                    'assessment_id' => $assessment->id,
                    'agreed_at' => $now->toDateTimeString(),
                ],
                userId: $user->id
            );
        });

        return redirect()->route('asesi.assessment.ruang-uji', $assessment)
            ->with('success', 'FR.AK01 berhasil disetujui dan ditandatangani.');
    }
}
