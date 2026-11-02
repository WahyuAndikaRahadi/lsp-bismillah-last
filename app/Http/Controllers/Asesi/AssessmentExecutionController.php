<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Ak01Agreement;
use App\Models\Ak03Feedback;
use App\Models\AssessmentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentExecutionController extends Controller
{
    /**
     * Tampilkan dan tandatangani FR-AK-01 (Persetujuan Asesmen & Kerahasiaan)
     */
    public function showAk01(AssessmentApplication $application): View|RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->load(['scheme', 'schedule.tuk', 'asesor']);

        $ak01 = Ak01Agreement::firstOrCreate(
            ['application_id' => $application->id],
            [
                'asesor_id' => $application->asesor_id,
                'hari_tanggal' => $application->schedule?->tgl_asesmen ?? now()->toDateString(),
                'waktu' => '09:00 - Selesai WIB',
                'tuk_nama' => $application->schedule?->tuk->nama_tuk ?? 'TUK Terverifikasi LSP',
                'is_asesi_agreed' => true,
            ]
        );

        return view('asesi.assessment.ak01', compact('application', 'ak01'));
    }

    public function signAk01(Request $request, AssessmentApplication $application): RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'ttd_asesi' => ['nullable', 'string'],
            'setuju' => ['accepted'],
        ]);

        $ak01 = Ak01Agreement::where('application_id', $application->id)->firstOrFail();
        $ak01->update([
            'is_asesi_agreed' => true,
            'ttd_asesi' => $request->ttd_asesi ?: $application->ttd_asesi,
            'tgl_ttd_asesi' => now(),
        ]);

        return back()->with('success', 'Persetujuan asesmen dan kerahasiaan (FR-AK-01) berhasil ditandatangani.');
    }

    /**
     * Tampilkan dan tandatangani penerimaan hasil FR-AK-02
     */
    public function showAk02(AssessmentApplication $application): View|RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $ak02 = $application->ak02;
        if (! $ak02) {
            return redirect()->route('asesi.applications.show', $application)
                ->with('error', 'Penilaian asesmen (FR-AK-02) belum diterbitkan oleh asesor.');
        }

        $ak02->load(['units.unit', 'asesor']);

        return view('asesi.assessment.ak02', compact('application', 'ak02'));
    }

    public function signAk02(Request $request, AssessmentApplication $application): RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'ttd_asesi' => ['nullable', 'string'],
            'konfirmasi' => ['accepted'],
        ]);

        $ak02 = $application->ak02()->firstOrFail();
        $ak02->update([
            'ttd_asesi' => $request->ttd_asesi ?: $application->ttd_asesi,
            'tgl_ttd_asesi' => now(),
        ]);

        return back()->with('success', 'Pengesahan penerimaan rekomendasi asesmen (FR-AK-02) berhasil disimpan.');
    }

    /**
     * Kuesioner Umpan Balik Asesmen (FR-AK-03)
     */
    public function showAk03(AssessmentApplication $application): View
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $ak03 = $application->ak03 ?? new Ak03Feedback;

        return view('asesi.assessment.ak03', compact('application', 'ak03'));
    }

    public function storeAk03(Request $request, AssessmentApplication $application): RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'q1_penjelasan' => ['required', 'boolean'],
            'q2_kesepakatan' => ['required', 'boolean'],
            'q3_hak_banding' => ['required', 'boolean'],
            'q4_adil_objektif' => ['required', 'boolean'],
            'q5_umpan_balik' => ['required', 'boolean'],
            'catatan_rekomendasi' => ['nullable', 'string', 'max:1000'],
            'ttd_asesi' => ['nullable', 'string'],
        ]);

        Ak03Feedback::updateOrCreate(
            ['application_id' => $application->id],
            [
                'q1_penjelasan' => $request->boolean('q1_penjelasan'),
                'q2_kesepakatan' => $request->boolean('q2_kesepakatan'),
                'q3_hak_banding' => $request->boolean('q3_hak_banding'),
                'q4_adil_objektif' => $request->boolean('q4_adil_objektif'),
                'q5_umpan_balik' => $request->boolean('q5_umpan_balik'),
                'catatan_rekomendasi' => $request->catatan_rekomendasi,
                'ttd_asesi' => $request->ttd_asesi ?: $application->ttd_asesi,
                'tgl_ttd_asesi' => now(),
            ]
        );

        return redirect()->route('asesi.applications.show', $application)
            ->with('success', 'Formulir umpan balik asesmen (FR-AK-03) berhasil dikirim. Terima kasih atas masukan Anda.');
    }
}
