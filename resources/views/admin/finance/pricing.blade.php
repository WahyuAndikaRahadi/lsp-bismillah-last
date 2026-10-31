<x-app-layout title="Biaya Uji Sertifikasi">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Tarif & Biaya Uji Sertifikasi</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar tarif resmi per skema sertifikasi kompetensi.</p>
            </div>
            <a href="{{ route('admin.schemes.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 transition">
                Kelola Skema
            </a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Kode Skema</th>
                            <th class="p-3.5">Nama Skema Sertifikasi</th>
                            <th class="p-3.5">Jenis</th>
                            <th class="p-3.5 text-right">Tarif Biaya Uji</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($schemes as $idx => $s)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $s->kode_skema }}</td>
                                <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $s->nama_skema }}</td>
                                <td class="p-3.5 uppercase text-[11px] font-mono text-zinc-500">{{ $s->jenis_skema }}</td>
                                <td class="p-3.5 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                    Rp {{ number_format($s->biaya ?? 500000, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
