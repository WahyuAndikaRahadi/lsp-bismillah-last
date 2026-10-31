<x-app-layout title="Verifikasi Fasilitas TUK">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Verifikasi Kelayakan TUK</h1>
                <p class="text-xs text-zinc-500 mt-1">Formulir penugasan verifikasi sarana, prasarana, dan K3 Tempat Uji Kompetensi (TUK) oleh Asesor.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Verifikasi TUK Baru (1 Col) -->
            <div class="bg-white rounded-xl border border-zinc-200 p-5 shadow-sm">
                <h2 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Input Hasil Verifikasi TUK</h2>

                <form method="POST" action="{{ route('asesor.tuks.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Pilih Tempat Uji (TUK)</label>
                        <select name="tuk_id" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="">-- Pilih TUK Terdaftar --</option>
                            @foreach ($tuks as $tuk)
                                <option value="{{ $tuk->id }}">{{ $tuk->name }} ({{ strtoupper($tuk->type) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Verifikasi</label>
                        <input type="date" name="verification_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jenis TUK</label>
                        <select name="verification_type" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="mandiri">TUK Mandiri</option>
                            <option value="sewaktu">TUK Sewaktu</option>
                            <option value="tempat_kerja">TUK Tempat Kerja</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Rekomendasi Kelayakan</label>
                        <select name="recommendation" required class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs py-2 px-3 focus:ring-zinc-900">
                            <option value="layak">LAYAK (Memenuhi Syarat Uji)</option>
                            <option value="belum_layak">BELUM LAYAK (Perlu Perbaikan Sarpras)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Catatan Verifikator & Temuan</label>
                        <textarea name="notes" rows="4" placeholder="Kondisi ruang uji, peralatan, K3, dan kesiapan operasional..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-xs p-3 focus:ring-zinc-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-lsp-navy text-white font-semibold text-xs hover:bg-zinc-800 active:scale-[0.98] transition">
                        Simpan Verifikasi TUK
                    </button>
                </form>
            </div>

            <!-- Daftar Riwayat Verifikasi TUK (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200">
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Riwayat Verifikasi TUK</h2>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Daftar verifikasi sarana prasarana yang telah Anda lakukan.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Tempat Uji (TUK)</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5 text-center">Rekomendasi</th>
                                <th class="p-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($verifications as $i => $v)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                    <td class="p-3.5 text-center font-mono text-zinc-400">{{ $i + 1 }}</td>
                                    <td class="p-3.5">
                                        <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $v->tuk?->name }}</strong>
                                        <span class="text-[11px] text-zinc-500 block capitalize">{{ $v->verification_type }}</span>
                                    </td>
                                    <td class="p-3.5 font-mono text-zinc-700 dark:text-zinc-300">
                                        {{ $v->verification_date?->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase
                                            {{ $v->recommendation === 'layak' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ str_replace('_', ' ', $v->recommendation) }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-zinc-600 dark:text-zinc-400 text-[11px]">
                                        {{ $v->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500">
                                        Belum ada riwayat verifikasi TUK yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($verifications->hasPages())
                    <div class="p-4 border-t border-zinc-200">
                        {{ $verifications->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
