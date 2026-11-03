@extends('layouts.app', ['title' => 'Review Jadwal & Peserta - ' . $jadwal->name])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.jadwal.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Daftar Jadwal</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $jadwal->name }}</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-500 mt-1">
                <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded">{{ $jadwal->code }}</span>
                <span>{{ $jadwal->scheme->name }}</span>
                <span>TUK: {{ $jadwal->tuk_name }}</span>
                <span>{{ \Carbon\Carbon::parse($jadwal->date)->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>
        @if(Route::has('admin.dokumen-muk.index'))
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.dokumen-muk.index') }}" class="px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-xs transition">
                Pusat Dokumen MUK &rarr;
            </a>
        </div>
        @endif
    </div>

    {{-- Review Asesi Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Peserta Asesmen ({{ $jadwal->assessments->count() }})</h2>
                <p class="text-xs text-slate-500">Status verifikasi APL oleh asesor, progres ujian, dan hasil rekomendasi asesmen</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">No</th>
                        <th class="px-4 py-3 text-left">Nama Asesi & Kontak</th>
                        <th class="px-3 py-3 text-left">Asesor Penguji</th>
                        <th class="px-3 py-3 text-center">Status APL (Asesor)</th>
                        <th class="px-3 py-3 text-center">Hasil Asesmen</th>
                        <th class="px-3 py-3 text-center">Banding (AK.04)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwal->assessments as $idx => $assessment)
                        @php
                            $asesorBadge = match($assessment->apl_status ?? 'DRAFT') {
                                'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                                'REVISION_REQUESTED' => 'bg-amber-50 text-amber-700 border-amber-300',
                                'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-300',
                                'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-300',
                                default => 'bg-slate-100 text-slate-600 border-slate-300',
                            };
                            $hasilBadge = match($assessment->final_result ?? 'BELUM_SELESAI') {
                                'KOMPETEN' => 'bg-emerald-100 text-emerald-800 border-emerald-400 font-black',
                                'BELUM_KOMPETEN' => 'bg-rose-100 text-rose-800 border-rose-400 font-black',
                                default => 'bg-slate-100 text-slate-500 border-slate-300',
                            };
                            $appeal = $assessment->latestAppeal;
                        @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block text-sm">{{ $assessment->asesi->full_name }}</span>
                                <span class="text-slate-500 font-mono text-[11px]">{{ $assessment->asesi->email }} | NIK: {{ $assessment->asesi->profile?->nik ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="font-semibold text-slate-800 block">{{ $assessment->assessor?->full_name ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $assessment->assessor?->assessor_registration_number ?? '' }}</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $asesorBadge }}">
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
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">
                                Belum ada asesi yang terdaftar pada jadwal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
