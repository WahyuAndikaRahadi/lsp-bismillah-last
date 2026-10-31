<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
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
            'asesi.profile',
            'schedule.scheme.competencyUnits.elements.kuks',
            'proofs',
            'apl02Items',
        ]);

        $profile = $assessment->asesi->profile;
        $scheme = $assessment->schedule->scheme;
        $proofs = $assessment->proofs;
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
            $assessorId = $assessment->assessor_id ?? auth()->id();
            $now = now();

            $assessment->update([
                'apl_status' => $status,
                'apl_revision_notes' => $notes,
                'assessor_id' => $assessorId,
                'approved_at' => $status === 'APPROVED' ? $now : null,
                'admin_status' => 'APPROVED',
            ]);

            if ($status === 'APPROVED') {
                // 1. Auto-generate standard FR.AK.07
                AssessmentAk07Item::firstOrCreate([
                    'assessment_id' => $assessment->id,
                ], [
                    'adjustment_category' => 'Standar',
                    'adjustment_detail' => 'Tidak ada penyesuaian khusus yang diperlukan (asesmen standar).',
                    'assessor_id' => $assessorId,
                ]);

                // 2. Auto-agree FR.AK.01 with signatures
                $asesiSig = $assessment->asesi_signature ?? $assessment->asesi?->profile?->signature_data;
                $assessorSig = $assessment->assessor_signature ?? auth()->user()->profile?->signature_data;

                $assessment->update([
                    'ak01_agreed_at' => $assessment->ak01_agreed_at ?? $now,
                    'asesi_signature' => $asesiSig,
                    'asesi_signed_at' => $assessment->asesi_signed_at ?? $now,
                    'last_active_step' => 'ruang-uji',
                ]);
            }
        });

        return redirect()->route('asesor.jadwal.asesi', $assessment->schedule_id)
            ->with('success', $status === 'APPROVED' ? 'APL asesi berhasil disetujui. Asesi telah siap memasuki Ruang Uji.' : 'Keputusan review APL berhasil disimpan.');
    }
}
