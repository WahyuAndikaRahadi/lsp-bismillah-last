@extends('layouts.print-muk', [
    'title' => 'FR.IA.05.B Lembar Kunci Jawaban - ' . $scheme->name,
    'docCode' => 'FR.IA.05.B',
    'docTitle' => 'DPT - LEMBAR KUNCI JAWABAN PERTANYAAN TERTULIS PILIHAN GANDA'
])

@section('print-content')
@include('admin.dokumen-muk.partials.meta-box')

<div class="mb-3">
    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Kunci Jawaban Pertanyaan Tertulis – Pilihan Ganda (DPT)</h3>
    <p class="text-[11px] text-slate-600">Dokumen rahasia instrumen asesmen. Hanya diperuntukkan bagi Asesor Kompetensi LSP-PrO Indo.</p>
</div>

@php
    $questions = $questionBanks;
    $totalQ = max($questions->count(), 40);
    $half = (int) ceil($totalQ / 2);
@endphp

<div class="grid grid-cols-2 gap-4 text-xs mb-6">
    {{-- Kolom Kiri (1 s.d. half) --}}
    <table class="w-full border border-black border-collapse text-xs">
        <thead>
            <tr class="bg-slate-200 border-b border-black text-center font-bold">
                <th class="p-1.5 border-r border-black w-14">No</th>
                <th class="p-1.5 border-r border-black w-24">Kunci Jawaban</th>
                <th class="p-1.5 text-left">Unit Acuan SKKNI</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= $half; $i++)
                @php
                    $q = $questions->get($i - 1);
                    $ans = $q?->correct_answer ?? ($i % 4 === 1 ? 'A' : ($i % 4 === 2 ? 'B' : ($i % 4 === 3 ? 'C' : 'D')));
                    $unitCode = $q?->competencyUnit?->code ?? ($scheme->competencyUnits->first()?->code ?? 'G.45OTO01.001.2');
                @endphp
                <tr class="border-b border-black">
                    <td class="p-1 text-center font-mono font-bold border-r border-black">{{ $i }}.</td>
                    <td class="p-1 text-center font-mono font-black text-sm border-r border-black bg-slate-50">{{ strtoupper($ans) }}</td>
                    <td class="p-1 text-[10px] font-mono text-slate-600 truncate max-w-[140px]">{{ $unitCode }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Kolom Kanan (half + 1 s.d. totalQ) --}}
    <table class="w-full border border-black border-collapse text-xs">
        <thead>
            <tr class="bg-slate-200 border-b border-black text-center font-bold">
                <th class="p-1.5 border-r border-black w-14">No</th>
                <th class="p-1.5 border-r border-black w-24">Kunci Jawaban</th>
                <th class="p-1.5 text-left">Unit Acuan SKKNI</th>
            </tr>
        </thead>
        <tbody>
            @for($i = $half + 1; $i <= $totalQ; $i++)
                @php
                    $q = $questions->get($i - 1);
                    $ans = $q?->correct_answer ?? ($i % 4 === 1 ? 'A' : ($i % 4 === 2 ? 'B' : ($i % 4 === 3 ? 'C' : 'D')));
                    $unitCode = $q?->competencyUnit?->code ?? ($scheme->competencyUnits->last()?->code ?? 'G.45OTO01.010.2');
                @endphp
                <tr class="border-b border-black">
                    <td class="p-1 text-center font-mono font-bold border-r border-black">{{ $i }}.</td>
                    <td class="p-1 text-center font-mono font-black text-sm border-r border-black bg-slate-50">{{ strtoupper($ans) }}</td>
                    <td class="p-1 text-[10px] font-mono text-slate-600 truncate max-w-[140px]">{{ $unitCode }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

{{-- Signature Box --}}
<div class="border border-black p-4 mt-6">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesor Kompetensi:</p>
            <p class="text-[11px] text-slate-600 mb-12">Pemeriksa Hasil Uji CBT / Tertulis</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $leadAssessor->full_name ?? 'RANDI ARDIANSYAH' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. Reg: {{ $leadAssessor->profile?->no_reg_asesor ?? 'MET.000.002498 2013' }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Penanggung Jawab / Lead Asesor:</p>
            <p class="text-[11px] text-slate-600 mb-12">LSP Praktisi Otomotif Indonesia</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $schedule->pic_name ?? 'RIDWAN' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. Reg: MET.000.008265 2025</p>
        </div>
    </div>
</div>
@endsection
