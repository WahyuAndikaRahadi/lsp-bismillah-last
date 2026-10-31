@php
    $isCompleted = (bool) $response;
@endphp
@extends('layouts.app', ['title' => 'FR.IA.03 - Pertanyaan Mendukung Observasi'])

@section('content')
<div class="space-y-6" x-data="{
    isEditing: {{ $isCompleted ? 'false' : 'true' }},
    setAllQuestions(val) {
        if (!this.isEditing) return;
        const target = val.toLowerCase();
        document.querySelectorAll(`.q-radio-${target}`).forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
}">
    @include('asesor.ruang-uji.partials.header', [
        'assessment' => $assessment,
        'code' => 'ia03',
        'title' => 'FR.IA.03 – Pertanyaan Mendukung Observasi',
        'subtitle' => 'Penilaian klarifikasi teknis dan tanya jawab lisan saat proses observasi demonstrasi.',
        'isCompleted' => $isCompleted,
    ])

    <form action="{{ route('asesor.assessment.instrument.store', [$assessment->id, 'ia03']) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Oral Questions Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Pertanyaan Lisan</h2>
                    <p class="text-xs text-slate-500">Nilai tanggapan lisan asesi terhadap pertanyaan terkait unjuk kerja</p>
                </div>
                <div class="flex items-center gap-2" x-show="isEditing">
                    <button type="button" @click="setAllQuestions('M')" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Semua M (Memuaskan)
                    </button>
                    <button type="button" @click="setAllQuestions('TM')" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Semua TM
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">No</th>
                            <th class="px-6 py-3">Daftar Pertanyaan &amp; Kunci Jawaban</th>
                            <th class="px-6 py-3 text-center w-40">Hasil (Memuaskan / Tidak)</th>
                            <th class="px-6 py-3 w-64">Tanggapan / Catatan Asesi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($questions ?? [] as $i => $q)
                            @php
                                $val = $savedAnswers[$q->id]['result'] ?? old("questions.{$q->id}.result");
                                $qResponse = $savedAnswers[$q->id]['response'] ?? old("questions.{$q->id}.response");
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-3 text-slate-500 font-mono text-xs text-center font-bold">{{ $i + 1 }}</td>
                                <td class="px-6 py-3 text-slate-800">
                                    <div class="font-medium text-sm leading-relaxed">{{ $q->question }}</div>
                                    @if(!empty($q->expected_answer))
                                        <div class="text-xs text-slate-600 mt-1.5 bg-slate-50 p-2.5 rounded border border-slate-200">
                                            <span class="font-bold text-slate-700">Kunci / Jawaban yang Diharapkan:</span> {{ $q->expected_answer }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <label class="p-2 inline-flex items-center cursor-pointer">
                                            <input type="radio" name="questions[{{ $q->id }}][result]" value="M" required :disabled="!isEditing" class="q-radio q-radio-m text-emerald-600 focus:ring-emerald-500 w-5 h-5 cursor-pointer" {{ $val === 'M' ? 'checked' : '' }}>
                                            <span class="ml-1.5 text-xs font-bold text-emerald-700">M</span>
                                        </label>
                                        <label class="p-2 inline-flex items-center cursor-pointer">
                                            <input type="radio" name="questions[{{ $q->id }}][result]" value="TM" required :disabled="!isEditing" class="q-radio q-radio-tm text-rose-600 focus:ring-rose-500 w-5 h-5 cursor-pointer" {{ $val === 'TM' ? 'checked' : '' }}>
                                            <span class="ml-1.5 text-xs font-bold text-rose-700">TM</span>
                                        </label>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">M: Memuaskan | TM: Tidak</div>
                                </td>
                                <td class="px-6 py-3">
                                    <textarea name="questions[{{ $q->id }}][response]" rows="2" :disabled="!isEditing" placeholder="Catatan tanggapan asesi..." class="w-full px-2.5 py-1 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-brand-500 focus:outline-none">{{ $qResponse }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-slate-500 text-xs">Belum ada daftar pertanyaan lisan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('asesor.ruang-uji.partials.signatures', [
            'assessment' => $assessment,
            'savedGeneralNotes' => $savedGeneralNotes ?? $savedNotes,
            'savedSignature' => $savedSignature,
        ])

        @include('asesor.ruang-uji.partials.actions', [
            'assessment' => $assessment,
            'code' => 'ia03',
            'isCompleted' => $isCompleted,
            'nextCandidate' => $nextCandidate,
        ])
    </form>
</div>
@endsection
