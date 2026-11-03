@extends('layouts.app', ['title' => 'Dashboard Asesi'])

@section('content')
<div class="space-y-6">
    {{-- Header Banner Lembaga Sertifikasi --}}
    <div class="bg-gradient-to-r from-lsp-navy to-lsp-navy-dark rounded-xl p-5 sm:p-6 md:p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 border-t-4 border-lsp-red">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/10 text-lsp-red border border-lsp-red/30 text-[10px] font-extrabold tracking-wider uppercase">
                    LSP PrO Indo &bull; Berlisensi BNSP
                </span>
                <span class="inline-block px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-200 border border-blue-400/20 text-[10px] font-semibold tracking-wider">
                    Sistem Asesmen Berbasis Modul MUK
                </span>
            </div>
            <h1 class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->full_name }}!
            </h1>
            <p class="text-blue-100/80 text-xs mt-1 max-w-xl">
                Portofolio digital asesmen kompetensi profesi teknisi otomotif terintegrasi standar Badan Nasional Sertifikasi Profesi (BNSP).
            </p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:space-x-3 shrink-0">
            <a href="{{ route('asesi.jadwal.index') }}" class="px-4 py-2.5 bg-lsp-red hover:bg-lsp-red-dark text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Gabung Jadwal Baru</span>
            </a>
            <a href="{{ route('asesi.profile.edit') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg text-xs font-semibold transition text-center justify-center">
                Profil & TTD Saya
            </a>
        </div>
    </div>

    {{-- Alert: Incomplete Profile / Signature --}}
    @php
        $profile = auth()->user()->profile;
        $hasCompleteProfile = $profile && $profile->nik && $profile->signature_data;
    @endphp
    @if(!$hasCompleteProfile)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4.5 text-amber-900 flex items-start space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1 text-sm">
                <h4 class="font-bold">Biodata atau Tanda Tangan Belum Lengkap!</h4>
                <p class="mt-0.5 text-amber-800 text-xs">
                    Sebelum mengikuti proses asesmen dan mengisi formulir FR.APL.01/02, Anda wajib melengkapi data diri serta tanda tangan digital pada profil akun Anda.
                </p>
                <div class="mt-2.5">
                    <a href="{{ route('asesi.profile.edit') }}" class="inline-flex items-center text-xs font-bold text-amber-950 underline hover:text-black">
                        Lengkapi Data Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Status Panduan / Single Status Guide (Non-animated, Professional, Sober) --}}
    @if(isset($nextStep))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 border-l-4 border-l-lsp-navy">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-300">
                            Langkah Berikutnya
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $nextStep['badge_color'] }}">
                            {{ $nextStep['badge'] }}
                        </span>
                    </div>

                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                        {{ $nextStep['title'] }}
                    </h2>

                    <p class="text-xs text-slate-600 max-w-3xl leading-relaxed">
                        {{ $nextStep['description'] }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                    @if(!empty($nextStep['is_code_form']))
                        <form action="{{ route('asesi.jadwal.join') }}" method="POST" class="flex flex-col sm:flex-row items-stretch gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="Kode Jadwal (4 Digit)" required maxlength="20" class="px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-lg focus:ring-2 focus:ring-lsp-navy text-slate-900 bg-white">
                            <button type="submit" class="px-4 py-2 bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center justify-center space-x-1.5">
                                <span>{{ $nextStep['action_label'] }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        @if(!empty($nextStep['action_url']) && !empty($nextStep['action_label']))
                            <a href="{{ $nextStep['action_url'] }}" class="px-4 py-2 bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center justify-center space-x-1.5 text-center">
                                <span>{{ $nextStep['action_label'] }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        @endif

                        @if(!empty($nextStep['secondary_url']) && !empty($nextStep['secondary_label']))
                            <a href="{{ $nextStep['secondary_url'] }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold border border-slate-300 transition text-center">
                                {{ $nextStep['secondary_label'] }}
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    @php
        $assessments = $assessments ?? auth()->user()->scheduleAssessments()->with(['schedule.scheme', 'assessor'])->latest()->get();
        $totalAssessments = $assessments->count();
        $approvedCount = $assessments->where('apl_status', 'APPROVED')->count();
        $completedCount = $assessments->where('status', 'COMPLETED')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jadwal Terdaftar</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $totalAssessments }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">APL Disetujui</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $approvedCount }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Asesmen Selesai</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $completedCount }}</h3>
            </div>
        </div>
    </div>

    {{-- Enrolled Schedules & Modular MUK System Matrix --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Program Sertifikasi Kompetensi Terdaftar</h2>
                <p class="text-xs text-slate-500">Setiap jadwal dijalankan berdasarkan modul instrumen asesmen MUK berstandar BNSP</p>
            </div>
            <a href="{{ route('asesi.jadwal.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                Lihat Semua Jadwal &rarr;
            </a>
        </div>

        @if($assessments->isEmpty())
            <div class="p-8 text-center">
                <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-700">Belum Ada Jadwal Uji</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Masukkan kode jadwal asesmen 4-digit dari penyelenggara TUK untuk mendaftar dan memulai modul sertifikasi.
                </p>
                <div class="mt-4">
                    <a href="{{ route('asesi.jadwal.index') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow-sm transition">
                        Gabung Jadwal Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="divide-y divide-slate-200">
                @foreach($assessments as $item)
                    @php
                        $sch = $item->schedule;
                        $hasFinalDecision = ($item->status === 'COMPLETED') || in_array($item->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true);
                        $isKompeten = $item->isCompetent();
                        $aplApproved = $item->apl_status === 'APPROVED';
                        $ak01Agreed = !empty($item->ak01_agreed_at);
                    @endphp

                    <div class="p-5 sm:p-6 space-y-5">
                        {{-- Schedule & Scheme Header --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $sch->code }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $aplApproved ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $aplApproved ? 'APL Disetujui' : 'APL: ' . str_replace('_', ' ', $item->apl_status ?? 'DRAFT') }}
                                    </span>
                                    @if($hasFinalDecision)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $isKompeten ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300' }}">
                                            Hasil: {{ $item->final_result }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">
                                    {{ $sch->name ?? $sch->scheme->name }}
                                </h3>
                                <div class="flex items-center text-xs text-slate-500 gap-x-4 flex-wrap gap-y-1 mt-1">
                                    <span>TUK: <strong class="text-slate-700">{{ $sch->tuk_name }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Tanggal: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($sch->date)->isoFormat('D MMMM Y') }}</strong> ({{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB)</span>
                                    @if($item->assessor)
                                        <span>&bull;</span>
                                        <span>Asesor Penguji: <strong class="text-slate-700">{{ $item->assessor->full_name }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            @if($item->apl_status === 'REVISION_REQUESTED' && ($item->apl_revision_notes || $item->admin_notes))
                                <div class="p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-xs text-rose-900 max-w-md">
                                    <div class="font-bold">Catatan Perbaikan Berkas:</div>
                                    <p class="text-rose-800 mt-0.5">{{ $item->apl_revision_notes ?? $item->admin_notes }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Modular MUK Architecture Cards (Hanya Menampilkan Modul yang Aktif / Terbuka) --}}
                        <div @class(['grid grid-cols-1 sm:grid-cols-2 gap-3', 'lg:grid-cols-4' => $hasFinalDecision, 'lg:grid-cols-3' => (!$hasFinalDecision && $aplApproved), 'lg:grid-cols-1' => (!$hasFinalDecision && !$aplApproved)])>
                            {{-- Modul 1: FR.APL.01 & FR.APL.02 --}}
                            <div class="border rounded-lg p-3.5 flex flex-col justify-between bg-slate-50/50 {{ $aplApproved ? 'border-emerald-200' : 'border-slate-200' }}">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-[10px] font-bold text-slate-500">MODUL 1</span>
                                        @if($aplApproved)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Disetujui</span>
                                        @elseif($item->apl_status === 'SUBMITTED')
                                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Diverifikasi</span>
                                        @elseif($item->apl_status === 'REVISION_REQUESTED')
                                            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">Revisi</span>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-600 bg-slate-200 px-1.5 py-0.5 rounded">Draft</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-xs text-slate-900 mt-1.5">{{ 'FR.APL.01 & 02' }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Permohonan pendaftaran & asesmen mandiri K/BK</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200/60">
                                    <a href="{{ route('asesi.assessment.apl', $item->id) }}" class="block text-center text-xs font-bold px-3 py-1.5 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 transition">
                                        {{ $aplApproved ? 'Lihat Formulir APL' : 'Lengkapi APL (01 & 02) →' }}
                                    </a>
                                </div>
                            </div>

                            @if($aplApproved)
                                {{-- Modul 2: FR.AK.01 (Persetujuan Otomatis - Asesi Tidak Perlu TTD Manual) --}}
                                <div class="border rounded-lg p-3.5 flex flex-col justify-between bg-slate-50/50 border-emerald-200">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono text-[10px] font-bold text-slate-500">MODUL 2</span>
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Disepakati</span>
                                        </div>
                                        <h4 class="font-bold text-xs text-slate-900 mt-1.5">FR.AK.01</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Persetujuan asesmen & kerahasiaan (tanda tangan otomatis tersemat)</p>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-200/60">
                                        <a href="{{ route('asesi.assessment.ak01', $item->id) }}" class="block text-center text-xs font-bold px-3 py-1.5 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 transition">
                                            Lihat Lembar AK.01
                                        </a>
                                    </div>
                                </div>

                                {{-- Modul 3: Ruang Uji MUK (Aktif & Terbuka) --}}
                                <div class="border rounded-lg p-3.5 flex flex-col justify-between bg-slate-50/50 {{ $hasFinalDecision ? 'border-emerald-200' : 'border-indigo-200 ring-1 ring-indigo-500/20' }}">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono text-[10px] font-bold text-slate-500">MODUL 3</span>
                                            @if($hasFinalDecision)
                                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Uji Selesai</span>
                                            @else
                                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200">Aktif Terbuka</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-xs text-slate-900 mt-1.5">Ruang Uji MUK</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Ujian Teori CBT (IA.05), Praktik & PMO sesuai instruksi asesor</p>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-200/60">
                                        <a href="{{ route('asesi.assessment.ruang-uji', $item->id) }}" class="block text-center text-xs font-bold px-3 py-1.5 rounded bg-brand-600 hover:bg-brand-700 text-white transition">
                                            Masuk Ruang Uji →
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($hasFinalDecision)
                                {{-- Modul 4: Hasil & Sertifikat BNSP / Umpan Balik --}}
                                <div class="border rounded-lg p-3.5 flex flex-col justify-between bg-slate-50/50 {{ $isKompeten ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200' }}">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono text-[10px] font-bold text-slate-500">MODUL 4</span>
                                            @if($isKompeten && $item->no_sertifikat)
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded border border-emerald-300">Sertifikat Terbit</span>
                                            @else
                                                <span class="text-[10px] font-bold text-slate-700 bg-slate-200 px-1.5 py-0.5 rounded">{{ $item->final_result }}</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-xs text-slate-900 mt-1.5">{{ 'Hasil & Sertifikat' }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            @if($isKompeten && $item->no_sertifikat)
                                                No: <strong class="text-slate-800 font-mono">{{ $item->no_sertifikat }}</strong>
                                            @else
                                                Rekaman AK.02, Umpan Balik (AK.03) & Sertifikat
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center space-x-1.5">
                                        @if($isKompeten && $item->no_sertifikat)
                                            <a href="{{ route('asesi.certificates.index') }}" class="flex-1 text-center text-xs font-bold px-2 py-1.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                                Lihat Sertifikat
                                            </a>
                                        @else
                                            <a href="{{ route('asesi.assessment.ruang-uji', $item->id) }}" class="flex-1 text-center text-xs font-bold px-2 py-1.5 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 transition">
                                                Lihat Rekap Hasil
                                            </a>
                                        @endif
                                        <a href="{{ route('asesi.assessment.ak03', $item->id) }}" class="text-center text-xs font-bold px-2 py-1.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 transition" title="FR.AK.03 Umpan Balik">
                                            AK.03
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!$aplApproved)
                            <div class="mt-3 px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs text-slate-600">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                    <span><strong>Ruang Uji Terbuka Otomatis:</strong> Setelah berkas APL disetujui asesor penguji, instrumen uji akan otomatis aktif di dasbor ini.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
