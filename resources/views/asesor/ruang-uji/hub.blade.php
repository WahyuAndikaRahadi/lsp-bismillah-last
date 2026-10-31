@extends('layouts.app', ['title' => 'Ruang Uji - ' . $assessment->asesi->full_name])

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Daftar Asesi</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Ruang Uji Asesmen</h1>
        <p class="text-sm text-slate-500">Asesi: <strong>{{ $assessment->asesi->full_name }}</strong> | Skema: <strong>{{ $assessment->schedule->scheme->name }}</strong></p>
    </div>

    {{-- Assessment Instrument Cards --}}
    @php
        $ia01Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA01') !== null;
        $ia01bDone = $assessment->iaResponses->firstWhere('instrument_code', 'IA01B') !== null;
        $ia02Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA02') !== null;
        $ia03Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA03') !== null;
        $ia05Resp = $assessment->iaResponses->firstWhere('instrument_code', 'IA05');
        $ia05Score = $ia05Resp?->score ?? $assessment->ia05_score;
        $ia05Done = $ia05Resp !== null || $ia05Score !== null;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- IA01: Observasi Langsung --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded bg-blue-50 text-blue-700 font-mono text-xs font-bold">FR.IA.01</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $ia01Done ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                        {{ $ia01Done ? '✓ Selesai' : 'Belum Selesai' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ceklis Observasi Aktivitas di Tempat Kerja</h3>
                    <p class="text-xs text-slate-500 mt-1">Penilaian observasi langsung terhadap Kriteria Unjuk Kerja (KUK) asesi.</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia01']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-sm transition">
                    Buka Penilaian IA01 &rarr;
                </a>
            </div>
        </div>

        {{-- IA01B: Lembar Hasil Pengukuran (Report Sheet) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 font-mono text-xs font-bold">FR.IA.01B</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $ia01bDone ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                        {{ $ia01bDone ? '✓ Selesai' : 'Report Sheet' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Lembar Pengukuran Teknis (Report Sheet)</h3>
                    <p class="text-xs text-slate-500 mt-1">Verifikasi hasil pengukuran parameter fisik komponen spesifik skema uji.</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia01b']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-sm transition">
                    Buka Penilaian IA01B &rarr;
                </a>
            </div>
        </div>

        {{-- IA02: Tugas Praktik Demonstrasi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded bg-purple-50 text-purple-700 font-mono text-xs font-bold">FR.IA.02</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $ia02Done ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                        {{ $ia02Done ? '✓ Selesai' : 'Belum Selesai' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tugas Praktik Demonstrasi</h3>
                    <p class="text-xs text-slate-500 mt-1">Evaluasi pelaksanaan tugas praktik dan demonstrasi asesi di TUK.</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia02']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                    Buka Penilaian IA02 &rarr;
                </a>
            </div>
        </div>

        {{-- IA03: Pertanyaan Mendukung Observasi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded bg-amber-50 text-amber-700 font-mono text-xs font-bold">FR.IA.03</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $ia03Done ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                        {{ $ia03Done ? '✓ Selesai' : 'Belum Selesai' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pertanyaan Mendukung Observasi (PMO / Lisan)</h3>
                    <p class="text-xs text-slate-500 mt-1">Evaluasi pertanyaan lisan untuk mengonfirmasi pemahaman dan pengetahuan asesi.</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia03']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                    Buka Penilaian IA03 &rarr;
                </a>
            </div>
        </div>

        {{-- IA05: Tes Tertulis CBT --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 font-mono text-xs font-bold">FR.IA.05</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $ia05Done ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                        {{ $ia05Done ? '✓ Selesai (Skor: ' . ($ia05Score ?? 0) . '%)' : 'Belum Dikerjakan' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pertanyaan Tertulis Pilihan Ganda (CBT)</h3>
                    <p class="text-xs text-slate-500 mt-1">Hasil ujian berbasis komputer otomatis beserta rincian jawaban asesi.</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia05']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white rounded-lg shadow-sm transition">
                    Lihat Hasil IA05 &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
