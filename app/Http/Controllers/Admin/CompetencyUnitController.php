<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\WorkKeyCriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompetencyUnitController extends Controller
{
    public function store(Request $request, CertificationScheme $scheme): RedirectResponse
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50'],
            'judul_unit' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['required', 'string', 'max:50'],
        ]);

        $validated['urutan'] = $scheme->units()->count() + 1;

        $scheme->units()->create($validated);

        return back()->with('success', 'Unit kompetensi berhasil ditambahkan ke skema.');
    }

    public function update(Request $request, CompetencyUnit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50'],
            'judul_unit' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['required', 'string', 'max:50'],
        ]);

        $unit->update($validated);

        return back()->with('success', 'Unit kompetensi berhasil diperbarui.');
    }

    public function destroy(CompetencyUnit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('success', 'Unit kompetensi berhasil dihapus.');
    }

    public function storeElement(Request $request, CompetencyUnit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'nama_elemen' => ['required', 'string'],
        ]);

        $nextNumber = $unit->elements()->count() + 1;
        $validated['nomor_elemen'] = $nextNumber;
        $validated['urutan'] = $nextNumber;

        $unit->elements()->create($validated);

        return back()->with('success', 'Elemen kompetensi berhasil ditambahkan.');
    }

    public function destroyElement(CompetencyElement $element): RedirectResponse
    {
        $element->delete();

        return back()->with('success', 'Elemen kompetensi berhasil dihapus.');
    }

    public function storeCriteria(Request $request, CompetencyElement $element): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_kuk' => ['required', 'string', 'max:20'],
            'pernyataan' => ['required', 'string'],
            'pertanyaan_observasi' => ['nullable', 'string'],
        ]);

        $validated['urutan'] = $element->criterias()->count() + 1;

        $element->criterias()->create($validated);

        return back()->with('success', 'Kriteria Unjuk Kerja (KUK) berhasil ditambahkan.');
    }

    public function destroyCriteria(WorkKeyCriteria $criteria): RedirectResponse
    {
        $criteria->delete();

        return back()->with('success', 'Kriteria Unjuk Kerja (KUK) berhasil dihapus.');
    }
}
