<x-app-layout title="Posisi Keuangan (Neraca)">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Posisi Keuangan (Neraca)</h1>
                <p class="text-xs text-zinc-500 mt-1">Laporan posisi aset neto, saldo kas/bank, dan kewajiban LSP.</p>
            </div>
            <a href="{{ route('admin.finance.transactions.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 transition">
                Buku Transaksi
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Total Penerimaan Kas</span>
                <p class="text-2xl font-bold font-mono text-emerald-700 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>
            <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Total Pengeluaran Kas</span>
                <p class="text-2xl font-bold font-mono text-rose-700 mt-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
            <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase">Saldo Kas Bersih</span>
                <p class="text-2xl font-bold font-mono text-zinc-900 dark:text-white mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
            </div>
        </div>

    </div>
</x-app-layout>
