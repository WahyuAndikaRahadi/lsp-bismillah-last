@extends('layouts.app')

@section('title', 'FR.AK.01 - Persetujuan Asesmen dan Kerahasiaan')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    {{-- Stepper Component --}}
    <x-progress-stepper currentStep="AK01" :assessment="$assessment" />

    {{-- Official BNSP MUK Form AK01 --}}
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
                FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN
            </h1>
            <p class="text-xs text-slate-600 mt-1 italic">
                Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen
            </p>
        </div>

        <form action="{{ route('asesi.assessment.ak01.sign', $assessment) }}" method="POST" class="p-5 sm:p-6 space-y-6">
            @csrf

            {{-- Tabel Metadata Sesuai MUK --}}
            <table class="w-full text-xs border border-slate-300 border-collapse">
                <tbody>
                    <tr>
                        <td class="w-1/3 bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800">
                            Skema Sertifikasi (KKNI/Okupasi/Klaster)
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-900 font-semibold" colspan="2">
                            Judul : <span class="font-bold text-lsp-navy">{{ $assessment->schedule->scheme->name }}</span><br>
                            Nomor : <span class="font-mono text-slate-700">{{ $assessment->schedule->scheme->code }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800">
                            Tempat Uji Kompetensi (TUK)
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-900" colspan="2">
                            {{ $assessment->schedule->tuk_name }} (Sewaktu / Tempat Kerja / Mandiri)
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800">
                            Nama Asesor
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-900 font-semibold" colspan="2">
                            {{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800">
                            Nama Asesi
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-900 font-semibold" colspan="2">
                            {{ auth()->user()->full_name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800 align-top">
                            Bukti yang akan dikumpulkan
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-800" colspan="2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Verifikasi Portofolio</label>
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Observasi Langsung</label>
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Pertanyaan Lisan (PMO)</label>
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Pertanyaan Tertulis (CBT)</label>
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Tugas Praktik Demonstrasi</label>
                                <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-lsp-navy"> Hasil Umpan Balik Asesmen</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2.5 border border-slate-300 text-slate-800 align-top">
                            Pelaksanaan asesmen disepakati pada
                        </td>
                        <td class="px-3 py-2.5 border border-slate-300 text-slate-900" colspan="2">
                            <div class="space-y-1">
                                <div>Hari / Tanggal : <strong>{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('l, d F Y') }}</strong></div>
                                <div>Waktu : <strong>{{ substr($assessment->schedule->start_time, 0, 5) }} - {{ substr($assessment->schedule->end_time, 0, 5) }} WIB</strong></div>
                                <div>TUK : <strong>{{ $assessment->schedule->tuk_name }}</strong></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Klausul Persetujuan Asesi & Asesor Sesuai MUK --}}
            <div class="border border-slate-300 rounded-lg p-4 bg-slate-50 space-y-4 text-xs leading-relaxed">
                <div>
                    <h3 class="font-bold text-slate-900 mb-1">Pernyataan Asesi:</h3>
                    <p class="text-slate-700 text-justify">
                        Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor. Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh pihak yang berwenang.
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-3">
                    <h3 class="font-bold text-slate-900 mb-1">Pernyataan Asesor:</h3>
                    <p class="text-slate-700 text-justify">
                        Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-3 space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer font-medium text-slate-800">
                        <input type="checkbox" name="esign_consent" value="1" required class="mt-0.5 rounded text-lsp-navy focus:ring-lsp-navy" {{ old('esign_consent', '1') ? 'checked' : '' }}>
                        <span>
                            Saya telah membaca, memahami, dan menyetujui seluruh klausul di atas serta menyetujui penggunaan tanda tangan digital elektronik saya pada formulir persetujuan ini.
                        </span>
                    </label>
                    @error('esign_consent')
                        <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tanda Tangan Sesuai Format MUK (Tabel Dua Kolom) --}}
            <table class="w-full text-xs border border-slate-300 border-collapse bg-white">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                        <th class="w-1/2 p-3 text-center border-r border-slate-300">Tanda Tangan Asesi</th>
                        <th class="w-1/2 p-3 text-center">Tanda Tangan Asesor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-4 border-r border-slate-300 align-top">
                            <div class="space-y-3">
                                <x-signature-pad 
                                    name="signature_data" 
                                    :value="old('signature_data')" 
                                    :profileSignature="auth()->user()->profile?->signature_data"
                                    label="Bubuhkan / Konfirmasi Tanda Tangan" 
                                    height="120" 
                                />
                                <div class="text-slate-700">
                                    Nama : <strong>{{ auth()->user()->full_name }}</strong><br>
                                    Tanggal : <strong>{{ \Carbon\Carbon::parse($assessment->schedule->date)->format('d/m/Y') }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 align-top bg-slate-50/50">
                            <div class="space-y-3">
                                <div class="h-[120px] border border-dashed border-slate-300 rounded flex items-center justify-center bg-white">
                                    @if(!empty($assessment->assessor_signature))
                                        <img src="{{ $assessment->assessor_signature }}" class="max-h-24 max-w-full" alt="TTD Asesor">
                                    @else
                                        <span class="text-xs text-slate-400 italic">Ditandatangani Asesor saat Verifikasi</span>
                                    @endif
                                </div>
                                <div class="text-slate-700">
                                    Nama : <strong>{{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}</strong><br>
                                    Tanggal : <strong>{{ \Carbon\Carbon::parse($assessment->schedule->date)->format('d/m/Y') }}</strong>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Tombol Aksi Utama --}}
            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="px-8 py-3 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold rounded-lg shadow-sm transition flex items-center gap-2">
                    <span>Setujui & Masuk Ruang Uji</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
