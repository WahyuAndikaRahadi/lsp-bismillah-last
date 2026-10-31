@extends('layouts.print-muk', [
    'title' => 'FR.IA.07 Pertanyaan Lisan - ' . $asesi->full_name,
    'docCode' => 'FR.IA.07',
    'docTitle' => 'PERTANYAAN LISAN (DPL)'
])

@section('print-content')
@include('admin.dokumen-muk.partials.meta-box')

{{-- Instructions Box --}}
<div class="border border-black p-2.5 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">PETUNJUK BAGI ASESOR:</span>
    <ol class="list-decimal pl-5 space-y-0.5 text-slate-700">
        <li>Ajukan pertanyaan lisan kepada asesi untuk mengonfirmasi pemahaman prinsip kerja, prosedur K3, atau KUK yang memerlukan klarifikasi lebih lanjut.</li>
        <li>Tuliskan tanggapan atau jawaban asesi pada kolom yang disediakan.</li>
        <li>Beri tanda centang (✓) pada kolom <strong>YA</strong> jika jawaban memenuhi standar, atau <strong>TIDAK</strong> jika belum memuaskan.</li>
    </ol>
</div>

{{-- Questions Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-8">No</th>
            <th class="p-2 border-r border-black w-36">Kode Unit</th>
            <th class="p-2 border-r border-black text-left">Daftar Pertanyaan Lisan &amp; Kunci Jawaban</th>
            <th class="p-2 border-r border-black text-left w-56">Tanggapan / Jawaban Asesi</th>
            <th class="p-2 border-r border-black w-10">YA</th>
            <th class="p-2 border-black w-12">TIDAK</th>
        </tr>
    </thead>
    <tbody>
        @forelse($scheme->competencyUnits as $idx => $unit)
            <tr class="border-b border-black">
                <td class="p-1.5 text-center font-mono border-r border-black align-top">{{ $idx + 1 }}</td>
                <td class="p-1.5 font-mono text-[11px] font-bold border-r border-black align-top">
                    {{ $unit->code }}
                    <div class="text-[10px] font-normal text-slate-600 mt-0.5">{{ $unit->title }}</div>
                </td>
                <td class="p-1.5 border-r border-black align-top">
                    <p class="font-medium text-slate-800">Jelaskan prosedur K3 dan urutan langkah kerja standar dalam melaksanakan unit kompetensi ini.</p>
                    <p class="text-[10px] text-slate-500 italic mt-1">Kunci: Menjelaskan APD, SOP perbengkelan otomotif, penanganan limbah B3, dan langkah kerja sesuai manual servis pabrikan.</p>
                </td>
                <td class="p-1.5 border-r border-black align-top bg-slate-50/50">
                    <div class="text-[11px] text-slate-700 italic min-h-[45px]">
                        Asesi mampu menjelaskan prosedur keselamatan kerja, penggunaan alat ukur dan manual servis dengan tepat.
                    </div>
                </td>
                <td class="p-1 text-center font-bold text-sm border-r border-black align-top bg-emerald-50/30">✓</td>
                <td class="p-1 text-center font-bold text-sm border-black align-top"></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-slate-400 italic">Belum ada unit kompetensi pada skema ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Signatures --}}
<div class="border border-black p-4 mt-6 avoid-break">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesi :</p>
            <p class="text-[11px] text-slate-600 mb-2">Saya menyatakan telah menjawab pertanyaan lisan di atas.</p>
            <div class="h-14 mb-1">
                @if($assessment->asesi_signature)
                    <img src="{{ $assessment->asesi_signature }}" class="max-h-12 object-contain" alt="TTD Asesi">
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $asesi->full_name }}</p>
            <p class="text-[10px] text-slate-500">Tanggal: {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesor Kompetensi :</p>
            <p class="text-[11px] text-slate-600 mb-2">Telah menguji dan menilai jawaban lisan asesi.</p>
            <div class="h-14 mb-1">
                @if($assessment->assessor_signature)
                    <img src="{{ $assessment->assessor_signature }}" class="max-h-12 object-contain" alt="TTD Asesor">
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $assessor->full_name ?? '-' }}</p>
            <p class="text-[10px] text-slate-500">No. Reg: {{ $assessor->profile?->no_reg_asesor ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
