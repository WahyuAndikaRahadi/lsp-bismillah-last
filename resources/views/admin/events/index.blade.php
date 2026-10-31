<x-app-layout title="Event Sertifikasi">
    <div class="space-y-6" x-data="{ createModal: false, editModal: false, activeItem: {} }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Event & Program Sertifikasi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Pengelompokan agenda uji kompetensi berdasarkan sumber pembiayaan dan tahun anggaran.</p>
            </div>
            <div>
                <button
                    @click="createModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Event Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.events.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode event, nama, atau keterangan..."
                        class="w-full pl-9 pr-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    />
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    name="tahun"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $yr)
                        <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>

                <select
                    name="sumber"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                >
                    <option value="">Semua Sumber Anggaran</option>
                    <option value="BNSP / PSKK" {{ request('sumber') === 'BNSP / PSKK' ? 'selected' : '' }}>BNSP / PSKK</option>
                    <option value="Mandiri" {{ request('sumber') === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    <option value="APBN" {{ request('sumber') === 'APBN' ? 'selected' : '' }}>APBN</option>
                    <option value="APBD" {{ request('sumber') === 'APBD' ? 'selected' : '' }}>APBD</option>
                </select>

                @if (request()->hasAny(['q', 'tahun', 'sumber']))
                    <a href="{{ route('admin.events.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $events->total() }} Event</span>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Kode & Nama Event</th>
                            <th class="px-6 py-3.5">Sumber Anggaran</th>
                            <th class="px-6 py-3.5 text-center">Tahun</th>
                            <th class="px-6 py-3.5">Periode Pelaksanaan</th>
                            <th class="px-6 py-3.5 text-center">Jadwal Uji</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($events as $event)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.events.show', $event) }}" class="font-bold text-zinc-900 hover:text-blue-600 transition block text-sm">
                                        {{ $event->nama_event }}
                                    </a>
                                    <span class="font-mono text-[11px] text-zinc-500">{{ $event->kode_event }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">
                                        {{ $event->sumber_anggaran }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-semibold">
                                    {{ $event->tahun_anggaran }}
                                </td>
                                <td class="px-6 py-4 text-zinc-700">
                                    {{ $event->formatted_periode }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.events.show', $event) }}" class="font-semibold text-blue-600 hover:underline">
                                        {{ $event->schedules_count }} Jadwal
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($event->is_active)
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
                                            href="{{ route('admin.events.show', $event) }}"
                                            class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium rounded-lg transition text-[11px]"
                                        >
                                            Rincian
                                        </a>

                                        <button
                                            @click="activeItem = {
                                                id: {{ $event->id }},
                                                kode_event: '{{ $event->kode_event }}',
                                                nama_event: '{{ addslashes($event->nama_event) }}',
                                                tahun_anggaran: {{ $event->tahun_anggaran }},
                                                sumber_anggaran: '{{ $event->sumber_anggaran }}',
                                                tgl_mulai: '{{ $event->tgl_mulai->format('Y-m-d') }}',
                                                tgl_selesai: '{{ $event->tgl_selesai->format('Y-m-d') }}',
                                                keterangan: '{{ addslashes($event->keterangan) }}',
                                                is_active: {{ $event->is_active ? 'true' : 'false' }}
                                            }; editModal = true"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Hapus event sertifikasi ini?');">
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
                                    Belum ada data event sertifikasi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($events->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Event Baru -->
        <div x-show="createModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Event Sertifikasi Baru</h3>
                    <button @click="createModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-3">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Kode Event <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_event" required placeholder="Contoh: EVT-2026-PSKK" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tahun Anggaran <span class="text-rose-500">*</span></label>
                            <input type="number" name="tahun_anggaran" required value="{{ date('Y') }}" min="2020" max="2099" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama Event / Program <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_event" required placeholder="Contoh: Program Sertifikasi Kompetensi Kerja (PSKK) 2026" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Sumber Anggaran / Pembiayaan <span class="text-rose-500">*</span></label>
                        <select name="sumber_anggaran" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl">
                            <option value="BNSP / PSKK">BNSP / PSKK</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="APBN">APBN (Kementerian/Lembaga)</option>
                            <option value="APBD">APBD (Pemerintah Daerah)</option>
                            <option value="Lainnya">Lainnya / Hibah / CSR</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tgl_mulai" required value="{{ date('Y-m-01') }}" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tgl_selesai" required value="{{ date('Y-m-t') }}" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Keterangan / Catatan Program</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan latar belakang, target peserta, atau dasar pelaksanaan..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="event_active" value="1" checked class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="event_active" class="text-xs text-zinc-700 cursor-pointer">Status Event Aktif</label>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="createModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan Event</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Event -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Ubah Data Event</h3>
                    <button @click="editModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>

                <form :action="'/admin/events/' + activeItem.id" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Kode Event</label>
                            <input type="text" name="kode_event" x-model="activeItem.kode_event" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tahun Anggaran</label>
                            <input type="number" name="tahun_anggaran" x-model="activeItem.tahun_anggaran" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama Event</label>
                        <input type="text" name="nama_event" x-model="activeItem.nama_event" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Sumber Anggaran</label>
                        <select name="sumber_anggaran" x-model="activeItem.sumber_anggaran" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl">
                            <option value="BNSP / PSKK">BNSP / PSKK</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="APBN">APBN</option>
                            <option value="APBD">APBD</option>
                            <option value="Lainnya">Lainnya / Hibah / CSR</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" x-model="activeItem.tgl_mulai" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" x-model="activeItem.tgl_selesai" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Keterangan</label>
                        <textarea name="keterangan" x-model="activeItem.keterangan" rows="2" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="edit_event_active" value="1" :checked="activeItem.is_active" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="edit_event_active" class="text-xs text-zinc-700 cursor-pointer">Status Event Aktif</label>
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
