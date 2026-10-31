<x-guest-layout title="Lupa Kata Sandi — LSP Sertifikasi">
    <div class="min-h-[calc(100vh-8rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl mx-auto shadow-md">
                LSP
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-zinc-900">
                Pemulihan Kata Sandi
            </h2>
            <p class="mt-1.5 text-center text-xs text-zinc-500 max-w-sm mx-auto">
                Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm border border-zinc-200 rounded-2xl sm:px-10">
                <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-zinc-700">
                            Alamat Email Terdaftar
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                required
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition placeholder:text-zinc-400 @error('email') border-rose-500 @enderror"
                            />
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition cursor-pointer"
                        >
                            Kirim Tautan Pemulihan
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs text-zinc-500">
                    Ingat kata sandi Anda?
                    <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
                        Kembali ke halaman Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
