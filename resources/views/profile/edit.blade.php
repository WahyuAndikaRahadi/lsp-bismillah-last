<x-app-layout title="Pengaturan Profil">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header Page Title -->
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Pengaturan Profil & Akun</h2>
            <p class="text-xs text-zinc-500 mt-1">Perbarui informasi data pribadi dan keamanan kata sandi akun Anda.</p>
        </div>

        <!-- Section 1: Profil Pengguna -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <div class="max-w-xl">
                <h3 class="text-sm font-bold text-zinc-900">Informasi Pribadi</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Perbarui nama, kontak, dan NIK identitas akun Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-zinc-700">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('name') border-rose-500 @enderror"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-zinc-700">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('email') border-rose-500 @enderror"
                        />
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-xs font-semibold text-zinc-700">Nomor Induk Kependudukan (NIK)</label>
                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                maxlength="16"
                                value="{{ old('nik', $user->nik) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('nik') border-rose-500 @enderror"
                            />
                            @error('nik')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-zinc-700">Nomor HP / WhatsApp</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('phone') border-rose-500 @enderror"
                            />
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Role (Read Only) -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700">Peran Akun Sistem</label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $user->role->badgeClasses() }}">
                                {{ $user->role->label() }}
                            </span>
                            <span class="text-xs text-zinc-400">Hak akses dikelola terpusat oleh Admin LSP.</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                        >
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 2: Ganti Kata Sandi -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <div class="max-w-xl">
                <h3 class="text-sm font-bold text-zinc-900">Perbarui Kata Sandi</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>

                <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Kata Sandi Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-zinc-700">Kata Sandi Saat Ini</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            placeholder="••••••••"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('current_password') border-rose-500 @enderror"
                        />
                        @error('current_password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kata Sandi Baru -->
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-zinc-700">Kata Sandi Baru</label>
                        <input
                            type="password"
                            id="new_password"
                            name="password"
                            required
                            placeholder="Minimal 8 karakter"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('password') border-rose-500 @enderror"
                        />
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-zinc-700">Konfirmasi Kata Sandi Baru</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi kata sandi baru"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                        >
                            Ubah Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
