<x-guest-layout title="Surveilans Pemeliharaan Sertifikasi Profesi">
    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                Pemeliharaan Lisensi Kompetensi BNSP
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 tracking-tight">
                Layanan Surveilans Pemegang Sertifikat
            </h1>
            <p class="mt-2 text-sm text-zinc-500 max-w-xl mx-auto">
                Sesuai Pedoman BNSP 214, setiap pemegang sertifikat kompetensi wajib melaporkan pemeliharaan kompetensi dan aktivitas kerja relevan secara berkala selama masa berlaku 3 tahun.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-8 p-6 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 shadow-sm text-center">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-lg font-bold">Laporan Surveilans Berhasil Disimpan!</h3>
                <p class="text-xs text-emerald-700 mt-1">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('surveillance.store') }}" class="space-y-6">
                @csrf

                <div>
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-2 mb-4">
                        1. Data Pemegang Sertifikat
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Lengkap Sesuai Sertifikat <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="nama_lengkap"
                                value="{{ old('nama_lengkap', auth()->user()?->name) }}"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('nama_lengkap') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">NIK (16 Digit) <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="nik"
                                value="{{ old('nik') }}"
                                maxlength="16"
                                required
                                placeholder="3271xxxxxxxxxxxx"
                                class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('nik') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Email Aktif <span class="text-rose-500">*</span></label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', auth()->user()?->email) }}"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('email') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="telepon"
                                value="{{ old('telepon') }}"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('telepon') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-2 mb-4">
                        2. Informasi Sertifikat Kompetensi
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor Sertifikat BNSP <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="no_sertifikat"
                                value="{{ old('no_sertifikat') }}"
                                required
                                placeholder="Contoh: BNSP-SKM-TI-001-2026-0001"
                                class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('no_sertifikat') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor Registrasi Uji / Permohonan</label>
                            <input
                                type="text"
                                name="no_registrasi"
                                value="{{ old('no_registrasi') }}"
                                placeholder="Contoh: REG-2026-0001"
                                class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Skema Sertifikasi</label>
                            <select
                                name="scheme_id"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            >
                                <option value="">Pilih Skema Sertifikasi</option>
                                @foreach ($schemes as $s)
                                    <option value="{{ $s->id }}" {{ old('scheme_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_skema }} ({{ $s->kode_skema }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-2 mb-4">
                        3. Aktivitas Kerja Terkini & Kesesuaian Kompetensi
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Tempat Kerja / Perusahaan Saat Ini <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="tempat_kerja"
                                value="{{ old('tempat_kerja') }}"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('tempat_kerja') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Jabatan / Posisi <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="jabatan"
                                value="{{ old('jabatan') }}"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                            @error('jabatan') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Proyek / Penugasan Terkini</label>
                            <input
                                type="text"
                                name="proyek_saat_ini"
                                value="{{ old('proyek_saat_ini') }}"
                                placeholder="Sebutkan proyek, produk, atau kegiatan yang ditangani"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-700 mb-2">Apakah Pekerjaan Terkini Sesuai dengan Skema Sertifikasi? <span class="text-rose-500">*</span></label>
                            <div class="flex flex-wrap items-center gap-6 text-xs">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kesesuaian_skk" value="Y" {{ old('kesesuaian_skk', 'Y') === 'Y' ? 'checked' : '' }} class="text-blue-600">
                                    <span><strong>Ya (Sesuai)</strong> — Pekerjaan harian menerapkan unit kompetensi skema</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kesesuaian_skk" value="N" {{ old('kesesuaian_skk') === 'N' ? 'checked' : '' }} class="text-rose-600">
                                    <span><strong>Tidak Sesuai</strong> — Berbeda bidang tugas</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kesesuaian_skk" value="O" {{ old('kesesuaian_skk') === 'O' ? 'checked' : '' }} class="text-amber-600">
                                    <span><strong>Lainnya / Alih Profesi</strong></span>
                                </label>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Keterangan / Uraian Tugas Ringkas</label>
                            <textarea
                                name="keterangan"
                                rows="3"
                                placeholder="Jelaskan secara ringkas bagaimana kompetensi sertifikasi diterapkan pada pekerjaan Anda..."
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            >{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition"
                    >
                        Kirim Laporan Surveilans
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-guest-layout>
