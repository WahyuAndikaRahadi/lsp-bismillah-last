<x-app-layout title="Laporan Umum Sertifikasi">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Laporan Umum Sertifikasi</h1>
                <p class="text-xs text-zinc-500 mt-1">Rekapitulasi total asesi terdaftar, lulus kompeten, dan performa skema sertifikasi.</p>
            </div>
            <a href="{{ route('admin.exports.bnsp.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-lsp-navy text-white hover:bg-zinc-800 transition">
                Ekspor Sisfo BNSP
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-xl bg-white border border-zinc-200">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Total Pendaftar Asesi</span>
                <p class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1">{{ $totalApplications }}</p>
                <span class="text-[11px] text-zinc-500 mt-1 block">Permohonan APL-01 masuk</span>
            </div>
            <div class="p-5 rounded-xl bg-white border border-zinc-200">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Asesi Kompeten (K)</span>
                <p class="text-2xl font-bold font-mono text-emerald-700 mt-1">{{ $competentCount }}</p>
                <span class="text-[11px] text-zinc-500 mt-1 block">Ditetapkan melalui sidang pleno</span>
            </div>
            <div class="p-5 rounded-xl bg-white border border-zinc-200">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Jumlah Skema Aktif</span>
                <p class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1">{{ $schemes->count() }}</p>
                <span class="text-[11px] text-zinc-500 mt-1 block">Tersedia untuk pendaftaran</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-zinc-200">
                <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Rekapitulasi Per Skema Sertifikasi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Kode Skema</th>
                            <th class="p-3.5">Nama Skema Sertifikasi</th>
                            <th class="p-3.5 text-center">Jenis</th>
                            <th class="p-3.5 text-center">Total Peserta Asesmen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($schemes as $idx => $s)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $s->kode_skema }}</td>
                                <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $s->nama_skema }}</td>
                                <td class="p-3.5 text-center uppercase text-[11px] font-mono text-zinc-500">{{ $s->jenis_skema }}</td>
                                <td class="p-3.5 text-center font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $s->applications_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
