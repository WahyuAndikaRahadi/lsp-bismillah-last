@extends('layouts.app')

@section('title', 'Ekspor Dokumen - ' . strtoupper($exportType))

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('admin.dokumen-muk.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Dokumen MUK</a>
        <button onclick="window.print()" class="px-4 py-2 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
            Cetak / Download PDF
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 space-y-6">
        <div class="border-b-2 border-slate-900 pb-4 text-center">
            <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">LEMBAGA SERTIFIKASI PROFESI PRO INDO</h1>
            <p class="text-xs text-slate-600">Lisensi BNSP - Jl. Kemakmuran Raya No. 15, Ruko Blessing Center Blok B-06, Bekasi Selatan</p>
            <h2 class="text-base font-bold text-brand-700 mt-3 uppercase tracking-wide">
                {{ str_replace('-', ' ', strtoupper($exportType)) }}
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-500 font-medium">Jadwal:</span>
                <span class="font-bold text-slate-800 ml-2">{{ $schedule->name }} ({{ $schedule->code }})</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">Skema:</span>
                <span class="font-bold text-slate-800 ml-2">{{ $schedule->scheme->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">TUK:</span>
                <span class="font-bold text-slate-800 ml-2">{{ $schedule->tuk_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">Tanggal:</span>
                <span class="font-bold text-slate-800 ml-2">{{ \Carbon\Carbon::parse($schedule->date)->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-700 font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">No</th>
                        <th class="px-3 py-2 text-left">Nama Asesi</th>
                        <th class="px-3 py-2 text-left">NIK</th>
                        <th class="px-3 py-2 text-left">No Telp</th>
                        <th class="px-3 py-2 text-center">Status APL</th>
                        <th class="px-3 py-2 text-center">Rekomendasi / TTD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($schedule->assessments as $idx => $assessment)
                        <tr>
                            <td class="px-3 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2 font-bold text-slate-900">{{ $assessment->asesi->full_name }}</td>
                            <td class="px-3 py-2 font-mono text-slate-600">{{ $assessment->asesi->profile?->nik ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $assessment->asesi->phone ?? '-' }}</td>
                            <td class="px-3 py-2 text-center font-bold {{ $assessment->apl_status === 'APPROVED' ? 'text-emerald-600' : 'text-slate-500' }}">
                                {{ $assessment->apl_status }}
                            </td>
                            <td class="px-3 py-2 text-center font-bold {{ $assessment->status === 'COMPLETED' ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $assessment->status === 'COMPLETED' ? 'KOMPETEN (K)' : 'BELUM SELESAI' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-slate-400 italic">Belum ada asesi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
