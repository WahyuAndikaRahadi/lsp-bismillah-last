<x-app-layout title="Jadwal MKVA">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Jadwal Penugasan MKVA</h1>
                <p class="text-xs text-zinc-500 mt-1">Meninjau dan Mengembangkan Perangkat Asesmen (MKVA BNSP) pasca-pelaksanaan uji kompetensi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form MKVA Baru (1 Col) -->
            <div class="bg-white rounded-xl border border-zinc-200 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Input Evaluasi Perangkat Asesmen</h2>

                <form method="POST" action="{{ route('asesor.mkva.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Skema Sertifikasi</label>
                        <select name="scheme_id" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="">-- Pilih Skema --</option>
                            @foreach ($schemes as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jadwal Asesmen Terkait (Opsional)</label>
                        <select name="schedule_id" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="">-- Tidak Terikat Jadwal Spesifik --</option>
                            @foreach ($schedules as $sched)
                                <option value="{{ $sched->id }}">{{ $sched->schedule_code }} - {{ $sched->assessment_date?->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Evaluasi MKVA</label>
                        <input type="date" name="assignment_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Temuan & Evaluasi Perangkat</label>
                        <textarea name="findings" required rows="4" placeholder="Temuan terkait kejelasan instruksi soal, alokasi waktu uji, kendala bahasa atau alat..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Rencana Perbaikan & Rekomendasi Pengembangan</label>
                        <textarea name="improvement_plan" rows="3" placeholder="Saran pembaruan bank soal atau lembar observasi..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-lsp-navy text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Laporan MKVA
                    </button>
                </form>
            </div>

            <!-- Daftar Riwayat MKVA (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Riwayat Evaluasi MKVA</h2>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Laporan tinjauan dan pengembangan perangkat asesmen oleh asesor.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Skema Sertifikasi</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Temuan Evaluasi</th>
                                <th class="p-3.5">Rencana Pengembangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($assignments as $idx => $m)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="p-3.5">
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $m->scheme?->name }}</strong>
                                        <span class="text-[11px] font-mono text-zinc-500 block">{{ $m->scheme?->code }}</span>
                                    </td>
                                    <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $m->assignment_date?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3.5 text-zinc-700 dark:text-zinc-300 text-[11px] max-w-xs">
                                        {{ $m->findings }}
                                    </td>
                                    <td class="p-3.5 text-zinc-500 text-[11px] max-w-xs">
                                        {{ $m->improvement_plan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500">
                                        Belum ada riwayat evaluasi MKVA yang tersimpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($assignments->hasPages())
                    <div class="p-4 border-t border-zinc-200">
                        {{ $assignments->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
