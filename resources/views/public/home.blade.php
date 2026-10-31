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

    <!-- Skema Sertifikasi Section -->
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
                @forelse ($schemes as $scheme)
                    <div class="p-6 rounded-2xl border border-zinc-200 bg-zinc-50/50 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                    {{ $scheme->jenis_skema }}
                                </span>
                                <span class="font-mono text-xs font-bold text-zinc-900">{{ $scheme->formatted_biaya }}</span>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900">{{ $scheme->nama_skema }}</h3>
                            <p class="text-xs text-zinc-600 mt-1.5 leading-relaxed line-clamp-3">{{ $scheme->ringkasan }}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                            <span class="font-mono text-[11px]">{{ $scheme->kode_skema }}</span>
                            <span class="font-semibold text-blue-600">{{ $scheme->units_count }} Unit Kompetensi</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-zinc-400 text-xs py-8 col-span-3">Belum ada skema sertifikasi yang dipublikasikan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Jadwal Asesmen Publik (Fase 3) -->
    <section id="jadwal" class="py-16 bg-zinc-50 border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Jadwal Terbuka</span>
                    <h2 class="text-2xl font-bold text-zinc-900 tracking-tight mt-1">Jadwal Uji Kompetensi Mendatang</h2>
                    <p class="text-xs text-zinc-500 mt-1">Daftar pelaksanaan asesmen sertifikasi yang sedang membuka pendaftaran peserta.</p>
                </div>
                <div>
                    <a href="{{ route('register') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">Daftar Sekarang &rarr;</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($upcomingSchedules as $sch)
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sch->status->badgeClasses() }}">
                                    {{ $sch->status->label() }}
                                </span>
                                <span class="text-xs font-mono font-semibold text-blue-600">Kuota: {{ $sch->kuota_peserta }}</span>
                            </div>

                            <h3 class="text-sm font-bold text-zinc-900">{{ $sch->nama_jadwal }}</h3>
                            <p class="text-xs text-zinc-500 mt-1 font-medium">{{ $sch->scheme->nama_skema }}</p>

                            <div class="mt-4 space-y-1.5 text-xs text-zinc-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-bold text-zinc-800">{{ $sch->formatted_tanggal }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="truncate">{{ $sch->tuk->nama_tuk }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-zinc-100 flex items-center justify-between">
                            <span class="text-[11px] text-zinc-400 font-mono">{{ $sch->kode_jadwal }}</span>
                            @if ($sch->status->isRegistrationOpen())
                                @auth
                                    @if(auth()->user()->isAsesi())
                                        <a href="{{ route('asesi.applications.create', ['schedule_id' => $sch->id, 'scheme_id' => $sch->scheme_id]) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                            Daftar Uji
                                        </a>
                                    @else
                                        <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                            Dashboard
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                        Daftar Uji
                                    </a>
                                @endauth
                            @else
                                <span class="text-xs text-zinc-400">Segera Dibuka</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-zinc-400 text-xs py-8 col-span-3">Belum ada jadwal uji kompetensi terbuka saat ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- TUK Section -->
    <section id="tuk" class="py-16 bg-white border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center max-w-xl mx-auto">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Lokasi Uji Resmi</span>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight mt-1">Tempat Uji Kompetensi (TUK) Terverifikasi</h2>
                <p class="text-xs text-zinc-500 mt-1">Fasilitas laboratorium dan sarana praktek berstandar teknis BNSP.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($tuks as $tk)
                    <div class="p-6 rounded-2xl border border-zinc-200 bg-zinc-50/40">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold {{ $tk->jenis_badge_classes }} mb-2">
                            TUK {{ $tk->jenis_tuk }}
                        </span>
                        <h3 class="text-sm font-bold text-zinc-900">{{ $tk->nama_tuk }}</h3>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">{{ $tk->alamat ?? 'Lokasi resmi terdaftar' }}</p>
                        <div class="mt-4 pt-3 border-t border-zinc-200/60 text-[11px] text-zinc-400 flex items-center justify-between">
                            <span>PIC: {{ $tk->penanggung_jawab ?? '-' }}</span>
                            <span class="font-mono">{{ $tk->kode_tuk }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-zinc-400 text-xs py-8 col-span-3">Belum ada data TUK.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Layanan Pengaduan & Surveilans Section -->
    <section id="pengaduan" class="py-16 bg-zinc-50 border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card Pengaduan -->
                <div class="p-8 rounded-2xl bg-white border border-zinc-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 mb-3">
                            Transparansi & Aduan Publik
                        </span>
                        <h3 class="text-xl font-bold text-zinc-900">Layanan Pengaduan & Aspirasi</h3>
                        <p class="text-xs text-zinc-500 mt-2 leading-relaxed">
                            Punya keluhan, laporan ketidaksesuaian prosedur asesmen, atau masukan perbaikan layanan? Sampaikan aduan resmi melalui sistem tiket terpadu kami.
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="{{ route('pengaduan.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition">
                            Buat Pengaduan Baru
                        </a>
                        <a href="{{ route('pengaduan.tracking') }}" class="px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-semibold rounded-xl text-xs transition">
                            Lacak Status Tiket &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card Surveilans -->
                <div class="p-8 rounded-2xl bg-white border border-zinc-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                            Pemeliharaan Lisensi BNSP
                        </span>
                        <h3 class="text-xl font-bold text-zinc-900">Surveilans Pemegang Sertifikat</h3>
                        <p class="text-xs text-zinc-500 mt-2 leading-relaxed">
                            Bagi alumni pemegang sertifikat kompetensi LSP yang masih berlaku, laporkan pemeliharaan kompetensi, tempat kerja terkini, dan proyek profesi secara berkala.
                        </p>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('surveillance.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition inline-block">
                            Isi Formulir Surveilans Berkala
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
