@extends('layouts.app', ['title' => 'Rekapitulasi Daftar Hadir Asesmen'])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Rekapitulasi Daftar Hadir Peserta (FR-SOP-04.3)</h1>
            <p class="text-xs text-slate-500 mt-1">Jadwal: {{ $schedule->name }} ({{ $schedule->code }}) &bull; TUK: {{ $schedule->tuk_name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.schedules.minutes.show', $schedule->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                &larr; Kembali ke Berita Acara
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Asesi</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3 text-center">Hasil Asesmen</th>
                        <th class="px-4 py-3">Rencana Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facts['roster'] as $idx => $r)
                        <tr>
                            <td class="px-4 py-3 text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $r['name'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $r['is_present'] ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $r['is_present'] ? 'HADIR' : 'TIDAK HADIR' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $r['final_result'] === 'KOMPETEN' ? 'bg-emerald-50 text-emerald-700' : ($r['final_result'] === 'BELUM_KOMPETEN' ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-600') }}">
                                    {{ $r['final_result'] ?? 'BELUM_SELESAI' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $r['follow_up_action'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-slate-400">Belum ada peserta terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
