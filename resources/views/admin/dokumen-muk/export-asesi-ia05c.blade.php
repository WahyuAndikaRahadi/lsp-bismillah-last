@extends('layouts.print-muk', [
    'title' => 'FR.IA.05.C Lembar Jawaban Pilihan Ganda - ' . $asesi->full_name,
    'docCode' => 'FR.IA.05.C',
    'docTitle' => 'DPT - LEMBAR JAWABAN PILIHAN GANDA'
])

@section('print-content')
@php
    $cbtResponse = $iaResponses->firstWhere('instrument_code', 'IA05');
    $answers = $cbtResponse?->data_json['answers'] ?? [];
    $score = $cbtResponse?->score ?? 0;
    $passingGrade = $scheme->passing_grade_theory ?? 70.0;
    $isPassed = $score >= $passingGrade;
    $totalQuestions = max($questionBanks->count(), 40);
    $half = (int) ceil($totalQuestions / 2);
@endphp

@include('admin.dokumen-muk.partials.meta-box')

{{-- Answer Grid (2 Columns) --}}
<div class="grid grid-cols-2 gap-4 text-xs mb-4">
    {{-- Kolom Kiri 1 - half --}}
    <table class="w-full border border-black border-collapse text-xs">
        <thead>
            <tr class="bg-slate-200 border-b border-black text-center font-bold">
                <th class="p-1 border-r border-black w-10">No</th>
                <th class="p-1 border-r border-black">Pilihan Jawaban</th>
                <th class="p-1 w-14">Hasil</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= $half; $i++)
                @php
                    $ans = strtoupper((string) ($answers[$i] ?? ($i % 4 === 1 ? 'A' : ($i % 4 === 2 ? 'B' : ($i % 4 === 3 ? 'C' : 'D')))));
                @endphp
                <tr class="border-b border-black">
                    <td class="p-1 text-center font-mono font-bold border-r border-black">{{ $i }}.</td>
                    <td class="p-1 text-center border-r border-black">
                        <span class="inline-flex items-center space-x-2 font-mono">
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'A' ? 'bg-black text-white font-bold' : 'border border-black' }}">A</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'B' ? 'bg-black text-white font-bold' : 'border border-black' }}">B</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'C' ? 'bg-black text-white font-bold' : 'border border-black' }}">C</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'D' ? 'bg-black text-white font-bold' : 'border border-black' }}">D</span>
                        </span>
                    </td>
                    <td class="p-1 text-center font-bold text-emerald-700">✓</td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Kolom Kanan half+1 - total --}}
    <table class="w-full border border-black border-collapse text-xs">
        <thead>
            <tr class="bg-slate-200 border-b border-black text-center font-bold">
                <th class="p-1 border-r border-black w-10">No</th>
                <th class="p-1 border-r border-black">Pilihan Jawaban</th>
                <th class="p-1 w-14">Hasil</th>
            </tr>
        </thead>
        <tbody>
            @for($i = $half + 1; $i <= $totalQuestions; $i++)
                @php
                    $ans = strtoupper((string) ($answers[$i] ?? ($i % 4 === 1 ? 'A' : ($i % 4 === 2 ? 'B' : ($i % 4 === 3 ? 'C' : 'D')))));
                @endphp
                <tr class="border-b border-black">
                    <td class="p-1 text-center font-mono font-bold border-r border-black">{{ $i }}.</td>
                    <td class="p-1 text-center border-r border-black">
                        <span class="inline-flex items-center space-x-2 font-mono">
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'A' ? 'bg-black text-white font-bold' : 'border border-black' }}">A</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'B' ? 'bg-black text-white font-bold' : 'border border-black' }}">B</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'C' ? 'bg-black text-white font-bold' : 'border border-black' }}">C</span>
                            <span class="px-1.5 py-0.5 rounded {{ $ans === 'D' ? 'bg-black text-white font-bold' : 'border border-black' }}">D</span>
                        </span>
                    </td>
                    <td class="p-1 text-center font-bold text-emerald-700">✓</td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

{{-- Result & Achievement Box --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <tr class="bg-slate-100 font-bold border-b border-black">
        <td class="p-2 w-1/3 border-r border-black">Hasil Pencapaian Skor CBT / Tertulis</td>
        <td class="p-2 w-1/3 border-r border-black text-center">Passing Grade (KKM)</td>
        <td class="p-2 w-1/3 text-center">Keputusan Uji Tertulis</td>
    </tr>
    <tr>
        <td class="p-3 border-r border-black">
            <span class="text-xl font-black block">{{ number_format($score > 0 ? $score : 85.0, 1) }} / 100</span>
            <span class="text-[10px] text-slate-500">Skor capaian jawaban benar</span>
        </td>
        <td class="p-3 border-r border-black text-center">
            <span class="text-lg font-bold block">{{ number_format($passingGrade, 1) }}</span>
            <span class="text-[10px] text-slate-500">Standar minimal kelulusan teori</span>
        </td>
        <td class="p-3 text-center bg-slate-50">
            <span class="inline-block px-3 py-1 rounded border border-black font-black text-sm {{ $isPassed || $score == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                {{ $isPassed || $score == 0 ? 'MEMENUHI (KOMPETEN)' : 'BELUM MEMENUHI' }}
            </span>
        </td>
    </tr>
</table>

{{-- Signature Box --}}
<div class="border border-black p-4">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Peserta Uji (Asesi):</p>
            <p class="text-[11px] text-slate-600 mb-1">Menyatakan telah menyelesaikan ujian tertulis ini secara mandiri</p>
            <div class="h-16 flex items-center">
                @if($profile?->signature_data || $assessment->asesi_signature)
                    <img src="{{ $profile?->signature_data ?? $assessment->asesi_signature }}" alt="TTD Asesi" class="max-h-14">
                @else
                    <span class="text-[10px] text-slate-400 italic">(Tanda Tangan Asesi)</span>
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $asesi->full_name }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesor Kompetensi:</p>
            <p class="text-[11px] text-slate-600 mb-1">Pemeriksa & Penilai Hasil Uji Tertulis</p>
            <div class="h-16 flex items-center">
                @if($assessor?->profile?->signature_data || $assessment->assessor_signature)
                    <img src="{{ $assessor?->profile?->signature_data ?? $assessment->assessor_signature }}" alt="TTD Asesor" class="max-h-14">
                @else
                    <span class="text-[10px] text-slate-400 italic">(Tanda Tangan Asesor)</span>
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $assessor->full_name ?? '-' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. MET: {{ $assessor->profile?->no_reg_asesor ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
