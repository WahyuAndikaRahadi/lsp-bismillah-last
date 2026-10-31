<x-guest-layout title="Layanan Pengaduan & Aspirasi Masyarakat">
    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 mb-3">
                Transparansi & Akuntabilitas LSP
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 tracking-tight">
                Layanan Pengaduan & Aspirasi Publik
            </h1>
            <p class="mt-2 text-sm text-zinc-500 max-w-xl mx-auto">
                Sampaikan pengaduan, laporan dugaan pelanggaran etik asesmen, kendala teknis TUK, atau aspirasi perbaikan layanan secara resmi. Setiap laporan akan mendapatkan nomor tiket pelacakan.
            </p>
        </div>

        <!-- Success Alert with Ticket -->
        @if (session('ticket_success'))
            <div class="mb-8 p-6 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 shadow-sm text-center">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-lg font-bold">Pengaduan Anda Berhasil Diterima!</h3>
                <p class="text-xs text-emerald-700 mt-1">Terima kasih, {{ session('ticket_success')['nama'] }}. Pengaduan Anda telah masuk ke sistem dan akan segera ditindaklanjuti.</p>
                <div class="mt-4 p-3 bg-white border border-emerald-200 rounded-xl inline-block">
                    <span class="text-xs text-zinc-500 block">Nomor Tiket Pengaduan Anda:</span>
                    <span class="font-mono text-base font-extrabold text-emerald-800 tracking-wider">
                        {{ session('ticket_success')['ticket_number'] }}
                    </span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('pengaduan.tracking', ['ticket' => session('ticket_success')['ticket_number']]) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                        Lacak Status Tiket Pengaduan Ini &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('pengaduan.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Lengkap Pelapor <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="nama_pelapor"
                            value="{{ old('nama_pelapor', auth()->user()?->name) }}"
                            required
                            placeholder="Nama sesuai KTP"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                        @error('nama_pelapor') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="telepon"
                            value="{{ old('telepon') }}"
                            required
                            placeholder="081234567890"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                        @error('telepon') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Alamat Email (Opsional)</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()?->email) }}"
                            placeholder="email@example.com"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                        @error('email') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Kategori Pelapor <span class="text-rose-500">*</span></label>
                        <select
                            name="jenis_responden"
                            required
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        >
                            <option value="Asesi" {{ old('jenis_responden') == 'Asesi' ? 'selected' : '' }}>Peserta Asesmen (Asesi)</option>
                            <option value="Asesor" {{ old('jenis_responden') == 'Asesor' ? 'selected' : '' }}>Asesor Kompetensi</option>
                            <option value="TUK" {{ old('jenis_responden') == 'TUK' ? 'selected' : '' }}>Pengelola TUK</option>
                            <option value="Instansi" {{ old('jenis_responden') == 'Instansi' ? 'selected' : '' }}>Instansi / Perusahaan</option>
                            <option value="Umum" {{ old('jenis_responden', 'Umum') == 'Umum' ? 'selected' : '' }}>Masyarakat Umum</option>
                        </select>
                        @error('jenis_responden') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Judul / Perihal Pengaduan <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        required
                        placeholder="Ringkasan topik pengaduan atau aspirasi"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                    />
                    @error('judul') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Rincian Lengkap Pengaduan <span class="text-rose-500">*</span></label>
                    <textarea
                        name="isi_aduan"
                        rows="5"
                        required
                        placeholder="Uraikan kronologi, tanggal kejadian, pihak terkait, lokasi TUK, dan fakta pendukung secara jelas..."
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                    >{{ old('isi_aduan') }}</textarea>
                    @error('isi_aduan') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <a href="{{ route('pengaduan.tracking') }}" class="text-xs text-blue-600 hover:underline">
                        Sudah punya nomor tiket? Cek status di sini &rarr;
                    </a>

                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition"
                    >
                        Kirim Pengaduan Resmi
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-guest-layout>
