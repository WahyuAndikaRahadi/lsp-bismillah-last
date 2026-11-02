<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AsesorProfile;
use App\Models\CertificationScheme;
use App\Models\InstrumentReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstrumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;

        $reviews = InstrumentReview::with(['scheme', 'asesor.user'])
            ->when($asesor, function ($q) use ($asesor) {
                $q->where('asesor_id', $asesor->id);
            })
            ->latest('review_date')
            ->paginate(15);

        $schemes = CertificationScheme::where('is_active', true)->orderBy('nama_skema')->get();

        return view('asesor.instrument_reviews.index', [
            'reviews' => $reviews,
            'schemes' => $schemes,
            'asesor' => $asesor,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $asesor = $request->user()->asesorProfile;
        if (! $asesor && $request->user()->isAsesor()) {
            $asesor = AsesorProfile::firstOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'no_met' => $request->user()->assessor_registration_number ?? $request->user()->profile?->assessor_registration_number ?? ('MET.000.00'.$request->user()->id.' 2026'),
                    'masa_berlaku' => now()->addYears(3),
                    'bidang_keahlian' => 'Otomotif Kendaraan Ringan & Sepeda Motor',
                    'is_active' => true,
                ]
            );
        }
        if (! $asesor) {
            abort(403, 'Akses khusus asesor terdaftar.');
        }

        $validated = $request->validate([
            'scheme_id' => ['required', 'exists:certification_schemes,id'],
            'instrument_type' => ['required', 'string'],
            'review_date' => ['required', 'date'],
            'recommendation' => ['required', 'in:disetujui,perlu_perbaikan'],
            'notes' => ['nullable', 'string'],
        ]);

        InstrumentReview::create([
            'scheme_id' => $validated['scheme_id'],
            'asesor_id' => $asesor->id,
            'instrument_type' => $validated['instrument_type'],
            'review_date' => $validated['review_date'],
            'recommendation' => $validated['recommendation'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'disetujui',
        ]);

        return redirect()->route('asesor.instruments.index')
            ->with('success', 'Validasi dan peninjauan instrumen uji berhasil disimpan.');
    }
}
