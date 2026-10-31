@extends('layouts.app')

@section('title', 'Dokumen Asesi - ' . strtoupper($exportType))

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('admin.dokumen-muk.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Dokumen MUK</a>
        <button onclick="window.print()" class="px-4 py-2 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
            Cetak / Download PDF
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 space-y-6">
        <div class="border-b-2 border-slate-900 pb-4 text-center">
            <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">LEMBAGA SERTIFIKASI PROFESI PRO INDO</h1>
            <p class="text-xs text-slate-600">Lisensi BNSP - Berkas Portofolio Asesmen {{ strtoupper($exportType) }}</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-2 text-xs">
            <div class="grid grid-cols-2 gap-2">
                <div><span class="text-slate-500">Nama Asesi:</span> <strong class="text-slate-900">{{ $assessment->asesi->full_name }}</strong></div>
                <div><span class="text-slate-500">NIK:</span> <span class="font-mono">{{ $assessment->asesi->profile?->nik ?? '-' }}</span></div>
                <div><span class="text-slate-500">Skema:</span> <strong>{{ $assessment->schedule->scheme->name }}</strong></div>
                <div><span class="text-slate-500">Jadwal:</span> {{ $assessment->schedule->name }} ({{ $assessment->schedule->tuk_name }})</div>
                <div><span class="text-slate-500">Status APL:</span> <strong class="text-emerald-700">{{ $assessment->apl_status }}</strong></div>
                <div><span class="text-slate-500">Hasil Asesmen:</span> <strong class="{{ $assessment->status === 'COMPLETED' ? 'text-emerald-700' : 'text-slate-600' }}">{{ $assessment->status }}</strong></div>
            </div>
        </div>

        @if($assessment->asesi_signature || $assessment->asesi->profile?->signature_data)
            <div class="pt-6 border-t border-slate-200 flex justify-between items-end">
                <div></div>
                <div class="text-center w-64">
                    <p class="text-xs text-slate-500 mb-2">Tanda Tangan Digital Asesi</p>
                    <div class="border border-slate-200 rounded p-2 bg-white inline-block">
                        <img src="{{ $assessment->asesi_signature ?? $assessment->asesi->profile?->signature_data }}" alt="TTD Asesi" class="max-h-20">
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">{{ $assessment->asesi->full_name }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
