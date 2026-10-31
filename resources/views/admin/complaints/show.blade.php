<x-app-layout title="Tindak Lanjut Pengaduan {{ $complaint->ticket_number }}">
    <div class="space-y-6">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.complaints.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Pengaduan</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Detail Pengaduan Tiket: {{ $complaint->ticket_number }}</h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-zinc-500">
                    <span>Masuk pada: {{ $complaint->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $complaint->statusBadge() }}">
                Status: {{ $complaint->statusLabel() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Detail Pengaduan (2 Kolom) -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-3">Informasi Pengaduan</h3>

                    <div>
                        <span class="text-xs text-zinc-400 block">Subjek / Perihal:</span>
                        <h4 class="text-base font-bold text-zinc-900 mt-0.5">{{ $complaint->judul }}</h4>
                    </div>

                    <div>
                        <span class="text-xs text-zinc-400 block mb-1">Rincian Lengkap Aduan:</span>
                        <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 leading-relaxed whitespace-pre-line">
                            {{ $complaint->isi_aduan }}
                        </div>
                    </div>
                </div>

                <!-- Form Tindak Lanjut -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-3">Formulir Keputusan & Tindak Lanjut Admin LSP</h3>

                    <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Perbarui Status Penanganan <span class="text-rose-500">*</span></label>
                            <select
                                name="status"
                                required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            >
                                <option value="diproses" {{ old('status', $complaint->status) === 'diproses' ? 'selected' : '' }}>Sedang Diproses / Dalam Investigasi</option>
                                <option value="selesai" {{ old('status', $complaint->status) === 'selesai' ? 'selected' : '' }}>Selesai Ditindaklanjuti</option>
                                <option value="ditolak" {{ old('status', $complaint->status) === 'ditolak' ? 'selected' : '' }}>Ditolak / Tidak Berdasar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Catatan Tindak Lanjut / Jawaban Resmi untuk Pelapor <span class="text-rose-500">*</span></label>
                            <textarea
                                name="tindak_lanjut"
                                rows="4"
                                required
                                placeholder="Uraikan hasil telaah komite etik/administrasi dan rekomendasi tindakan yang telah diputuskan..."
                                class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                            >{{ old('tindak_lanjut', $complaint->tindak_lanjut) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition"
                            >
                                Simpan Keputusan Tindak Lanjut
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profil Pelapor (1 Kolom) -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-3">Identitas Pelapor</h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-zinc-400 block">Nama Lengkap:</span>
                            <span class="font-bold text-zinc-900 text-sm">{{ $complaint->nama_pelapor }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block">Kategori Responden:</span>
                            <span class="inline-flex px-2 py-0.5 rounded font-semibold text-[10px] bg-zinc-100 text-zinc-800">{{ $complaint->jenis_responden }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block">Nomor Telepon:</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $complaint->telepon) }}" target="_blank" class="font-mono font-semibold text-blue-600 hover:underline">
                                {{ $complaint->telepon }}
                            </a>
                        </div>
                        <div>
                            <span class="text-zinc-400 block">Alamat Email:</span>
                            <span class="text-zinc-700">{{ $complaint->email ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                @if ($complaint->tgl_tindak_lanjut)
                    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-3 text-xs">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-2">Riwayat Pengesahan</h3>
                        <div>
                            <span class="text-zinc-400 block">Petugas Penindak:</span>
                            <span class="font-semibold text-zinc-800">{{ $complaint->petugas?->name ?? 'Admin LSP' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block">Waktu Penetapan:</span>
                            <span class="text-zinc-700">{{ $complaint->tgl_tindak_lanjut->format('d M Y H:i:s') }} WIB</span>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
