@extends('layouts.app', ['title' => 'FR.IA.05 - Hasil CBT'])

@section('content')
<div class="space-y-6">
    @include('asesor.ruang-uji.partials.header', [
        'assessment' => $assessment,
        'code' => 'ia05',
        'title' => 'FR.IA.05 – Hasil Ujian Tertulis Pilihan Ganda (CBT)',
        'subtitle' => 'Hasil penilaian otomatis ujian tertulis pilihan ganda (CBT) asesi.',
        'isCompleted' => true,
    ])

    {{-- Score Overview Card --}}
    @php
        $totalQuestions = count($breakdown ?? []);
        $correctAnswers = collect($breakdown ?? [])->where('is_correct', true)->count();
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : ($assessment->ia05_score ?? 0);
        $passingGrade = (float) ($assessment->schedule?->scheme?->passing_grade_theory ?? 70.0);
        $isPassed = $score >= $passingGrade;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg {{ $isPassed ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nilai Akhir CBT</p>
                <h3 class="text-2xl font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5">{{ $score }}%</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jawaban Benar</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-0.5">{{ $correctAnswers }} / {{ $totalQuestions }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg {{ $isPassed ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
                <span class="font-bold text-lg">{{ $isPassed ? 'K' : 'BK' }}</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Kelulusan</p>
                <h3 class="text-base font-bold {{ $isPassed ? 'text-emerald-700' : 'text-rose-700' }} mt-0.5">
                    {{ $isPassed ? 'Kompeten' : 'Belum Kompeten' }}
                </h3>
            </div>
        </div>
    </div>

    {{-- Answer Breakdown Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Rincian Jawaban Soal CBT</h2>
            <p class="text-xs text-slate-500">Rincian pilihan asesi per butir soal</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">Pertanyaan</th>
                        <th class="px-6 py-3 text-center w-28">Jawaban Asesi</th>
                        <th class="px-6 py-3 text-center w-28">Kunci Jawaban</th>
                        <th class="px-6 py-3 text-center w-28">Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($breakdown ?? [] as $i => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 text-slate-800">
                                <div class="font-medium text-sm">{{ $item->question ?? $item['question'] }}</div>
                            </td>
                            <td class="px-6 py-3 text-center font-mono font-bold text-xs">
                                {{ $item->user_answer ?? $item['user_answer'] ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-center font-mono font-bold text-xs text-slate-600">
                                {{ $item->correct_answer ?? $item['correct_answer'] ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($item->is_correct ?? $item['is_correct'] ?? false)
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Benar</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Salah</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-slate-500 text-xs">Belum ada rincian jawaban CBT.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <a href="{{ route('asesor.schedule.instrument', ['schedule' => $assessment->schedule_id, 'code' => 'ia05']) }}" class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition text-center">
            &larr; Kembali ke Daftar Asesi
        </a>
        @if($nextCandidate)
            <a href="{{ route('asesor.assessment.instrument', [$nextCandidate->id, 'ia05']) }}" class="px-5 py-2.5 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-xs transition flex items-center gap-1.5">
                <span>Lihat Asesi Berikutnya ({{ $nextCandidate->full_name }})</span>
                <span>&rarr;</span>
            </a>
        @endif
    </div>
</div>
@endsection
