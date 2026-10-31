@extends('layouts.app', ['title' => 'Jadwal Asesmen'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Jadwal Asesmen Kompetensi</h1>
            <p class="text-sm text-slate-500">
                Pilih atau masukkan kode jadwal untuk mengikuti asesmen sertifikasi profesi
            </p>
        </div>
    </div>

    {{-- Join Schedule Form Box --}}
    @php
        $profileComplete = auth()->user()->isProfileComplete();
    @endphp

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="max-w-2xl">
            <h2 class="text-base font-bold text-slate-900">Gabung Jadwal Asesmen Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">
                Masukkan kode verifikasi jadwal (contoh: <code class="px-1.5 py-0.5 bg-slate-100 rounded text-brand-700 font-semibold font-mono">LSP-xxxx</code>) yang diberikan oleh penyelenggara atau sekolah Anda.
            </p>

            @if(! $profileComplete)
                <div class="p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800">Biodata & Tanda Tangan Belum Lengkap</h3>
                            <p class="text-xs text-amber-700 mt-0.5">Form pendaftaran jadwal dikunci sampai Anda melengkapi seluruh data profil dan tanda tangan digital Anda.</p>
                        </div>
                    </div>
                    <a href="{{ route('asesi.profile.edit') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shrink-0 text-center">
                        Lengkapi Profil & TTD &rarr;
                    </a>
                </div>
            @endif

            <form action="{{ route('asesi.jadwal.join') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="code" 
                        required 
                        @disabled(! $profileComplete)
                        placeholder="Masukkan Kode Jadwal (Misal: LSP-K9P2)" 
                        value="{{ old('code') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 font-mono uppercase tracking-wider text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none placeholder-normal @if(! $profileComplete) bg-slate-100 cursor-not-allowed opacity-60 @endif"
                    >
                    @error('code')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" @disabled(! $profileComplete) class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-lg shadow-sm transition shrink-0 flex items-center justify-center space-x-2 @if(! $profileComplete) opacity-50 cursor-not-allowed pointer-events-none @endif">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Gabung Jadwal</span>
                </button>
            </form>
        </div>
    </div>

    {{-- List of Enrolled Schedules --}}
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Jadwal yang Sedang Diikuti</h2>

        @php
            $assessments = $assessments ?? auth()->user()->scheduleAssessments()->with(['schedule.scheme', 'assessor', 'schedule.assessors'])->latest()->get();
        @endphp

        @if($assessments->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Belum Terdaftar di Jadwal Mana Pun</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Silakan minta kode jadwal kepada pengelola TUK atau asesor Anda dan masukkan pada kolom di atas.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($assessments as $item)
                    @php
                        $sch = $item->schedule;
                        $aplStatus = $item->apl_status;

                        $aplBadge = match($aplStatus) {
                            'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'REVISION_REQUESTED' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };

                        $isResume = ($aplStatus === 'APPROVED');
                        $actionUrl = route('asesi.assessment.apl', $item->id);

                        if ($aplStatus === 'APPROVED') {
                            $actionUrl = route('asesi.assessment.ruang-uji', $item->id);
                        }
                    @endphp

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded bg-brand-50 text-brand-700 font-mono text-xs font-bold">
                                    {{ $sch->code }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $aplBadge }}">
                                    APL: {{ str_replace('_', ' ', $aplStatus) }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">
                                    {{ $sch->name ?? $sch->scheme->name }}
                                </h3>
                                <p class="text-xs text-brand-600 font-medium mt-0.5">
                                    Skema: {{ $sch->scheme->name }} ({{ $sch->scheme->code }})
                                </p>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>TUK: <strong class="text-slate-800">{{ $sch->tuk_name }}</strong></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($sch->date)->isoFormat('dddd, D MMMM Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB ({{ $sch->duration_minutes ?? '-' }} Menit)</span>
                                </div>
                                @if($item->assessor)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/>
                                        </svg>
                                        <span>Asesor: <strong class="text-slate-800">{{ $item->assessor->full_name }}</strong></span>
                                    </div>
                                @endif
                            </div>

                            @if($aplStatus === 'REVISION_REQUESTED' && $item->apl_revision_notes)
                                <div class="p-2.5 rounded bg-amber-50 border border-amber-200 text-xs text-amber-900">
                                    <strong class="font-bold">Catatan Revisi Asesor:</strong>
                                    <p class="mt-0.5">{{ $item->apl_revision_notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                Terdaftar: {{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}
                            </span>

                            @if($item->status === 'COMPLETED')
                                <a href="{{ route('asesi.assessment.ruang-uji', $item->id) }}" class="px-4 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white rounded-lg transition">
                                    Lihat Hasil Asesmen &rarr;
                                </a>
                            @elseif($isResume)
                                <a href="{{ $actionUrl }}" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">
                                    Lanjutkan Asesmen &rarr;
                                </a>
                            @elseif($aplStatus === 'REVISION_REQUESTED')
                                <a href="{{ route('asesi.assessment.apl', $item->id) }}" class="px-4 py-2 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition">
                                    Perbaiki APL &rarr;
                                </a>
                            @else
                                <a href="{{ route('asesi.assessment.apl', $item->id) }}" class="px-4 py-2 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                                    Masuk Jadwal Uji &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
