<x-app-layout title="Rapat Pleno Penetapan Kelulusan">
    <div class="space-y-6">

        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Rapat Pleno Penetapan Kelulusan</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Penetapan keputusan hasil asesmen, pengesahan berita acara pleno komite teknis, dan penerbitan sertifikat BNSP.</p>
            </div>
        </div>

        <!-- Schedules Table Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Daftar Jadwal Asesmen (Tahap Pleno & Pasca-Uji)</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50/75 text-zinc-500 font-semibold border-b border-zinc-200">
                        <tr>
                            <th class="px-6 py-3.5">Jadwal & Skema</th>
                            <th class="px-6 py-3.5">TUK & Waktu</th>
                            <th class="px-6 py-3.5 text-center">Rekap Asesor (K / BK)</th>
                            <th class="px-6 py-3.5 text-center">Status Pleno</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($schedules as $sched)
                            @php
                                $totalPeserta = $sched->applications->count();
                                $kCount = $sched->applications->filter(fn($a) => $a->ak02 && $a->ak02->rekomendasi_akhir === 'K')->count();
                                $bkCount = $sched->applications->filter(fn($a) => $a->ak02 && $a->ak02->rekomendasi_akhir === 'BK')->count();
                                $isPlenoDone = $sched->plenoMeeting && $sched->plenoMeeting->status === 'disahkan';
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900">{{ $sched->nama_jadwal }}</div>
                                    <div class="text-[11px] text-zinc-500 flex items-center gap-2 mt-0.5">
                                        <span class="font-mono font-medium text-zinc-700">{{ $sched->kode_jadwal }}</span>
                                        <span>•</span>
                                        <span>{{ $sched->scheme->nama_skema ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-zinc-800 font-medium">{{ $sched->tuk->nama_tuk ?? '-' }}</div>
                                    <div class="text-[11px] text-zinc-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($sched->tgl_asesmen)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 font-semibold">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[11px]">{{ $kCount }} K</span>
                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded text-[11px]">{{ $bkCount }} BK</span>
                                        <span class="text-zinc-400 text-[11px]">/ {{ $totalPeserta }} Peserta</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($isPlenoDone)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Disahkan ({{ $sched->plenoMeeting->nomor_ba_pleno }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Sidang Pleno
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.pleno.show', $sched) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs transition"
                                    >
                                        <span>{{ $isPlenoDone ? 'Buka Lembar Pleno' : 'Sidang Pleno & Sahkan' }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-400">
                                    Belum ada jadwal asesmen yang mencapai tahap pleno atau pelaksanaan uji.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schedules->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
