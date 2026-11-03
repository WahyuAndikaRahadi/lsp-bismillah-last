<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessorReplacementEvent;
use App\Models\ConflictDeclaration;
use App\Models\ImpartialityCommitment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImpartialityController extends Controller
{
    /**
     * Tampilkan monitoring ketidakberpihakan, deklarasi benturan, dan riwayat pergantian asesor.
     */
    public function index(): View
    {
        $commitments = ImpartialityCommitment::with('user')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'commitments_page');

        $openConflicts = ConflictDeclaration::with(['declarant', 'assessment.scheme', 'relatedAsesi', 'schedule'])
            ->where('has_conflict', true)
            ->where('status', 'OPEN')
            ->orderByDesc('id')
            ->get();

        $replacementEvents = AssessorReplacementEvent::with([
            'assessment.asesi',
            'assessment.scheme',
            'previousAssessor',
            'replacementAssessor',
            'approver',
        ])->orderByDesc('id')->take(20)->get();

        return view('admin.impartiality.index', compact('commitments', 'openConflicts', 'replacementEvents'));
    }

    /**
     * Catat dan laksanakan pergantian asesor resmi (FR-SOP-01.7).
     */
    public function replaceAssessor(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'replacement_assessor_id' => ['required', 'exists:users,id'],
            'reason' => ['required', 'string', 'min:5'],
        ], [
            'replacement_assessor_id.required' => 'Asesor pengganti wajib dipilih.',
            'reason.required' => 'Dasar/alasan pergantian asesor wajib diisi.',
        ]);

        $replacementAssessor = User::findOrFail($validated['replacement_assessor_id']);
        if ($replacementAssessor->role !== UserRole::Asesor && $replacementAssessor->role !== UserRole::Asesor->value && ! $replacementAssessor->isRole('asesor')) {
            return back()->withInput()->with('error', 'Pengguna yang dipilih bukan seorang asesor kompetensi.');
        }

        $previousAssessorId = $assessment->assessor_id;
        if ((int) $previousAssessorId === (int) $replacementAssessor->id) {
            return back()->withInput()->with('error', 'Asesor pengganti harus berbeda dari asesor saat ini.');
        }

        DB::beginTransaction();
        try {
            // 1. Rekam Event Pergantian Asesor (FR-SOP-01.7)
            AssessorReplacementEvent::create([
                'assessment_id' => $assessment->id,
                'schedule_id' => $assessment->schedule_id,
                'previous_assessor_id' => $previousAssessorId ?: Auth::id(),
                'replacement_assessor_id' => $replacementAssessor->id,
                'reason' => $validated['reason'],
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // 2. Perbarui penugasan pada asesmen
            $assessment->update([
                'assessor_id' => $replacementAssessor->id,
            ]);

            // 3. Jika jadwal belum meng-attach asesor pengganti, kaitkan ke schedule
            if ($assessment->schedule && ! $assessment->schedule->assessors()->where('users.id', $replacementAssessor->id)->exists()) {
                $assessment->schedule->assessors()->attach($replacementAssessor->id);
            }

            // 4. Update status deklarasi benturan kepentingan asesor lama menjadi TRANSFERRED
            ConflictDeclaration::where('assessment_id', $assessment->id)
                ->where('declarant_id', $previousAssessorId)
                ->where('has_conflict', true)
                ->where('status', 'OPEN')
                ->update(['status' => 'TRANSFERRED']);

            DB::commit();

            return redirect()->back()
                ->with('success', "Pergantian asesor (FR-SOP-01.7) berhasil disahkan. Asesi dialihkan ke {$replacementAssessor->name}.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses pergantian asesor: ' . $e->getMessage());
        }
    }
}
