<?php

namespace App\Http\Controllers\Asesor;

use App\Enums\Apl02Status;
use App\Http\Controllers\Controller;
use App\Models\Apl02Assessment;
use App\Models\Apl02Item;
use App\Models\AssessmentSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Apl02ReviewController extends Controller
{
    /**
     * Daftar permohonan APL-02 yang ditugaskan ke Asesor ini
     */
    public function index(Request $request): View
    {
        $asesor = $request->user();

        // Ambil jadwal yang ditugaskan kepada asesor ini
        $schedules = AssessmentSchedule::whereHas('scheduleAsesors', function ($q) use ($asesor) {
            $q->where('asesor_id', $asesor->id);
        })->with(['scheme', 'tuk'])->get();

        // Ambil daftar permohonan APL-02 yang di-plot ke asesor ini
        $apl02List = Apl02Assessment::with([
            'application.user.asesiProfile',
            'application.scheme',
            'application.schedule.tuk',
            'items',
        ])
            ->where(function ($q) use ($asesor) {
                $q->where('asesor_id', $asesor->id)
                    ->orWhereHas('application', fn ($sub) => $sub->where('asesor_id', $asesor->id));
            })
            ->latest()
            ->paginate(10);

        return view('asesor.apl02.index', [
            'apl02List' => $apl02List,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Lembar kerja peninjauan APL-02 oleh Asesor
     */
    public function review(Apl02Assessment $apl02): View
    {
        $apl02->load([
            'application.user.asesiProfile',
            'application.scheme.competencyUnits.elements.criterias',
            'application.schedule.tuk',
            'application.documents',
            'items.kuk',
        ]);

        $itemsByKuk = $apl02->items->keyBy('kuk_id');

        return view('asesor.apl02.review', [
            'apl02' => $apl02,
            'application' => $apl02->application,
            'itemsByKuk' => $itemsByKuk,
        ]);
    }

    /**
     * Simpan verifikasi dan rekomendasi pra-asesmen APL-02
     */
    public function update(Request $request, Apl02Assessment $apl02): RedirectResponse
    {
        $request->validate([
            'rekomendasi_asesor' => ['required', 'in:Asesmen dapat dilanjutkan,Asesmen tidak dapat dilanjutkan'],
            'catatan_asesor' => ['nullable', 'string'],
            'status' => ['required', 'in:diverifikasi,perlu_revisi'],
            'items' => ['required', 'array'],
            'ttd_asesor' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $newStatus = Apl02Status::from($request->status);

            // Update item VATM
            foreach ($request->items as $itemId => $itemData) {
                $item = Apl02Item::find($itemId);
                if ($item && $item->apl02_assessment_id === $apl02->id) {
                    $item->update([
                        'is_valid' => isset($itemData['v']),
                        'is_asli' => isset($itemData['a']),
                        'is_terkini' => isset($itemData['t']),
                        'is_memadai' => isset($itemData['m']),
                        'catatan_asesor' => $itemData['catatan'] ?? null,
                    ]);
                }
            }

            // Update status & rekomendasi APL-02
            $apl02->update([
                'status' => $newStatus,
                'asesor_id' => Auth::id(),
                'rekomendasi_asesor' => $request->rekomendasi_asesor,
                'catatan_asesor' => $request->catatan_asesor,
                'ttd_asesor' => $request->ttd_asesor ?: Auth::user()->signature_image,
                'tgl_ttd_asesor' => now(),
            ]);

            DB::commit();

            return redirect()->route('asesor.apl02.index')
                ->with('success', "Hasil peninjauan FR-APL-02 untuk asesi {$apl02->application->user->name} berhasil disimpan.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal menyimpan peninjauan APL-02: '.$e->getMessage());
        }
    }
}
