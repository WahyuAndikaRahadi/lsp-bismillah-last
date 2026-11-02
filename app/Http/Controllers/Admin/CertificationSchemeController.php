<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\SchemeRequirement;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificationSchemeController extends Controller
{
    public function index(Request $request): View
    {
        $query = CertificationScheme::query()->with('standard')->withCount(['units', 'requirements']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_skema', 'like', "%{$q}%")
                    ->orWhere('nama_skema', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_skema', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $schemes = $query->latest()->paginate(10)->withQueryString();
        $standards = Standard::where('is_active', true)->get();

        return view('admin.master.schemes.index', compact('schemes', 'standards'));
    }

    public function create(): View
    {
        $standards = Standard::where('is_active', true)->get();

        return view('admin.master.schemes.create', compact('standards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_skema' => ['required', 'string', 'max:50', 'unique:certification_schemes,kode_skema'],
            'nama_skema' => ['required', 'string', 'max:255'],
            'standard_id' => ['nullable', 'exists:standards,id'],
            'jenis_skema' => ['required', 'string', 'in:KKNI,Okupasi,Klaster'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'ringkasan' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $scheme = CertificationScheme::create($validated);

        return redirect()->route('admin.schemes.show', $scheme)
            ->with('success', 'Skema sertifikasi berhasil dibuat. Silakan lengkapi Unit Kompetensi dan Persyaratan Dokumen.');
    }

    public function show(CertificationScheme $scheme): View
    {
        $scheme->load([
            'standard',
            'requirements',
            'units.elements.criterias',
            'asesors.user',
        ]);

        $standards = Standard::where('is_active', true)->get();

        return view('admin.master.schemes.show', compact('scheme', 'standards'));
    }

    public function edit(CertificationScheme $scheme): View
    {
        $standards = Standard::where('is_active', true)->get();

        return view('admin.master.schemes.edit', compact('scheme', 'standards'));
    }

    public function update(Request $request, CertificationScheme $scheme): RedirectResponse
    {
        $validated = $request->validate([
            'kode_skema' => ['required', 'string', 'max:50', 'unique:certification_schemes,kode_skema,'.$scheme->id],
            'nama_skema' => ['required', 'string', 'max:255'],
            'standard_id' => ['nullable', 'exists:standards,id'],
            'jenis_skema' => ['required', 'string', 'in:KKNI,Okupasi,Klaster'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'ringkasan' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $scheme->update($validated);

        return redirect()->route('admin.schemes.show', $scheme)
            ->with('success', 'Informasi skema sertifikasi berhasil diperbarui.');
    }

    public function destroy(CertificationScheme $scheme): RedirectResponse
    {
        $scheme->delete();

        return redirect()->route('admin.schemes.index')
            ->with('success', 'Skema sertifikasi berhasil dihapus.');
    }

    public function storeRequirement(Request $request, CertificationScheme $scheme): RedirectResponse
    {
        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);
        $validated['urutan'] = $scheme->requirements()->count() + 1;

        $scheme->requirements()->create($validated);

        return back()->with('success', 'Persyaratan dokumen berhasil ditambahkan.');
    }

    public function destroyRequirement(SchemeRequirement $requirement): RedirectResponse
    {
        $requirement->delete();

        return back()->with('success', 'Persyaratan dokumen berhasil dihapus.');
    }
}
