<x-app-layout title="{{ $title }}">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ $title }}</h1>
                <p class="text-xs text-zinc-500 mt-1">{{ $subtitle }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.verifications.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-lsp-navy text-white hover:bg-zinc-800 transition">
                    Verifikasi Dokumen APL-01
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Asesi & Kontak</th>
                            <th class="p-3.5">Skema Sertifikasi</th>
                            <th class="p-3.5">Jadwal Uji / TUK</th>
                            <th class="p-3.5 text-center">Tanggal Daftar</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($applications as $idx => $app)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $applications->firstItem() + $idx }}</td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $app->asesi?->user?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">NIK: {{ $app->asesi?->nik }}</span>
                                    <span class="text-[10px] text-zinc-400 block">{{ $app->asesi?->user?->email }}</span>
                                </td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-800 dark:text-zinc-200 block">{{ $app->scheme?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">{{ $app->scheme?->code }}</span>
                                </td>
                                <td class="p-3.5">
                                    @if ($app->schedule)
                                        <span class="font-mono font-semibold text-zinc-800 dark:text-zinc-200 block">{{ $app->schedule->kode_jadwal }}</span>
                                        <span class="text-[11px] text-zinc-500 block">{{ $app->schedule->tuk?->name }}</span>
                                    @else
                                        <span class="text-[11px] text-amber-600 font-medium italic">Belum di-plotting</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ $app->created_at?->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase
                                        {{ $app->status->value === 'diterima' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($app->status->value === 'ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                        {{ $app->status->value }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.verifications.show', $app) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-lsp-navy text-white hover:bg-zinc-800 active:scale-[0.98] transition">
                                        Detail & Dokumen
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-zinc-500">
                                    Tidak ada data asesi pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($applications->hasPages())
                <div class="p-4 border-t border-zinc-200">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
