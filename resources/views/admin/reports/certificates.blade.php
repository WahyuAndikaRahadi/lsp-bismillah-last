<x-app-layout title="Daftar Pemegang Sertifikat BNSP">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Daftar Pemegang Sertifikat BNSP</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar asesi yang telah dinyatakan kompeten dan diterbitkan sertifikat kompetensi resmi BNSP.</p>
            </div>
            <a href="{{ route('admin.exports.bnsp.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-lsp-navy text-white hover:bg-zinc-800 transition">
                Ekspor Sisfo BNSP
            </a>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Asesi & NIK</th>
                            <th class="p-3.5">Skema Sertifikasi</th>
                            <th class="p-3.5">Nomor Sertifikat BNSP</th>
                            <th class="p-3.5">Tanggal Terbit</th>
                            <th class="p-3.5">Masa Berlaku</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($certificates as $idx => $cert)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $certificates->firstItem() + $idx }}</td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $cert->asesi?->user?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">NIK: {{ $cert->asesi?->nik }}</span>
                                </td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-800 dark:text-zinc-200 block">{{ $cert->scheme?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">{{ $cert->scheme?->code }}</span>
                                </td>
                                <td class="p-3.5 font-mono font-bold text-zinc-900 dark:text-white">
                                    {{ $cert->no_sertifikat ?? $cert->nomor_sertifikat_bnsp }}
                                </td>
                                <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ $cert->tgl_terbit_sertifikat?->translatedFormat('d M Y') ?? '-' }}
                                </td>
                                <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ $cert->masa_berlaku_sertifikat?->translatedFormat('d M Y') ?? '3 Tahun' }}
                                </td>
                                <td class="p-3.5 text-right">
                                    <a href="{{ route('documents.sertifikat', $cert) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-lsp-navy text-white hover:bg-zinc-800 transition">
                                        Cetak PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-zinc-500">
                                    Belum ada sertifikat kompetensi yang diterbitkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($certificates->hasPages())
                <div class="p-4 border-t border-zinc-200">
                    {{ $certificates->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
