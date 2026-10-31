<x-app-layout title="Laporan Kinerja Asesor">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Laporan Kinerja Asesor</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar asesor kompetensi, nomor registrasi MET, status lisensi, dan portfolio pengujian.</p>
            </div>
            <a href="{{ route('admin.asesors.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 transition">
                Kelola Asesor
            </a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Asesor</th>
                            <th class="p-3.5">No. MET BNSP</th>
                            <th class="p-3.5">Masa Berlaku Lisensi</th>
                            <th class="p-3.5">Ruang Lingkup Skema</th>
                            <th class="p-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($asesors as $idx => $a)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $a->user?->name }}</strong>
                                    <span class="text-[11px] font-mono text-zinc-500">{{ $a->user?->email }}</span>
                                </td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $a->no_met ?? '-' }}</td>
                                <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ $a->masa_berlaku?->translatedFormat('d F Y') ?? 'Aktif' }}
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-zinc-100 text-zinc-800">
                                        {{ $a->schemes->count() }} Skema
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $a->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ $a->is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-500">
                                    Belum ada data asesor kompetensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
