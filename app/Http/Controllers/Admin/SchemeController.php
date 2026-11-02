<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\Scheme;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchemeController extends Controller
{
    public function index(): View
    {
        $schemes = Scheme::withCount('competencyUnits', 'questionBanks')
            ->latest()
            ->paginate(20);

        return view('admin.schemes.index', compact('schemes'));
    }

    public function show($scheme): View
    {
        if ($scheme instanceof Scheme) {
            $scheme->load('competencyUnits.elements.kuks');

            return view('admin.schemes.show', compact('scheme'));
        }

        $mScheme = Scheme::find($scheme);
        if ($mScheme) {
            $mScheme->load('competencyUnits.elements.kuks');

            return view('admin.schemes.show', ['scheme' => $mScheme]);
        }

        $certScheme = CertificationScheme::with([
            'standard',
            'requirements',
            'units.elements.criterias',
            'asesors.user',
        ])->find($scheme);

        if ($certScheme) {
            $standards = Standard::where('is_active', true)->get();

            return view('admin.master.schemes.show', ['scheme' => $certScheme, 'standards' => $standards]);
        }

        abort(404);
    }

    public function create(): View
    {
        return view('admin.schemes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('kode_skema')) {
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
            $certScheme = CertificationScheme::create($validated);

            Scheme::updateOrCreate(
                ['code' => $certScheme->kode_skema],
                [
                    'name' => $certScheme->nama_skema,
                    'scheme_type' => strtoupper($certScheme->jenis_skema),
                    'is_active' => $certScheme->is_active,
                ]
            );

            return redirect()->route('admin.schemes.show', $certScheme->id)
                ->with('success', 'Skema sertifikasi berhasil dibuat. Silakan lengkapi Unit Kompetensi dan Persyaratan Dokumen.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:m_schemes,code',
            'name' => 'required|string|max:255',
            'scheme_type' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'passing_grade_theory' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        Scheme::create($validated);

        return redirect()->route('admin.schemes.index')
            ->with('success', 'Skema berhasil dibuat.');
    }

    public function edit($scheme): View
    {
        $mScheme = $scheme instanceof Scheme ? $scheme : Scheme::find($scheme);
        if ($mScheme) {
            return view('admin.schemes.edit', ['scheme' => $mScheme]);
        }

        $certScheme = CertificationScheme::findOrFail($scheme);
        $standards = Standard::where('is_active', true)->get();

        return view('admin.master.schemes.edit', ['scheme' => $certScheme, 'standards' => $standards]);
    }

    public function update(Request $request, $scheme): RedirectResponse
    {
        $mScheme = $scheme instanceof Scheme ? $scheme : Scheme::find($scheme);
        if ($mScheme) {
            $validated = $request->validate([
                'code' => ['required', 'string', 'max:50', Rule::unique('m_schemes')->ignore($mScheme->id)],
                'name' => 'required|string|max:255',
                'scheme_type' => 'nullable|string|max:50',
                'version' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'passing_grade_theory' => 'nullable|numeric|min:0|max:100',
                'is_active' => 'boolean',
            ]);

            $mScheme->update($validated);

            return redirect()->route('admin.schemes.show', $mScheme->id)
                ->with('success', 'Skema berhasil diperbarui.');
        }

        $certScheme = CertificationScheme::findOrFail($scheme);
        $validated = $request->validate([
            'kode_skema' => ['required', 'string', 'max:50', Rule::unique('certification_schemes')->ignore($certScheme->id)],
            'nama_skema' => ['required', 'string', 'max:255'],
            'standard_id' => ['nullable', 'exists:standards,id'],
            'jenis_skema' => ['required', 'string', 'in:KKNI,Okupasi,Klaster'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'ringkasan' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $certScheme->update($validated);

        return redirect()->route('admin.schemes.show', $certScheme->id)
            ->with('success', 'Skema sertifikasi berhasil diperbarui.');
    }

    public function destroy($scheme): RedirectResponse
    {
        $mScheme = $scheme instanceof Scheme ? $scheme : Scheme::find($scheme);
        if ($mScheme) {
            if ($mScheme->schedules()->exists()) {
                $mScheme->update(['is_active' => false]);

                return redirect()->route('admin.schemes.index')
                    ->with('warning', 'Skema tidak dapat dihapus permanen karena sudah digunakan dalam jadwal asesmen. Status skema telah dinonaktifkan.');
            }

            $mScheme->delete();

            return redirect()->route('admin.schemes.index')
                ->with('success', 'Skema berhasil dihapus.');
        }

        $certScheme = CertificationScheme::findOrFail($scheme);
        if ($certScheme->schedules()->exists()) {
            $certScheme->update(['is_active' => false]);

            return redirect()->route('admin.schemes.index')
                ->with('warning', 'Skema tidak dapat dihapus permanen karena sudah digunakan dalam jadwal asesmen.');
        }

        $certScheme->delete();

        return redirect()->route('admin.schemes.index')
            ->with('success', 'Skema berhasil dihapus.');
    }
}
