@extends('layouts.print-muk', [
    'title' => 'FR.IA.01B Report Sheet - ' . $asesi->full_name,
    'docCode' => 'FR.IA.01B',
    'docTitle' => 'REPORT SHEET - LEMBAR PEMERIKSAAN & PENGUKURAN PRAKTIK'
])

@section('print-content')
@php
    $ia01bResponse = $iaResponses->firstWhere('instrument_code', 'IA01B');
    $items = $ia01bResponse?->data_json['items'] ?? [];
    $defaultItems = [
        ['name' => 'Tegangan Baterai (Accu)', 'spec' => '12.4 - 12.8 Volt', 'measured' => '12.6 Volt', 'status' => 'SESUAI'],
        ['name' => 'Berat Jenis Air Aki (Elektrolit)', 'spec' => '1.26 - 1.28 pada 20°C', 'measured' => '1.27', 'status' => 'SESUAI'],
        ['name' => 'Celah Busi (Spark Plug Gap)', 'spec' => '0.80 - 0.90 mm', 'measured' => '0.85 mm', 'status' => 'SESUAI'],
        ['name' => 'Tahanan Koil Pengapian (Primer)', 'spec' => '1.5 - 2.5 Ohm', 'measured' => '1.8 Ohm', 'status' => 'SESUAI'],
        ['name' => 'Tekanan Kompresi Silinder', 'spec' => '10.0 - 12.5 kg/cm²', 'measured' => '11.2 kg/cm²', 'status' => 'SESUAI'],
        ['name' => 'Tekanan Pompa Bahan Bakar (Fuel Pressure)', 'spec' => '294 - 314 kPa', 'measured' => '300 kPa', 'status' => 'SESUAI'],
        ['name' => 'Celah Katup Masuk (Intake Valve Gap)', 'spec' => '0.15 - 0.20 mm', 'measured' => '0.18 mm', 'status' => 'SESUAI'],
        ['name' => 'Celah Katup Buang (Exhaust Valve Gap)', 'spec' => '0.25 - 0.30 mm', 'measured' => '0.28 mm', 'status' => 'SESUAI'],
    ];
    $displayItems = !empty($items) ? $items : $defaultItems;
@endphp

@include('admin.dokumen-muk.partials.meta-box')

{{-- Measurement Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-10">No</th>
            <th class="p-2 border-r border-black text-left">Komponen / Item Pemeriksaan & Pengukuran</th>
            <th class="p-2 border-r border-black w-40 text-left">Standar Spesifikasi (Manual Service)</th>
            <th class="p-2 border-r border-black w-36 text-center">Hasil Pengukuran Asesi</th>
            <th class="p-2 border-black w-28 text-center">Kesimpulan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($displayItems as $idx => $item)
            @php
                $status = strtoupper($item['status'] ?? 'SESUAI');
                $isSesuai = $status === 'SESUAI';
            @endphp
            <tr class="border-b border-black">
                <td class="p-2 text-center font-mono font-bold border-r border-black">{{ $idx + 1 }}</td>
                <td class="p-2 font-semibold border-r border-black">{{ $item['name'] ?? $item['item_name'] ?? '-' }}</td>
                <td class="p-2 border-r border-black font-mono text-[11px] text-slate-700">{{ $item['spec'] ?? $item['standard'] ?? '-' }}</td>
                <td class="p-2 border-r border-black font-mono font-bold text-center text-[11px] bg-slate-50">{{ $item['measured'] ?? '-' }}</td>
                <td class="p-2 text-center border-black font-bold text-[11px] {{ $isSesuai ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }}">
                    {{ $status }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Assessor Notes Box --}}
<div class="border border-black p-3 bg-slate-50 text-xs mb-6">
    <span class="font-bold block mb-1">Catatan / Umpan Balik Asesor:</span>
    <p class="text-slate-700 italic">
        {{ $ia01bResponse?->data_json['notes'] ?? 'Prosedur pengukuran dan penggunaan alat ukur (multitester, jangka sorong, compression tester) telah dilaksanakan sesuai SOP K3 dan manual service bengkel resmi.' }}
    </p>
</div>

{{-- Signature Box --}}
<div class="border border-black p-4">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesi (Peserta Uji):</p>
            <p class="text-[11px] text-slate-600 mb-1">Menyatakan data hasil pengukuran adalah benar</p>
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
            <p class="text-[11px] text-slate-600 mb-1">Pemeriksa Pelaksanaan Pengukuran Praktik</p>
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
