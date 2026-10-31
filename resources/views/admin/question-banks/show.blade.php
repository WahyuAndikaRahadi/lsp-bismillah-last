@extends('layouts.app', ['title' => 'Detail Soal CBT'])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.question-banks.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Bank Soal</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Detail Butir Soal CBT</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-500 mt-1">
                <span>Skema: <strong>{{ $questionBank->scheme->name ?? '-' }}</strong></span>
                <span>&bull;</span>
                <span>Unit: {{ $questionBank->competencyUnit?->code ?? 'Umum' }}</span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.question-banks.edit', $questionBank->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                Edit Soal
            </a>
        </div>
    </div>

    {{-- Question Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pertanyaan:</span>
            <p class="text-base font-semibold text-slate-900 leading-relaxed">{{ $questionBank->question_text }}</p>
        </div>

        <div class="pt-4 border-t border-slate-100 space-y-2.5">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pilihan Jawaban:</span>

            @foreach(['A' => $questionBank->option_a, 'B' => $questionBank->option_b, 'C' => $questionBank->option_c, 'D' => $questionBank->option_d] as $key => $opt)
                @php
                    $isCorrect = ($questionBank->correct_answer === $key);
                @endphp
                <div class="p-3 rounded-lg border text-sm flex items-center justify-between {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-950 font-bold' : 'bg-slate-50/70 border-slate-200 text-slate-700' }}">
                    <div class="flex items-center space-x-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $key }}</span>
                        <span>{{ $opt }}</span>
                    </div>
                    @if($isCorrect)
                        <span class="px-2 py-0.5 text-xs rounded bg-emerald-200 text-emerald-900 font-bold uppercase tracking-wider">Kunci Benar</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
