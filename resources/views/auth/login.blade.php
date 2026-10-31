@extends('layouts.app', ['title' => 'Masuk ke Akun Anda'])

@section('content')
<div class="w-full max-w-4xl mx-auto my-auto py-4">
    <!-- Main Split-Card Container -->
    <div class="w-full bg-white shadow-xl flex flex-col md:flex-row overflow-hidden relative border border-slate-200 rounded-2xl">
        
        <!-- Top Horizontal Dual Stripe (Spanning Entire Card: 65% Navy, 35% Red) -->
        <div class="absolute top-0 left-0 right-0 w-full flex h-1.5 z-10">
            <div class="w-3/5 md:w-[65%] bg-lsp-navy"></div>
            <div class="w-2/5 md:w-[35%] bg-lsp-red"></div>
        </div>

        <!-- Left Panel: Info & Branding (40%) -->
        <div class="w-full md:w-[40%] p-6 sm:p-8 md:p-10 flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-200 bg-[#F8FAFC]">
            <div>
                <!-- Official Logo -->
                <div class="mb-4 md:mb-6">
                    <img src="{{ asset('images/logo-lsp-wisindo.png') }}" 
                         alt="LSP Praktisi Otomotif Indonesia" 
                         class="h-14 sm:h-16 md:h-20 w-auto object-contain">
                </div>

                <h1 class="font-sans text-2xl md:text-3xl font-extrabold text-lsp-title tracking-tight mb-2">
                    LSP PrO Indo
                </h1>
                <p class="text-xs text-lsp-muted leading-relaxed">
                    Layanan sertifikasi kompetensi profesi yang tertib, transparan, dan berstandar BNSP.
                </p>
            </div>

            <div class="hidden md:block mt-8 pt-6 border-t border-slate-200">
                <span class="text-[10px] font-extrabold text-lsp-red uppercase tracking-widest block mb-1">
                    PORTAL SERTIFIKASI
                </span>
                <p class="text-[11px] text-lsp-muted leading-relaxed">
                    Gunakan akun yang telah terdaftar untuk mengakses layanan sesuai peran Anda.
                </p>
            </div>
        </div>

        <!-- Right Panel: Form (60%) -->
        <div class="w-full md:w-[60%] p-6 sm:p-8 md:p-12 bg-white flex flex-col justify-center">
            <span class="text-[10px] font-extrabold text-lsp-red uppercase tracking-wider block mb-1">
                PORTAL PENGGUNA
            </span>
            <h2 class="font-sans text-2xl md:text-3xl font-extrabold text-lsp-title mb-2">
                Masuk ke akun Anda
            </h2>
            <p class="text-xs text-lsp-muted mb-6">
                Masukkan email dan kata sandi yang telah terdaftar.
            </p>

            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-emerald-900">Pendaftaran Berhasil</p>
                        <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5" for="email">
                        Alamat email
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email', 'adminlsp@gmail.com') }}" required
                        class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('email') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="contoh@email.com" />
                    @error('email')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5" for="password">
                        Kata sandi
                    </label>
                    <input id="password" type="password" name="password" required value="@Kompeten86"
                        class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('password') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="••••••••••••" />
                    @error('password')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember" type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-lsp-navy focus:ring-0" />
                        <span class="text-xs text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-bold rounded-lg shadow-sm transition-colors duration-150">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Footer Link -->
            <div class="mt-6 md:mt-8 pt-4 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="text-lsp-navy font-bold hover:underline ml-1">
                        Daftar sebagai asesi
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
