<x-app-layout title="Jadwal Uji Kompetensi Asesor">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Jadwal Uji Kompetensi</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar jadwal asesmen kompetensi yang ditugaskan kepada Anda sebagai Asesor Penguji.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('asesor.dashboard') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    &larr; Dashboard
                </a>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Kode & Skema Asesmen</th>
                            <th class="p-3.5">Tempat Uji (TUK)</th>
                            <th class="p-3.5">Tanggal Asesmen</th>
                            <th class="p-3.5 text-center">Kuota & Asesi</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($schedules as $index => $schedule)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">
                                    {{ $schedules->firstItem() + $index }}
                                </td>
                                <td class="p-3.5">
                                    <span class="font-mono font-semibold text-zinc-900 dark:text-zinc-100 block">{{ $schedule->schedule_code }}</span>
                                    <span class="text-zinc-600 dark:text-zinc-300 font-medium block mt-0.5">{{ $schedule->scheme?->name }}</span>
                                    <span class="text-[10px] text-zinc-400 font-mono">{{ $schedule->scheme?->code }}</span>
                                </td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-800 dark:text-zinc-200 font-medium block">{{ $schedule->tuk?->name }}</strong>
                                    <span class="text-[11px] text-zinc-500 block truncate max-w-xs">{{ $schedule->tuk?->address ?? '-' }}</span>
                                </td>
                                <td class="p-3.5">
                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 block">
                                        {{ $schedule->assessment_date?->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-[11px] text-zinc-500 block">
                                        Pra: {{ $schedule->pra_assessment_date?->translatedFormat('d M Y') ?? 'Sebelum Uji' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                        {{ $schedule->applications->count() }} / {{ $schedule->quota }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider
                                        {{ $schedule->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ $schedule->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('asesor.schedules.show', $schedule) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 active:scale-[0.98] transition">
                                        Peserta & Penilaian
                                    </a>
                                    <a href="{{ route('asesor.ak05.report', $schedule) }}" class="inline-flex items-center px-2 py-1.5 rounded-lg text-xs font-medium border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                        AK-05
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-zinc-500">
                                    Tidak ada jadwal asesmen kompetensi yang aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schedules->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
