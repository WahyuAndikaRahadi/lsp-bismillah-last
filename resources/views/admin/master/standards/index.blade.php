<x-app-layout title="Standar Acuan (SKKNI)">
    <div class="space-y-6" x-data="{ createModal: false, editModal: false, activeItem: {} }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Standar Acuan Kompetensi (SKKNI)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Basis regulasi penetapan unit kompetensi dan skema sertifikasi LSP.</p>
            </div>
            <div>
                <button
                    @click="createModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Standar Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.standards.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode standar atau judul..."
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
                    <option value="">Semua Jenis Standar</option>
                    <option value="SKKNI" {{ request('jenis') === 'SKKNI' ? 'selected' : '' }}>SKKNI</option>
                    <option value="Khusus" {{ request('jenis') === 'Khusus' ? 'selected' : '' }}>Standar Khusus</option>
                    <option value="Internasional" {{ request('jenis') === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                </select>

                @if (request()->hasAny(['q', 'jenis']))
                    <a href="{{ route('admin.standards.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $standards->total() }} Standar</span>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Jenis Standar</th>
                            <th class="px-6 py-3.5">Judul Standar Acuan</th>
                            <th class="px-6 py-3.5">Nomor SK Penetapan</th>
                            <th class="px-6 py-3.5 text-center">Tahun</th>
                            <th class="px-6 py-3.5 text-center">Skema Terkait</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($standards as $standard)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-zinc-900 block">{{ $standard->kode_standar }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200/60">
                                        {{ $standard->jenis_standar }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 max-w-md">
                                    <p class="font-medium text-zinc-800 line-clamp-2 leading-relaxed">{{ $standard->judul_standar }}</p>
                                </td>
                                <td class="px-6 py-3.5 font-mono text-[11px]">
                                    {{ $standard->nomor_sk ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center font-mono">
                                    {{ $standard->tahun ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center font-semibold text-blue-600">
                                    {{ $standard->schemes_count }} Skema
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @if ($standard->is_active)
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
                                <td class="px-6 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="activeItem = {{ json_encode($standard) }}; editModal = true"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.standards.destroy', $standard) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus standar ini?');">
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
                                    Tidak ada data standar kompetensi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($standards->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $standards->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Standar Baru -->
        <div
            x-show="createModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs"
            x-transition
            style="display: none;"
        >
            <div @click.away="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Standar Acuan Baru</h3>
                    <button @click="createModal = false" class="text-zinc-400 hover:text-zinc-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.standards.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="kode_standar" class="block text-xs font-semibold text-zinc-700">Kode Standar <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="kode_standar"
                            id="kode_standar"
                            required
                            placeholder="Contoh: SKKNI No. 282 Tahun 2016"
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label for="judul_standar" class="block text-xs font-semibold text-zinc-700">Judul Standar Kompetensi <span class="text-rose-500">*</span></label>
                        <textarea
                            name="judul_standar"
                            id="judul_standar"
                            rows="3"
                            required
                            placeholder="Judul lengkap standar kompetensi..."
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="jenis_standar" class="block text-xs font-semibold text-zinc-700">Jenis Standar <span class="text-rose-500">*</span></label>
                            <select
                                name="jenis_standar"
                                id="jenis_standar"
                                required
                                class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            >
                                <option value="SKKNI">SKKNI</option>
                                <option value="Khusus">Standar Khusus</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>

                        <div>
                            <label for="tahun" class="block text-xs font-semibold text-zinc-700">Tahun Penetapan</label>
                            <input
                                type="number"
                                name="tahun"
                                id="tahun"
                                value="{{ date('Y') }}"
                                min="1990"
                                max="2099"
                                class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="nomor_sk" class="block text-xs font-semibold text-zinc-700">Nomor SK Penetapan</label>
                        <input
                            type="text"
                            name="nomor_sk"
                            id="nomor_sk"
                            placeholder="Contoh: KEP.282/MEN/IX/2016"
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="is_active" class="text-xs text-zinc-700 cursor-pointer">Status Standar Aktif</label>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="createModal = false" class="px-4 py-2 text-xs text-zinc-600 hover:bg-zinc-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            Simpan Standar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Standar -->
        <div
            x-show="editModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs"
            x-transition
            style="display: none;"
        >
            <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Ubah Standar Acuan</h3>
                    <button @click="editModal = false" class="text-zinc-400 hover:text-zinc-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/standards/' + activeItem.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Kode Standar</label>
                        <input
                            type="text"
                            name="kode_standar"
                            x-model="activeItem.kode_standar"
                            required
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Judul Standar</label>
                        <textarea
                            name="judul_standar"
                            x-model="activeItem.judul_standar"
                            rows="3"
                            required
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Jenis Standar</label>
                            <select
                                name="jenis_standar"
                                x-model="activeItem.jenis_standar"
                                required
                                class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            >
                                <option value="SKKNI">SKKNI</option>
                                <option value="Khusus">Standar Khusus</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tahun Penetapan</label>
                            <input
                                type="number"
                                name="tahun"
                                x-model="activeItem.tahun"
                                min="1990"
                                max="2099"
                                class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nomor SK Penetapan</label>
                        <input
                            type="text"
                            name="nomor_sk"
                            x-model="activeItem.nomor_sk"
                            class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="activeItem.is_active" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="edit_is_active" class="text-xs text-zinc-700 cursor-pointer">Status Standar Aktif</label>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="editModal = false" class="px-4 py-2 text-xs text-zinc-600 hover:bg-zinc-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
