@php
    $isCompleted = (bool) $response;
@endphp
@extends('layouts.app', ['title' => 'FR.IA.01B - Lembar Pemeriksaan Teknis (Report Sheet)'])

@section('content')
<div class="space-y-6" x-data="{
    isEditing: {{ $isCompleted ? 'false' : 'true' }},
    setAllItems(val) {
        if (!this.isEditing) return;
        const targetVal = (val === 'K' || val === 'SESUAI') ? 'sesuai' : 'tidak';
        document.querySelectorAll(`.item-radio-${targetVal}`).forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
}">
    @include('asesor.ruang-uji.partials.header', [
        'assessment' => $assessment,
        'code' => 'ia01b',
        'title' => 'FR.IA.01B – Lembar Hasil Pengukuran & Pemeriksaan Teknis (Report Sheet)',
        'subtitle' => 'Verifikasi parameter fisik komponen spesifik skema uji.',
        'isCompleted' => $isCompleted,
    ])

    <form action="{{ route('asesor.assessment.instrument.store', [$assessment->id, 'ia01b']) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Single Table for All Units with Universal Fast Actions at Top --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Item Pemeriksaan Teknis &amp; Pengukuran Komponen</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catat nilai hasil pengukuran asesi dan tentukan kesesuaian terhadap standar spesifikasi servis</p>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap" x-show="isEditing">
                    <span class="text-xs font-semibold text-slate-500 mr-1 hidden sm:inline">Aksi Cepat Seluruh Item:</span>
                    <button type="button" @click="setAllItems('SESUAI')" class="px-3.5 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-xs transition flex items-center gap-1.5">
                        <span>✓</span>
                        <span>Semua Sesuai</span>
                    </button>
                    <button type="button" @click="setAllItems('TIDAK')" class="px-3.5 py-1.5 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-xs transition flex items-center gap-1.5">
                        <span>✗</span>
                        <span>Semua Tidak</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100 text-xs uppercase font-mono text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">No</th>
                            <th class="px-6 py-3">Komponen / Item Pemeriksaan</th>
                            <th class="px-6 py-3 w-44">Standar Spesifikasi</th>
                            <th class="px-6 py-3 w-48">Hasil Pengukuran Asesi</th>
                            <th class="px-6 py-3 w-44 text-center">Hasil (Sesuai / Tidak)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reportSheetItems ?? [] as $idx => $item)
                            @php
                                $val = $savedAnswers[$idx]['result'] ?? null;
                                $measured = $savedAnswers[$idx]['measured'] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-3 text-slate-500 font-mono text-xs text-center font-bold">{{ $idx + 1 }}</td>
                                <td class="px-6 py-3 text-slate-800 font-medium">
                                    {{ $item['item'] }}
                                </td>
                                <td class="px-6 py-3 text-slate-600 text-xs">
                                    <span class="bg-slate-100 px-2.5 py-1 rounded border border-slate-200 inline-block font-mono">
                                        {{ $item['standard'] ?? 'Workshop Manual' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" name="items[{{ $idx }}][measured]" value="{{ $measured }}" :disabled="!isEditing" placeholder="Nilai / kondisi..." class="w-full px-2.5 py-1 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" name="items[{{ $idx }}][result]" value="SESUAI" required :disabled="!isEditing" class="item-radio item-radio-sesuai text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer" {{ $val === 'SESUAI' ? 'checked' : '' }}>
                                            <span class="ml-1 text-xs font-semibold text-emerald-700">Sesuai</span>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" name="items[{{ $idx }}][result]" value="TIDAK" required :disabled="!isEditing" class="item-radio item-radio-tidak text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer" {{ $val === 'TIDAK' ? 'checked' : '' }}>
                                            <span class="ml-1 text-xs font-semibold text-rose-700">Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-sm">Tidak ada item lembar ukur khusus untuk skema ini.</td>
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
            'code' => 'ia01b',
            'isCompleted' => $isCompleted,
            'nextCandidate' => $nextCandidate,
        ])
    </form>
</div>
@endsection
