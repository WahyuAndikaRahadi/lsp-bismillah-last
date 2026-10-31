<x-app-layout title="Detail Event Sertifikasi">
    <div class="space-y-6">

        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Event</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">{{ $event->nama_event }}</h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-zinc-500">
                    <span class="font-mono font-semibold text-zinc-700">{{ $event->kode_event }}</span>
                    <span>•</span>
                    <span class="font-medium text-blue-700">{{ $event->sumber_anggaran }}</span>
                    <span>•</span>
                    <span>Tahun Anggaran {{ $event->tahun_anggaran }}</span>
                </div>
            </div>

            <div>
                <a
                    href="{{ route('admin.schedules.create', ['event_id' => $event->id]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Jadwal untuk Event Ini</span>
                </a>
            </div>
        </div>

        <!-- Event Overview Summary Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100">
                <span class="text-zinc-500 block">Periode Pelaksanaan</span>
                <span class="text-sm font-bold text-zinc-900 mt-1 block">{{ $event->formatted_periode }}</span>
            </div>
            <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100">
                <span class="text-zinc-500 block">Sumber Anggaran</span>
                <span class="text-sm font-bold text-blue-600 mt-1 block">{{ $event->sumber_anggaran }}</span>
            </div>
            <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100">
                <span class="text-zinc-500 block">Total Jadwal Uji</span>
                <span class="text-sm font-bold text-zinc-900 mt-1 block">{{ $event->schedules->count() }} Jadwal</span>
            </div>
            <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100">
                <span class="text-zinc-500 block">Total Kapasitas Kuota</span>
                <span class="text-sm font-bold text-emerald-600 mt-1 block">{{ $event->schedules->sum('kuota_peserta') }} Asesi</span>
            </div>
        </div>

        <!-- Linked Schedules Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">Jadwal Asesmen dalam Event Ini</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Daftar pelaksanaan uji kompetensi per skema dan Tempat Uji Kompetensi (TUK).</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Nama Jadwal</th>
                            <th class="px-6 py-3.5">Skema Sertifikasi</th>
                            <th class="px-6 py-3.5">Tempat Uji (TUK)</th>
                            <th class="px-6 py-3.5">Tanggal Asesmen</th>
                            <th class="px-6 py-3.5 text-center">Kuota Peserta</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($event->schedules as $schedule)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}" class="font-bold text-zinc-900 hover:text-blue-600 block text-sm">
                                        {{ $schedule->nama_jadwal }}
                                    </a>
                                    <span class="font-mono text-[11px] text-zinc-500">{{ $schedule->kode_jadwal }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-zinc-800">
                                    {{ $schedule->scheme->nama_skema ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-zinc-700">
                                    {{ $schedule->tuk->nama_tuk ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-zinc-900">
                                    {{ $schedule->formatted_tanggal }}
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-semibold text-zinc-800">
                                    {{ $schedule->kuota_peserta }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $schedule->status->badgeClasses() }}">
                                        {{ $schedule->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.schedules.show', $schedule) }}"
                                        class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium rounded-lg transition text-[11px]"
                                    >
                                        Kelola Jadwal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-zinc-400">
                                    Belum ada jadwal uji kompetensi yang dibuat untuk event ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
