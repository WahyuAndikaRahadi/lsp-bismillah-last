<x-app-layout title="Jadwal Meninjau Instrumen">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Meninjau Instrumen Asesmen</h1>
                <p class="text-xs text-zinc-500 mt-1">Penelaahan dan validasi perangkat asesmen (MAPA 01, MAPA 02, Soal Uji Tulis & Praktik) berdasarkan kaidah VRFK.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Telaah Baru (1 Col) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Validasi Instrumen Asesmen</h2>

                <form method="POST" action="{{ route('asesor.instruments.store') }}" class="space-y-4 text-xs">
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
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jenis Perangkat / Instrumen</label>
                        <select name="instrument_type" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="mapa_01">FR.MAPA.01 - Merencanakan Aktivitas & Proses Asesmen</option>
                            <option value="mapa_02">FR.MAPA.02 - Peta Instrumen Asesmen</option>
                            <option value="soal_tulis">FR.IA.05 - Pertanyaan Tertulis (DPT)</option>
                            <option value="soal_praktik">FR.IA.02 - Tugas Praktik Demonstrasi (TPD)</option>
                            <option value="wawancara">FR.IA.07 - Pertanyaan Lisan (DPL)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Telaah</label>
                        <input type="date" name="review_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Rekomendasi Validator</label>
                        <select name="recommendation" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="disetujui">DISETUJUI (Memenuhi Kaidah VRFK)</option>
                            <option value="perlu_perbaikan">PERLU PERBAIKAN</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Catatan Hasil Peninjauan</label>
                        <textarea name="notes" rows="4" placeholder="Evaluasi kesesuaian KUK, elemen, dan indikator unjuk kerja..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Telaah Instrumen
                    </button>
                </form>
            </div>

            <!-- Daftar Riwayat Telaah Instrumen (2 Cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Daftar Peninjauan Instrumen Asesmen</h2>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Riwayat instrumen yang telah ditelaah dan divalidasi oleh Anda.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Skema & Perangkat</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5 text-center">Rekomendasi</th>
                                <th class="p-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($reviews as $idx => $rev)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="p-3.5">
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $rev->scheme?->name }}</strong>
                                        <span class="text-[11px] font-mono uppercase text-zinc-500 block">{{ str_replace('_', ' ', $rev->instrument_type) }}</span>
                                    </td>
                                    <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $rev->review_date?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase
                                            {{ $rev->recommendation === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ str_replace('_', ' ', $rev->recommendation) }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-zinc-600 dark:text-zinc-400 text-[11px]">
                                        {{ $rev->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500">
                                        Belum ada riwayat peninjauan instrumen yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reviews->hasPages())
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
