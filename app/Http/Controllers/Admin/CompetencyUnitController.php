<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\Scheme;
use App\Models\UnitElement;
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

        $legacyUnit = $scheme->units()->create($validated);

        $mScheme = Scheme::where('id', $scheme->id)->orWhere('code', $scheme->kode_skema)->first();
        if ($mScheme) {
            CompetencyUnit::updateOrCreate(
                ['id' => $legacyUnit->id],
                [
                    'scheme_id' => $mScheme->id,
                    'code' => $validated['kode_unit'],
                    'title' => $validated['judul_unit'],
                    'standard_reference' => $validated['jenis_standar'],
                    'sequence' => $validated['urutan'],
                    'kode_unit' => $validated['kode_unit'],
                    'judul_unit' => $validated['judul_unit'],
                    'jenis_standar' => $validated['jenis_standar'],
                    'urutan' => $validated['urutan'],
                ]
            );
        }

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

        $nextNumber = CompetencyElement::where('unit_id', $unit->id)->count() + 1;
        $validated['unit_id'] = $unit->id;
        $validated['nomor_elemen'] = $nextNumber;
        $validated['urutan'] = $nextNumber;

        $element = CompetencyElement::create($validated);

        UnitElement::updateOrCreate(
            ['id' => $element->id],
            [
                'competency_unit_id' => $unit->id,
                'title' => $validated['nama_elemen'],
                'sequence' => $nextNumber,
            ]
        );

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
