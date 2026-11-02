<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\CompetencyElement;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\WorkKeyCriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompetencyUnitController extends Controller
{
    public function store(Request $request, $scheme): RedirectResponse
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50'],
            'judul_unit' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['nullable', 'string', 'max:100'],
        ]);

        $mScheme = $scheme instanceof Scheme ? $scheme : Scheme::find($scheme);
        $certScheme = $scheme instanceof CertificationScheme ? $scheme : CertificationScheme::find($scheme);

        if (! $mScheme && $certScheme) {
            $mScheme = Scheme::where('code', $certScheme->kode_skema)->first();
        }
        if (! $certScheme && $mScheme) {
            $certScheme = CertificationScheme::where('kode_skema', $mScheme->code)->first();
        }

        $standard = $validated['jenis_standar'] ?? 'SKKNI';

        if ($mScheme) {
            $nextUrutan = CompetencyUnit::where('scheme_id', $mScheme->id)->count() + 1;
            CompetencyUnit::create([
                'scheme_id' => $mScheme->id,
                'code' => $validated['kode_unit'],
                'title' => $validated['judul_unit'],
                'standard_reference' => $standard,
                'sequence' => $nextUrutan,
                'kode_unit' => $validated['kode_unit'],
                'judul_unit' => $validated['judul_unit'],
                'jenis_standar' => $standard,
                'urutan' => $nextUrutan,
            ]);
        }

        if ($certScheme) {
            $nextUrutanLegacy = $certScheme->units()->count() + 1;
            $certScheme->units()->create([
                'kode_unit' => $validated['kode_unit'],
                'judul_unit' => $validated['judul_unit'],
                'jenis_standar' => $standard,
                'urutan' => $nextUrutanLegacy,
            ]);
        }

        return back()->with('success', 'Unit kompetensi berhasil ditambahkan ke skema.');
    }

    public function update(Request $request, $unit): RedirectResponse
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50'],
            'judul_unit' => ['required', 'string', 'max:255'],
            'jenis_standar' => ['nullable', 'string', 'max:100'],
            'urutan' => ['nullable', 'integer'],
        ]);

        $mUnit = $unit instanceof CompetencyUnit ? $unit : CompetencyUnit::find($unit);
        $standard = $validated['jenis_standar'] ?? ($mUnit?->standard_reference ?? 'SKKNI');

        if ($mUnit) {
            $mUnit->update([
                'code' => $validated['kode_unit'],
                'title' => $validated['judul_unit'],
                'standard_reference' => $standard,
                'sequence' => $validated['urutan'] ?? $mUnit->sequence,
                'kode_unit' => $validated['kode_unit'],
                'judul_unit' => $validated['judul_unit'],
                'jenis_standar' => $standard,
                'urutan' => $validated['urutan'] ?? $mUnit->sequence,
            ]);
        }

        $unitId = is_object($unit) ? $unit->id : $unit;
        if (Schema::hasTable('competency_units')) {
            DB::table('competency_units')->where('id', $unitId)->update([
                'kode_unit' => $validated['kode_unit'],
                'judul_unit' => $validated['judul_unit'],
                'jenis_standar' => $standard,
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Unit kompetensi berhasil diperbarui.');
    }

    public function destroy($unit): RedirectResponse
    {
        $mUnit = $unit instanceof CompetencyUnit ? $unit : CompetencyUnit::find($unit);
        if ($mUnit) {
            $mUnit->delete();
        }

        $unitId = is_object($unit) ? $unit->id : $unit;
        if (Schema::hasTable('competency_units')) {
            DB::table('competency_units')->where('id', $unitId)->delete();
        }

        return back()->with('success', 'Unit kompetensi berhasil dihapus.');
    }

    public function storeElement(Request $request, $unit): RedirectResponse
    {
        $validated = $request->validate([
            'nama_elemen' => ['required', 'string', 'max:255'],
        ]);

        $mUnit = $unit instanceof CompetencyUnit ? $unit : CompetencyUnit::find($unit);

        if ($mUnit) {
            $nextSeq = UnitElement::where('competency_unit_id', $mUnit->id)->count() + 1;
            $elem = UnitElement::create([
                'competency_unit_id' => $mUnit->id,
                'title' => $validated['nama_elemen'],
                'sequence' => $nextSeq,
            ]);

            if (Schema::hasTable('competency_elements')) {
                $legacyUnitExists = DB::table('competency_units')->where('id', $mUnit->id)->exists();
                if ($legacyUnitExists) {
                    DB::table('competency_elements')->insertOrIgnore([
                        'id' => $elem->id,
                        'unit_id' => $mUnit->id,
                        'nomor_elemen' => $nextSeq,
                        'nama_elemen' => $validated['nama_elemen'],
                        'urutan' => $nextSeq,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } else {
            $unitId = is_object($unit) ? $unit->id : $unit;
            $legacyUnit = DB::table('competency_units')->where('id', $unitId)->first();
            if ($legacyUnit) {
                $nextSeq = DB::table('competency_elements')->where('unit_id', $legacyUnit->id)->count() + 1;
                CompetencyElement::create([
                    'unit_id' => $legacyUnit->id,
                    'nomor_elemen' => $nextSeq,
                    'nama_elemen' => $validated['nama_elemen'],
                    'urutan' => $nextSeq,
                ]);
            }
        }

        return back()->with('success', 'Elemen kompetensi berhasil ditambahkan.');
    }

    public function updateElement(Request $request, $element): RedirectResponse
    {
        $validated = $request->validate([
            'nama_elemen' => ['required', 'string', 'max:255'],
        ]);

        $mElem = $element instanceof UnitElement ? $element : UnitElement::find($element);
        if ($mElem) {
            $mElem->update([
                'title' => $validated['nama_elemen'],
            ]);
        }

        $elemId = is_object($element) ? $element->id : $element;
        if (Schema::hasTable('competency_elements')) {
            DB::table('competency_elements')->where('id', $elemId)->update([
                'nama_elemen' => $validated['nama_elemen'],
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Elemen kompetensi berhasil diperbarui.');
    }

    public function destroyElement($element): RedirectResponse
    {
        $mElem = $element instanceof UnitElement ? $element : UnitElement::find($element);
        if ($mElem) {
            $mElem->delete();
        }

        $elemId = is_object($element) ? $element->id : $element;
        if (Schema::hasTable('competency_elements')) {
            DB::table('competency_elements')->where('id', $elemId)->delete();
        }

        return back()->with('success', 'Elemen kompetensi berhasil dihapus.');
    }

    public function storeCriteria(Request $request, $element): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_kuk' => ['required', 'string', 'max:20'],
            'pernyataan' => ['required', 'string'],
            'pertanyaan_observasi' => ['nullable', 'string'],
        ]);

        $mElem = $element instanceof UnitElement ? $element : UnitElement::find($element);

        if ($mElem) {
            $nextSeq = Kuk::where('unit_element_id', $mElem->id)->count() + 1;
            $kuk = Kuk::create([
                'unit_element_id' => $mElem->id,
                'code_number' => $validated['nomor_kuk'],
                'statement' => $validated['pernyataan'],
                'sequence' => $nextSeq,
            ]);

            if (Schema::hasTable('work_key_criterias')) {
                $legacyElemExists = DB::table('competency_elements')->where('id', $mElem->id)->exists();
                if ($legacyElemExists) {
                    DB::table('work_key_criterias')->insertOrIgnore([
                        'id' => $kuk->id,
                        'element_id' => $mElem->id,
                        'nomor_kuk' => $validated['nomor_kuk'],
                        'pernyataan' => $validated['pernyataan'],
                        'pertanyaan_observasi' => $validated['pertanyaan_observasi'] ?? null,
                        'urutan' => $nextSeq,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } else {
            $elemId = is_object($element) ? $element->id : $element;
            $legacyElem = CompetencyElement::find($elemId);
            if ($legacyElem) {
                $nextSeq = $legacyElem->criterias()->count() + 1;
                $validated['urutan'] = $nextSeq;
                $legacyElem->criterias()->create($validated);
            }
        }

        return back()->with('success', 'Kriteria Unjuk Kerja (KUK) berhasil ditambahkan.');
    }

    public function updateCriteria(Request $request, $criteria): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_kuk' => ['required', 'string', 'max:20'],
            'pernyataan' => ['required', 'string'],
        ]);

        $mKuk = $criteria instanceof Kuk ? $criteria : Kuk::find($criteria);
        if ($mKuk) {
            $mKuk->update([
                'code_number' => $validated['nomor_kuk'],
                'statement' => $validated['pernyataan'],
            ]);
        }

        $critId = is_object($criteria) ? $criteria->id : $criteria;
        if (Schema::hasTable('work_key_criterias')) {
            DB::table('work_key_criterias')->where('id', $critId)->update([
                'nomor_kuk' => $validated['nomor_kuk'],
                'pernyataan' => $validated['pernyataan'],
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Kriteria Unjuk Kerja (KUK) berhasil diperbarui.');
    }

    public function destroyCriteria($criteria): RedirectResponse
    {
        $mKuk = $criteria instanceof Kuk ? $criteria : Kuk::find($criteria);
        if ($mKuk) {
            $mKuk->delete();
        }

        $critId = is_object($criteria) ? $criteria->id : $criteria;
        if (Schema::hasTable('work_key_criterias')) {
            DB::table('work_key_criterias')->where('id', $critId)->delete();
        }

        return back()->with('success', 'Kriteria Unjuk Kerja (KUK) berhasil dihapus.');
    }
}
