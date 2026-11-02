@extends('layouts.app')

@section('title', 'FR.AK.03 - Umpan Balik dan Catatan Asesmen')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 py-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('asesi.assessment.ruang-uji', $assessment->id) }}" class="text-xs text-lsp-navy hover:underline font-semibold flex items-center gap-1">
            <span>&larr;</span>
            <span>Kembali ke Ruang Uji</span>
        </a>
    </div>

    {{-- Official BNSP MUK Form AK03 --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden">
        {{-- Header Dokumen Resmi MUK --}}
        <div class="border-b border-slate-300 p-5 bg-slate-50 text-slate-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3 mb-3">
                <div class="font-extrabold text-sm tracking-wider uppercase text-lsp-navy">
                    Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo
                </div>
                <div class="text-[11px] font-mono text-slate-500">
                    Sistem Sertifikasi BNSP
                </div>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN
            </h1>
            <p class="text-xs text-slate-600 mt-1 italic">
                Umpan balik dari Asesi (diisi oleh Asesi setelah pengambilan keputusan rekomendasi asesmen):
            </p>
        </div>

        <form action="{{ route('asesi.assessment.ak03.submit', $assessment->id) }}" method="POST" class="p-5 sm:p-6 space-y-6">
            @csrf

            {{-- Metadata Table Sesuai MUK --}}
            <table class="w-full text-xs border border-slate-300 border-collapse">
                <tbody>
                    <tr>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Skema Sertifikasi (KKNI/Okupasi/Klaster)
                        </td>
                        <td class="px-3 py-2 border border-slate-300 text-slate-900 font-semibold" colspan="3">
                            Judul : <strong class="text-lsp-navy">{{ $assessment->schedule->scheme->name }}</strong><br>
                            Nomor : <span class="font-mono text-slate-700">{{ $assessment->schedule->scheme->code }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            TUK
                        </td>
                        <td class="px-3 py-2 border border-slate-300 text-slate-900" colspan="3">
                            {{ $assessment->schedule->tuk_name }} (Sewaktu / Tempat Kerja / Mandiri)
                        </td>
                    </tr>
                    <tr>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Nama Asesor
                        </td>
                        <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-semibold">
                            {{ $assessment->assessor->full_name ?? 'Tim Asesor LSP' }}
                        </td>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Nama Asesi
                        </td>
                        <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-semibold">
                            {{ $assessment->asesi->full_name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Tanggal Asesmen
                        </td>
                        <td class="px-3 py-2 border border-slate-300 text-slate-900" colspan="3">
                            {{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @php
                $items = [
                    1 => 'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen / uji kompetensi.',
                    2 => 'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya.',
                    3 => 'Asesor memberikan kesempatan untuk mendiskusikan / menegosiasikan metoda, instrumen, dan sumber asesmen serta jadwal uji.',
                    4 => 'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman saya.',
                    5 => 'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen berlangsung.',
                    6 => 'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen (K / BK).',
                    7 => 'Asesor memberikan umpan balik yang mendukung setelah asesmen serta arahan tindak lanjutnya.',
                    8 => 'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya secara transparan.',
                    9 => 'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen.',
                    10 => 'Asesor menggunakan keterampilan komunikasi yang efektif dan santun selama asesmen.',
                ];
                $savedRatings = $ak03?->ratings_json ?? [];
            @endphp

            {{-- Tabel Komponen Penilaian Sesuai MUK --}}
            <table class="w-full text-xs border border-slate-300 border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                        <th class="p-2.5 w-12 text-center border-r border-slate-300">No</th>
                        <th class="p-2.5 text-left border-r border-slate-300">KOMPONEN</th>
                        <th class="p-2.5 w-24 text-center border-r border-slate-300">Hasil (Ya / Tidak)</th>
                        <th class="p-2.5 w-64 text-left">Catatan / Komentar Asesi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($items as $no => $text)
                        @php
                            $val = $savedRatings[$no]['value'] ?? null;
                            $comment = $savedRatings[$no]['comment'] ?? '';
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-2.5 text-center text-slate-500 font-mono border-r border-slate-300">{{ $no }}</td>
                            <td class="p-2.5 text-slate-800 leading-relaxed border-r border-slate-300">{{ $text }}</td>
                            <td class="p-2.5 text-center border-r border-slate-300">
                                <div class="inline-flex items-center space-x-3">
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" name="ratings[{{ $no }}][value]" value="YA" {{ $val === 'YA' ? 'checked' : '' }} required class="text-lsp-navy focus:ring-lsp-navy">
                                        <span class="font-bold text-emerald-700">Ya</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" name="ratings[{{ $no }}][value]" value="TIDAK" {{ $val === 'TIDAK' ? 'checked' : '' }} required class="text-rose-600 focus:ring-rose-500">
                                        <span class="font-bold text-rose-700">Tidak</span>
                                    </label>
                                </div>
                            </td>
                            <td class="p-2.5">
                                <input type="text" name="ratings[{{ $no }}][comment]" value="{{ $comment }}" class="w-full text-xs rounded border border-slate-300 px-2 py-1 focus:ring-1 focus:ring-lsp-navy" placeholder="Opsional">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Catatan / Komentar Lainnya --}}
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-800">Catatan / komentar lainnya (apabila ada):</label>
                <textarea name="general_notes" rows="3" class="w-full text-xs border border-slate-300 rounded p-2.5 focus:ring-1 focus:ring-lsp-navy" placeholder="Tuliskan saran atau masukan untuk pelaksanaan asesmen...">{{ $ak03?->general_notes }}</textarea>
            </div>

            {{-- Signature Table Footer --}}
            <table class="w-full text-xs border border-slate-300 border-collapse bg-white">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                        <th class="w-1/2 p-3 text-center border-r border-slate-300">Tanda Tangan Asesi</th>
                        <th class="w-1/2 p-3 text-center">Tanggal Pengisian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-4 border-r border-slate-300 text-center">
                            @if(!empty(auth()->user()->profile?->signature_data))
                                <img src="{{ auth()->user()->profile->signature_data }}" class="max-h-20 mx-auto" alt="TTD Asesi">
                            @endif
                            <div class="mt-2 font-bold text-slate-900">{{ auth()->user()->full_name }}</div>
                        </td>
                        <td class="p-4 text-center align-middle font-bold text-slate-800">
                            {{ now()->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Submit Action --}}
            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="px-8 py-3 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold rounded-lg shadow-sm transition flex items-center gap-2">
                    <span>Kirim Formulir Umpan Balik (FR.AK.03)</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
