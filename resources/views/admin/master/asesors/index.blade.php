<x-app-layout title="Master Asesor Kompetensi">
    <div class="space-y-6" x-data="{ createModal: false, editModal: false, activeItem: {} }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Master Asesor Kompetensi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Direktori asesor bersertifikat BNSP yang berwenang melaksanakan asesmen uji kompetensi.</p>
            </div>
            <div>
                <button
                    @click="createModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Daftarkan Asesor Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-zinc-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.asesors.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama, No. MET, NIK, atau keahlian..."
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
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                </select>

                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.asesors.index') }}" class="text-xs text-rose-600 hover:underline px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-zinc-400 shrink-0">Total: {{ $asesors->total() }} Asesor</span>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Nama & Kontak Asesor</th>
                            <th class="px-6 py-3.5">No. Registrasi MET (BNSP)</th>
                            <th class="px-6 py-3.5">No. KTP / NIK</th>
                            <th class="px-6 py-3.5">Masa Berlaku Lisensi</th>
                            <th class="px-6 py-3.5">Skema yang Diuji</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($asesors as $asesor)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ $asesor->user->initials }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-zinc-900 block">{{ $asesor->user->name }}</span>
                                            <span class="text-[11px] text-zinc-500">{{ $asesor->user->email }}</span>
                                            <span class="text-[10px] text-zinc-400 block font-mono">{{ $asesor->user->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="font-mono font-bold text-blue-600 block">{{ $asesor->no_met }}</span>
                                    <span class="text-[10px] text-zinc-400 block">Blangko: {{ $asesor->no_blangko ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-3.5 font-mono text-[11px]">
                                    {{ $asesor->user->nik ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5">
                                    @if ($asesor->masa_berlaku)
                                        <span class="font-medium text-zinc-800">{{ $asesor->masa_berlaku->format('d M Y') }}</span>
                                        @if ($asesor->masa_berlaku->isPast())
                                            <span class="text-[10px] font-semibold text-rose-600 block">Kadaluarsa</span>
                                        @else
                                            <span class="text-[10px] text-emerald-600 block">Masih Berlaku</span>
                                        @endif
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 max-w-xs">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($asesor->schemes as $scheme)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200/60 truncate">
                                                {{ $scheme->nama_skema }}
                                            </span>
                                        @empty
                                            <span class="text-zinc-400 text-[11px]">Belum ditautkan</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @if ($asesor->is_active && $asesor->user->is_active)
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
                                            @click="activeItem = {
                                                id: {{ $asesor->id }},
                                                name: '{{ addslashes($asesor->user->name) }}',
                                                email: '{{ $asesor->user->email }}',
                                                nik: '{{ $asesor->user->nik }}',
                                                phone: '{{ $asesor->user->phone }}',
                                                no_met: '{{ $asesor->no_met }}',
                                                no_blangko: '{{ $asesor->no_blangko }}',
                                                no_sertifikat: '{{ $asesor->no_sertifikat }}',
                                                masa_berlaku: '{{ $asesor->masa_berlaku?->format('Y-m-d') }}',
                                                bidang_keahlian: '{{ addslashes($asesor->bidang_keahlian) }}',
                                                is_active: {{ $asesor->is_active ? 'true' : 'false' }},
                                                scheme_ids: {{ json_encode($asesor->schemes->pluck('id')->toArray()) }}
                                            }; editModal = true"
                                            class="p-1 text-zinc-500 hover:text-blue-600 hover:bg-zinc-100 rounded-lg transition"
                                            title="Ubah"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.asesors.destroy', $asesor) }}" onsubmit="return confirm('Hapus akun asesor kompetensi ini?');">
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
                                    Belum ada data asesor kompetensi terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($asesors->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $asesors->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Daftarkan Asesor Baru -->
        <div x-show="createModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="createModal = false" class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-zinc-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Daftarkan Asesor Kompetensi Baru</h3>
                    <button @click="createModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.asesors.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">1. Akun Pengguna</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required placeholder="Contoh: Abu, S.T." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Alamat Email Login <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" required placeholder="asesor@lsp.localhost" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">NIK KTP (16 Digit) <span class="text-rose-500">*</span></label>
                                <input type="text" name="nik" maxlength="16" required placeholder="3504010710670002" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                                <input type="text" name="phone" required placeholder="081234567890" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-zinc-700">Kata Sandi Akun <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required value="password123" placeholder="Minimal 8 karakter" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100">
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">2. Lisensi Asesor BNSP</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. Registrasi MET (BNSP) <span class="text-rose-500">*</span></label>
                                <input type="text" name="no_met" required placeholder="Contoh: MET.000.003456 2021" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. Blangko Sertifikat</label>
                                <input type="text" name="no_blangko" placeholder="B-987654" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Masa Berlaku Lisensi Asesor</label>
                                <input type="date" name="masa_berlaku" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Bidang Keahlian Asesmen</label>
                                <input type="text" name="bidang_keahlian" placeholder="Contoh: Web Development" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100">
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">3. Penugasan Skema Sertifikasi yang Berhak Diuji</span>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto p-2 bg-zinc-50 rounded-xl border border-zinc-200">
                            @foreach ($schemes as $scheme)
                                <label class="flex items-center gap-2 text-xs text-zinc-700 cursor-pointer hover:bg-zinc-100 p-1 rounded">
                                    <input type="checkbox" name="scheme_ids[]" value="{{ $scheme->id }}" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                                    <span>{{ $scheme->nama_skema }} ({{ $scheme->kode_skema }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="createModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Daftarkan Asesor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Asesor -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-zinc-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Ubah Data Asesor Kompetensi</h3>
                    <button @click="editModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>

                <form :action="'/admin/asesors/' + activeItem.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">1. Data Pribadi Asesor</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Nama Lengkap & Gelar</label>
                                <input type="text" name="name" x-model="activeItem.name" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Email Login</label>
                                <input type="email" name="email" x-model="activeItem.email" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">NIK KTP (16 Digit)</label>
                                <input type="text" name="nik" maxlength="16" x-model="activeItem.nik" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. HP / WhatsApp</label>
                                <input type="text" name="phone" x-model="activeItem.phone" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100">
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">2. Lisensi Asesor BNSP</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. Registrasi MET</label>
                                <input type="text" name="no_met" x-model="activeItem.no_met" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">No. Blangko</label>
                                <input type="text" name="no_blangko" x-model="activeItem.no_blangko" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Masa Berlaku</label>
                                <input type="date" name="masa_berlaku" x-model="activeItem.masa_berlaku" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700">Bidang Keahlian</label>
                                <input type="text" name="bidang_keahlian" x-model="activeItem.bidang_keahlian" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100">
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">3. Skema yang Diuji</span>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto p-2 bg-zinc-50 rounded-xl border border-zinc-200">
                            @foreach ($schemes as $scheme)
                                <label class="flex items-center gap-2 text-xs text-zinc-700 cursor-pointer hover:bg-zinc-100 p-1 rounded">
                                    <input type="checkbox" name="scheme_ids[]" value="{{ $scheme->id }}" :checked="activeItem.scheme_ids && activeItem.scheme_ids.includes({{ $scheme->id }})" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                                    <span>{{ $scheme->nama_skema }} ({{ $scheme->kode_skema }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="edit_asesor_active" value="1" :checked="activeItem.is_active" class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="edit_asesor_active" class="text-xs text-zinc-700 cursor-pointer">Status Lisensi Asesor Aktif</label>
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
