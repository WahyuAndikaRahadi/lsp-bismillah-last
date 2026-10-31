<x-app-layout title="Tinjauan Asesmen Mandiri (FR-APL-02)">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesor</span>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">Pra-Asesmen Mandiri (APL-02)</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                    Peninjauan Asesmen Mandiri (FR-APL-02)
                </h1>
                <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                    Periksa kesesuaian bukti portofolio (VATM) dan berikan rekomendasi kelanjutan uji kompetensi.
                </p>
            </div>
        </div>

        <!-- Assigned Schedules Overview -->
        @if ($schedules->isNotEmpty())
            <div class="bg-zinc-900 rounded-2xl p-5 text-white space-y-3">
                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Jadwal Asesmen yang Ditugaskan Kepada Anda</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($schedules as $sch)
                        <div class="p-3 bg-white/10 rounded-xl border border-white/10 text-xs">
                            <div class="font-bold text-white">{{ $sch->nama_jadwal }}</div>
                            <div class="text-[11px] text-zinc-300 mt-0.5">{{ $sch->scheme->nama_skema }}</div>
                            <div class="text-[11px] text-zinc-400 mt-1">Tgl: {{ $sch->formatted_tanggal }} &bull; {{ $sch->tuk->nama_tuk }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- APL-02 Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-zinc-900">Daftar Asesi Bimbingan & Permohonan APL-02</h3>
                <span class="text-xs text-zinc-500">{{ $apl02List->total() }} Peserta</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                    <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                        <tr>
                            <th class="px-5 py-3.5">Asesi (Pemohon)</th>
                            <th class="px-5 py-3.5">No. Registrasi & Skema</th>
                            <th class="px-5 py-3.5">Jadwal (TUK)</th>
                            <th class="px-5 py-3.5 text-center">Hasil Mandiri</th>
                            <th class="px-5 py-3.5 text-center">Status APL-02</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white">
                        @forelse ($apl02List as $item)
                            <tr class="hover:bg-zinc-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-zinc-900">{{ $item->application->user->name }}</div>
                                    <div class="text-[11px] text-zinc-500 font-mono">NIK: {{ $item->application->user->nik ?? '-' }}</div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-mono font-bold text-zinc-900">{{ $item->application->nomor_pendaftaran }}</div>
                                    <div class="text-[11px] text-zinc-500">{{ $item->application->scheme->nama_skema }}</div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-medium text-zinc-900">{{ $item->application->schedule?->nama_jadwal ?? '-' }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $item->application->schedule?->tuk->nama_tuk ?? '-' }}</div>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @php
                                        $kompetenCount = $item->items->where('penilaian_mandiri', 'K')->count();
                                        $totalCount = $item->items->count();
                                    @endphp
                                    <span class="font-bold {{ $kompetenCount === $totalCount ? 'text-emerald-700' : 'text-zinc-700' }}">
                                        {{ $kompetenCount }} / {{ $totalCount }} K
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $item->status->badgeClasses() }}">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('asesor.apl02.review', $item) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Tinjau & Verifikasi</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-zinc-500">
                                    <p class="font-bold text-zinc-700">Belum ada permohonan APL-02 yang perlu ditinjau</p>
                                    <p class="text-xs text-zinc-400 mt-1">Daftar akan muncul setelah peserta mengisi asesmen mandiri.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200">
                {{ $apl02List->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
