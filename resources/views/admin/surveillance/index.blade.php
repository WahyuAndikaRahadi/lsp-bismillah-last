<x-app-layout title="Pemeliharaan Sertifikasi (Surveilans)">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Surveilans Pemegang Sertifikat</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Pemantauan berkala pemeliharaan kompetensi pemegang sertifikat BNSP sesuai Pedoman BNSP 214.</p>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.surveillance.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.surveillance.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ !request('status') ? 'bg-lsp-navy text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                        Semua Laporan
                    </a>
                    <a href="{{ route('admin.surveillance.index', ['status' => 'menunggu_verifikasi']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'menunggu_verifikasi' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Menunggu Verifikasi
                    </a>
                    <a href="{{ route('admin.surveillance.index', ['status' => 'terverifikasi']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'terverifikasi' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Terverifikasi Aktif
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama, NIK, nomor sertifikat..."
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
                            <th class="px-6 py-3.5">Pemegang Sertifikat & NIK</th>
                            <th class="px-6 py-3.5">Nomor Sertifikat & Skema</th>
                            <th class="px-6 py-3.5">Pekerjaan & Kesesuaian</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($records as $r)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900">{{ $r->nama_lengkap }}</div>
                                    <div class="text-[11px] text-zinc-500 font-mono mt-0.5">
                                        NIK: {{ $r->nik }} • {{ $r->telepon }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-mono font-bold text-blue-700">{{ $r->no_sertifikat }}</div>
                                    <div class="text-[11px] text-zinc-500 mt-0.5">
                                        {{ $r->scheme->nama_skema ?? 'Skema Tidak Terkait' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-800">{{ $r->jabatan }} di {{ $r->tempat_kerja }}</div>
                                    <div class="text-[11px] mt-0.5">
                                        <span class="inline-flex px-1.5 py-0.5 rounded font-semibold text-[10px] {{ $r->kesesuaian_skk === 'Y' ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                                            {{ $r->kesesuaianLabel() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $r->statusBadge() }}">
                                        {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($r->status === 'menunggu_verifikasi')
                                        <form method="POST" action="{{ route('admin.surveillance.verify', $r) }}" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="terverifikasi">
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs transition"
                                            >
                                                Sahkan Verifikasi
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-zinc-400 text-[11px]">
                                            Diverifikasi oleh {{ $r->verifier?->name ?? 'Admin' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-400">
                                    Belum ada data surveilans yang masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($records->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $records->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
