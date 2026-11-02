<?php

namespace App\Http\Controllers\Asesi;

use App\Enums\Apl02Status;
use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Apl02Assessment;
use App\Models\Apl02Item;
use App\Models\AssessmentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Apl02Controller extends Controller
{
    /**
     * Tampilkan formulir pengisian Asesmen Mandiri (FR-APL-02)
     */
    public function create(Request $request, AssessmentApplication $application): View|RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        if ($application->status !== ApplicationStatus::Diterima) {
            return redirect()->route('asesi.applications.show', $application)
                ->with('error', 'Formulir APL-02 hanya dapat diisi setelah berkas APL-01 disetujui (diterima).');
        }

        $existingApl02 = $application->apl02;
        if ($existingApl02 && $existingApl02->status !== Apl02Status::Draft && $existingApl02->status !== Apl02Status::PerluRevisi) {
            return redirect()->route('asesi.applications.apl02.show', $application);
        }

        $application->load([
            'scheme.competencyUnits.elements.criterias',
            'documents',
            'user.asesiProfile',
            'schedule.tuk',
            'asesor.asesorProfile',
        ]);

        return view('asesi.applications.apl02_form', [
            'application' => $application,
            'apl02' => $existingApl02,
            'existingItems' => $existingApl02 ? $existingApl02->items->keyBy('kuk_id') : collect(),
        ]);
    }

    /**
     * Simpan pengisian Asesmen Mandiri FR-APL-02
     */
    public function store(Request $request, AssessmentApplication $application): RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        if ($application->status !== ApplicationStatus::Diterima) {
            return redirect()->route('asesi.applications.show', $application)
                ->with('error', 'Permohonan belum berstatus diterima.');
        }

        $request->validate([
            'items' => ['required', 'array'],
            'items.*.penilaian_mandiri' => ['required', 'in:K,BK'],
            'items.*.bukti_pendukung' => ['nullable', 'string', 'max:255'],
            'ttd_asesi' => ['nullable', 'string'],
            'pernyataan_asesmen' => ['accepted'],
        ], [
            'pernyataan_asesmen.accepted' => 'Anda wajib menyetujui pernyataan kejujuran pengisian asesmen mandiri.',
        ]);

        DB::beginTransaction();
        try {
            $isDraft = $request->has('action_draft');
            $status = $isDraft ? Apl02Status::Draft : Apl02Status::Diajukan;

            $apl02 = Apl02Assessment::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'asesor_id' => $application->asesor_id,
                    'status' => $status,
                    'ttd_asesi' => $request->ttd_asesi ?: $application->ttd_asesi,
                    'tgl_ttd_asesi' => now(),
                ]
            );

            foreach ($request->items as $kukId => $itemData) {
                Apl02Item::updateOrCreate(
                    [
                        'apl02_assessment_id' => $apl02->id,
                        'kuk_id' => $kukId,
                    ],
                    [
                        'penilaian_mandiri' => $itemData['penilaian_mandiri'],
                        'bukti_pendukung' => $itemData['bukti_pendukung'] ?? 'Portofolio / Berkas APL-01',
                    ]
                );
            }

            DB::commit();

            $msg = $isDraft
                ? 'Draft asesmen mandiri FR-APL-02 berhasil disimpan.'
                : 'Asesmen Mandiri (FR-APL-02) berhasil disubmit! Menunggu peninjauan oleh Asesor Penguji.';

            return redirect()->route('asesi.applications.apl02.show', $application)
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal menyimpan APL-02: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan blangko resmi FR-APL-02
     */
    public function show(Request $request, AssessmentApplication $application): View|RedirectResponse
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $apl02 = $application->apl02;
        if (! $apl02) {
            return redirect()->route('asesi.applications.apl02.create', $application);
        }

        $application->load([
            'scheme.competencyUnits.elements.criterias',
            'user.asesiProfile',
            'schedule.tuk',
            'asesor.asesorProfile',
        ]);

        $apl02->load('items.kuk');
        $itemsByKuk = $apl02->items->keyBy('kuk_id');

        return view('asesi.applications.apl02_show', [
            'application' => $application,
            'apl02' => $apl02,
            'itemsByKuk' => $itemsByKuk,
        ]);
    }
}
