@php
    $jadwal = $jadwal ?? $schedule;
    $user = auth()->user();
    $isSingleAssessor = $jadwal->assessors->count() === 1 && $jadwal->assessors->first()?->id === $user->id;

    $myAssessments = $assessments->filter(function ($a) use ($user, $isSingleAssessor) {
        if ((int) $a->assessor_id === (int) $user->id) {
            return true;
        }
        if (empty($a->assessor_id) && $isSingleAssessor) {
            return true;
        }
        return false;
    });

    $otherAssessments = $assessments->reject(function ($a) use ($user, $isSingleAssessor) {
        if ((int) $a->assessor_id === (int) $user->id) {
            return true;
        }
        if (empty($a->assessor_id) && $isSingleAssessor) {
            return true;
        }
        return false;
    });

    $totalAsesi = $assessments->count();
    $myTotal = $myAssessments->count();
    $otherTotal = $otherAssessments->count();

    $myApproved = $myAssessments->where('apl_status', 'APPROVED')->count();
    $totalApproved = $assessments->where('apl_status', 'APPROVED')->count();
    $canAccessRuangUji = ($myTotal > 0 && $myApproved > 0) || ($myTotal === 0 && $totalApproved > 0);
@endphp
@extends('layouts.app', ['title' => 'Daftar Asesi - ' . ($jadwal->name ?? $jadwal->scheme->name)])

@section('content')
<div class="space-y-6" x-data="{ tab: 'all' }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('asesor.jadwal.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Jadwal</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $jadwal->name ?? $jadwal->scheme->name }}</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-500 mt-1 flex-wrap gap-y-1">
                <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded">{{ $jadwal->code }}</span>
                <span>{{ \Carbon\Carbon::parse($jadwal->date)->isoFormat('D MMMM Y') }}</span>
                <span>{{ substr($jadwal->start_time, 0, 5) }} - {{ substr($jadwal->end_time, 0, 5) }} WIB</span>
                <span class="text-slate-400">&bull;</span>
                <span>TUK: <strong>{{ $jadwal->tuk_name }}</strong></span>
            </div>
        </div>
        <div class="flex items-center space-x-2 flex-wrap gap-y-2">
            @if($canAccessRuangUji)
                <a href="{{ route('asesor.ak07.create', $jadwal->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-xs transition" title="FR.AK.07 Penyesuaian Wajar">
                    1. FR.AK.07
                </a>
                <a href="{{ route('asesor.schedule.ak01', $jadwal->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-xs transition" title="FR.AK.01 Persetujuan Asesmen">
                    2. FR.AK.01
                </a>
                <a href="{{ route('asesor.schedule.reports', $jadwal->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-xs transition" title="FR.AK.05 & FR.AK.06 Laporan Asesmen & Tutup Jadwal">
                    3. Laporan &amp; Tutup Jadwal
                </a>
            @else
                <div class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg shadow-xs">
                    <span class="mr-2 inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Verifikasi APL Berjalan ({{ $totalApproved }}/{{ $totalAsesi }} Disetujui)</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Filter Tab Buttons --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-3 flex-wrap gap-3">
        <div class="flex items-center space-x-2">
            <button type="button" @click="tab = 'all'" 
                :class="tab === 'all' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                Semua Asesi ({{ $totalAsesi }})
            </button>
            <button type="button" @click="tab = 'mine'" 
                :class="tab === 'mine' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-300 inline-block"></span>
                <span>Plottingan Saya ({{ $myTotal }})</span>
            </button>
            @if($otherTotal > 0)
                <button type="button" @click="tab = 'other'" 
                    :class="tab === 'other' ? 'bg-slate-700 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center space-x-1.5">
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Asesor Lain ({{ $otherTotal }})</span>
                </button>
            @endif
        </div>
        <div class="text-xs text-slate-500">
            Login Asesor: <strong class="text-slate-800">{{ $user->full_name }}</strong>
        </div>
    </div>

    {{-- CONTAINER 1: ASESI PLOTTINGAN SAYA --}}
    <div x-show="tab === 'all' || tab === 'mine'" class="bg-white rounded-xl border-2 border-emerald-500/30 shadow-sm overflow-hidden transition">
        <div class="px-6 py-4 bg-emerald-50/50 border-b border-emerald-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <h2 class="text-base font-bold text-slate-900">Asesi Plottingan Saya ({{ $myTotal }})</h2>
                </div>
                <p class="text-xs text-slate-600 mt-0.5">Peserta yang memilih / ditugaskan kepada Anda. Anda memiliki akses penuh untuk verifikasi APL dan pengujian.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                ✓ Akses Pengujian Aktif
            </span>
        </div>

        @if($myAssessments->isEmpty())
            <div class="p-8 text-center text-slate-500 text-sm">
                <p class="font-medium text-slate-700">Belum ada peserta yang memilih Anda sebagai asesor penguji pada jadwal ini.</p>
                <p class="text-xs text-slate-400 mt-1">Peserta dapat memilih Anda saat mengisi formulir FR.APL.01 Bagian 1.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Nama Asesi & Kontak</th>
                            <th class="px-3 py-3 text-left">Plotting Asesor</th>
                            <th class="px-3 py-3 text-center">Status APL</th>
                            <th class="px-3 py-3 text-center">Hasil Asesmen</th>
                            <th class="px-3 py-3 text-center">Banding (AK.04)</th>
                            <th class="px-4 py-3 text-right">Aksi Asesor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($myAssessments as $i => $assessment)
                            @php
                                $aplBadge = match($assessment->apl_status ?? 'DRAFT') {
                                    'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'REVISION_REQUESTED' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                                $hasilBadge = match($assessment->final_result ?? 'BELUM_SELESAI') {
                                    'KOMPETEN' => 'bg-emerald-100 text-emerald-800 border-emerald-400 font-black',
                                    'BELUM_KOMPETEN' => 'bg-rose-100 text-rose-800 border-rose-400 font-black',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                                $appeal = $assessment->latestAppeal;
                            @endphp
                            <tr class="hover:bg-emerald-50/20 transition">
                                <td class="px-4 py-3 text-slate-500 font-mono text-xs text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $assessment->asesi->full_name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $assessment->asesi->email }} | NIK: {{ $assessment->asesi->profile?->nik ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-left">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✓ Anda
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $aplBadge }}">
                                        {{ str_replace('_', ' ', $assessment->apl_status ?? 'DRAFT') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] border {{ $hasilBadge }}">
                                        {{ $assessment->final_result ?? ($assessment->status === 'COMPLETED' ? 'KOMPETEN' : 'BELUM SELESAI') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($appeal)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold border bg-purple-50 text-purple-700 border-purple-300">
                                            {{ $appeal->status }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5 flex-wrap gap-y-1">
                                        @if(in_array($assessment->apl_status, ['SUBMITTED', 'REVISION_REQUESTED']))
                                            <a href="{{ route('asesor.review-apl', $assessment->id) }}" class="px-3 py-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-xs transition">
                                                Review APL &rarr;
                                            </a>
                                        @elseif($assessment->apl_status === 'APPROVED')
                                            <a href="{{ route('asesor.assessment.ruang-uji', $assessment->id) }}" class="px-3 py-1.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-xs transition">
                                                Ruang Uji &rarr;
                                            </a>
                                            <a href="{{ route('asesor.assessment.ak02', $assessment->id) }}" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-xs transition" title="FR.AK.02 Rekaman Asesmen">
                                                FR.AK.02
                                            </a>
                                        @elseif($assessment->apl_status === 'REJECTED')
                                            <span class="px-2 py-0.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-300 rounded">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded">
                                                Menunggu Pengajuan APL
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- CONTAINER 2: ASESI ASESOR LAIN PADA JADWAL INI --}}
    @if($otherTotal > 0)
        <div x-show="tab === 'all' || tab === 'other'" class="bg-white rounded-xl border border-slate-300 shadow-sm overflow-hidden opacity-95 transition">
            <div class="px-6 py-4 bg-slate-100/70 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <h2 class="text-base font-bold text-slate-800">Daftar Asesi Asesor Lain ({{ $otherTotal }})</h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Peserta berikut di-plot kepada rekan asesor lain. Formulir penilaian dinonaktifkan (mode pantau saja).</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 border border-slate-300">
                    🔒 Terkunci (Bukan Plottingan Anda)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Nama Asesi & Kontak</th>
                            <th class="px-3 py-3 text-left">Asesor Penguji (Di-plot)</th>
                            <th class="px-3 py-3 text-center">Status APL</th>
                            <th class="px-3 py-3 text-center">Hasil Asesmen</th>
                            <th class="px-3 py-3 text-center">Banding (AK.04)</th>
                            <th class="px-4 py-3 text-right">Aksi Asesor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($otherAssessments as $i => $assessment)
                            @php
                                $aplBadge = match($assessment->apl_status ?? 'DRAFT') {
                                    'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'REVISION_REQUESTED' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                                $hasilBadge = match($assessment->final_result ?? 'BELUM_SELESAI') {
                                    'KOMPETEN' => 'bg-emerald-100 text-emerald-800 border-emerald-400 font-black',
                                    'BELUM_KOMPETEN' => 'bg-rose-100 text-rose-800 border-rose-400 font-black',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                                $appeal = $assessment->latestAppeal;
                            @endphp
                            <tr class="hover:bg-slate-50/50 bg-slate-50/20 text-slate-500 transition">
                                <td class="px-4 py-3 text-slate-400 font-mono text-xs text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">{{ $assessment->asesi->full_name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $assessment->asesi->email }} | NIK: {{ $assessment->asesi->profile?->nik ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-left">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-white text-slate-700 border border-slate-300">
                                        {{ $assessment->assessor?->full_name ?? 'Belum Memilih Asesor' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $aplBadge }}">
                                        {{ str_replace('_', ' ', $assessment->apl_status ?? 'DRAFT') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] border {{ $hasilBadge }}">
                                        {{ $assessment->final_result ?? ($assessment->status === 'COMPLETED' ? 'KOMPETEN' : 'BELUM SELESAI') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($appeal)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold border bg-purple-50 text-purple-700 border-purple-300">
                                            {{ $appeal->status }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" disabled 
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 rounded-lg cursor-not-allowed select-none" 
                                        title="Asesi ini di-plot kepada {{ $assessment->assessor?->full_name ?? 'rekan asesor lain' }}. Anda tidak dapat menilai asesi ini.">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
