<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TukController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tuk::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_tuk', 'like', "%{$q}%")
                    ->orWhere('kode_tuk', 'like', "%{$q}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_tuk', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $tuks = $query->latest()->paginate(10)->withQueryString();

        return view('admin.master.tuks.index', compact('tuks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tuk' => ['required', 'string', 'max:50', 'unique:tuks,kode_tuk'],
            'nama_tuk' => ['required', 'string', 'max:255'],
            'jenis_tuk' => ['required', 'string', 'in:Sewaktu,Mandiri,Tempat Kerja'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Tuk::create($validated);

        return redirect()->route('admin.tuks.index')
            ->with('success', 'Tempat Uji Kompetensi (TUK) berhasil ditambahkan.');
    }

    public function update(Request $request, Tuk $tuk): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tuk' => ['required', 'string', 'max:50', 'unique:tuks,kode_tuk,'.$tuk->id],
            'nama_tuk' => ['required', 'string', 'max:255'],
            'jenis_tuk' => ['required', 'string', 'in:Sewaktu,Mandiri,Tempat Kerja'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $tuk->update($validated);

        return back()->with('success', 'Data Tempat Uji Kompetensi (TUK) berhasil diperbarui.');
    }

    public function destroy(Tuk $tuk): RedirectResponse
    {
        $tuk->delete();

        return back()->with('success', 'Tempat Uji Kompetensi (TUK) berhasil dihapus.');
    }

    public function toggleStatus(Tuk $tuk): RedirectResponse
    {
        $tuk->update([
            'is_active' => ! $tuk->is_active,
        ]);

        $statusText = $tuk->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status TUK {$tuk->nama_tuk} berhasil {$statusText}.");
    }
}
