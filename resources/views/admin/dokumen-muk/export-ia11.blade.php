@extends('layouts.print-muk', [
    'title' => 'FR.IA.11 Meninjau Instrumen Asesmen - ' . $scheme->name,
    'docCode' => 'FR.IA.11',
    'docTitle' => 'CEKLIS MENINJAU INSTRUMEN ASESMEN'
])

@section('print-content')
@include('admin.dokumen-muk.partials.meta-box', [
    'assessorLabel' => 'Nama Peninjau / Asesor',
])

{{-- Instrument Review Context --}}
<div class="border border-black p-2.5 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">PANDUAN PENINJAUAN INSTRUMEN ASESMEN:</span>
    <p class="text-slate-700 leading-relaxed">
        Peninjauan dilakukan terhadap seluruh instrumen asesmen kompetensi yang digunakan pada skema sertifikasi ini guna menjamin pemenuhan 4 Prinsip Asesmen (Validitas, Reliabilitas, Fleksibilitas, dan Keadilan) serta 5 Dimensi Kompetensi (Task Skills, Task Management Skills, Contingency Management Skills, Job Role Environment Skills, dan Transfer Skills).
    </p>
</div>

{{-- Review Matrix Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-10">No</th>
            <th class="p-2 border-r border-black w-48 text-left">Aspek Peninjauan Instrumen</th>
            <th class="p-2 border-r border-black w-12">YA</th>
            <th class="p-2 border-r border-black w-12">TIDAK</th>
            <th class="p-2 border-black text-left">Komentar / Catatan Peninjau</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b border-black">
            <td class="p-2 text-center font-mono border-r border-black font-bold">1</td>
            <td class="p-2 border-r border-black font-semibold">
                Validitas (Validity)
                <div class="text-[10px] font-normal text-slate-600 mt-0.5">Instrumen mengukur seluruh Kriteria Unjuk Kerja (KUK) dan elemen kompetensi pada standar SKKNI.</div>
            </td>
            <td class="p-2 text-center font-bold text-sm border-r border-black bg-emerald-50/50">✓</td>
            <td class="p-2 text-center border-r border-black"></td>
            <td class="p-2 text-slate-700 text-[11px]">Seluruh elemen unit terpetakan dengan lengkap pada FR.MAPA.02 dan rubrik observasi.</td>
        </tr>
        <tr class="border-b border-black">
            <td class="p-2 text-center font-mono border-r border-black font-bold">2</td>
            <td class="p-2 border-r border-black font-semibold">
                Reliabilitas (Reliability)
                <div class="text-[10px] font-normal text-slate-600 mt-0.5">Instruksi kerja dan kriteria penilaian konsisten bila diterapkan oleh asesor berbeda.</div>
            </td>
            <td class="p-2 text-center font-bold text-sm border-r border-black bg-emerald-50/50">✓</td>
            <td class="p-2 text-center border-r border-black"></td>
            <td class="p-2 text-slate-700 text-[11px]">Rubrik penilaian observasi praktik (IA.01) dan kunci jawaban DPT (IA.05.B) terdefinisi jelas.</td>
        </tr>
        <tr class="border-b border-black">
            <td class="p-2 text-center font-mono border-r border-black font-bold">3</td>
            <td class="p-2 border-r border-black font-semibold">
                Fleksibilitas (Flexibility)
                <div class="text-[10px] font-normal text-slate-600 mt-0.5">Instrumen dapat disesuaikan dengan variasi peralatan bengkel dan konteks TUK tanpa mengurangi standar.</div>
            </td>
            <td class="p-2 text-center font-bold text-sm border-r border-black bg-emerald-50/50">✓</td>
            <td class="p-2 text-center border-r border-black"></td>
            <td class="p-2 text-slate-700 text-[11px]">Mengakomodasi sarana bengkel simulasi TUK Sewaktu maupun TUK Mandiri terverifikasi.</td>
        </tr>
        <tr class="border-b border-black">
            <td class="p-2 text-center font-mono border-r border-black font-bold">4</td>
            <td class="p-2 border-r border-black font-semibold">
                Keadilan (Fairness)
                <div class="text-[10px] font-normal text-slate-600 mt-0.5">Instrumen bebas dari bias gender, latar belakang, dan memberikan kesempatan yang setara.</div>
            </td>
            <td class="p-2 text-center font-bold text-sm border-r border-black bg-emerald-50/50">✓</td>
            <td class="p-2 text-center border-r border-black"></td>
            <td class="p-2 text-slate-700 text-[11px]">Bahasa instruksi jelas, lugas, dan mengakomodasi penyesuaian yang wajar (FR.AK.07).</td>
        </tr>
    </tbody>
</table>

{{-- Recommendation Box --}}
<div class="border border-black p-3 bg-slate-50 text-xs mb-6">
    <span class="font-bold block mb-1">KESIMPULAN DAN REKOMENDASI PENINJAU:</span>
    <p class="text-slate-800 leading-relaxed font-semibold">
        Perangkat instrumen asesmen Materi Uji Kompetensi (MUK) untuk Skema {{ $scheme->name }} ({{ $scheme->code }}) dinyatakan MEMENUHI STANDAR MUTU BNSP dan DIREKOMENDASIKAN untuk dipergunakan dalam asesmen kompetensi kerja.
    </p>
</div>

{{-- Signatures --}}
<div class="border border-black p-4 mt-6 avoid-break">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Ketua Komite Skema / Lead Assessor:</p>
            <p class="text-[11px] text-slate-600 mb-12">LSP Praktisi Otomotif Indonesia</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $leadAssessor->full_name ?? 'RIDWAN' }}</p>
            <p class="text-[10px] text-slate-500">Tanggal: {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Peninjau Instrumen / Master Asesor:</p>
            <p class="text-[11px] text-slate-600 mb-12">Tim Penjaminan Mutu LSP PrO Indo</p>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $assessors->first()?->full_name ?? 'Dr. Ir. Bambang Supriyadi, M.T.' }}</p>
            <p class="text-[10px] text-slate-500">No. MET: {{ $assessors->first()?->profile?->no_reg_asesor ?? 'MET.000.001234 2021' }}</p>
        </div>
    </div>
</div>
@endsection
