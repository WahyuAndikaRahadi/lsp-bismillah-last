<x-app-layout title="Jadwal Audit Internal">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Audit Internal LSP</h1>
                <p class="text-xs text-zinc-500 mt-1">Perencanaan, pelaksanaan, dan rekaman temuan audit mutu internal LSP berdasarkan ISO 17024 & Pedoman BNSP.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Tambah Audit (1 Col) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Input Jadwal Audit</h2>

                <form method="POST" action="{{ route('admin.quality.audits.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kode Audit</label>
                        <input type="text" name="audit_code" required placeholder="AUD-2026-001" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Audit</label>
                        <input type="date" name="audit_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Auditor Internal</label>
                        <input type="text" name="auditor_name" required placeholder="Dr. Bambang Sutrisno" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Lingkup Audit (Klausul)</label>
                        <textarea name="scope" required rows="3" placeholder="Klausul 4 s/d 10 Pedoman BNSP 201..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Temuan / Catatan Ketidaksesuaian</label>
                        <textarea name="findings" rows="3" placeholder="Catatan hasil audit..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Audit Internal
                    </button>
                </form>
            </div>

            <!-- Tabel Jadwal Audit (2 Cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Rekaman Audit Mutu Internal</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Kode Audit</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Auditor</th>
                                <th class="p-3.5">Lingkup Klausul</th>
                                <th class="p-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($audits as $idx => $aud)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $audits->firstItem() + $idx }}</td>
                                    <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $aud->audit_code }}</td>
                                    <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">{{ $aud->audit_date?->translatedFormat('d M Y') }}</td>
                                    <td class="p-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $aud->auditor_name }}</td>
                                    <td class="p-3.5 text-zinc-600 dark:text-zinc-400 max-w-xs">{{ $aud->scope }}</td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $aud->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-zinc-500">
                                        Belum ada rekaman audit internal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($audits->hasPages())
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                        {{ $audits->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
