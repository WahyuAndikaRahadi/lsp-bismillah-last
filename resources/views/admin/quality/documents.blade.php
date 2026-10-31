<x-app-layout title="Dokumen Mutu LSP">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Dokumen Mutu LSP</h1>
                <p class="text-xs text-zinc-500 mt-1">Panduan Mutu, Standar Operasional Prosedur (SOP), Formulir, dan Dokumen Kendali Mutu BNSP.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Tambah Dokumen Mutu (1 Col) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Tambah Dokumen Mutu</h2>

                <form method="POST" action="{{ route('admin.quality.documents.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kode Dokumen</label>
                        <input type="text" name="document_code" required placeholder="PM-LSP-01 / SOP-LSP-02" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Judul Dokumen</label>
                        <input type="text" name="title" required placeholder="Panduan Mutu Pelaksanaan Asesmen" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kategori</label>
                        <select name="category" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="panduan_mutu">Panduan Mutu (PM)</option>
                            <option value="sop">Standar Operasional Prosedur (SOP)</option>
                            <option value="formulir">Formulir Kendali Mutu (FRM)</option>
                            <option value="renstra">Rencana Strategis (Renstra)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Versi / Edisi</label>
                            <input type="text" name="version" required value="1.0" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tgl. Berlaku</label>
                            <input type="date" name="effective_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Dokumen Mutu
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Dokumen Mutu (2 Cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Daftar Dokumen Mutu Aktif</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Kode & Versi</th>
                                <th class="p-3.5">Judul Dokumen Mutu</th>
                                <th class="p-3.5">Kategori</th>
                                <th class="p-3.5 text-center">Tgl. Efektif</th>
                                <th class="p-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($documents as $idx => $doc)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $documents->firstItem() + $idx }}</td>
                                    <td class="p-3.5 font-mono">
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $doc->document_code }}</strong>
                                        <span class="text-[10px] text-zinc-400">Ver. {{ $doc->version }}</span>
                                    </td>
                                    <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $doc->title }}</td>
                                    <td class="p-3.5 uppercase text-[10px] font-mono text-zinc-600 dark:text-zinc-400">
                                        {{ str_replace('_', ' ', $doc->category) }}
                                    </td>
                                    <td class="p-3.5 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $doc->effective_date?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $doc->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-zinc-500">
                                        Belum ada dokumen mutu yang didaftarkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
