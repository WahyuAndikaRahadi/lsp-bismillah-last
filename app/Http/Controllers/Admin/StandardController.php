<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandardController extends Controller
{
    public function index(Request $request): View
    {
        $query = Standard::query()->withCount('schemes');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_standar', 'like', "%{$q}%")
                    ->orWhere('judul_standar', 'like', "%{$q}%")
                    ->orWhere('nomor_sk', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_standar', $request->jenis);
        }

        $standards = $query->latest()->paginate(10)->withQueryString();

        return view('admin.master.standards.index', compact('standards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_standar' => ['required', 'string', 'max:100', 'unique:standards,kode_standar'],
            'judul_standar' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['required', 'string', 'in:SKKNI,Khusus,Internasional'],
            'nomor_sk' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:1990', 'max:2099'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Standard::create($validated);

        return redirect()->route('admin.standards.index')
            ->with('success', 'Standar kompetensi acuan berhasil ditambahkan.');
    }

    public function update(Request $request, Standard $standard): RedirectResponse
    {
        $validated = $request->validate([
            'kode_standar' => ['required', 'string', 'max:100', 'unique:standards,kode_standar,' . $standard->id],
            'judul_standar' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['required', 'string', 'in:SKKNI,Khusus,Internasional'],
            'nomor_sk' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:1990', 'max:2099'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $standard->update($validated);

        return back()->with('success', 'Standar kompetensi acuan berhasil diperbarui.');
    }

    public function destroy(Standard $standard): RedirectResponse
    {
        if ($standard->schemes()->exists()) {
            return back()->with('error', 'Standar ini tidak dapat dihapus karena masih digunakan oleh skema sertifikasi aktif.');
        }

        $standard->delete();

        return back()->with('success', 'Standar kompetensi berhasil dihapus.');
    }
}
