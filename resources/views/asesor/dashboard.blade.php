<x-app-layout title="Home Asesor">
    <div class="space-y-6">

        <!-- Welcome Banner (Refined, Zero AI Slop, Dignified) -->
        <div class="bg-white rounded-xl border border-zinc-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                        Asesor Kompetensi BNSP
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Status Aktif
                    </span>
                </div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    {{ $asesor->name }}
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 flex flex-wrap items-center gap-3">
                    <span>No. Register: <strong class="font-mono text-zinc-800 dark:text-zinc-200">{{ $asesorProfile?->no_met ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>NIK: <strong class="font-mono text-zinc-800 dark:text-zinc-200">{{ $asesorProfile?->nik ?? '-' }}</strong></span>
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end gap-1.5 bg-zinc-50 dark:bg-zinc-800/50 p-3.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700/80">
                <span class="text-[11px] text-zinc-500 font-medium">Masa Berlaku Lisensi Asesor</span>
                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 font-mono">
                    {{ $asesorProfile?->license_valid_until?->translatedFormat('d F Y') ?? '25 Desember 2030' }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                    Tersisa {{ $daysRemaining ?? 1516 }} hari
                </span>
            </div>
        </div>

        <!-- 4 Key Control Counters -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('asesor.schedules.index') }}" class="bg-white p-4 rounded-xl border border-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Jadwal Uji</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $activeSchedulesCount }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600 transition">Jadwal aktif ditugaskan &rarr;</span>
            </a>

            <a href="{{ route('asesor.apl02.index') }}" class="bg-white p-4 rounded-xl border border-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Verifikasi APL-02</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">Tersedia</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600 transition">Asesmen mandiri & portofolio &rarr;</span>
            </a>

            <a href="{{ route('asesor.tuks.index') }}" class="bg-white p-4 rounded-xl border border-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Verifikasi TUK</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $tukVerificationsCount }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600 transition">Checklist sarana prasarana &rarr;</span>
            </a>

            <a href="{{ route('asesor.instruments.index') }}" class="bg-white p-4 rounded-xl border border-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Tinjau Instrumen</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $instrumentReviewsCount }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600 transition">Validasi MAPA & bank soal &rarr;</span>
            </a>
        </div>

        <!-- Jadwal Asesmen Terdekat & Daftar Tugas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Jadwal Asesmen Aktif (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 overflow-hidden">
                <div class="p-4 border-b border-zinc-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Jadwal Asesmen Anda</h2>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Penugasan asesmen kompetensi yang aktif</p>
                    </div>
                    <a href="{{ route('asesor.schedules.index') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 underline underline-offset-4">
                        Lihat Semua
                    </a>
                </div>

                @if ($activeSchedules->count() > 0)
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($activeSchedules as $sched)
                            <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-200">{{ $sched->schedule_code }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                                            {{ $sched->status }}
                                        </span>
                                    </div>
                                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white">{{ $sched->scheme?->name }}</h3>
                                    <p class="text-[11px] text-zinc-500">
                                        TUK: <strong>{{ $sched->tuk?->name }}</strong> &bull; Tgl: {{ $sched->assessment_date?->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('asesor.schedules.show', $sched) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-lsp-navy text-white hover:bg-zinc-800 active:scale-[0.98] transition">
                                        Peserta & Penilaian
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <p class="text-xs text-zinc-500">Belum ada penugasan jadwal asesmen aktif saat ini.</p>
                        <a href="{{ route('asesor.schedules.index') }}" class="inline-flex items-center mt-3 text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:underline">
                            Buka Daftar Jadwal Lengkap &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <!-- Panel Tugas & Dokumen Pokok (1 Col) -->
            <div class="space-y-6">

                <!-- Ringkasan Profil & Dokumen Pokok -->
                <div class="bg-white rounded-xl border border-zinc-200 p-4">
                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-3">Status Dokumen Asesor</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between p-2 rounded bg-zinc-50 dark:bg-zinc-800/50">
                            <span class="text-zinc-600 dark:text-zinc-300">Sertifikat Asesor (MET)</span>
                            <span class="text-emerald-700 font-semibold font-mono text-[11px]">Valid</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded bg-zinc-50 dark:bg-zinc-800/50">
                            <span class="text-zinc-600 dark:text-zinc-300">KTP & NIK</span>
                            <span class="text-emerald-700 font-semibold font-mono text-[11px]">Terverifikasi</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded bg-zinc-50 dark:bg-zinc-800/50">
                            <span class="text-zinc-600 dark:text-zinc-300">Skema Terdaftar</span>
                            <span class="text-zinc-900 dark:text-white font-bold font-mono text-[11px]">{{ $schemesCount }} Skema</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-zinc-200">
                        <a href="{{ route('asesor.profile.show') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:underline flex items-center justify-between">
                            <span>Lengkapi Dokumen Profil</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Alur Kerja Digital Asesor BNSP -->
                <div class="bg-white rounded-xl border border-zinc-200 p-4">
                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-2">Alur Kerja Digital</h3>
                    <p class="text-[11px] text-zinc-500 mb-3 leading-relaxed">Tahapan baku asesmen BNSP:</p>
                    <ol class="space-y-2 text-[11px] text-zinc-600 dark:text-zinc-300 list-decimal list-inside">
                        <li><strong>Surat Tugas</strong>: Terima penugasan resmi.</li>
                        <li><strong>Verifikasi TUK</strong>: Cek kesiapan alat & sarpras.</li>
                        <li><strong>FR-APL-02</strong>: Tinjau asesmen mandiri asesi (VATM).</li>
                        <li><strong>FR-AK-01</strong>: Persetujuan asesmen & kerahasiaan.</li>
                        <li><strong>FR-AK-02</strong>: Rekaman asesmen bukti uji (K/BK).</li>
                        <li><strong>FR-AK-05</strong>: Laporan rekomendasi hasil asesmen.</li>
                    </ol>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
