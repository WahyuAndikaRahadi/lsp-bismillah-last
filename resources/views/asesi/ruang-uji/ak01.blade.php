@extends('layouts.app')

@section('title', 'FR.AK.01 - Persetujuan Asesmen dan Kerahasiaan')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    {{-- Stepper Component --}}
    <x-progress-stepper currentStep="AK01" :assessment="$assessment" />

    {{-- Form AK01 Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-800 to-indigo-900 px-6 py-4 text-white">
            <h1 class="text-xl font-bold">FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN</h1>
            <p class="text-xs text-slate-300 mt-1">Kesepakatan pelaksanaan asesmen dan perlindungan kerahasiaan materi uji</p>
        </div>

        <form action="{{ route('asesi.assessment.ak01.sign', $assessment) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 text-sm grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <span class="text-xs text-slate-500 block">Skema Sertifikasi</span>
                    <span class="font-bold text-slate-800">{{ $assessment->schedule->scheme->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Nomor Skema</span>
                    <span class="font-bold text-slate-800">{{ $assessment->schedule->scheme->code }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Tempat Uji Kompetensi (TUK)</span>
                    <span class="font-semibold text-slate-700">{{ $assessment->schedule->tuk_name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Tanggal Pelaksanaan</span>
                    <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Nama Asesi</span>
                    <span class="font-semibold text-slate-700">{{ auth()->user()->full_name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Asesor Ditugaskan</span>
                    <span class="font-semibold text-slate-700">{{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}</span>
                </div>
            </div>

            {{-- Bukti Yang Akan Dikumpulkan --}}
            <div class="border border-slate-200 rounded-lg p-4 bg-white">
                <h3 class="text-sm font-bold text-slate-800 mb-2">Bukti yang akan dikumpulkan:</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs text-slate-700">
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Verifikasi Portofolio</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Observasi Langsung</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Pertanyaan Lisan (PMO)</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Pertanyaan Tertulis (CBT)</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Tugas Demonstrasi</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled class="rounded text-indigo-600"> Umpan Balik Asesmen</label>
                </div>
            </div>

            {{-- Klausul Persetujuan --}}
            <div class="space-y-4 text-xs text-slate-600 leading-relaxed bg-amber-50/60 border border-amber-200 p-4 rounded-lg">
                <p class="font-semibold text-slate-800">Pernyataan Asesi:</p>
                <p>1. Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor.</p>
                <p>2. Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh pihak yang berwenang.</p>
                <p>3. Saya menyatakan akan mematuhi seluruh tata tertib dan memelihara kerahasiaan materi uji kompetensi.</p>

                <div class="pt-2 space-y-2">
                    <label class="flex items-start gap-2 cursor-pointer font-medium text-slate-800">
                        <input type="checkbox" required class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Saya telah membaca, memahami, dan menyetujui seluruh klausul di atas.</span>
                    </label>
                    {{-- TODO: konfirmasi pemilik/LSP bila ada penyesuaian redaksi hukum konsen e-sign --}}
                    <label class="flex items-start gap-2 cursor-pointer font-medium text-indigo-950 bg-indigo-50/70 border border-indigo-200 p-2.5 rounded-lg">
                        <input type="checkbox" name="esign_consent" value="1" required class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500" {{ old('esign_consent', '1') ? 'checked' : '' }}>
                        <span>Saya menyetujui tanda tangan elektronik saya digunakan pada formulir asesmen yang berkaitan dengan asesmen ini.</span>
                    </label>
                    @error('esign_consent')
                        <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tanda Tangan & Konfirmasi --}}
            <div class="flex flex-col md:flex-row items-end justify-between gap-6 pt-4 border-t border-slate-200">
                <div class="w-full md:w-80">
                    <x-signature-pad 
                        name="signature_data" 
                        :value="old('signature_data')" 
                        :profileSignature="auth()->user()->profile?->signature_data"
                        label="Tanda Tangan Persetujuan Asesi" 
                        height="140" 
                    />
                </div>

                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        <span>Setujui & Masuk Ruang Uji</span>
                        <span>→</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
