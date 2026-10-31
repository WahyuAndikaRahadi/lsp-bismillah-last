<x-app-layout title="Layanan Banding Asesmen">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Layanan Banding Asesmen</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar permohonan banding atas hasil keputusan asesmen kompetensi dari asesi.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Asesi</th>
                            <th class="p-3.5">Skema Sertifikasi</th>
                            <th class="p-3.5">Alasan Permohonan Banding</th>
                            <th class="p-3.5 text-center">Tanggal Pengajuan</th>
                            <th class="p-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($appeals as $idx => $appeal)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $appeals->firstItem() + $idx }}</td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $appeal->application?->asesi?->user?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">NIK: {{ $appeal->application?->asesi?->nik }}</span>
                                </td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-800 dark:text-zinc-200 block">{{ $appeal->application?->scheme?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">{{ $appeal->application?->scheme?->code }}</span>
                                </td>
                                <td class="p-3.5 text-zinc-700 dark:text-zinc-300 max-w-sm">
                                    {{ $appeal->reason }}
                                </td>
                                <td class="p-3.5 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ $appeal->created_at?->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $appeal->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-500">
                                    Belum ada permohonan banding asesmen yang diajukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($appeals->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $appeals->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
