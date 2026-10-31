<x-app-layout title="Skema Sertifikasi">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Skema Sertifikasi Profesi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Kelola paket skema sertifikasi, unit kompetensi kemasan, dan dokumen persyaratan uji.</p>
            </div>
            <div>
                <a
                    href="{{ route('admin.schemes.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Skema Baru</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.schemes.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode skema, nama, atau kategori..."
                        class="w-full pl-9 pr-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    />
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    name="jenis"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Jenis Skema</option>
                    <option value="Okupasi" {{ request('jenis') === 'Okupasi' ? 'selected' : '' }}>Okupasi</option>
                    <option value="KKNI" {{ request('jenis') === 'KKNI' ? 'selected' : '' }}>KKNI</option>
                    <option value="Klaster" {{ request('jenis') === 'Klaster' ? 'selected' : '' }}>Klaster</option>
                </select>

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                </select>

                @if (request()->hasAny(['q', 'jenis', 'status']))
                    <a href="{{ route('admin.schemes.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $schemes->total() }} Skema</span>
        </div>

        <!-- Schemes Grid / Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Nama Skema</th>
                            <th class="px-6 py-3.5">Standar Acuan</th>
                            <th class="px-6 py-3.5">Biaya Uji</th>
                            <th class="px-6 py-3.5 text-center">Unit Kompetensi</th>
                            <th class="px-6 py-3.5 text-center">Persyaratan</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($schemes as $scheme)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                            {{ substr($scheme->kode_skema, 0, 3) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.schemes.show', $scheme) }}" class="font-bold text-zinc-900 hover:text-blue-600 transition block text-sm">
                                                {{ $scheme->nama_skema }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="font-mono text-[11px] text-zinc-500">{{ $scheme->kode_skema }}</span>
                                                <span class="text-zinc-300">•</span>
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700">
                                                    {{ $scheme->jenis_skema }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <span class="font-medium text-zinc-800 block truncate">{{ $scheme->standard->kode_standar ?? '-' }}</span>
                                    <span class="text-[11px] text-zinc-400 block truncate">{{ $scheme->kategori ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900 font-mono">
                                    {{ $scheme->formatted_biaya }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.schemes.show', $scheme) }}" class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:underline">
                                        <span>{{ $scheme->units_count }} Unit</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-zinc-600">
                                    {{ $scheme->requirements_count }} Dokumen
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($scheme->is_active)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                            Non-aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.schemes.show', $scheme) }}"
                                            class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium rounded-lg transition text-[11px]"
                                        >
                                            Kelola Unit (MUK)
                                        </a>

                                        <a
                                            href="{{ route('admin.schemes.edit', $scheme) }}"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.schemes.destroy', $scheme) }}" onsubmit="return confirm('Hapus skema sertifikasi ini beserta seluruh unit kompetensinya?');">
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
                                    Belum ada skema sertifikasi terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schemes->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $schemes->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
