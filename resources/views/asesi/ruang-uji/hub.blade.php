@extends('layouts.app')

@section('title', 'Ruang Uji Asesi - LSP PrO Indo')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    <x-progress-stepper currentStep="{{ $assessment->last_active_step ?? 'ruang-uji' }}" :assessment="$assessment" />

    {{-- Header Info --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-brand-900 text-white uppercase tracking-wider font-mono">Ruang Uji Asesi</span>
                <span class="text-xs font-mono font-semibold text-slate-500">{{ $assessment->schedule->code }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-2">{{ $assessment->schedule->scheme->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                TUK: <strong class="text-slate-700">{{ $assessment->schedule->tuk_name }}</strong> &bull; 
                Asesor: <strong class="text-slate-700">{{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}</strong> &bull;
                Pelaksanaan: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d M Y') }}</strong>
            </p>
        </div>
        <div class="shrink-0">
            @if($assessment->status === 'COMPLETED')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-bold uppercase">
                    ✓ Selesai ({{ $assessment->final_result ?? 'KOMPETEN' }})
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Sedang Berlangsung
                </span>
            @endif
        </div>
    </div>

    {{-- Upfront E-sign Consent Banner (if not yet granted, e.g. for ongoing assessment pre-migration) --}}
    @if(! $assessment->esign_consent_at)
        <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-5 shadow-xs">
            <div class="flex items-start gap-4">
                <span class="text-2xl shrink-0">✍️</span>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-amber-950">Persetujuan Penggunaan Tanda Tangan Elektronik Diperlukan</h3>
                    <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                        Sesuai standar operasional BNSP, tanda tangan elektronik Anda memerlukan persetujuan eksplisit untuk disematkan pada seluruh formulir instrumen penilaian dalam asesmen ini. Asesor belum dapat merekam penilaian sebelum persetujuan ini diberikan.
                    </p>
                    <form action="{{ route('asesi.assessment.consent-esign', $assessment) }}" method="POST" class="mt-3">
                        @csrf
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="esign_consent" value="1" required class="mt-0.5 rounded border-amber-400 text-brand-600 focus:ring-brand-500">
                            <span class="text-xs font-semibold text-amber-950">
                                Saya menyetujui tanda tangan elektronik saya digunakan pada formulir asesmen yang berkaitan dengan asesmen ini.
                            </span>
                        </label>
                        @error('esign_consent')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div>
                            <button type="submit" class="mt-3 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg shadow-xs transition">
                                Setujui Penggunaan E-Sign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @php
        $unlockedInstruments = $assessment->getUnlockedInstruments();
        $responses = $responses ?? $assessment->iaResponses->keyBy('instrument_code');
        $hasCbt = in_array('IA05', $unlockedInstruments);
        $hasIa02 = in_array('IA02', $unlockedInstruments);
    @endphp

    @if($hasCbt || $hasIa02)
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Tugas & Aktivitas Asesi (Instrumen Terbuka)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($hasCbt)
                    @php $ia05Resp = $responses['IA05'] ?? null; @endphp
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $ia05Resp ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $ia05Resp ? '✓ CBT Selesai' : 'Belum Dikerjakan' }}
                                </span>
                                @if($ia05Resp)<span class="text-xs font-mono font-bold text-slate-700">Skor: {{ $ia05Resp->score }}%</span>@endif
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">FR.IA.05 Ujian Tertulis (CBT)</h3>
                            <p class="text-xs text-slate-500 mt-1">Ujian teori pilihan ganda terstandar secara komputer/daring.</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="{{ route('asesi.assessment.instrument.ia05', $assessment) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-brand-900 hover:bg-brand-800 text-white text-xs font-bold rounded-lg transition">
                                {{ $ia05Resp ? 'Lihat Hasil CBT →' : 'Mulai Ujian CBT →' }}
                            </a>
                        </div>
                    </div>
                @endif
                @if($hasIa02)
                    @php
                        $ia02Resp = $responses['IA02'] ?? null;
                        $hasEvidence = ! empty($ia02Resp?->data_json['evidence_image_path']);
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $hasEvidence ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $hasEvidence ? '✓ Lembar Diunggah' : 'Wajib Unggah' }}
                                </span>
                                <span class="text-[11px] text-slate-400">Lembar Fisik</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">FR.IA.02 Bukti Praktik Demonstrasi</h3>
                            <p class="text-xs text-slate-500 mt-1">Unggah foto lembar jawaban fisik jika demonstrasi berbasis kertas di TUK.</p>
                            @if($hasEvidence)
                                <div class="mt-2 text-xs text-emerald-700 bg-emerald-50 rounded p-1.5 truncate">File: {{ $ia02Resp->data_json['evidence_original_name'] ?? 'Bukti fisik terunggah' }}</div>
                            @endif
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <form action="{{ route('asesi.assessment.instrument.upload-evidence', [$assessment, 'IA02']) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="file" name="evidence_image" required accept="image/*,.pdf" class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 flex-1">
                                <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition shrink-0">{{ $hasEvidence ? 'Ganti' : 'Unggah' }}</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Tracker Status Instrumen Asesmen --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 font-mono">Daftar Instrumen Uji Kompetensi yang Disepakati</h2>
            <span class="text-xs text-slate-500">Tanda tangan otomatis tersemat (FR.AK.01)</span>
        </div>
        <div class="divide-y divide-slate-100 text-xs">
            @php
                $labels = ['IA01' => ['FR.IA.01 Ceklis Observasi Praktik', 'Dinilai Asesor'], 'IA01B' => ['FR.IA.01B Lembar Laporan Pemeriksaan', 'Dinilai Asesor'], 'IA02' => ['FR.IA.02 Tugas Praktik Demonstrasi', 'Asesi / Asesor'], 'IA03' => ['FR.IA.03 Pertanyaan Mendukung Observasi', 'Dinilai Asesor'], 'IA05' => ['FR.IA.05 Pertanyaan Tertulis (CBT)', 'Mandiri Asesi']];
            @endphp
            @foreach($unlockedInstruments as $instCode)
                @php
                    $meta = $labels[$instCode] ?? ['FR.'.$instCode, 'Instrumen'];
                    $resp = $responses[$instCode] ?? null;
                    $isAssessed = ! empty($resp?->assessor_signed_at) || ! empty($resp?->score) || ! empty($resp?->data_json);
                @endphp
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-800">{{ $meta[0] }}</div>
                        <div class="text-slate-400 text-[11px]">{{ $meta[1] }}</div>
                    </div>
                    <div>
                        @if($instCode === 'IA05')
                            <span class="px-2.5 py-1 rounded-full font-semibold {{ $resp ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">{{ $resp ? 'Selesai ('.$resp->score.'%)' : 'Belum Ujian' }}</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full font-semibold {{ $isAssessed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">{{ $isAssessed ? 'Telah Dinilai' : 'Menunggu Penilaian' }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Banner Pasca Asesmen --}}
    @if($assessment->status === 'COMPLETED' && in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK']))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 font-mono">Tahap Pasca Asesmen</span>
                <h4 class="text-base font-bold text-slate-900">Rangkaian Asesmen Selesai ({{ $assessment->final_result }})</h4>
                <p class="text-xs text-slate-600 mt-0.5">Silakan isi formulir umpan balik (FR.AK.03) atau ajukan banding (FR.AK.04) bila diperlukan.</p>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('asesi.assessment.ak03', $assessment) }}" class="px-4 py-2 bg-brand-900 hover:bg-brand-800 text-white text-xs font-bold rounded-lg shadow-xs transition">FR.AK.03 Umpan Balik &rarr;</a>
                @if($assessment->canAccessAppeal())
                    <a href="{{ route('asesi.assessment.appeal', $assessment) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-bold rounded-lg shadow-xs transition">FR.AK.04 Banding</a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
