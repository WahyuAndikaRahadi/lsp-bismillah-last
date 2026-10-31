@extends('layouts.print-muk', [
    'title' => 'FR.IA.01 Ceklis Observasi Praktik - ' . $asesi->full_name,
    'docCode' => 'FR.IA.01',
    'docTitle' => 'CL - CEKLIS OBSERVASI AKTIVITAS DI TEMPAT KERJA ATAU TEMPAT KERJA SIMULASI'
])

@section('print-content')
@php
    $ia01Response = $iaResponses->firstWhere('instrument_code', 'IA01');
    $savedKuks = $ia01Response?->data_json['kuks'] ?? [];
@endphp

@include('admin.dokumen-muk.partials.meta-box')

{{-- Guidance Box --}}
<div class="border border-black p-2.5 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">PANDUAN BAGI ASESOR:</span>
    <ul class="list-disc pl-5 space-y-0.5 text-slate-700">
        <li>Amati seluruh aktivitas unjuk kerja demonstrasi praktik yang dilakukan asesi di bengkel/meja uji TUK.</li>
        <li>Beri tanda centang (✓) pada kolom <strong>YA</strong> jika asesi mendemonstrasikan unjuk kerja sesuai standar KUK SKKNI, atau <strong>TIDAK</strong> jika belum terpenuhi.</li>
    </ul>
</div>

{{-- Observation Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-10">No</th>
            <th class="p-2 border-r border-black text-left">Elemen & Kriteria Unjuk Kerja (KUK)</th>
            <th class="p-1 border-r border-black w-12">Ya</th>
            <th class="p-1 border-r border-black w-12">Tidak</th>
            <th class="p-2 border-black w-40 text-left">Penilaian / Catatan</th>
        </tr>
    </thead>
    <tbody>
        @php $counter = 1; @endphp
        @forelse($scheme->competencyUnits as $uIdx => $unit)
            <tr class="bg-slate-100 border-t-2 border-b border-black font-bold">
                <td colspan="5" class="p-2">
                    Unit {{ $uIdx + 1 }}: <span class="font-mono">{{ $unit->code }}</span> - {{ $unit->title }}
                </td>
            </tr>
            @foreach($unit->elements as $element)
                <tr class="bg-slate-50 border-b border-slate-300 italic font-semibold">
                    <td class="p-1 text-center font-mono text-slate-500">#</td>
                    <td colspan="4" class="p-1 pl-4 text-slate-700">Elemen: {{ $element->title }}</td>
                </tr>
                @foreach($element->kuks as $kuk)
                    @php
                        $res = $savedKuks[$kuk->id]['result'] ?? 'K';
                        $isYa = in_array(strtoupper((string) $res), ['YA', 'K', '1', 'TRUE']);
                    @endphp
                    <tr class="border-b border-black">
                        <td class="p-1.5 text-center font-mono border-r border-black">{{ $counter++ }}</td>
                        <td class="p-1.5 pl-4 border-r border-black leading-tight">
                            <span class="font-mono font-bold mr-1">{{ $kuk->code_number }}</span>
                            {{ $kuk->statement }}
                        </td>
                        <td class="p-1 text-center font-black text-sm border-r border-black {{ $isYa ? 'bg-emerald-50 text-emerald-800' : '' }}">
                            {{ $isYa ? '✓' : '' }}
                        </td>
                        <td class="p-1 text-center font-black text-sm border-r border-black {{ !$isYa ? 'bg-rose-50 text-rose-800' : '' }}">
                            {{ !$isYa ? '✕' : '' }}
                        </td>
                        <td class="p-1.5 border-black text-[11px] text-slate-600">
                            {{ $savedKuks[$kuk->id]['note'] ?? 'Memenuhi standar unjuk kerja' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @empty
            <tr>
                <td colspan="5" class="p-4 text-center text-slate-400 italic">Tidak ada unit kompetensi pada skema ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Signature Box --}}
<div class="border border-black p-4">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesi (Peserta Uji):</p>
            <p class="text-[11px] text-slate-600 mb-1">Telah mengikuti seluruh observasi unjuk kerja</p>
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
            <p class="text-[11px] text-slate-600 mb-1">Penilai Pelaksanaan Observasi Tempat Kerja</p>
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
