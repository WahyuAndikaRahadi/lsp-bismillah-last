<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Tampilan form pengaduan masyarakat publik
     */
    public function create(): View
    {
        return view('public.pengaduan');
    }

    /**
     * Simpan pengaduan baru & terbitkan nomor tiket bertanggal
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'jenis_responden' => ['required', 'string', 'in:Asesi,Asesor,Instansi,TUK,Umum'],
            'judul' => ['required', 'string', 'max:255'],
            'isi_aduan' => ['required', 'string'],
        ]);

        $ticket = Complaint::generateTicketNumber();

        $complaint = Complaint::create([
            'ticket_number' => $ticket,
            'nama_pelapor' => $request->nama_pelapor,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'jenis_responden' => $request->jenis_responden,
            'judul' => $request->judul,
            'isi_aduan' => $request->isi_aduan,
            'status' => 'diproses',
        ]);

        return redirect()->route('pengaduan.create')
            ->with('ticket_success', [
                'ticket_number' => $ticket,
                'nama' => $complaint->nama_pelapor,
            ]);
    }

    /**
     * Pelacakan status tiket pengaduan
     */
    public function tracking(Request $request): View
    {
        $complaint = null;
        if ($request->filled('ticket')) {
            $complaint = Complaint::where('ticket_number', trim($request->ticket))->first();
        }

        return view('public.pengaduan_track', compact('complaint'));
    }
}
