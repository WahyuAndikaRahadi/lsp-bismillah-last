<x-app-layout title="Laporan Tahunan Per Skema">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Laporan Tahunan Per Skema</h1>
                <p class="text-xs text-zinc-500 mt-1">Akumulasi jumlah sertifikasi per skema dalam satu tahun anggaran.</p>
            </div>
            <form method="GET" action="{{ route('admin.reports.annual') }}" class="flex items-center gap-2">
                <select name="year" class="rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-1.5 px-3">
                    @foreach (range(now()->year - 3, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white rounded-lg text-xs font-semibold hover:bg-zinc-800 transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Kode Skema</th>
                            <th class="p-3.5">Nama Skema Sertifikasi</th>
                            <th class="p-3.5 text-center">Total Asesi Tahun {{ $year }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($schemes as $idx => $s)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $s->kode_skema }}</td>
                                <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $s->nama_skema }}</td>
                                <td class="p-3.5 text-center font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $s->applications_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
