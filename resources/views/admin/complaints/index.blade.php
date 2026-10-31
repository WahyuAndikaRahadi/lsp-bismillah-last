<x-app-layout title="Layanan Pengaduan & Aduan Masuk">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Layanan Pengaduan Masyarakat</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Penanganan aduan, laporan kendala pelaksanaan asesmen, dan tindak lanjut keluhan publik.</p>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.complaints.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.complaints.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ !request('status') ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                        Semua Aduan
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'diproses']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'diproses' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Sedang Diproses
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'selesai']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Selesai
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                        Ditolak
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nomor tiket, nama..."
                        class="px-3 py-1.5 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                    />
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50/75 text-zinc-500 font-semibold border-b border-zinc-200">
                        <tr>
                            <th class="px-6 py-3.5">No. Tiket & Tanggal</th>
                            <th class="px-6 py-3.5">Pelapor & Kategori</th>
                            <th class="px-6 py-3.5">Perihal / Judul</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($complaints as $c)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-mono font-bold text-zinc-900">{{ $c->ticket_number }}</div>
                                    <div class="text-[11px] text-zinc-400 mt-0.5">{{ $c->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900">{{ $c->nama_pelapor }}</div>
                                    <div class="text-[11px] text-zinc-500 flex items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 bg-zinc-100 rounded text-[10px]">{{ $c->jenis_responden }}</span>
                                        <span>Telp: {{ $c->telepon }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 max-w-sm truncate">{{ $c->judul }}</div>
                                    <div class="text-[11px] text-zinc-400 max-w-sm truncate mt-0.5">{{ Str::limit($c->isi_aduan, 60) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $c->statusBadge() }}">
                                        {{ $c->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.complaints.show', $c) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs transition"
                                    >
                                        <span>Tindak Lanjut</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-400">
                                    Tidak ada data pengaduan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($complaints->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
