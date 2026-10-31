<x-app-layout title="Profil Lembaga LSP">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Profil Lembaga Sertifikasi Profesi (LSP)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Informasi resmi identitas lembaga, lisensi BNSP, kop surat, dan penanggung jawab.</p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">
                    Lisensi: {{ $profile->no_lisensi_bnsp ?? 'Belum Diatur' }}
                </span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.lsp-profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section: Data Identitas Lembaga -->
                <div>
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                        1. Identitas & Lisensi BNSP
                    </h3>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="nama_lsp" class="block text-xs font-semibold text-zinc-700">Nama Resmi LSP <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                id="nama_lsp"
                                name="nama_lsp"
                                value="{{ old('nama_lsp', $profile->nama_lsp) }}"
                                required
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('nama_lsp') border-rose-500 @enderror"
                            />
                            @error('nama_lsp')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kode_lsp" class="block text-xs font-semibold text-zinc-700">Kode LSP</label>
                            <input
                                type="text"
                                id="kode_lsp"
                                name="kode_lsp"
                                value="{{ old('kode_lsp', $profile->kode_lsp) }}"
                                placeholder="Contoh: LSP-021-ID"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div>
                            <label for="no_lisensi_bnsp" class="block text-xs font-semibold text-zinc-700">Nomor Lisensi BNSP <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                id="no_lisensi_bnsp"
                                name="no_lisensi_bnsp"
                                value="{{ old('no_lisensi_bnsp', $profile->no_lisensi_bnsp) }}"
                                required
                                placeholder="Contoh: BNSP-LSP-021-ID"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('no_lisensi_bnsp') border-rose-500 @enderror"
                            />
                            @error('no_lisensi_bnsp')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="no_sk_lisensi" class="block text-xs font-semibold text-zinc-700">Nomor SK Lisensi</label>
                            <input
                                type="text"
                                id="no_sk_lisensi"
                                name="no_sk_lisensi"
                                value="{{ old('no_sk_lisensi', $profile->no_sk_lisensi) }}"
                                placeholder="Contoh: KEP.0123/BNSP/III/2024"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div>
                            <label for="masa_berlaku_lisensi" class="block text-xs font-semibold text-zinc-700">Masa Berlaku Lisensi</label>
                            <input
                                type="date"
                                id="masa_berlaku_lisensi"
                                name="masa_berlaku_lisensi"
                                value="{{ old('masa_berlaku_lisensi', $profile->masa_berlaku_lisensi?->format('Y-m-d')) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div>
                            <label for="nama_ketua" class="block text-xs font-semibold text-zinc-700">Nama Direktur / Ketua LSP <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                id="nama_ketua"
                                name="nama_ketua"
                                value="{{ old('nama_ketua', $profile->nama_ketua) }}"
                                required
                                placeholder="Nama lengkap beserta gelar"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('nama_ketua') border-rose-500 @enderror"
                            />
                            @error('nama_ketua')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Kontak & Alamat -->
                <div class="pt-4">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                        2. Kontak & Alamat Kantor
                    </h3>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="telepon" class="block text-xs font-semibold text-zinc-700">No. Telepon / Fax</label>
                            <input
                                type="text"
                                id="telepon"
                                name="telepon"
                                value="{{ old('telepon', $profile->telepon) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-zinc-700">Email Resmi</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $profile->email) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div>
                            <label for="website" class="block text-xs font-semibold text-zinc-700">Website</label>
                            <input
                                type="text"
                                id="website"
                                name="website"
                                value="{{ old('website', $profile->website) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            />
                        </div>

                        <div class="sm:col-span-3">
                            <label for="alamat" class="block text-xs font-semibold text-zinc-700">Alamat Lengkap Kantor LSP</label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="2"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            >{{ old('alamat', $profile->alamat) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section: Format Kop Surat & Pengesahan Dokumen -->
                <div class="pt-4">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                        3. Kop Surat & Pengesahan Dokumen Resmi BNSP
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="kop_surat" class="block text-xs font-semibold text-zinc-700">Teks Header Kop Surat</label>
                            <textarea
                                id="kop_surat"
                                name="kop_surat"
                                rows="3"
                                placeholder="Teks yang akan dicantumkan pada bagian atas Surat Tugas, Berita Acara, dan Formulir MUK..."
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                            >{{ old('kop_surat', $profile->kop_surat) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label for="logo" class="block text-xs font-semibold text-zinc-700">Logo Resmi LSP</label>
                                <input
                                    type="file"
                                    id="logo"
                                    name="logo"
                                    accept="image/*"
                                    class="mt-1.5 block w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                @if ($profile->logo)
                                    <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Logo tersimpan</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label for="ttd_ketua" class="block text-xs font-semibold text-zinc-700">Tanda Tangan Digital Ketua (PNG Transparan)</label>
                                <input
                                    type="file"
                                    id="ttd_ketua"
                                    name="ttd_ketua"
                                    accept="image/*"
                                    class="mt-1.5 block w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                @if ($profile->ttd_ketua)
                                    <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Tanda tangan tersimpan</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-zinc-100 flex items-center justify-end">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                    >
                        Simpan Profil Lembaga
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
