<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AsesorProfile;
use App\Models\Tuk;
use App\Models\TukVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TukVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;

        $verifications = TukVerification::with(['tuk', 'asesor.user'])
            ->when($asesor, function ($q) use ($asesor) {
                $q->where('asesor_id', $asesor->id);
            })
            ->latest('verification_date')
            ->paginate(15);

        $tuks = Tuk::where('is_active', true)->orderBy('nama_tuk')->get();

        return view('asesor.tuk_verifications.index', [
            'verifications' => $verifications,
            'tuks' => $tuks,
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
            'tuk_id' => ['required', 'exists:tuks,id'],
            'spt_number' => ['nullable', 'string', 'max:100'],
            'assigner_name' => ['nullable', 'string', 'max:255'],
            'assigner_position' => ['nullable', 'string', 'max:255'],
            'verifier_name' => ['nullable', 'string', 'max:255'],
            'verifier_position' => ['nullable', 'string', 'max:255'],
            'verification_date' => ['required', 'date'],
            'verification_type' => ['required', 'in:sewaktu,mandiri,tempat_kerja'],
            'recommendation' => ['required', 'in:layak,belum_layak'],
            'notes' => ['nullable', 'string'],
        ]);

        TukVerification::create([
            'tuk_id' => $validated['tuk_id'],
            'asesor_id' => $asesor->id,
            'spt_number' => $validated['spt_number'] ?? null,
            'assigner_name' => $validated['assigner_name'] ?? 'Direktur LSP P3 PrO Indo',
            'assigner_position' => $validated['assigner_position'] ?? 'Direktur Eksekutif',
            'verifier_name' => $validated['verifier_name'] ?? $request->user()->full_name,
            'verifier_position' => $validated['verifier_position'] ?? 'Asesor Verifikator TUK',
            'verification_date' => $validated['verification_date'],
            'verification_type' => $validated['verification_type'],
            'recommendation' => $validated['recommendation'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'diverifikasi',
        ]);

        return redirect()->route('asesor.tuks.index')
            ->with('success', 'Hasil verifikasi TUK berhasil dicatat.');
    }
}
