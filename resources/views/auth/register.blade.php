<x-guest-layout title="Pendaftaran Calon Asesi — LSP Sertifikasi">
    <div class="min-h-[calc(100vh-8rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl mx-auto shadow-md">
                LSP
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-zinc-900">
                Pendaftaran Calon Asesi
            </h2>
            <p class="mt-1.5 text-center text-xs text-zinc-500 max-w-md mx-auto">
                Daftarkan akun mandiri Anda untuk mengajukan permohonan sertifikasi kompetensi profesi terstandarisasi BNSP.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="bg-white py-8 px-6 shadow-sm border border-zinc-200 rounded-2xl sm:px-10">
                <form class="space-y-4" action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Nama Lengkap Sesuai KTP -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-zinc-700">
                            Nama Lengkap (Sesuai KTP / Tanda Pengenal) <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="name"
                                name="name"
                                type="text"
                                required
                                value="{{ old('name') }}"
                                placeholder="Contoh: Budi Santoso"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('name') border-rose-500 @enderror"
                            />
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK KTP -->
                        <div>
                            <label for="nik" class="block text-xs font-semibold text-zinc-700">
                                Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="nik"
                                    name="nik"
                                    type="text"
                                    maxlength="16"
                                    required
                                    value="{{ old('nik') }}"
                                    placeholder="16 digit nomor KTP"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 font-mono @error('nik') border-rose-500 @enderror"
                                />
                            </div>
                            @error('nik')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No HP / WhatsApp -->
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-zinc-700">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    required
                                    value="{{ old('phone') }}"
                                    placeholder="081234567890"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 font-mono @error('phone') border-rose-500 @enderror"
                                />
                            </div>
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-zinc-700">
                            Alamat Email Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                value="{{ old('email') }}"
                                placeholder="asesi@domain.com"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('email') border-rose-500 @enderror"
                            />
                        </div>
                        <p class="mt-1 text-[11px] text-zinc-400">Bukti pendaftaran dan jadwal uji akan dikirimkan ke alamat email ini.</p>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-zinc-700">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder="Minimal 8 karakter"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('password') border-rose-500 @enderror"
                                />
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-zinc-700">
                                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    placeholder="Ulangi kata sandi"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Terms agreement -->
                    <div class="pt-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="terms"
                                    name="terms"
                                    type="checkbox"
                                    required
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-zinc-300 rounded cursor-pointer"
                                />
                            </div>
                            <label for="terms" class="ml-2.5 text-xs text-zinc-600 leading-relaxed cursor-pointer">
                                Saya menyatakan bahwa seluruh data yang saya berikan adalah benar dan bersedia mengikuti seluruh proses sertifikasi sesuai ketentuan LSP dan standar BNSP.
                            </label>
                        </div>
                        @error('terms')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition cursor-pointer"
                        >
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs text-zinc-500">
                    Sudah pernah mendaftar?
                    <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
                        Masuk ke akun Anda
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
