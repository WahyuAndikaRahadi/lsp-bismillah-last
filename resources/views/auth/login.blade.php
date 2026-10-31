<x-guest-layout title="Masuk ke Sistem — LSP Sertifikasi">
    <div class="min-h-[calc(100vh-8rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl mx-auto shadow-md">
                LSP
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-zinc-900">
                Masuk ke Portal Sertifikasi
            </h2>
            <p class="mt-1.5 text-center text-xs text-zinc-500">
                Gunakan Email atau NIK terdaftar untuk mengakses akun Anda
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm border border-zinc-200 rounded-2xl sm:px-10">
                <form class="space-y-5" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email or NIK -->
                    <div>
                        <label for="login" class="block text-xs font-semibold text-zinc-700">
                            Email atau Nomor KTP / NIK
                        </label>
                        <div class="mt-1.5 relative">
                            <input
                                id="login"
                                name="login"
                                type="text"
                                autocomplete="username"
                                required
                                value="{{ old('login') }}"
                                placeholder="nama@email.com atau 16 digit NIK"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('login') border-rose-500 @enderror"
                            />
                        </div>
                        @error('login')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-zinc-700">
                                Kata Sandi
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition">
                                Lupa kata sandi?
                            </a>
                        </div>
                        <div class="mt-1.5 relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('password') border-rose-500 @enderror"
                            />
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-zinc-300 rounded cursor-pointer"
                        />
                        <label for="remember" class="ml-2 block text-xs text-zinc-600 cursor-pointer">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition cursor-pointer"
                        >
                            Masuk ke Akun
                        </button>
                    </div>
                </form>

                <!-- Demo Credentials Helper Card -->
                <div class="mt-6 pt-5 border-t border-zinc-100">
                    <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider text-center mb-2.5">
                        Kredensial Akun Pengujian (Seeded)
                    </p>
                    <div class="grid grid-cols-3 gap-2 text-center text-[11px]">
                        <div class="p-2 bg-zinc-50 rounded-lg border border-zinc-200/80">
                            <span class="block font-semibold text-purple-700">Admin LSP</span>
                            <span class="text-zinc-500 font-mono text-[10px]">admin@lsp.localhost</span>
                        </div>
                        <div class="p-2 bg-zinc-50 rounded-lg border border-zinc-200/80">
                            <span class="block font-semibold text-blue-700">Asesor</span>
                            <span class="text-zinc-500 font-mono text-[10px]">asesor@lsp.localhost</span>
                        </div>
                        <div class="p-2 bg-zinc-50 rounded-lg border border-zinc-200/80">
                            <span class="block font-semibold text-emerald-700">Asesi</span>
                            <span class="text-zinc-500 font-mono text-[10px]">asesi@lsp.localhost</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-zinc-400 text-center mt-1.5 font-mono">Password semua akun: password123</p>
                </div>

                <!-- Registration link -->
                <div class="mt-6 text-center text-xs text-zinc-500">
                    Belum memiliki akun calon asesi?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
                        Daftar sebagai Asesi
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
