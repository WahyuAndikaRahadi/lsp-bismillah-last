<x-app-layout title="Master Tempat Uji Kompetensi (TUK)">
    <div class="space-y-6" x-data="{ createModal: false, editModal: false, activeItem: {} }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Tempat Uji Kompetensi (TUK)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Master data lokasi dan sarana prasarana pelaksanaan uji sertifikasi profesi.</p>
            </div>
            <div>
                <button
                    @click="createModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah TUK Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.tuks.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode TUK, nama, atau lokasi..."
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
                    <option value="">Semua Jenis TUK</option>
                    <option value="Sewaktu" {{ request('jenis') === 'Sewaktu' ? 'selected' : '' }}>TUK Sewaktu</option>
                    <option value="Mandiri" {{ request('jenis') === 'Mandiri' ? 'selected' : '' }}>TUK Mandiri</option>
                    <option value="Tempat Kerja" {{ request('jenis') === 'Tempat Kerja' ? 'selected' : '' }}>TUK Tempat Kerja</option>
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
                    <a href="{{ route('admin.tuks.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $tuks->total() }} TUK</span>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Nama TUK</th>
                            <th class="px-6 py-3.5">Jenis Lokasi</th>
                            <th class="px-6 py-3.5">Penanggung Jawab (PIC)</th>
                            <th class="px-6 py-3.5">Kontak</th>
                            <th class="px-6 py-3.5">Alamat Lokasi Uji</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($tuks as $tuk)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-zinc-900 block text-sm">{{ $tuk->nama_tuk }}</span>
                                    <span class="font-mono text-[11px] text-zinc-500">{{ $tuk->kode_tuk }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tuk->jenis_badge_classes }}">
                                        {{ $tuk->jenis_tuk }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-zinc-800">
                                    {{ $tuk->penanggung_jawab ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <div>{{ $tuk->telepon ?? '-' }}</div>
                                    <div class="text-[11px] text-zinc-400 font-mono">{{ $tuk->pic_phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-zinc-500">
                                    {{ $tuk->alamat ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <form method="POST" action="{{ route('admin.tuks.toggle-status', $tuk) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 cursor-pointer">
                                            @if ($tuk->is_active)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 text-zinc-500">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                                    Non-aktif
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="activeItem = {{ json_encode($tuk) }}; editModal = true"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.tuks.destroy', $tuk) }}" onsubmit="return confirm('Hapus master Tempat Uji Kompetensi ini?');">
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
                                    Belum ada Tempat Uji Kompetensi (TUK) terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tuks->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $tuks->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah TUK Baru -->
        <div x-show="createModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Tempat Uji Kompetensi (TUK)</h3>
                    <button @click="createModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.tuks.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Kode TUK <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_tuk" required placeholder="Contoh: TUK-001" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Jenis TUK <span class="text-rose-500">*</span></label>
                            <select name="jenis_tuk" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl">
                                <option value="Sewaktu">TUK Sewaktu</option>
                                <option value="Mandiri">TUK Mandiri</option>
                                <option value="Tempat Kerja">TUK Tempat Kerja</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama TUK <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_tuk" required placeholder="Contoh: TUK Kampus Utama Lab Jaringan" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Nama Penanggung Jawab (PIC)</label>
                            <input type="text" name="penanggung_jawab" placeholder="Nama PIC TUK" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">No. HP / WhatsApp PIC</label>
                            <input type="text" name="pic_phone" placeholder="081234567890" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Alamat Lengkap TUK</label>
                        <textarea name="alamat" rows="2" placeholder="Alamat lokasi tempat uji kompetensi..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="tuk_active" value="1" checked class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="tuk_active" class="text-xs text-zinc-700 cursor-pointer">Status TUK Siap Pakai & Aktif</label>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="createModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan TUK</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit TUK -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Ubah Data TUK</h3>
                    <button @click="editModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form :action="'/admin/tuks/' + activeItem.id" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Kode TUK</label>
                            <input type="text" name="kode_tuk" x-model="activeItem.kode_tuk" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Jenis TUK</label>
                            <select name="jenis_tuk" x-model="activeItem.jenis_tuk" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl">
                                <option value="Sewaktu">TUK Sewaktu</option>
                                <option value="Mandiri">TUK Mandiri</option>
                                <option value="Tempat Kerja">TUK Tempat Kerja</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama TUK</label>
                        <input type="text" name="nama_tuk" x-model="activeItem.nama_tuk" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Penanggung Jawab (PIC)</label>
                            <input type="text" name="penanggung_jawab" x-model="activeItem.penanggung_jawab" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">No. HP / WhatsApp PIC</label>
                            <input type="text" name="pic_phone" x-model="activeItem.pic_phone" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Alamat Lengkap TUK</label>
                        <textarea name="alamat" x-model="activeItem.alamat" rows="2" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="edit_tuk_active" value="1" :checked="activeItem.is_active" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="edit_tuk_active" class="text-xs text-zinc-700 cursor-pointer">Status TUK Siap Pakai & Aktif</label>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="editModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
