@extends('layouts.app', ['title' => 'Jadwal Penugasan Asesor'])

@section('content')
@php
    $jadwals = $jadwals ?? $schedules ?? collect();
@endphp
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Jadwal Uji Kompetensi</h1>
            <p class="text-sm text-slate-500">
                Asesor: <strong>{{ auth()->user()->full_name }}</strong>
                @php
                    $metNo = auth()->user()->assessor_registration_number ?? auth()->user()->asesorProfile?->no_met ?? auth()->user()->profile?->met_number;
                @endphp
                @if($metNo)
                    <span class="ml-2 font-mono text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">{{ $metNo }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Banner Reminder if Asesor Signature is Not Set --}}
    @php
        $asesorProfile = auth()->user()->profile;
        $hasSignature = !empty($asesorProfile?->signature_data);
    @endphp
    @if(!$hasSignature)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-900 flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1 text-sm">
                <h4 class="font-bold text-amber-900">Tanda Tangan Digital Belum Disimpan!</h4>
                <p class="mt-0.5 text-xs text-amber-800">
                    Anda belum menyimpan master tanda tangan digital. Setel tanda tangan Anda sekarang agar otomatis digunakan saat menandatangani persetujuan AK.01 dan rubrik instrumen uji.
                </p>
                <div class="mt-2.5">
                    <a href="{{ route('asesor.profile.edit') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                        Atur Biodata & TTD Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Perhatian Asesor: Tugas Yang Perlu Dikerjakan --}}
    @if((isset($pendingAplAssessments) && $pendingAplAssessments->count() > 0) || (isset($pendingScoringAssessments) && $pendingScoringAssessments->count() > 0))
        <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-amber-200/80 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-amber-950">Perhatian Asesor: Tugas Yang Perlu Diselesaikan</h2>
                </div>
                <span class="text-xs font-semibold text-amber-800">
                    {{ ($pendingAplAssessments->count() ?? 0) + ($pendingScoringAssessments->count() ?? 0) }} Asesi Memerlukan Tindakan
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- 1. Asesi Menunggu Review APL --}}
                @if(isset($pendingAplAssessments) && $pendingAplAssessments->count() > 0)
                    <div class="bg-white rounded-lg border border-amber-200 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Menunggu Review APL (01/02)</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">
                                {{ $pendingAplAssessments->count() }} Asesi
                            </span>
                        </div>
                        <div class="space-y-2">
                            @foreach($pendingAplAssessments->take(3) as $aplItem)
                                <div class="flex items-center justify-between p-2 rounded bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $aplItem->asesi->full_name }}</span>
                                        <span class="text-[11px] text-slate-500 font-mono">{{ $aplItem->schedule->code }} &bull; {{ $aplItem->schedule->scheme->name }}</span>
                                    </div>
                                    <a href="{{ route('asesor.review-apl', $aplItem->id) }}" class="px-2.5 py-1 text-[11px] font-bold bg-amber-500 hover:bg-amber-600 text-white rounded transition shrink-0">
                                        Review APL &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 2. Asesi Menunggu Penilaian Instrumen --}}
                @if(isset($pendingScoringAssessments) && $pendingScoringAssessments->count() > 0)
                    <div class="bg-white rounded-lg border border-blue-200 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Siap Diuji & Dinilai (Instrumen Praktik/CBT)</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800">
                                {{ $pendingScoringAssessments->count() }} Asesi
                            </span>
                        </div>
                        <div class="space-y-2">
                            @foreach($pendingScoringAssessments->take(3) as $scItem)
                                <div class="flex items-center justify-between p-2 rounded bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $scItem->asesi->full_name }}</span>
                                        <span class="text-[11px] text-slate-500 font-mono">{{ $scItem->schedule->code }}</span>
                                    </div>
                                    <a href="{{ route('asesor.assessment.ruang-uji', $scItem->id) }}" class="px-2.5 py-1 text-[11px] font-bold bg-brand-600 hover:bg-brand-700 text-white rounded transition shrink-0">
                                        Ruang Uji &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($jadwals->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center shadow-sm">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-700">Belum Ada Penugasan</h3>
            <p class="text-xs text-slate-500 mt-1">Anda belum ditugaskan pada jadwal asesmen mana pun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($jadwals as $jadwal)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded bg-brand-50 text-brand-700 font-mono text-xs font-bold">
                                {{ $jadwal->code }}
                            </span>
                            @php
                                $statusBadge = match($jadwal->status ?? 'ACTIVE') {
                                    'COMPLETED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $statusBadge }}">
                                {{ $jadwal->status ?? 'AKTIF' }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                {{ $jadwal->name ?? $jadwal->scheme->name }}
                            </h3>
                            <p class="text-xs text-brand-600 font-medium mt-0.5">
                                Skema: {{ $jadwal->scheme->name }} ({{ $jadwal->scheme->code }})
                            </p>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>TUK: <strong class="text-slate-800">{{ $jadwal->tuk_name }}</strong></span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($jadwal->date)->isoFormat('dddd, D MMMM Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ substr($jadwal->start_time, 0, 5) }} - {{ substr($jadwal->end_time, 0, 5) }} WIB</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Jumlah Asesi: <strong class="text-slate-800">{{ $jadwal->assessments_count ?? $jadwal->assessments->count() }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <a href="{{ route('asesor.jadwal.asesi', $jadwal->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                            Masuk Asesmen &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
