@extends('layouts.app', ['title' => 'FR.AK.05 - Laporan Asesmen'])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <h1 class="text-xl font-bold text-slate-900">FR.AK.05 Laporan Asesmen</h1>
        <p class="text-xs text-slate-500 mt-1">Jadwal: {{ $schedule->name }} ({{ $schedule->code }}) &bull; Skema: {{ $schedule->scheme?->name }}</p>
    </div>

    <form action="{{ route('asesor.schedule.ak05.store', $schedule->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="lock_version" value="{{ $report->lock_version ?? 1 }}">

        {{-- Roster Snapshot Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-800">Daftar Rekapitulasi Asesi Lingkup Tugas Asesor</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3">Nama Asesi</th>
                            <th class="px-4 py-3 text-center">Rekomendasi</th>
                            <th class="px-4 py-3">Keterangan / Unit Belum Kompeten</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignedAssessments as $idx => $ass)
                            <tr>
                                <td class="px-4 py-3 text-center text-slate-500">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $ass->asesi?->name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $ass->final_result === 'KOMPETEN' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $ass->final_result ?? 'BELUM_SELESAI' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @php
                                        $bk = $ass->ak02Items->where('is_competent', false);
                                    @endphp
                                    @if($bk->isNotEmpty())
                                        {{ $bk->pluck('competencyUnit.code')->filter()->implode(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-slate-400">Belum ada asesi yang ditugaskan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Fields --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Aspek Positif Selama Pelaksanaan Asesmen *</label>
                <textarea name="aspects_positive" rows="3" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('aspects_positive', $report->content_json['aspects_positive'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Aspek Negatif / Hambatan yang Ditemukan *</label>
                <textarea name="aspects_negative" rows="3" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('aspects_negative', $report->content_json['aspects_negative'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penolakan Hasil Asesmen oleh Asesi (Bila Ada)</label>
                <textarea name="rejections_record" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('rejections_record', $report->content_json['rejections_record'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Saran &amp; Rekomendasi Peningkatan *</label>
                <textarea name="improvements" rows="3" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('improvements', $report->content_json['improvements'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('notes', $report->content_json['notes'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-brand-900 hover:bg-brand-800 text-white rounded-lg text-xs font-bold shadow-xs transition">
                Simpan &amp; Serahkan Laporan FR.AK.05
            </button>
        </div>
    </form>
</div>
@endsection
