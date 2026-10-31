<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Ak01Agreement;
use App\Models\Ak02Assessment;
use App\Models\Ak02UnitAssessment;
use App\Models\Ak05Report;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentExecutionController extends Controller
{
    /**
     * Tanda tangan AK-01 oleh Asesor
     */
    public function signAk01(Request $request, AssessmentApplication $application): RedirectResponse
    {
        $request->validate([
            'ttd_asesor' => ['nullable', 'string'],
        ]);

        $ak01 = Ak01Agreement::firstOrCreate(
            ['application_id' => $application->id],
            [
                'asesor_id' => Auth::id(),
                'hari_tanggal' => $application->schedule?->tgl_asesmen ?? now()->toDateString(),
                'waktu' => '09:00 - Selesai WIB',
                'tuk_nama' => $application->schedule?->tuk->nama_tuk ?? 'TUK Terverifikasi',
                'is_asesor_agreed' => true,
            ]
        );

        $ak01->update([
            'asesor_id' => Auth::id(),
            'is_asesor_agreed' => true,
            'ttd_asesor' => $request->ttd_asesor ?: Auth::user()->signature_image,
            'tgl_ttd_asesor' => now(),
        ]);

        return back()->with('success', 'Formulir persetujuan FR-AK-01 telah ditandatangani oleh Asesor.');
    }

    /**
     * Form Penilaian Uji Kompetensi Digital (FR-AK-02)
     */
    public function showAk02(AssessmentApplication $application): View
    {
        $application->load([
            'scheme.competencyUnits.elements.criterias',
            'user.asesiProfile',
            'schedule.tuk',
            'ak01',
            'ak02.units',
        ]);

        $ak02 = $application->ak02 ?? new Ak02Assessment([
            'application_id' => $application->id,
            'asesor_id' => Auth::id(),
            'rekomendasi_akhir' => 'K',
        ]);

        $unitsAssessed = $ak02->exists ? $ak02->units->keyBy('unit_id') : collect();

        return view('asesor.assessment.ak02_form', compact('application', 'ak02', 'unitsAssessed'));
    }

    public function storeAk02(Request $request, AssessmentApplication $application): RedirectResponse
    {
        $request->validate([
            'rekomendasi_akhir' => ['required', 'in:K,BK'],
            'tindak_lanjut' => ['nullable', 'string'],
            'komentar_asesor' => ['nullable', 'string'],
            'units' => ['required', 'array'],
            'units.*.rekomendasi_unit' => ['required', 'in:K,BK'],
            'ttd_asesor' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $ak02 = Ak02Assessment::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'asesor_id' => Auth::id(),
                    'rekomendasi_akhir' => $request->rekomendasi_akhir,
                    'tindak_lanjut' => $request->tindak_lanjut,
                    'komentar_asesor' => $request->komentar_asesor,
                    'ttd_asesor' => $request->ttd_asesor ?: Auth::user()->signature_image,
                    'tgl_ttd_asesor' => now(),
                ]
            );

            foreach ($request->units as $unitId => $uData) {
                Ak02UnitAssessment::updateOrCreate(
                    [
                        'ak02_assessment_id' => $ak02->id,
                        'unit_id' => $unitId,
                    ],
                    [
                        'metode_observasi_langsung' => isset($uData['metode_l']),
                        'metode_portofolio' => isset($uData['metode_tl']),
                        'metode_pertanyaan' => isset($uData['metode_t']),
                        'rekomendasi_unit' => $uData['rekomendasi_unit'],
                        'catatan' => $uData['catatan'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('asesor.apl02.index')
                ->with('success', "Penilaian asesmen kompetensi (FR-AK-02) untuk {$application->user->name} berhasil disimpan.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Asesmen Jadwal (FR-AK-05)
     */
    public function showAk05(AssessmentSchedule $schedule): View
    {
        $schedule->load([
            'scheme',
            'tuk',
            'applications.user.asesiProfile',
            'applications.ak02',
            'scheduleAsesors.asesor.asesorProfile',
            'ak05Report',
        ]);

        $report = $schedule->ak05Report ?? new Ak05Report([
            'schedule_id' => $schedule->id,
            'nomor_laporan' => Ak05Report::generateNomorLaporan($schedule),
            'tgl_laporan' => now()->toDateString(),
        ]);

        return view('asesor.assessment.ak05_report', compact('schedule', 'report'));
    }

    public function storeAk05(Request $request, AssessmentSchedule $schedule): RedirectResponse
    {
        $request->validate([
            'nomor_laporan' => ['required', 'string', 'max:100'],
            'aspek_positif' => ['nullable', 'string'],
            'kendala_hambatan' => ['nullable', 'string'],
            'saran_perbaikan' => ['nullable', 'string'],
            'ttd_asesor_lead' => ['nullable', 'string'],
        ]);

        Ak05Report::updateOrCreate(
            ['schedule_id' => $schedule->id],
            [
                'nomor_laporan' => $request->nomor_laporan,
                'aspek_positif' => $request->aspek_positif,
                'kendala_hambatan' => $request->kendala_hambatan,
                'saran_perbaikan' => $request->saran_perbaikan,
                'ttd_asesor_lead' => $request->ttd_asesor_lead ?: Auth::user()->signature_image,
                'tgl_laporan' => now()->toDateString(),
            ]
        );

        return back()->with('success', 'Laporan asesmen (FR-AK-05) berhasil disimpan.');
    }
}
