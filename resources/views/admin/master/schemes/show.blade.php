<x-app-layout title="Detail Skema & Unit Kompetensi">
    <div class="space-y-6" x-data="{ unitModal: false, reqModal: false, elementModal: false, criteriaModal: false, activeUnitId: null, activeElementId: null }">

        <!-- Top Breadcrumbs & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.schemes.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Skema</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">{{ $scheme->nama_skema }}</h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-zinc-500">
                    <span class="font-mono font-semibold text-zinc-700">{{ $scheme->kode_skema }}</span>
                    <span>•</span>
                    <span class="font-medium text-zinc-700">{{ $scheme->standard->kode_standar ?? 'SKKNI' }}</span>
                    <span>•</span>
                    <span class="font-bold text-blue-600 font-mono">{{ $scheme->formatted_biaya }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.schemes.edit', $scheme) }}"
                    class="px-3.5 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-300 shadow-xs transition"
                >
                    Ubah Skema
                </a>
                <button
                    @click="unitModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Unit Kompetensi</span>
                </button>
            </div>
        </div>

        <!-- Section 1: Dokumen Persyaratan Pendaftaran (FR-APL-01) -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">1. Persyaratan Dokumen Asesi (FR-APL-01)</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Dokumen bukti kelayakan yang wajib diunggah oleh calon asesi saat mendaftar skema ini.</p>
                </div>
                <button
                    @click="reqModal = true"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition"
                >
                    + Tambah Syarat Dokumen
                </button>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 w-12 text-center">No</th>
                            <th class="px-4 py-2.5">Nama Dokumen Persyaratan</th>
                            <th class="px-4 py-2.5">Keterangan / Spesifikasi</th>
                            <th class="px-4 py-2.5 text-center">Kategori Wajib</th>
                            <th class="px-4 py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($scheme->requirements as $index => $req)
                            <tr class="hover:bg-zinc-50/80">
                                <td class="px-4 py-3 text-center font-mono">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-zinc-900">{{ $req->nama_dokumen }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ $req->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($req->is_required)
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                            Wajib
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 text-zinc-600">
                                            Opsional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.schemes.requirements.destroy', $req) }}" onsubmit="return confirm('Hapus persyaratan dokumen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-zinc-400 hover:text-rose-600 p-1" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-zinc-400">
                                    Belum ada persyaratan dokumen yang diatur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Unit Kompetensi Kemasan (MUK Tree) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">2. Kemasan Unit Kompetensi (Materi Uji Kompetensi)</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Daftar unit kompetensi, elemen kompetensi, dan kriteria unjuk kerja (KUK) standar BNSP.</p>
                </div>
                <span class="text-xs font-semibold text-zinc-500">Total: {{ $scheme->units->count() }} Unit</span>
            </div>

            @forelse ($scheme->units as $uIndex => $unit)
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden" x-data="{ expanded: true }">
                    <!-- Unit Header Card -->
                    <div class="p-4 sm:p-5 bg-zinc-50/80 border-b border-zinc-200 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $uIndex + 1 }}
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900">{{ $unit->judul_unit }}</h4>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-zinc-500">
                                    <span class="font-mono font-semibold text-zinc-700">{{ $unit->kode_unit }}</span>
                                    <span>•</span>
                                    <span>{{ $unit->jenis_standar }}</span>
                                    <span>•</span>
                                    <span class="font-medium text-blue-600">{{ $unit->elements->count() }} Elemen</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                @click="activeUnitId = {{ $unit->id }}; elementModal = true"
                                class="px-2.5 py-1 bg-white hover:bg-zinc-100 text-zinc-700 text-xs font-medium rounded-lg border border-zinc-200 shadow-2xs transition"
                            >
                                + Elemen
                            </button>

                            <button @click="expanded = !expanded" class="p-1.5 text-zinc-400 hover:text-zinc-600 rounded-lg hover:bg-zinc-200/50">
                                <svg class="w-4 h-4 transform transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <form method="POST" action="{{ route('admin.schemes.units.destroy', $unit) }}" onsubmit="return confirm('Hapus unit kompetensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-zinc-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Hapus Unit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Elemen & KUK Content -->
                    <div x-show="expanded" class="p-5 space-y-4">
                        @forelse ($unit->elements as $eIndex => $element)
                            <div class="border border-zinc-200/80 rounded-xl p-4 bg-zinc-50/40">
                                <div class="flex items-start justify-between gap-4 pb-2.5 border-b border-zinc-200/60">
                                    <div>
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Elemen {{ $element->nomor_elemen }}</span>
                                        <h5 class="text-xs font-bold text-zinc-900 mt-0.5">{{ $element->nama_elemen }}</h5>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="activeElementId = {{ $element->id }}; criteriaModal = true"
                                            class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-2 py-0.5 rounded"
                                        >
                                            + Tambah KUK
                                        </button>
                                        <form method="POST" action="{{ route('admin.schemes.elements.destroy', $element) }}" onsubmit="return confirm('Hapus elemen kompetensi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-zinc-400 hover:text-rose-600 p-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- KUK Table inside Elemen -->
                                <div class="mt-3 overflow-x-auto">
                                    <table class="w-full text-left text-xs text-zinc-600">
                                        <thead class="text-zinc-400 uppercase font-semibold text-[10px]">
                                            <tr>
                                                <th class="py-1.5 w-16">No KUK</th>
                                                <th class="py-1.5">Pernyataan Kriteria Unjuk Kerja</th>
                                                <th class="py-1.5">Pertanyaan Observasi / Lisan</th>
                                                <th class="py-1.5 w-10 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100">
                                            @forelse ($element->criterias as $criteria)
                                                <tr>
                                                    <td class="py-2 font-mono font-bold text-blue-600">{{ $criteria->nomor_kuk }}</td>
                                                    <td class="py-2 text-zinc-800 pr-4 leading-relaxed">{{ $criteria->pernyataan }}</td>
                                                    <td class="py-2 text-zinc-500 italic pr-4">{{ $criteria->pertanyaan_observasi ?? '-' }}</td>
                                                    <td class="py-2 text-right">
                                                        <form method="POST" action="{{ route('admin.schemes.criterias.destroy', $criteria) }}" onsubmit="return confirm('Hapus KUK ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-zinc-300 hover:text-rose-600 p-0.5">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-3 text-center text-zinc-400 text-[11px]">
                                                        Belum ada Kriteria Unjuk Kerja (KUK) pada elemen ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-zinc-400 text-xs py-4">Belum ada elemen kompetensi pada unit ini. Klik "+ Elemen" di atas untuk menambahkan.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-zinc-200 p-8 text-center text-zinc-400">
                    <p class="text-sm">Belum ada Unit Kompetensi pada skema ini.</p>
                    <button @click="unitModal = true" class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl">
                        + Tambah Unit Kompetensi Pertama
                    </button>
                </div>
            @endforelse
        </div>

        <!-- MODAL 1: Tambah Syarat Dokumen -->
        <div x-show="reqModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="reqModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Persyaratan Dokumen Asesi</h3>
                    <button @click="reqModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.schemes.requirements.store', $scheme) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama Dokumen Persyaratan <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_dokumen" required placeholder="Contoh: Ijazah Terakhir Minimal S1" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Keterangan Tambahan</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Scan dokumen asli berwarna format PDF" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_required" id="req_required" value="1" checked class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                        <label for="req_required" class="text-xs text-zinc-700 cursor-pointer">Wajib Dipenuhi (Mandatory)</label>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="reqModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan Syarat</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: Tambah Unit Kompetensi -->
        <div x-show="unitModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="unitModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Unit Kompetensi Baru</h3>
                    <button @click="unitModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.schemes.units.store', $scheme) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Kode Unit Kompetensi <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_unit" required placeholder="Contoh: J.620100.004.01" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Judul Unit Kompetensi <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul_unit" required placeholder="Contoh: Menggunakan Struktur Data" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Jenis Standar</label>
                        <input type="text" name="jenis_standar" value="SKKNI" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl" />
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="unitModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan Unit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 3: Tambah Elemen Kompetensi -->
        <div x-show="elementModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="elementModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Elemen Kompetensi</h3>
                    <button @click="elementModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form :action="'/admin/units/' + activeUnitId + '/elements'" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nama / Bunyi Elemen Kompetensi <span class="text-rose-500">*</span></label>
                        <textarea name="nama_elemen" required rows="3" placeholder="Contoh: Mengidentifikasi konsep dan struktur data yang dibutuhkan..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="elementModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan Elemen</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 4: Tambah KUK -->
        <div x-show="criteriaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-xs" style="display: none;">
            <div @click.away="criteriaModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-zinc-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Kriteria Unjuk Kerja (KUK)</h3>
                    <button @click="criteriaModal = false" class="text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
                <form :action="'/admin/elements/' + activeElementId + '/criterias'" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Nomor KUK <span class="text-rose-500">*</span></label>
                        <input type="text" name="nomor_kuk" required placeholder="Contoh: 1.1 atau 2.1" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Pernyataan KUK Standar BNSP <span class="text-rose-500">*</span></label>
                        <textarea name="pernyataan" required rows="3" placeholder="Pernyataan unjuk kerja yang harus dicapai peserta..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Panduan Pertanyaan Observasi Asesor (FR-AK-02)</label>
                        <textarea name="pertanyaan_observasi" rows="2" placeholder="Pertanyaan lisan yang diajukan asesor untuk membuktikan KUK ini..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-zinc-300 rounded-xl"></textarea>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
                        <button type="button" @click="criteriaModal = false" class="px-3 py-1.5 text-xs text-zinc-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl">Simpan KUK</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
