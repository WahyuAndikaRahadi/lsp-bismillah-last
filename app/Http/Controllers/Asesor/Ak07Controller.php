<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ak07Request;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AuditEvent;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Ak07Controller extends Controller
{
    use AuthorizesAssessor;

    public function index(Schedule $schedule, Request $request): View|RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $schedule->load('scheme', 'assessments.asesi', 'assessments.ak07Items');

        $unapprovedCount = $schedule->assessments()->where('apl_status', '!=', 'APPROVED')->count();
        if ($unapprovedCount > 0) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', "Formulir FR.AK07 belum dapat dibuka. Masih ada {$unapprovedCount} asesi yang belum disetujui APL-nya.");
        }

        $jadwal = $schedule;
        $assessments = $schedule->assessments;
        $selectedAssessmentId = $request->query('assessment');

        return view('asesor.ak07', compact('schedule', 'jadwal', 'assessments', 'selectedAssessmentId'));
    }

    public function store(Schedule $schedule, Ak07Request $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        if ($schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        $unapprovedCount = $schedule->assessments()->where('apl_status', '!=', 'APPROVED')->count();
        if ($unapprovedCount > 0) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', 'Penyesuaian tidak dapat disimpan karena belum seluruh asesi disetujui APL-nya.');
        }

        $validated = $request->validated();

        $ids = $request->input('assessment_ids', []);
        if (empty($ids) && $request->filled('assessment_id')) {
            $ids = [$request->input('assessment_id')];
        }

        if (empty($ids)) {
            $ids = $schedule->assessments()->pluck('id')->toArray();
        }

        $items = $request->input('items', []);
        $timing = $request->input('timing', 'sebelum_pra_asesmen');
        $isAgreed = $request->boolean('is_agreed') || strtoupper((string) $request->input('agreement_status')) === 'YA';
        $agreedBenchmark = $request->input('agreed_benchmark', $request->input('agreement_benchmark'));
        $agreedMethod = $request->input('agreed_method', $request->input('agreement_method'));
        $agreedInstrument = $request->input('agreed_instrument', $request->input('agreement_instrument'));
        $agreementNotes = $request->input('agreement_notes');

        DB::transaction(function () use (
            $ids,
            $items,
            $timing,
            $isAgreed,
            $agreedBenchmark,
            $agreedMethod,
            $agreedInstrument,
            $agreementNotes
        ) {
            $assessorId = auth()->id();

            foreach ($ids as $assessmentId) {
                $assessment = Assessment::find($assessmentId);
                if (! $assessment) {
                    continue;
                }

                // Check if existing items had adjustments to record audit and invalidate AK-01
                $hadPreviousAdjustments = AssessmentAk07Item::where('assessment_id', $assessmentId)
                    ->where('decision', 'ya')
                    ->exists();

                // Idempotently purge prior AK07 items
                AssessmentAk07Item::where('assessment_id', $assessmentId)->delete();

                $anyYes = false;

                foreach ($items as $idx => $itemData) {
                    $catNum = (int) ($itemData['category_number'] ?? $idx);
                    $title = $itemData['title'] ?? "Karakteristik #{$catNum}";
                    $decision = strtolower($itemData['decision'] ?? $itemData['needed'] ?? 'tidak');
                    $options = $itemData['options'] ?? [];
                    $detail = trim(($itemData['detail'] ?? '') . ' ' . ($itemData['custom_note'] ?? ''));

                    if ($decision === 'ya') {
                        $anyYes = true;
                    }

                    AssessmentAk07Item::create([
                        'assessment_id' => $assessmentId,
                        'assessor_id' => $assessorId,
                        'category_number' => $catNum,
                        'adjustment_category' => $title,
                        'decision' => $decision,
                        'selected_options' => $options,
                        'adjustment_detail' => $detail ?: ($decision === 'ya' ? 'Penyesuaian wajar' : null),
                        'timing' => $timing,
                        'is_agreed' => $isAgreed,
                        'agreed_benchmark' => $agreedBenchmark,
                        'agreed_method' => $agreedMethod,
                        'agreed_instrument' => $agreedInstrument,
                        'asesor_signed_at' => now(),
                        'asesi_signed_at' => null, // Never automatically sign for asesi
                    ]);
                }

                // Perubahan akomodasi memicu plan revision/acceptance yang relevan tanpa mengubah kompetensi
                if ($anyYes || $hadPreviousAdjustments) {
                    if ($assessment->ak01_agreed_at !== null) {
                        $assessment->update([
                            'ak01_agreed_at' => null,
                        ]);
                    }

                    AuditEvent::record(
                        action: 'ak07_adjustment_updated',
                        subject: $assessment,
                        reason: 'Penyesuaian yang wajar (FR.AK.07) dimutakhirkan. Persetujuan AK-01 direset untuk konfirmasi ulang.',
                        details: [
                            'assessment_id' => $assessmentId,
                            'timing' => $timing,
                            'has_adjustments' => $anyYes,
                            'invalidated_ak01' => $assessment->ak01_agreed_at !== null,
                        ]
                    );
                }
            }
        });

        return redirect()->route('asesor.schedule.ak01', $schedule->id)
            ->with('success', 'FR.AK07 berhasil disimpan. Melanjutkan ke FR.AK01 (Persetujuan Asesmen & Kerahasiaan).');
    }

    /**
     * Asesi acknowledges agreed adjustments.
     */
    public function acknowledge(Request $request, Assessment $assessment): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isAsesi() || $assessment->asesi_id !== $user->id) {
            abort(403, 'Hanya asesi bersangkutan yang dapat menandatangani persetujuan akomodasi ini.');
        }

        AssessmentAk07Item::where('assessment_id', $assessment->id)
            ->whereNull('asesi_signed_at')
            ->update([
                'asesi_signed_at' => now(),
            ]);

        AuditEvent::record(
            action: 'ak07_asesi_acknowledged',
            subject: $assessment,
            reason: 'Asesi menyetujui dan menandatangani kesepakatan penyesuaian wajar (FR.AK.07).',
            details: ['asesi_id' => $user->id]
        );

        return back()->with('success', 'Kesepakatan penyesuaian wajar (FR.AK.07) berhasil ditandatangani.');
    }
}
