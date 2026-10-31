<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Daftar seluruh pengaduan masuk
     */
    public function index(Request $request): View
    {
        $query = Complaint::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_number', 'LIKE', "%{$q}%")
                    ->orWhere('nama_pelapor', 'LIKE', "%{$q}%")
                    ->orWhere('judul', 'LIKE', "%{$q}%");
            });
        }

        $complaints = $query->paginate(15)->withQueryString();

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Detail dan lembar kerja tindak lanjut pengaduan
     */
    public function show(Complaint $complaint): View
    {
        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Simpan tindak lanjut & ubah status pengaduan
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:diproses,selesai,ditolak'],
            'tindak_lanjut' => ['required', 'string'],
        ]);

        $complaint->update([
            'status' => $request->status,
            'tindak_lanjut' => $request->tindak_lanjut,
            'petugas_id' => Auth::id(),
            'tgl_tindak_lanjut' => now(),
        ]);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', "Status pengaduan tiket {$complaint->ticket_number} berhasil diperbarui.");
    }
}
