<x-guest-layout title="Lembaga Sertifikasi Profesi (LSP) — Sertifikasi Kompetensi Berstandar BNSP">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-blue-50 via-white to-zinc-50 border-b border-zinc-200/80 py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 mb-6 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Sistem Sertifikasi Kompetensi Terlisensi BNSP
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-zinc-900 tracking-tight leading-tight sm:leading-none">
                    Uji Kompetensi Profesi Terpercaya, Akurat & Terstandarisasi
                </h1>
                <p class="mt-5 text-sm sm:text-base text-zinc-600 leading-relaxed">
                    Platform operasional resmi Lembaga Sertifikasi Profesi (LSP). Menyelenggarakan sertifikasi kompetensi kerja berbasis SKKNI dengan asesor tersertifikasi dan Tempat Uji Kompetensi (TUK) terverifikasi.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition text-center">
                        Daftar Sebagai Calon Asesi
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-zinc-50 text-zinc-800 text-sm font-semibold rounded-xl border border-zinc-300 shadow-xs transition text-center">
                        Masuk ke Akun Portal
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-2 sm:grid-cols-4 gap-4 text-left">
                    <div class="p-4 bg-white rounded-xl border border-zinc-200/80 shadow-xs">
                        <span class="text-xs text-zinc-500 block">Asesor Terlisensi</span>
                        <span class="text-xl font-bold text-zinc-900 mt-1 block">{{ $asesorCount }}+ Asesor</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-zinc-200/80 shadow-xs">
                        <span class="text-xs text-zinc-500 block">Peserta Terdaftar</span>
                        <span class="text-xl font-bold text-zinc-900 mt-1 block">{{ $asesiCount }}+ Asesi</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-zinc-200/80 shadow-xs">
                        <span class="text-xs text-zinc-500 block">Akreditasi Mutu</span>
                        <span class="text-xl font-bold text-zinc-900 mt-1 block">BNSP RI</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-zinc-200/80 shadow-xs">
                        <span class="text-xs text-zinc-500 block">Standar Uji</span>
                        <span class="text-xl font-bold text-zinc-900 mt-1 block">ISO 17024</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skema Sertifikasi Preview -->
    <section id="skema" class="py-16 bg-white border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Katalog Standar</span>
                    <h2 class="text-2xl font-bold text-zinc-900 tracking-tight mt-1">Skema Sertifikasi Kompetensi</h2>
                    <p class="text-xs text-zinc-500 mt-1">Skema sertifikasi okupasi dan kualifikasi kerja yang dibuka untuk pengujian.</p>
                </div>
                <div>
                    <span class="text-xs text-zinc-400 font-medium">Diperbarui secara berkala sesuai SKKNI</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl border border-zinc-200 bg-zinc-50/50 hover:border-blue-300 hover:shadow-md transition">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">Okupasi Nasional</span>
                    <h3 class="text-base font-bold text-zinc-900 mt-3">Junior Web Developer</h3>
                    <p class="text-xs text-zinc-600 mt-1.5 leading-relaxed">Sertifikasi kompetensi pemrograman web mencakup basis data relasional, pengkodean logika server-side, dan implementasi antarmuka klien.</p>
                    <div class="mt-4 pt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                        <span>Standar SKKNI</span>
                        <span class="font-semibold text-blue-600">6 Unit Kompetensi</span>
                    </div>
                </div>

                <div class="p-6 rounded-2xl border border-zinc-200 bg-zinc-50/50 hover:border-blue-300 hover:shadow-md transition">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Klaster Industri</span>
                    <h3 class="text-base font-bold text-zinc-900 mt-3">Network Administrator</h3>
                    <p class="text-xs text-zinc-600 mt-1.5 leading-relaxed">Sertifikasi keahlian perancangan topologi jaringan komputer, konfigurasi routing, switching, dan pengamanan perimeter sistem.</p>
                    <div class="mt-4 pt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                        <span>Standar SKKNI</span>
                        <span class="font-semibold text-blue-600">8 Unit Kompetensi</span>
                    </div>
                </div>

                <div class="p-6 rounded-2xl border border-zinc-200 bg-zinc-50/50 hover:border-blue-300 hover:shadow-md transition">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">Okupasi Profesional</span>
                    <h3 class="text-base font-bold text-zinc-900 mt-3">Database Administrator</h3>
                    <p class="text-xs text-zinc-600 mt-1.5 leading-relaxed">Pengelolaan performa basis data, backup & restore terintegrasi, integritas relasi tabel, dan proteksi kerahasiaan data perusahaan.</p>
                    <div class="mt-4 pt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                        <span>Standar SKKNI</span>
                        <span class="font-semibold text-blue-600">7 Unit Kompetensi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Sertifikasi Publik -->
    <section class="py-16 bg-zinc-50 border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Tahapan Uji</span>
            <h2 class="text-2xl font-bold text-zinc-900 tracking-tight mt-1">Bagaimana Cara Mengikuti Sertifikasi?</h2>
            <p class="text-xs text-zinc-500 mt-1 max-w-xl mx-auto">Prosedur pendaftaran dan asesmen terstandarisasi yang transparan, objektif, dan dapat dipertanggungjawabkan.</p>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-6 text-left">
                <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm mb-4">1</div>
                    <h3 class="text-sm font-bold text-zinc-900">Registrasi Akun</h3>
                    <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Daftar mandiri melalui website dengan mengisikan data NIK dan email aktif.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm mb-4">2</div>
                    <h3 class="text-sm font-bold text-zinc-900">Pengajuan APL-01</h3>
                    <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Pilih skema sertifikasi yang diinginkan dan unggah berkas bukti persyaratan dasar.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm mb-4">3</div>
                    <h3 class="text-sm font-bold text-zinc-900">Uji Kompetensi</h3>
                    <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Menjalani asesmen mandiri (APL-02) dan pelaksanaan uji di Tempat Uji Kompetensi (TUK).</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm mb-4">4</div>
                    <h3 class="text-sm font-bold text-zinc-900">Sertifikat BNSP</h3>
                    <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Peserta yang direkomendasikan Kompeten (K) dan disahkan pleno menerima sertifikat resmi.</p>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
