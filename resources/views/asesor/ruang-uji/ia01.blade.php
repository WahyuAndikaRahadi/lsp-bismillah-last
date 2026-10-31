@php
    $isCompleted = (bool) $response;
@endphp
@extends('layouts.app', ['title' => 'FR.IA.01 - Ceklis Observasi'])

@section('content')
<div class="space-y-6" x-data="{
    isEditing: {{ $isCompleted ? 'false' : 'true' }},
    setAllKuk(val) {
        if (!this.isEditing) return;
        const targetVal = val.toLowerCase();
        document.querySelectorAll(`.kuk-radio-${targetVal}`).forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    },
    setUnitKuk(unitId, val) {
        if (!this.isEditing) return;
        const targetVal = val.toLowerCase();
        document.querySelectorAll(`.kuk-radio-${targetVal}[data-unit-id='${unitId}']`).forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
}">
    @include('asesor.ruang-uji.partials.header', [
        'assessment' => $assessment,
        'code' => 'ia01',
        'title' => 'FR.IA.01 – Ceklis Observasi Aktivitas di Tempat Kerja',
        'subtitle' => 'Penilaian pengamatan langsung unjuk kerja seluruh Kriteria Unjuk Kerja (KUK).',
        'isCompleted' => $isCompleted,
    ])

    <form action="{{ route('asesor.assessment.instrument.store', [$assessment->id, 'ia01']) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Single Table for All Units with Universal Fast Actions at Top --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Ceklis Observasi Unit Kompetensi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Observasi aktivitas unjuk kerja asesi pada seluruh unit kompetensi dan kriteria unjuk kerja</p>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap" x-show="isEditing">
                    <span class="text-xs font-semibold text-slate-500 mr-1 hidden sm:inline">Aksi Cepat Seluruh KUK:</span>
                    <button type="button" @click="setAllKuk('K')" class="px-3.5 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-xs transition flex items-center gap-1.5">
                        <span>✓</span>
                        <span>Semua K</span>
                    </button>
                    <button type="button" @click="setAllKuk('BK')" class="px-3.5 py-1.5 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-xs transition flex items-center gap-1.5">
                        <span>✗</span>
                        <span>Semua BK</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100 text-xs uppercase font-mono text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">No. KUK</th>
                            <th class="px-6 py-3">Pernyataan Kriteria Unjuk Kerja (KUK)</th>
                            <th class="px-6 py-3 w-28 text-center">Kompeten (K)</th>
                            <th class="px-6 py-3 w-32 text-center">Belum (BK)</th>
                            <th class="px-6 py-3 w-48">Catatan Observasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($units ?? [] as $uIdx => $unit)
                            <tr class="bg-indigo-50/70 border-t-2 border-indigo-200">
                                <td colspan="5" class="px-6 py-2.5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-indigo-700 text-white text-[10px] font-mono font-bold">UNIT {{ $uIdx + 1 }}</span>
                                            <span class="font-mono font-bold text-xs text-indigo-950">{{ $unit->code }}</span>
                                            <span class="font-bold text-xs text-slate-900">&mdash; {{ $unit->title }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0" x-show="isEditing">
                                            <span class="text-[11px] font-medium text-slate-500">Unit Ini:</span>
                                            <button type="button" @click="setUnitKuk({{ $unit->id }}, 'K')" class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded border border-emerald-300 transition">
                                                K
                                            </button>
                                            <button type="button" @click="setUnitKuk({{ $unit->id }}, 'BK')" class="px-2 py-0.5 text-[10px] font-bold bg-rose-100 hover:bg-rose-200 text-rose-800 rounded border border-rose-300 transition">
                                                BK
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @foreach($unit->elements as $element)
                                <tr class="bg-slate-50/60 font-semibold text-slate-800">
                                    <td class="px-6 py-2 text-xs font-mono text-slate-500 text-center">&bull;</td>
                                    <td colspan="4" class="px-6 py-2 text-xs font-bold text-slate-700">
                                        Elemen: {{ $element->title }}
                                    </td>
                                </tr>

                                @foreach($element->kuks as $kuk)
                                    @php
                                        $savedValue = $savedAnswers[$kuk->id]['result'] ?? null;
                                        $savedNote = $savedAnswers[$kuk->id]['note'] ?? null;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-3 font-mono text-xs text-slate-500 text-center font-bold">
                                            {{ $kuk->code_number ?? $kuk->sequence }}
                                        </td>
                                        <td class="px-6 py-3 text-slate-800 text-xs">
                                            {{ $kuk->statement ?? $kuk->title ?? 'Instruksi kerja unjuk kerja' }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="kuks[{{ $kuk->id }}][result]" value="K" data-unit-id="{{ $unit->id }}" required :disabled="!isEditing" class="kuk-radio kuk-radio-k text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer" {{ $savedValue === 'K' ? 'checked' : '' }}>
                                                <span class="ml-1.5 text-xs font-semibold text-emerald-700">K</span>
                                            </label>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="kuks[{{ $kuk->id }}][result]" value="BK" data-unit-id="{{ $unit->id }}" required :disabled="!isEditing" class="kuk-radio kuk-radio-bk text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer" {{ $savedValue === 'BK' ? 'checked' : '' }}>
                                                <span class="ml-1.5 text-xs font-semibold text-rose-700">BK</span>
                                            </label>
                                        </td>
                                        <td class="px-6 py-3">
                                            <input type="text" name="kuks[{{ $kuk->id }}][note]" value="{{ $savedNote }}" :disabled="!isEditing" placeholder="Catatan..." class="w-full px-2.5 py-1 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-sm">
                                    Tidak ada data unit/KUK untuk skema ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('asesor.ruang-uji.partials.signatures', [
            'assessment' => $assessment,
            'savedGeneralNotes' => $savedGeneralNotes,
            'savedSignature' => $savedSignature,
        ])

        @include('asesor.ruang-uji.partials.actions', [
            'assessment' => $assessment,
            'code' => 'ia01',
            'isCompleted' => $isCompleted,
            'nextCandidate' => $nextCandidate,
        ])
    </form>
</div>
@endsection
