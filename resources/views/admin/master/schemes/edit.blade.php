<x-app-layout title="Ubah Skema Sertifikasi">
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.schemes.show', $scheme) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Detail Skema</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Ubah Informasi Skema Sertifikasi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">{{ $scheme->nama_skema }} ({{ $scheme->kode_skema }})</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.schemes.update', $scheme) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="kode_skema" class="block text-xs font-semibold text-zinc-700">Kode Skema <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="kode_skema"
                            id="kode_skema"
                            required
                            value="{{ old('kode_skema', $scheme->kode_skema) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('kode_skema') border-rose-500 @enderror"
                        />
                        @error('kode_skema')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="nama_skema" class="block text-xs font-semibold text-zinc-700">Nama Skema Sertifikasi <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="nama_skema"
                            id="nama_skema"
                            required
                            value="{{ old('nama_skema', $scheme->nama_skema) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('nama_skema') border-rose-500 @enderror"
                        />
                        @error('nama_skema')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="standard_id" class="block text-xs font-semibold text-zinc-700">Standar Acuan (SKKNI)</label>
                        <select
                            name="standard_id"
                            id="standard_id"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            <option value="">Pilih Standar Acuan...</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}" {{ old('standard_id', $scheme->standard_id) == $standard->id ? 'selected' : '' }}>
                                    {{ $standard->kode_standar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="jenis_skema" class="block text-xs font-semibold text-zinc-700">Jenis Skema <span class="text-rose-500">*</span></label>
                        <select
                            name="jenis_skema"
                            id="jenis_skema"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            <option value="Okupasi" {{ old('jenis_skema', $scheme->jenis_skema) === 'Okupasi' ? 'selected' : '' }}>Okupasi Nasional</option>
                            <option value="KKNI" {{ old('jenis_skema', $scheme->jenis_skema) === 'KKNI' ? 'selected' : '' }}>Kualifikasi Nasional (KKNI)</option>
                            <option value="Klaster" {{ old('jenis_skema', $scheme->jenis_skema) === 'Klaster' ? 'selected' : '' }}>Klaster / Unit Tertentu</option>
                        </select>
                    </div>

                    <div>
                        <label for="kategori" class="block text-xs font-semibold text-zinc-700">Kategori / Sektor</label>
                        <input
                            type="text"
                            name="kategori"
                            id="kategori"
                            value="{{ old('kategori', $scheme->kategori) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>
                </div>

                <div>
                    <label for="biaya" class="block text-xs font-semibold text-zinc-700">Biaya Asesmen (Rp) <span class="text-rose-500">*</span></label>
                    <input
                        type="number"
                        name="biaya"
                        id="biaya"
                        required
                        min="0"
                        step="1000"
                        value="{{ old('biaya', (int)$scheme->biaya) }}"
                        class="mt-1.5 w-full sm:w-64 px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('biaya') border-rose-500 @enderror"
                    />
                    @error('biaya')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ringkasan" class="block text-xs font-semibold text-zinc-700">Ringkasan / Deskripsi Skema</label>
                    <textarea
                        name="ringkasan"
                        id="ringkasan"
                        rows="3"
                        class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    >{{ old('ringkasan', $scheme->ringkasan) }}</textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $scheme->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded border-zinc-300" />
                    <label for="is_active" class="text-xs text-zinc-700 cursor-pointer">Skema Aktif dan Dibuka untuk Pendaftaran Asesi</label>
                </div>

                <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.schemes.show', $scheme) }}" class="px-4 py-2 text-xs text-zinc-600 hover:bg-zinc-100 rounded-xl transition">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                    >
                        Simpan Perubahan Skema
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
