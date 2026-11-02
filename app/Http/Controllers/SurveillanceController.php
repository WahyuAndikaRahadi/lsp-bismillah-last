<?php

namespace App\Http\Controllers;

use App\Models\CertificationScheme;
use App\Models\SurveillanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    /**
     * Tampilan form surveilans pemeliharaan kompetensi pemegang sertifikat
     */
    public function create(): View
    {
        $schemes = CertificationScheme::where('is_active', true)->get();

        return view('public.surveillance', compact('schemes'));
    }

    /**
     * Simpan data surveilans berkala
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'digits:16'],
            'email' => ['required', 'email', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'no_sertifikat' => ['required', 'string', 'max:100'],
            'no_registrasi' => ['nullable', 'string', 'max:100'],
            'scheme_id' => ['nullable', 'exists:certification_schemes,id'],
            'tempat_kerja' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:100'],
            'alamat_kantor' => ['nullable', 'string'],
            'proyek_saat_ini' => ['nullable', 'string', 'max:255'],
            'kesesuaian_skk' => ['required', 'in:Y,N,O'],
            'keterangan' => ['nullable', 'string'],
        ]);

        SurveillanceRecord::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'no_sertifikat' => $request->no_sertifikat,
            'no_registrasi' => $request->no_registrasi,
            'scheme_id' => $request->scheme_id,
            'tempat_kerja' => $request->tempat_kerja,
            'jabatan' => $request->jabatan,
            'alamat_kantor' => $request->alamat_kantor,
            'proyek_saat_ini' => $request->proyek_saat_ini,
            'kesesuaian_skk' => $request->kesesuaian_skk,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu_verifikasi',
        ]);

        return redirect()->route('surveillance.create')
            ->with('success', 'Laporan surveilans pemeliharaan kompetensi Anda berhasil dikirim dan tersimpan untuk diverifikasi LSP.');
    }
}
