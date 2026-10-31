<x-app-layout title="Jadwal Uji Kompetensi">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Jadwal Uji Kompetensi (Asesmen)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Penjadwalan pelaksanaan uji kompetensi, plotting kuota, dan monitoring siklus asesmen.</p>
            </div>
            <div>
                <a
                    href="{{ route('admin.schedules.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Jadwal Baru</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-72">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode jadwal, nama, atau TUK..."
                        class="w-full pl-9 pr-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    />
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Status Siklus</option>
                    @foreach (\App\Enums\ScheduleStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <select
                    name="scheme_id"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Skema</option>
                    @foreach ($schemes as $sch)
                        <option value="{{ $sch->id }}" {{ request('scheme_id') == $sch->id ? 'selected' : '' }}>
                            {{ $sch->nama_skema }}
                        </option>
                    @endforeach
                </select>

                <select
                    name="event_id"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Event</option>
                    @foreach ($events as $ev)
                        <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>
                            {{ $ev->nama_event }}
                        </option>
                    @endforeach
                </select>

                @if (request()->hasAny(['q', 'status', 'scheme_id', 'event_id']))
                    <a href="{{ route('admin.schedules.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $schedules->total() }} Jadwal</span>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Nama Jadwal</th>
                            <th class="px-6 py-3.5">Skema Sertifikasi</th>
                            <th class="px-6 py-3.5">Tempat Uji (TUK)</th>
                            <th class="px-6 py-3.5">Tanggal Asesmen</th>
                            <th class="px-6 py-3.5 text-center">Kuota</th>
                            <th class="px-6 py-3.5 text-center">Status Siklus</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}" class="font-bold text-zinc-900 hover:text-blue-600 block text-sm">
                                        {{ $schedule->nama_jadwal }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[11px] text-zinc-500">{{ $schedule->kode_jadwal }}</span>
                                        @if ($schedule->id_jadwal_bnsp)
                                            <span class="text-zinc-300">•</span>
                                            <span class="text-[10px] text-blue-600 font-mono">BNSP: {{ $schedule->id_jadwal_bnsp }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <span class="font-medium text-zinc-800 block truncate">{{ $schedule->scheme->nama_skema ?? '-' }}</span>
                                    <span class="text-[11px] text-zinc-400 block truncate">{{ $schedule->event->nama_event ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-zinc-700">
                                    <span class="font-medium text-zinc-900 block">{{ $schedule->tuk->nama_tuk ?? '-' }}</span>
                                    <span class="text-[10px] text-zinc-400 block">{{ $schedule->tuk->jenis_tuk ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-zinc-900 block">{{ $schedule->formatted_tanggal }}</span>
                                    @if ($schedule->tgl_pra_asesmen)
                                        <span class="text-[10px] text-zinc-400 block">Pra: {{ $schedule->tgl_pra_asesmen->format('d M Y') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-semibold text-zinc-800">
                                    {{ $schedule->kuota_peserta }} Asesi
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $schedule->status->badgeClasses() }}">
                                        {{ $schedule->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.schedules.show', $schedule) }}"
                                            class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium rounded-lg transition text-[11px]"
                                        >
                                            Kelola Jadwal
                                        </a>

                                        <a
                                            href="{{ route('admin.schedules.edit', $schedule) }}"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal uji kompetensi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-zinc-500 hover:text-rose-600 hover:bg-zinc-100 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-zinc-400">
                                    Belum ada jadwal uji kompetensi ditemukan.
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
