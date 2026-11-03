<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AuditEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AplReviewController extends Controller
{
    use AuthorizesAssessor;

    public function show(Assessment $assessment): View
    {
        $this->authorizeAssessorAssessment($assessment);

        $assessment->load([
            'schedule.scheme.competencyUnits.elements.kuks',
            'proofs',
            'apl02Items.kuk.element.competencyUnit',
            'asesi.profile',
        ]);

        $profile = $assessment->asesi->profile;
        $scheme = $assessment->schedule->scheme;
        $proofs = $assessment->proofs->keyBy('proof_type');
        $apl02Items = $assessment->apl02Items->keyBy('kuk_id');

        return view('asesor.review-apl', compact(
            'assessment',
            'profile',
            'scheme',
            'proofs',
            'apl02Items'
        ));
    }

    public function decide(Assessment $assessment, Request $request): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        $status = strtoupper((string) ($request->input('apl_status') ?? $request->input('decision') ?? $request->input('action') ?? ''));
        if ($status === 'APPROVE') {
            $status = 'APPROVED';
        }
        if ($status === 'REVISION') {
            $status = 'REVISION_REQUESTED';
        }
        if ($status === 'REJECT') {
            $status = 'REJECTED';
        }

        if (! in_array($status, ['APPROVED', 'REVISION_REQUESTED', 'REJECTED'])) {
            return back()->withErrors(['apl_status' => 'Status keputusan tidak valid.']);
        }

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah ditutup dan dikunci.');
        }

        if (in_array($status, ['APPROVED', 'REVISION_REQUESTED']) && ! in_array($assessment->apl_status, ['SUBMITTED', 'REVISION_REQUESTED', 'APPROVED'])) {
            return back()->with('error', 'Tidak dapat memproses permohonan APL yang belum diajukan oleh asesi (status masih DRAFT).');
        }

        if ($assessment->admin_status === 'REJECTED') {
            return back()->with('error', 'Pendaftaran asesi telah ditolak oleh Admin LSP.');
        }

        $notes = $request->input('notes') ?? $request->input('apl_revision_notes') ?? $request->input('catatan');

        DB::transaction(function () use ($assessment, $status, $notes) {
            $assessor = auth()->user();
            $assessorId = $assessment->assessor_id ?? $assessor->id;
            $now = now();

            $assessment->update([
                'apl_status' => $status,
                'apl_revision_notes' => $notes,
                'assessor_id' => $assessorId,
                'approved_at' => $status === 'APPROVED' ? $now : null,
                'admin_status' => 'APPROVED',
            ]);

            // Clean audit trail without fabricating AK07, AK01, or competence results
            AuditEvent::record(
                action: 'apl.' . strtolower($status),
                subject: $assessment,
                reason: "Keputusan review APL oleh Asesor {$assessor->name}: {$status}" . ($notes ? " (Catatan: {$notes})" : ''),
                details: [
                    'assessment_id' => $assessment->id,
                    'status' => $status,
                    'notes' => $notes,
                    'assessor_id' => $assessorId,
                ],
                userId: $assessor->id
            );
        });

        return redirect()->route('asesor.jadwal.asesi', $assessment->schedule_id)
            ->with('success', $status === 'APPROVED' ? 'APL asesi berhasil disetujui. Lanjutkan ke penyusunan MAPA / verifikasi penyesuaian AK07.' : 'Keputusan review APL berhasil disimpan.');
    }
}
