@extends('layouts.app', ['title' => 'Pendaftaran Akun Peserta'])

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
                    Pendaftaran mandiri peserta (asesi) sertifikasi profesi otomotif standar BNSP.
                </p>
            </div>

            <div class="hidden md:block mt-8 pt-6 border-t border-slate-200">
                <span class="text-[10px] font-extrabold text-lsp-red uppercase tracking-widest block mb-1">
                    SERTIFIKASI PROFESI
                </span>
                <p class="text-[11px] text-lsp-muted leading-relaxed">
                    Pastikan data nama dan NIK yang Anda isi sesuai kartu tanda penduduk (KTP).
                </p>
            </div>
        </div>

        <!-- Right Panel: Form (60%) -->
        <div class="w-full md:w-[60%] p-6 sm:p-8 md:p-10 bg-white flex flex-col justify-center">
            <span class="text-[10px] font-extrabold text-lsp-red uppercase tracking-wider block mb-1">
                REGISTRASI PESERTA
            </span>
            <h2 class="font-sans text-2xl md:text-3xl font-extrabold text-lsp-title mb-1">
                Pendaftaran Calon Asesi
            </h2>
            <p class="text-xs text-lsp-muted mb-6">
                Isi data di bawah untuk membuat akun peserta asesmen.
            </p>

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                @if($errors->any())
                    <div class="p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-800">
                        <span class="font-bold block mb-1">Pendaftaran belum berhasil:</span>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1" for="name">
                        Nama Lengkap Sesuai KTP <span class="text-lsp-red">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name', old('full_name')) }}" required autofocus
                        class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('name') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="Contoh: Budi Santoso" />
                    @error('name')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1" for="nik">
                        Nomor Induk Kependudukan (NIK 16 Digit) <span class="text-lsp-red">*</span>
                    </label>
                    <input id="nik" type="text" name="nik" value="{{ old('nik') }}" required inputmode="numeric" maxlength="16"
                        class="numeric-only w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition font-mono @error('nik') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="Contoh: 3201012345670001" />
                    @error('nik')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1" for="email">
                        Alamat Email Aktif <span class="text-lsp-red">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('email') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="budi@example.com" />
                    @error('email')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1" for="phone">
                        Nomor Telepon / WhatsApp <span class="text-lsp-red">*</span>
                    </label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15"
                        class="numeric-only w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('phone') border-lsp-red ring-1 ring-lsp-red @enderror"
                        placeholder="Contoh: 081234567890" />
                    @error('phone')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1" for="password">
                            Kata Sandi <span class="text-lsp-red">*</span>
                        </label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition @error('password') border-lsp-red ring-1 ring-lsp-red @enderror"
                            placeholder="Min. 6 karakter" />
                        @error('password')
                            <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1" for="password_confirmation">
                            Konfirmasi Sandi <span class="text-lsp-red">*</span>
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 bg-lsp-input-bg border border-slate-300 focus:border-lsp-navy text-sm sm:text-xs rounded-lg text-slate-900 focus:outline-none focus:ring-1 focus:ring-lsp-navy transition"
                            placeholder="Ulangi sandi" />
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-start space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required
                            class="mt-0.5 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                        <span class="text-xs text-slate-600 leading-relaxed">
                            Saya menyatakan bahwa data yang diisi adalah benar sesuai KTP dan menyetujui ketentuan pendaftaran asesmen LSP PrO Indo. <span class="text-lsp-red">*</span>
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-1 text-[11px] text-lsp-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-bold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                        Daftar Sebagai Peserta
                    </button>
                </div>
            </form>

            <!-- Footer Link -->
            <div class="mt-6 pt-4 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-lsp-navy font-bold hover:underline ml-1">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
