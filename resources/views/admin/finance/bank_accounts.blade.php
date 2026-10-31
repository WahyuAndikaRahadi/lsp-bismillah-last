<x-app-layout title="Rekening Bank LSP">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Rekening Bank LSP</h1>
                <p class="text-xs text-zinc-500 mt-1">Daftar rekening bank resmi penerimaan pembayaran biaya uji kompetensi asesi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Tambah Rekening (1 Col) -->
            <div class="bg-white rounded-xl border border-zinc-200 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Tambah Rekening Bank</h2>

                <form method="POST" action="{{ route('admin.finance.bank-accounts.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" required placeholder="Bank Mandiri / BCA / BNI / BRI" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nomor Rekening</label>
                        <input type="text" name="account_number" required placeholder="137001928374" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Atas Nama Rekening</label>
                        <input type="text" name="account_holder" required placeholder="LSP Sertifikasi Profesi" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kantor Cabang (Opsional)</label>
                        <input type="text" name="branch" placeholder="Cabang Sudirman" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-lsp-navy text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Rekening Bank
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Rekening (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Rekening Penerimaan Aktif</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Nama Bank</th>
                                <th class="p-3.5">Nomor Rekening</th>
                                <th class="p-3.5">Atas Nama</th>
                                <th class="p-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($accounts as $idx => $acc)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="p-3.5 font-bold text-zinc-900 dark:text-white">{{ $acc->bank_name }}</td>
                                    <td class="p-3.5 font-mono text-zinc-800 dark:text-zinc-200 font-semibold text-sm">{{ $acc->account_number }}</td>
                                    <td class="p-3.5 text-zinc-700 dark:text-zinc-300">{{ $acc->account_holder }}</td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500">
                                        Belum ada rekening bank yang didaftarkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
