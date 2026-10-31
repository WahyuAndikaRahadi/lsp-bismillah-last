@extends('layouts.app', ['title' => 'AK01 - Persetujuan Asesmen'])

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('asesor.schedule.instrument', ['schedule' => $assessment->schedule_id, 'code' => 'ak01']) }}" class="text-xs text-brand-600 hover:text-brand-700 font-bold">&larr; Kembali ke Daftar Asesi AK.01</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">AK01 – Persetujuan Asesmen</h1>
        <p class="text-sm text-slate-500">Asesi: <strong>{{ $assessment->asesi->full_name }}</strong></p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Detail Persetujuan</h2>
        </div>

        <div class="p-6 space-y-6">
            {{-- Schedule Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Skema Sertifikasi</span>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->schedule->scheme->name }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">TUK</span>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->schedule->tuk_name }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Asesmen</span>
                    <p class="text-slate-900 font-medium mt-0.5">{{ \Carbon\Carbon::parse($assessment->schedule->date)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</span>
                    <p class="text-slate-900 font-medium mt-0.5">{{ substr($assessment->schedule->start_time, 0, 5) }} - {{ substr($assessment->schedule->end_time, 0, 5) }} WIB</p>
                </div>
            </div>

            {{-- Asesi Info --}}
            <div class="border-t border-slate-200 pt-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Identitas Asesi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Lengkap</span>
                        <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->asesi->full_name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</span>
                        <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->asesi->email }}</p>
                    </div>
                    @if($assessment->asesi->profile)
                        <div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">NIK</span>
                            <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->asesi->profile->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tempat, Tanggal Lahir</span>
                            <p class="text-slate-900 font-medium mt-0.5">{{ $assessment->asesi->profile->tempat_lahir ?? '-' }}, {{ $assessment->asesi->profile->tanggal_lahir ? \Carbon\Carbon::parse($assessment->asesi->profile->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assessor Info --}}
            <div class="border-t border-slate-200 pt-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Identitas Asesor</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Asesor</span>
                        <p class="text-slate-900 font-medium mt-0.5">{{ auth()->user()->full_name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Registrasi</span>
                        <p class="text-slate-900 font-medium mt-0.5">{{ auth()->user()->profile->no_registrasi ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Agreement --}}
            <div class="border-t border-slate-200 pt-4">
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-sm text-blue-900">
                    <h4 class="font-bold mb-2">Pernyataan Persetujuan</h4>
                    <p class="text-xs leading-relaxed">
                        Dengan ini asesor dan asesi menyatakan bahwa proses asesmen telah didiskusikan dan disepakati bersama, termasuk tujuan asesmen, konteks asesmen, persyaratan bukti, metode asesmen, serta mekanisme pengajuan banding.
                    </p>
                </div>
            </div>

            {{-- Signatures --}}
            <div class="border-t border-slate-200 pt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanda Tangan Asesi</h4>
                    @if($assessment->asesi_signature)
                        <div class="border border-slate-200 rounded-lg p-2 bg-white">
                            <img src="{{ $assessment->asesi_signature }}" alt="TTD Asesi" class="max-h-24">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Ditandatangani: {{ $assessment->asesi_signed_at ? \Carbon\Carbon::parse($assessment->asesi_signed_at)->isoFormat('D MMM Y HH:mm') : '-' }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum ditandatangani</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanda Tangan Asesor</h4>
                    @if($assessment->assessor_signature)
                        <div class="border border-slate-200 rounded-lg p-2 bg-white">
                            <img src="{{ $assessment->assessor_signature }}" alt="TTD Asesor" class="max-h-24">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Ditandatangani: {{ $assessment->assessor_signed_at ? \Carbon\Carbon::parse($assessment->assessor_signed_at)->isoFormat('D MMM Y HH:mm') : '-' }}</p>
                    @else
                        <form action="{{ route('asesor.ak01.sign', $assessment->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <x-signature-pad name="assessor_signature" :value="auth()->user()->profile?->signature_data" label="Tanda tangan di bawah ini" :required="true" />
                            <button type="submit" class="px-5 py-2 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                                Tanda Tangani AK01
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
