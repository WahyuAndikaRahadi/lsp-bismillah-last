<x-app-layout title="Pengaturan Profil">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-extrabold text-lsp-title tracking-tight font-heading">Pengaturan Profil & Akun</h2>
                <p class="text-xs text-lsp-muted mt-1">Perbarui informasi data pribadi dan keamanan kata sandi akun Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold {{ $user->role_badge_classes }}">
                    {{ $user->role_label }}
                </span>
            </div>
        </div>

        <!-- Section 1: Profil Pengguna -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="max-w-xl">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-1.5 h-4 bg-lsp-navy rounded-full"></span>
                    <h3 class="text-sm font-bold text-lsp-title">Informasi Pribadi</h3>
                </div>
                <p class="text-xs text-lsp-muted">Perbarui nama lengkap, kontak email/telepon, dan NIK identitas akun Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('name') border-lsp-red @enderror"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('email') border-lsp-red @enderror"
                        />
                        @error('email')
                            <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-xs font-semibold text-slate-700">Nomor Induk Kependudukan (NIK)</label>
                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                maxlength="16"
                                value="{{ old('nik', $user->nik) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('nik') border-lsp-red @enderror"
                            />
                            @error('nik')
                                <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700">Nomor HP / WhatsApp</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('phone') border-lsp-red @enderror"
                            />
                            @error('phone')
                                <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Role (Read Only) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700">Peran Akun Sistem</label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $user->role_badge_classes }}">
                                {{ $user->role_label }}
                            </span>
                            <span class="text-xs text-lsp-muted">Hak akses dikelola terpusat oleh Admin LSP.</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-semibold rounded-lg shadow-xs transition inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 2: Ganti Kata Sandi -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="max-w-xl">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-1.5 h-4 bg-lsp-red rounded-full"></span>
                    <h3 class="text-sm font-bold text-lsp-title">Perbarui Kata Sandi</h3>
                </div>
                <p class="text-xs text-lsp-muted">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>

                <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Kata Sandi Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700">Kata Sandi Saat Ini</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            placeholder="••••••••"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('current_password') border-lsp-red @enderror"
                        />
                        @error('current_password')
                            <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kata Sandi Baru -->
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-700">Kata Sandi Baru</label>
                        <input
                            type="password"
                            id="new_password"
                            name="password"
                            required
                            placeholder="Minimal 8 karakter"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition @error('password') border-lsp-red @enderror"
                        />
                        @error('password')
                            <p class="mt-1 text-xs text-lsp-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi kata sandi baru"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-lsp-navy focus:border-lsp-navy transition"
                        />
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xs transition inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Ubah Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
