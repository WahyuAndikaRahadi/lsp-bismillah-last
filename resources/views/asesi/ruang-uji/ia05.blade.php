@extends('layouts.app')

@section('title', 'FR.IA.05 - Ujian Teori CBT Pilihan Ganda')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between">
        <div>
            <a href="{{ route('asesi.assessment.ruang-uji', $assessment) }}" class="text-xs text-indigo-600 hover:underline font-medium block mb-1">
                ← Kembali ke Ruang Uji
            </a>
            <h1 class="text-xl font-bold text-slate-800">FR.IA.05. PERTANYAAN TERTULIS (CBT)</h1>
            <p class="text-xs text-slate-500 mt-1">Skema: {{ $assessment->schedule->scheme->name }}</p>
        </div>

        @php
            $existingResp = $assessment->iaResponses->firstWhere('instrument_code', 'IA05');
        @endphp

        @if($existingResp)
            <div class="text-right">
                <span class="text-xs font-semibold text-slate-500 block">Skor CBT Anda</span>
                <span class="text-2xl font-black {{ $existingResp->score >= 75 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $existingResp->score }}%
                </span>
                <span class="text-xs block font-bold {{ $existingResp->score >= 75 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $existingResp->score >= 75 ? 'KOMPETEN' : 'BELUM KOMPETEN' }}
                </span>
            </div>
        @endif
    </div>

    @if($questions->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center text-amber-800 text-sm">
            Belum ada bank soal yang diinput untuk skema ini. Silakan hubungi asesor/admin.
        </div>
    @else
        <form action="{{ route('asesi.assessment.instrument.ia05.submit', $assessment) }}" method="POST" class="space-y-6" id="cbtForm">
            @csrf

            @php
                $savedAnswers = $existingResp?->data_json ?? [];
            @endphp

            @foreach($questions as $index => $q)
                @php
                    $ans = $savedAnswers[$q->id] ?? null;
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-grow">
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed">{{ $q->question_text }}</p>

                            @if($q->question_image)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $q->question_image) }}" alt="Soal Gambar" class="max-h-60 rounded border border-slate-200">
                                </div>
                            @endif

                            <div class="mt-4 space-y-2.5">
                                @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d] as $optKey => $optText)
                                    @if($optText)
                                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optKey }}" class="text-indigo-600 focus:ring-indigo-500 w-4 h-4" @checked($ans === $optKey) @disabled($existingResp !== null)>
                                            <span class="font-bold text-xs text-slate-700 w-4">{{ $optKey }}.</span>
                                            <span class="text-xs text-slate-700">{{ $optText }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(! $existingResp)
                <div class="p-6 bg-white rounded-xl border border-slate-200 flex items-center justify-between shadow-sm">
                    <span class="text-xs text-slate-500">Periksa seluruh jawaban sebelum mengirim. Sistem akan langsung mengoreksi.</span>
                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan ujian CBT ini?')">
                        Kirim Jawaban CBT ✓
                    </button>
                </div>
            @else
                <div class="p-4 bg-slate-100 rounded-xl text-center text-xs text-slate-600">
                    Ujian ini telah selesai dikerjakan pada {{ $existingResp->asesi_signed_at ? \Carbon\Carbon::parse($existingResp->asesi_signed_at)->format('d/m/Y H:i') : '-' }}.
                </div>
            @endif
        </form>
    @endif
</div>
@endsection
