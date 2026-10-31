@extends('layouts.app', ['title' => $meta['code'] . ' - ' . $meta['title']])

@section('content')
<div class="space-y-6">
    {{-- Header & Back Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold mb-1">
                <a href="{{ route('asesor.schedule.ruang-uji', $schedule->id) }}" class="text-brand-600 hover:text-brand-700 font-bold">&larr; Kembali ke Ruang Uji Jadwal</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-700">{{ $meta['code'] }}</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <span class="font-mono text-xs font-black px-2.5 py-1 rounded border-2 border-black {{ $meta['badge_color'] }}">
                    {{ $meta['code'] }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $meta['title'] }}</h1>
            </div>
            <p class="text-xs text-slate-600 mt-1">{{ $meta['desc'] }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('asesor.schedule.ruang-uji', $schedule->id) }}" class="px-4 py-2 text-xs font-semibold bg-white text-slate-800 border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 transition">
                Ganti Instrumen
            </a>
        </div>
    </div>

    {{-- Meta Stats Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Peserta Asesi</span>
            <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $asesiList->count() }} Orang</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Sudah Dinilai / Selesai</span>
            <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $asesiList->where('is_completed', true)->count() }} Asesi</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Belum Dinilai</span>
            <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $asesiList->where('is_completed', false)->count() }} Asesi</div>
        </div>
    </div>

    {{-- Asesi List Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-black text-slate-900">Daftar Asesi untuk {{ $meta['code'] }}</h2>
                <p class="text-xs text-slate-500">Pilih asesi untuk memeriksa lembar hasil kerja dan memberikan penilaian</p>
            </div>
            <div class="text-xs font-bold text-slate-700 bg-white px-3 py-1 rounded border border-slate-300">
                Skema: {{ $schedule->scheme->code }}
            </div>
        </div>

        @if($asesiList->isEmpty())
            <div class="p-8 text-center text-slate-500 text-sm">
                Belum ada asesi terdaftar pada jadwal ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 text-left text-xs font-bold text-slate-700 uppercase tracking-wider border-b-2 border-black">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">No</th>
                            <th class="px-6 py-3">Data Peserta Asesi</th>
                            <th class="px-6 py-3 text-center">Status APL</th>
                            <th class="px-6 py-3 text-center">Status Penilaian {{ $meta['code'] }}</th>
                            <th class="px-6 py-3 text-right">Aksi Penilaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-200">
                        @foreach($asesiList as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 text-center font-mono font-bold text-xs text-slate-600">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shrink-0 border border-black">
                                            {{ strtoupper(substr($item->asesi->full_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">{{ $item->asesi->full_name }}</div>
                                            <div class="text-xs text-slate-500">
                                                {{ $item->asesi->email }}
                                                @if(!empty($item->asesi->profile?->nik))
                                                    &bull; <span class="font-mono">NIK: {{ $item->asesi->profile->nik }}</span>
                                                @endif
                                            </div>
                                            @if($item->is_my_candidate)
                                                <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 mt-1">
                                                    ✓ Plottingan Anda
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 mt-1">
                                                    Asesor: {{ $item->assessment->assessor?->full_name ?? 'Lain' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold border border-slate-300 {{ $item->assessment->apl_status === 'APPROVED' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ str_replace('_', ' ', $item->assessment->apl_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center space-y-1">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $item->status_class }}">
                                            {{ $item->status_label }}
                                        </span>
                                        @if($item->score_label)
                                            <span class="text-[11px] font-bold text-slate-600">
                                                {{ $item->score_label }}
                                            </span>
                                        @endif
                                        @if($meta['code'] === 'IA02')
                                            @if($item->has_evidence)
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-300">
                                                    ✓ Lembar Kertas Ada
                                                </span>
                                            @else
                                                <span class="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-300">
                                                    Lembar Kertas Belum Diunggah
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($item->is_my_candidate)
                                        <a href="{{ $item->inspect_url }}"
                                           class="inline-flex items-center space-x-1.5 px-4 py-2 text-xs font-bold {{ $item->is_completed ? 'bg-slate-900 hover:bg-slate-800 text-white' : 'bg-brand-600 hover:bg-brand-700 text-white' }} rounded-lg shadow-sm transition">
                                            <span>{{ $item->is_completed ? 'Tinjau / Ubah Penilaian' : 'Periksa Hasil Asesi' }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @else
                                        <button type="button" disabled 
                                            class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 rounded-lg cursor-not-allowed select-none" 
                                            title="Asesi ini di-plot kepada {{ $item->assessment->assessor?->full_name ?? 'rekan asesor lain' }}. Anda tidak dapat menilai asesi ini.">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Terkunci (Asesor Lain)</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
