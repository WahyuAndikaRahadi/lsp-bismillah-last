@extends('layouts.print-muk', [
    'title' => 'FR.IA.02 Tugas Praktik Demonstrasi - ' . $asesi->full_name,
    'docCode' => 'FR.IA.02',
    'docTitle' => 'TPD - TUGAS PRAKTIK DEMONSTRASI'
])

@section('print-content')
@php
    $ia02Response = $iaResponses->firstWhere('instrument_code', 'IA02');
    $tasks = $ia02Response?->data_json['tasks'] ?? [];
    $defaultTasks = [
        ['title' => 'Menerapkan prosedur keselamatan dan kesehatan kerja (K3) serta pemakaian APD bengkel.', 'result' => 'YA', 'note' => 'Memakai wearpack, safety shoes, dan kacamata pelindung.'],
        ['title' => 'Menyiapkan peralatan tangan (hand tools) dan special service tools (SST) sesuai kebutuhan pekerjaan.', 'result' => 'YA', 'note' => 'Peralatan disiapkan di caddy/toolbox dalam kondisi bersih dan terkalibrasi.'],
        ['title' => 'Melaksanakan pemeriksaan dan penyetelan celah katup sesuai standar spesifikasi manual service.', 'result' => 'YA', 'note' => 'Menggunakan feeler gauge dengan teknik tarikan yang tepat.'],
        ['title' => 'Melakukan pemeriksaan sistem pengapian dan penggantian busi sesuai spesifikasi pabrik.', 'result' => 'YA', 'note' => 'Mengukur celah elektroda dengan wire thickness gauge.'],
        ['title' => 'Melakukan pemeriksaan tekanan bahan bakar dan pembersihan injektor / sistem suplai bahan bakar.', 'result' => 'YA', 'note' => 'Tekanan bahan bakar terbaca 300 kPa pada pressure gauge.'],
        ['title' => 'Membersihkan area kerja dan mengembalikan peralatan ke tempat semula (5S / Housekeeping).', 'result' => 'YA', 'note' => 'Area kerja bersih dari ceceran oli dan limbah B3 dipisahkan.'],
    ];
    $displayTasks = !empty($tasks) ? $tasks : $defaultTasks;
@endphp

@include('admin.dokumen-muk.partials.meta-box', [
    'dateLabel' => 'Tanggal & Alokasi Waktu',
    'timeAllocation' => '45 Menit',
])

{{-- Scenario & Instructions Box --}}
<div class="border border-black p-3 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">A. Skenario & Petunjuk Kerja Praktik:</span>
    <p class="text-slate-700 mb-2 leading-relaxed">
        Anda adalah seorang teknisi otomotif pada bengkel resmi. Laksanakan demonstrasi praktik perawatan berkala engine dan kelistrikan kendaraan sesuai standar operasional prosedur (SOP) bengkel dan manual service manufaktur.
    </p>
    <span class="font-bold block mb-1">B. Peralatan dan Bahan:</span>
    <p class="text-slate-700">
        Vehicle engine stand/unit kendaraan, Toolkit set, Multimeter digital, Feeler gauge, Compression tester, Fuel pressure gauge gauge set, Manual service book, APD lengkap.
    </p>
</div>

{{-- Demonstration Tasks Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-10">No</th>
            <th class="p-2 border-r border-black text-left">Instruksi Kerja / Rincian Langkah Praktik</th>
            <th class="p-1 border-r border-black w-14">Pencapaian<br><span class="text-[9px] font-normal">Ya / Tidak</span></th>
            <th class="p-2 border-black w-48 text-left">Catatan Hasil Pengamatan Asesor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($displayTasks as $idx => $task)
            @php
                $res = strtoupper($task['result'] ?? 'YA');
                $isYa = in_array($res, ['YA', 'K', '1', 'TRUE']);
            @endphp
            <tr class="border-b border-black">
                <td class="p-2 text-center font-mono font-bold border-r border-black">{{ $idx + 1 }}</td>
                <td class="p-2 font-medium border-r border-black leading-tight">{{ $task['title'] ?? $task['instruction'] ?? '-' }}</td>
                <td class="p-1 text-center font-black text-xs border-r border-black {{ $isYa ? 'text-emerald-800 bg-emerald-50' : 'text-rose-800 bg-rose-50' }}">
                    {{ $isYa ? 'YA' : 'TIDAK' }}
                </td>
                <td class="p-2 border-black text-[11px] text-slate-600">
                    {{ $task['note'] ?? 'Telah dikerjakan sesuai SOP pabrikan.' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Notes Box --}}
<div class="border border-black p-3 bg-slate-50 text-xs mb-6">
    <span class="font-bold block mb-1">Catatan Umpan Balik Asesor Terhadap Praktik Demonstrasi:</span>
    <p class="text-slate-700 italic">
        {{ $ia02Response?->data_json['notes'] ?? 'Asesi mendemonstrasikan penguasaan keterampilan praktik secara mandiri, sistematis, dan mengutamakan keselamatan kerja (K3).' }}
    </p>
</div>

{{-- Signature Box --}}
<div class="border border-black p-4">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesi (Peserta Uji):</p>
            <p class="text-[11px] text-slate-600 mb-1">Menyatakan telah menyelesaikan tugas demonstrasi</p>
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
            <p class="text-[11px] text-slate-600 mb-1">Penilai Pelaksanaan Tugas Praktik Demonstrasi</p>
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
