<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\ConflictDeclaration;
use App\Models\ImpartialityCommitment;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ImpartialityController extends Controller
{
    /**
     * Tampilkan formulir Pakta Integritas Asesor (FR-SOP-01.5).
     */
    public function showCommitment(): View
    {
        $user = Auth::user();

        $activeCommitment = ImpartialityCommitment::where('user_id', $user->id)
            ->where('commitment_type', 'PAKTA_INTEGRITAS_ASESOR')
            ->where('is_active', true)
            ->latest('id')
            ->first();

        return view('asesor.impartiality.commitment', compact('activeCommitment'));
    }

    /**
     * Tandatangani dan simpan Pakta Integritas Asesor (FR-SOP-01.5).
     */
    public function signCommitment(Request $request): RedirectResponse
    {
        $request->validate([
            'agreement' => ['required', 'accepted'],
            'signature_data' => ['nullable', 'string'],
        ], [
            'agreement.accepted' => 'Anda wajib menyetujui seluruh butir klausul Pakta Integritas Asesor.',
        ]);

        $user = Auth::user();
        $signatureData = $request->input('signature_data') ?? $user->profile?->signature_data;

        $clauses = "1. Bertindak independen, jujur, objektif, dan tidak memihak dalam setiap proses asesmen.\n" .
            "2. Menolak segala bentuk suap, gratifikasi, atau pengaruh dari pihak manapun.\n" .
            "3. Segera menyatakan dan mendeklarasikan benturan kepentingan apabila memiliki hubungan keluarga, rekan kerja langsung, atau pernah melatih asesi dalam kurun waktu 2 tahun terakhir.\n" .
            "4. Menjaga kerahasiaan materi uji, data pribadi asesi, dan hasil asesmen sesuai SOP LSP ProIndo.";

        ImpartialityCommitment::create([
            'user_id' => $user->id,
            'commitment_type' => 'PAKTA_INTEGRITAS_ASESOR',
            'statement_text' => $clauses,
            'signed_at' => now(),
            'signature_data' => $signatureData,
            'is_active' => true,
        ]);

        return redirect()->route('asesor.impartiality.commitment')
            ->with('success', 'Pakta Integritas Asesor (FR-SOP-01.5) berhasil ditandatangani dan dicatat secara sah.');
    }

    /**
     * Tampilkan formulir Deklarasi Benturan Kepentingan untuk jadwal asesmen.
     */
    public function showConflictDeclaration(Schedule $schedule): View
    {
        $user = Auth::user();

        $assessments = $schedule->assessments()
            ->with('asesi.profile')
            ->where('assessor_id', $user->id)
            ->get();

        $existingDeclarations = ConflictDeclaration::where('declarant_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->get()
            ->keyBy('assessment_id');

        return view('asesor.impartiality.conflict-declaration', compact('schedule', 'assessments', 'existingDeclarations'));
    }

    /**
     * Simpan Deklarasi Benturan Kepentingan per asesi (FR-SOP-01.5).
     */
    public function storeConflictDeclaration(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'declarations' => ['required', 'array'],
            'declarations.*.has_conflict' => ['required', 'boolean'],
            'declarations.*.conflict_nature' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        foreach ($validated['declarations'] as $assessmentId => $data) {
            $hasConflict = (bool) $data['has_conflict'];
            $nature = $hasConflict ? ($data['conflict_nature'] ?? 'Hubungan langsung / afiliasi') : null;

            $assessment = $schedule->assessments()->find($assessmentId);
            if (! $assessment) {
                continue;
            }

            ConflictDeclaration::updateOrCreate(
                [
                    'declarant_id' => $user->id,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'schedule_id' => $schedule->id,
                    'related_asesi_id' => $assessment->asesi_id,
                    'has_conflict' => $hasConflict,
                    'conflict_nature' => $nature,
                    'status' => $hasConflict ? 'OPEN' : 'RESOLVED',
                    'declared_at' => now(),
                ]
            );
        }

        return redirect()->back()
            ->with('success', 'Deklarasi benturan kepentingan berhasil dicatat secara resmi.');
    }
}
