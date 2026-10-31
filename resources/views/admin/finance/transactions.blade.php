<x-app-layout title="Transaksi Keuangan LSP">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Buku Transaksi Keuangan</h1>
                <p class="text-xs text-zinc-500 mt-1">Pencatatan mutasi kas masuk dan keluar operasional sertifikasi LSP.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.finance.balance-sheet') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    Neraca
                </a>
                <a href="{{ route('admin.finance.income-statement') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    Laba Rugi
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Transaksi Baru (1 Col) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Catat Transaksi</h2>

                <form method="POST" action="{{ route('admin.finance.transactions.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Pos Akun Keuangan</label>
                        <select name="account_id" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jenis Mutasi</label>
                        <select name="type" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="pemasukan">PEMASUKAN (Kredit)</option>
                            <option value="pengeluaran">PENGELUARAN (Debit)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" required min="0" placeholder="500000" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900 font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Uraian / Keterangan</label>
                        <textarea name="description" required rows="3" placeholder="Pembayaran pendaftaran uji asesi..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Transaksi
                    </button>
                </form>
            </div>

            <!-- Tabel Riwayat Transaksi (2 Cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Jurnal Transaksi Kas & Bank</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Akun & Uraian</th>
                                <th class="p-3.5 text-center">Jenis</th>
                                <th class="p-3.5 text-right">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($transactions as $idx => $t)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $transactions->firstItem() + $idx }}</td>
                                    <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">{{ $t->transaction_date?->translatedFormat('d M Y') }}</td>
                                    <td class="p-3.5">
                                        <span class="font-mono text-[10px] text-zinc-400 block">{{ $t->account?->account_code }} - {{ $t->account?->account_name }}</span>
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $t->description }}</strong>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono
                                            {{ $t->type === 'pemasukan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $t->type }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-right font-mono font-bold {{ $t->type === 'pemasukan' ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                        {{ $t->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500">
                                        Belum ada rekaman transaksi keuangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
