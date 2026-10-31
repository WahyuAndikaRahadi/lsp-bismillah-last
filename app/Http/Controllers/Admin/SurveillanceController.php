<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveillanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    /**
     * Daftar laporan surveilans pemegang sertifikat
     */
    public function index(Request $request): View
    {
        $query = SurveillanceRecord::with(['scheme', 'verifier'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'LIKE', "%{$q}%")
                    ->orWhere('nik', 'LIKE', "%{$q}%")
                    ->orWhere('no_sertifikat', 'LIKE', "%{$q}%");
            });
        }

        $records = $query->paginate(15)->withQueryString();

        return view('admin.surveillance.index', compact('records'));
    }

    /**
     * Verifikasi laporan surveilans
     */
    public function verify(Request $request, SurveillanceRecord $surveillance): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:terverifikasi,tidak_aktif'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $surveillance->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan ?? $surveillance->keterangan,
            'verified_by' => Auth::id(),
            'tgl_verifikasi' => now(),
        ]);

        return back()->with('success', "Status surveilans untuk sertifikat {$surveillance->no_sertifikat} berhasil diperbarui.");
    }
}
