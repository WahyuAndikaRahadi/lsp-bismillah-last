@extends('layouts.print-muk', [
    'title' => 'FR.MAPA.02 Peta Instrumen Asesmen - ' . $scheme->name,
    'docCode' => 'FR.MAPA.02',
    'docTitle' => 'PETA INSTRUMEN ASESMEN HASIL PENDEKATAN ASESMEN DAN PERENCANAAN ASESMEN'
])

@section('print-content')
@include('admin.dokumen-muk.partials.meta-box')

{{-- Instrument Mapping Description --}}
<p class="text-[11px] text-slate-700 mb-3 italic">
    *) Diisi berdasarkan hasil penentuan pendekatan asesmen dan perencanaan asesmen untuk setiap kelompok unit kompetensi.
</p>

{{-- Units and Instruments Matrix Table --}}
<table class="w-full text-xs border border-black mb-4 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-8">No</th>
            <th class="p-2 border-r border-black w-32">Kode Unit</th>
            <th class="p-2 border-r border-black text-left">Judul Unit Kompetensi</th>
            <th class="p-1 border-r border-black w-8" title="Ceklis Observasi">IA.01<br><span class="text-[9px] font-normal">CL</span></th>
            <th class="p-1 border-r border-black w-8" title="Report Sheet">IA.01B<br><span class="text-[9px] font-normal">RS</span></th>
            <th class="p-1 border-r border-black w-8" title="Tugas Praktik Demonstrasi">IA.02<br><span class="text-[9px] font-normal">TPD</span></th>
            <th class="p-1 border-r border-black w-8" title="Pertanyaan Lisan / PMO">IA.03<br><span class="text-[9px] font-normal">PMO</span></th>
            <th class="p-1 border-black w-8" title="Pilihan Ganda">IA.05<br><span class="text-[9px] font-normal">DPT</span></th>
        </tr>
    </thead>
    <tbody>
        @forelse($scheme->competencyUnits as $idx => $unit)
            <tr class="border-b border-black">
                <td class="p-1.5 text-center font-mono border-r border-black">{{ $idx + 1 }}</td>
                <td class="p-1.5 font-mono text-[11px] font-bold border-r border-black">{{ $unit->code }}</td>
                <td class="p-1.5 border-r border-black leading-tight">{{ $unit->title }}</td>
                <td class="p-1 text-center font-bold text-sm border-r border-black bg-slate-50">✓</td>
                <td class="p-1 text-center font-bold text-sm border-r border-black bg-slate-50">✓</td>
                <td class="p-1 text-center font-bold text-sm border-r border-black bg-slate-50">✓</td>
                <td class="p-1 text-center font-bold text-sm border-r border-black bg-slate-50">✓</td>
                <td class="p-1 text-center font-bold text-sm border-black bg-slate-50">✓</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="p-4 text-center text-slate-400 italic">Belum ada unit kompetensi pada skema ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Instrument Potency Table --}}
<div class="mb-4 border border-black p-3 bg-slate-50 text-[11px]">
    <span class="font-bold block mb-1">Keterangan Sumber Bukti / Potensi Asesi:</span>
    <ol class="list-decimal pl-5 space-y-0.5 text-slate-700">
        <li>Hasil pelatihan dan/atau pendidikan yang kurikulum dan fasilitas praktiknya mampu telusur terhadap standar kompetensi (SKKNI).</li>
        <li>Pekerja berpengalaman dari industri/tempat kerja yang operasionalnya mampu telusur dengan standar kompetensi.</li>
        <li>Pelatihan mandiri atau pembelajaran otodidak yang diverifikasi melalui uji tertulis dan demonstrasi praktik langsung.</li>
    </ol>
</div>

{{-- Signature Section --}}
<div class="border border-black p-4 mt-6">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Penyusun / Asesor Pengembang:</p>
            <p class="text-[11px] text-slate-600 mb-12">LSP Praktisi Otomotif Indonesia</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $leadAssessor->full_name ?? 'RIDWAN' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. Reg: {{ $leadAssessor->profile?->no_reg_asesor ?? 'MET.000.008265 2025' }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Validator / Komite Teknis:</p>
            <p class="text-[11px] text-slate-600 mb-12">LSP Praktisi Otomotif Indonesia</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $schedule->pic_name ?? 'GUNADI / KARYANTO' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. Reg: MET.000.000459 2007</p>
        </div>
    </div>
</div>
@endsection
