<x-app-layout title="Profil Asesor">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Profil Lengkap Asesor</h1>
                <p class="text-xs text-zinc-500 mt-1">Data identitas resmi, nomor registrasi MET BNSP, dan ruang lingkup skema sertifikasi.</p>
            </div>
            <a href="{{ route('asesor.dashboard') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                &larr; Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Card Identitas & Lisensi MET (1 Col) -->
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-6">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        LISENSI BNSP AKTIF
                    </span>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white mt-2">{{ $user->name }}</h2>
                    <p class="text-xs text-zinc-500 font-mono mt-0.5">{{ $user->email }}</p>
                </div>

                <div class="pt-4 border-t border-zinc-200 space-y-3 text-xs">
                    <div>
                        <span class="text-[11px] text-zinc-400 block font-semibold uppercase">No. Registrasi Asesor (MET)</span>
                        <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200 text-sm">{{ $asesor->no_met ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-zinc-400 block font-semibold uppercase">Nomor Induk Kependudukan (NIK)</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ $asesor->nik ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-zinc-400 block font-semibold uppercase">Nomor Handphone / WhatsApp</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ $asesor->phone ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-zinc-400 block font-semibold uppercase">Masa Berlaku Lisensi</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">
                            {{ $asesor->license_valid_until?->translatedFormat('d F Y') ?? '25 Desember 2030' }}
                        </span>
                    </div>
                </div>

                <!-- Skema Sertifikasi yang Diampu -->
                <div class="pt-4 border-t border-zinc-200">
                    <span class="text-[11px] text-zinc-400 block font-semibold uppercase mb-2">Ruang Lingkup Skema Kompetensi</span>
                    <div class="space-y-1.5">
                        @forelse ($asesor->schemes as $scheme)
                            <div class="p-2 rounded bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                                <strong class="text-zinc-900 dark:text-white block">{{ $scheme->name }}</strong>
                                <span class="font-mono text-[10px] text-zinc-500">{{ $scheme->code }}</span>
                            </div>
                        @empty
                            <p class="text-[11px] text-zinc-400 italic">Belum ada penetapan skema sertifikasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Form Edit Profil & Update Kontak (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">Perbarui Data Profil Asesor</h2>
                <p class="text-xs text-zinc-500 mb-6">Pastikan nomor handphone dan NIK sesuai dengan database BNSP untuk keperluan surat tugas & sertifikasi.</p>

                <form method="POST" action="{{ route('asesor.profile.update') }}" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap & Gelar</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full rounded-lg bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-xs py-2 px-3 text-zinc-500 cursor-not-allowed">
                            <span class="text-[10px] text-zinc-400 mt-1 block">Hubungi Admin LSP untuk perubahan nama gelar.</span>
                        </div>

                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Alamat Email Resmi</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-xs py-2 px-3 text-zinc-500 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" required maxlength="16" value="{{ old('nik', $asesor->nik) }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                            @error('nik') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">No. Handphone / WhatsApp</label>
                            <input type="text" name="phone" required maxlength="20" value="{{ old('phone', $asesor->phone) }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                            @error('phone') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-zinc-200 flex justify-end">
                        <button type="submit" class="py-2.5 px-5 rounded-lg bg-lsp-navy text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>
