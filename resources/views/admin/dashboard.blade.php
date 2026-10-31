<x-app-layout title="Dashboard Admin LSP">
    <div class="space-y-6">

        <!-- Top Header & Status LSP (Restrained, Dignified, Zero AI Slop) -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                        Panel Operasional Admin
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Sistem Beroperasi Normal
                    </span>
                </div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    Pusat Kendali Operasional LSP
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-xl">
                    Kelola seluruh siklus sertifikasi BNSP: master skema, verifikasi APL-01, penugasan asesor, pelaksanaan uji, sidang pleno, hingga penerbitan sertifikat.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 active:scale-[0.98] transition">
                    Verifikasi Berkas APL-01
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    + Jadwal Uji Baru
                </a>
            </div>
        </div>

        <!-- 8 Core Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.verifications.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Verifikasi Pending</span>
                <span class="text-2xl font-bold font-mono text-amber-600 mt-1 block">{{ $stats['pending_verifications'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Berkas APL-01 masuk &rarr;</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Jadwal Asesmen</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $stats['total_schedules'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Jadwal uji aktif &rarr;</span>
            </a>

            <a href="{{ route('admin.reports.certificates') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Sertifikat Terbit</span>
                <span class="text-2xl font-bold font-mono text-emerald-700 mt-1 block">{{ $stats['total_certificates'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">No. Sertifikat BNSP &rarr;</span>
            </a>

            <a href="{{ route('admin.schemes.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Skema Sertifikasi</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $stats['total_schemes'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Klaster / Okupasi / KKNI &rarr;</span>
            </a>

            <a href="{{ route('admin.asesors.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Asesor Terdaftar</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $stats['total_asesor'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Lisensi MET BNSP &rarr;</span>
            </a>

            <a href="{{ route('admin.tuks.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Lokasi Uji (TUK)</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $stats['total_tuks'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Mandiri / Sewaktu &rarr;</span>
            </a>

            <a href="{{ route('admin.asesi.competent') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Asesi Diterima</span>
                <span class="text-2xl font-bold font-mono text-emerald-700 mt-1 block">{{ $stats['accepted_applications'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Lulus asesmen &rarr;</span>
            </a>

            <a href="{{ route('admin.exports.bnsp.index') }}" class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition group">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block">Total Pendaftar</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1 block">{{ $stats['total_applications'] }}</span>
                <span class="text-[11px] text-zinc-400 mt-1 block group-hover:text-zinc-600">Data Sisfo BNSP &rarr;</span>
            </a>
        </div>

        <!-- 2 Grid Tables (Recent Applications & Upcoming Schedules) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Permohonan Asesi Terbaru -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Permohonan Asesi Terbaru</h2>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Asesi yang baru saja mendaftarkan diri secara mandiri</p>
                    </div>
                    <a href="{{ route('admin.verifications.index') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3">Nama Asesi</th>
                                <th class="p-3">Skema</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($recentApplications as $app)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3">
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $app->asesi?->user?->name }}</strong>
                                        <span class="font-mono text-[10px] text-zinc-400">NIK: {{ $app->asesi?->nik }}</span>
                                    </td>
                                    <td class="p-3 text-zinc-700 dark:text-zinc-300 truncate max-w-xs">
                                        {{ $app->scheme?->name }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase
                                            {{ $app->status->value === 'diterima' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($app->status->value === 'diajukan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-zinc-100 text-zinc-600') }}">
                                            {{ $app->status->value }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('admin.verifications.show', $app) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 transition">
                                            Tinjau
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-zinc-500">
                                        Belum ada permohonan asesi terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Jadwal Asesmen Terdekat -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Jadwal Uji Terdekat</h2>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Sesi uji kompetensi yang akan segera berlangsung</p>
                    </div>
                    <a href="{{ route('admin.schedules.index') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3">Kode & Skema</th>
                                <th class="p-3">TUK</th>
                                <th class="p-3 text-center">Tanggal</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($recentSchedules as $sched)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3">
                                        <strong class="font-mono text-zinc-900 dark:text-zinc-100 block">{{ $sched->kode_jadwal }}</strong>
                                        <span class="text-[11px] text-zinc-500 truncate max-w-xs block">{{ $sched->scheme?->name }}</span>
                                    </td>
                                    <td class="p-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $sched->tuk?->name }}
                                    </td>
                                    <td class="p-3 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $sched->tgl_asesmen?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('admin.schedules.show', $sched) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-zinc-500">
                                        Belum ada jadwal uji terdekat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
