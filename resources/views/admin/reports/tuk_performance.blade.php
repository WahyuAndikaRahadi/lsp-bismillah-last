<x-app-layout title="Laporan Kinerja TUK">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Laporan Kinerja TUK</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar Tempat Uji Kompetensi, tipe TUK, dan kapasitas pelaksanaan asesmen.</p>
            </div>
            <a href="{{ route('admin.tuks.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-lsp-navy text-white hover:bg-zinc-800 transition">
                Kelola TUK
            </a>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Kode TUK</th>
                            <th class="p-3.5">Nama Tempat Uji (TUK)</th>
                            <th class="p-3.5">Jenis TUK</th>
                            <th class="p-3.5">Alamat</th>
                            <th class="p-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($tuks as $idx => $tuk)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $tuk->kode_tuk }}</td>
                                <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $tuk->nama_tuk }}</td>
                                <td class="p-3.5 capitalize text-zinc-600 dark:text-zinc-300">{{ $tuk->jenis_tuk }}</td>
                                <td class="p-3.5 text-zinc-500 max-w-xs truncate">{{ $tuk->alamat ?? '-' }}</td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $tuk->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ $tuk->is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-500">
                                    Belum ada data Tempat Uji Kompetensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
