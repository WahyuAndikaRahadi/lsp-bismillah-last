<x-app-layout title="Ekspor Data Format BNSP (16 Kolom)">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Ekspor Data Format Sisfo BNSP</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Generator format spreadsheet XML 16-kolom resmi sesuai spesifikasi Sisfo BNSP untuk permohonan blanko dan sinkronisasi hasil uji.</p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.exports.bnsp.download', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Excel 16-Kolom (.xls)</span>
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.exports.bnsp.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Pilih Jadwal Asesmen</label>
                    <select name="schedule_id" class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="">Semua Jadwal</option>
                        @foreach ($schedules as $s)
                            <option value="{{ $s->id }}" {{ request('schedule_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_jadwal }} ({{ $s->kode_jadwal }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Pilih Skema</label>
                    <select name="scheme_id" class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="">Semua Skema</option>
                        @foreach ($schemes as $sc)
                            <option value="{{ $sc->id }}" {{ request('scheme_id') == $sc->id ? 'selected' : '' }}>
                                {{ $sc->nama_skema }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Pilih Event Sertifikasi</label>
                    <select name="event_id" class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="">Semua Event</option>
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>
                                {{ $ev->nama_event }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-semibold rounded-xl text-xs transition">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.exports.bnsp.index') }}" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-semibold rounded-xl text-xs transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 16-Column Interactive Preview Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider">
                    Pratinjau Data 16 Kolom Standar Sisfo BNSP ({{ $applications->total() }} Peserta)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[11px] text-zinc-600 whitespace-nowrap">
                    <thead class="bg-zinc-50/75 text-zinc-700 font-bold border-b border-zinc-200 font-mono text-[10px]">
                        <tr>
                            <th class="px-3 py-3 text-center">1. NO</th>
                            <th class="px-3 py-3">2. NIK</th>
                            <th class="px-3 py-3">3. NO_PESERTA</th>
                            <th class="px-3 py-3">4. NAMA_LENGKAP</th>
                            <th class="px-3 py-3">5. TEMPAT_LAHIR</th>
                            <th class="px-3 py-3">6. TANGGAL_LAHIR</th>
                            <th class="px-3 py-3 text-center">7. JK</th>
                            <th class="px-3 py-3">8. PENDIDIKAN</th>
                            <th class="px-3 py-3">9. KODE_SKEMA</th>
                            <th class="px-3 py-3">10. NAMA_SKEMA</th>
                            <th class="px-3 py-3">11. KODE_TUK</th>
                            <th class="px-3 py-3">12. NAMA_TUK</th>
                            <th class="px-3 py-3">13. NO_REG_ASESOR</th>
                            <th class="px-3 py-3">14. NAMA_ASESOR</th>
                            <th class="px-3 py-3 text-center">15. HASIL</th>
                            <th class="px-3 py-3">16. TANGGAL_ASESMEN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 font-mono">
                        @forelse ($applications as $idx => $app)
                            @php
                                $profile = $app->user?->asesiProfile;
                                $schedule = $app->schedule;
                                $scheme = $app->scheme ?? $schedule?->scheme;
                                $tuk = $schedule?->tuk;
                                $asesor = $app->asesor;
                                $asesorProfile = $asesor?->asesorProfile;
                                $ak02 = $app->ak02;
                                $hasil = ($app->status_kelulusan === 'kompeten' || ($ak02 && $ak02->rekomendasi_akhir === 'K')) ? 'K' : 'BK';
                            @endphp
                            <tr class="hover:bg-zinc-50/75 transition">
                                <td class="px-3 py-2.5 text-center font-bold text-zinc-400">{{ $applications->firstItem() + $idx }}</td>
                                <td class="px-3 py-2.5 font-bold text-zinc-900">{{ $profile?->nik ?? '-' }}</td>
                                <td class="px-3 py-2.5">{{ $app->nomor_permohonan ?? $app->nomor_pendaftaran }}</td>
                                <td class="px-3 py-2.5 font-sans font-semibold text-zinc-900">{{ $profile?->nama_lengkap ?? $app->user->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-sans">{{ $profile?->tempat_lahir ?? '-' }}</td>
                                <td class="px-3 py-2.5">{{ $profile?->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('Y-m-d') : '-' }}</td>
                                <td class="px-3 py-2.5 text-center">{{ $profile?->jenis_kelamin ?? 'L' }}</td>
                                <td class="px-3 py-2.5 font-sans">{{ $profile?->pendidikan_terakhir ?? '-' }}</td>
                                <td class="px-3 py-2.5">{{ $scheme?->kode_skema ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-sans">{{ $scheme?->nama_skema ?? '-' }}</td>
                                <td class="px-3 py-2.5">{{ $tuk?->kode_tuk ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-sans">{{ $tuk?->nama_tuk ?? '-' }}</td>
                                <td class="px-3 py-2.5">{{ $asesorProfile?->no_met ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-sans">{{ $asesor?->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-center font-bold">
                                    @if ($hasil === 'K')
                                        <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">K</span>
                                    @else
                                        <span class="text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded">BK</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">{{ $schedule?->tgl_asesmen ? \Carbon\Carbon::parse($schedule->tgl_asesmen)->format('Y-m-d') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="px-6 py-12 text-center text-zinc-400 font-sans">
                                    Tidak ada data peserta asesmen yang cocok dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($applications->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
