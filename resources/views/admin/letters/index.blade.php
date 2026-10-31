<x-app-layout title="Data Persuratan Resmi LSP">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Data Persuratan Resmi LSP</h1>
                <p class="text-xs text-zinc-500 mt-1">Generator penomoran surat otomatis, arsip Surat Tugas, SK Penetapan, Undangan, dan Perjanjian.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Buat Surat Baru (1 Col) -->
            <div class="bg-white rounded-xl border border-zinc-200 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Terbitkan Surat Baru</h2>

                <form method="POST" action="{{ route('admin.letters.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nomor Surat</label>
                        <input type="text" name="reference_number" required placeholder="001/ST-LSP/X/2026" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kategori Persuratan</label>
                        <select name="category" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="surat_tugas">Surat Tugas (ST)</option>
                            <option value="surat_keputusan">Surat Keputusan (SK)</option>
                            <option value="surat_edaran">Surat Edaran / Pengumuman</option>
                            <option value="surat_undangan">Surat Undangan</option>
                            <option value="surat_perjanjian">Surat Perjanjian Kerja</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Perihal / Subjek</label>
                        <input type="text" name="subject" required placeholder="Penugasan Asesor Uji Kompetensi TKJ" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Penerima / Ditujukan Kepada</label>
                        <input type="text" name="recipient" required placeholder="Abu, S.T. / Seluruh Asesi" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Surat</label>
                        <input type="date" name="letter_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-lsp-navy text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Terbitkan Dokumen Persuratan
                    </button>
                </form>
            </div>

            <!-- Tabel Arsip Surat (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Arsip Persuratan Resmi</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Nomor & Kategori Surat</th>
                                <th class="p-3.5">Perihal</th>
                                <th class="p-3.5">Penerima</th>
                                <th class="p-3.5 text-center">Tanggal</th>
                                <th class="p-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($letters as $idx => $l)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $letters->firstItem() + $idx }}</td>
                                    <td class="p-3.5">
                                        <strong class="font-mono font-semibold text-zinc-900 dark:text-zinc-100 block">{{ $l->reference_number }}</strong>
                                        <span class="text-[10px] uppercase font-mono text-zinc-400">{{ str_replace('_', ' ', $l->category) }}</span>
                                    </td>
                                    <td class="p-3.5 font-medium text-zinc-800 dark:text-zinc-200">{{ $l->subject }}</td>
                                    <td class="p-3.5 text-zinc-600 dark:text-zinc-400">{{ $l->recipient }}</td>
                                    <td class="p-3.5 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $l->letter_date?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $l->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-zinc-500">
                                        Belum ada dokumen persuratan yang diterbitkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($letters->hasPages())
                    <div class="p-4 border-t border-zinc-200">
                        {{ $letters->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
