<x-app-layout title="FR-AK-05: Laporan Asesmen — {{ $schedule->nama_jadwal }}">
    <div class="space-y-6">

        <!-- Top Navigation -->
        <div>
            <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                <span>Portal Asesor</span>
                <span>&rsaquo;</span>
                <span class="text-zinc-900 font-medium font-mono">{{ $schedule->kode_jadwal }}</span>
                <span>&rsaquo;</span>
                <span class="text-zinc-900 font-medium">Laporan FR-AK-05</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        FR-AK-05: Laporan Asesmen
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Laporan rekapitulasi pelaksanaan uji kompetensi untuk evaluasi dan kaji ulang materi uji oleh LSP.
                    </p>
                </div>
                <div class="shrink-0">
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak Laporan</span>
                    </button>
                </div>
            </div>
        </div>

        <form action="{{ route('asesor.ak05.store', $schedule) }}" method="POST" class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-8 font-sans">
            @csrf

            <!-- Kop Dokumen -->
            <div class="border-b-2 border-zinc-900 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                    <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase">FR-AK-05. LAPORAN ASESMEN</h2>
                </div>
                <div class="text-right text-xs">
                    <div class="font-mono font-bold text-zinc-900">{{ $report->nomor_laporan }}</div>
                    <div class="text-zinc-500">Jadwal: {{ $schedule->nama_jadwal }}</div>
                </div>
            </div>

            <!-- Identitas Pelaksanaan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs bg-zinc-50 p-4 rounded-xl border border-zinc-200">
                <div>
                    <span class="text-zinc-400 block">Skema Sertifikasi:</span>
                    <span class="font-bold text-zinc-900">{{ $schedule->scheme->nama_skema }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Tempat Uji Kompetensi:</span>
                    <span class="font-bold text-zinc-900">{{ $schedule->tuk->nama_tuk }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Tanggal Pelaksanaan:</span>
                    <span class="font-bold text-zinc-900">{{ $schedule->formatted_tanggal }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Nomor Laporan:</span>
                    <input type="text" name="nomor_laporan" value="{{ old('nomor_laporan', $report->nomor_laporan) }}" required class="w-full text-xs font-mono font-bold rounded-lg border-zinc-300 py-1">
                </div>
            </div>

            <!-- Rekapitulasi Peserta Uji -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Tabel Rekapitulasi Hasil Asesmen Peserta</h3>
                <div class="border border-zinc-200 rounded-xl overflow-hidden text-xs">
                    <table class="min-w-full divide-y divide-zinc-200 text-left">
                        <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                            <tr>
                                <th class="px-4 py-2.5 w-12 text-center">No</th>
                                <th class="px-4 py-2.5">Nama Calon Asesi</th>
                                <th class="px-4 py-2.5 w-44 font-mono">NIK</th>
                                <th class="px-4 py-2.5">Instansi / Perusahaan</th>
                                <th class="px-4 py-2.5 w-32 text-center">Hasil Asesmen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @forelse ($schedule->applications as $idx => $app)
                                <tr>
                                    <td class="px-4 py-3 text-center text-zinc-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-zinc-900">{{ $app->user->name }}</td>
                                    <td class="px-4 py-3 font-mono text-zinc-600">{{ $app->user->nik ?? '-' }}</td>
                                    <td class="px-4 py-3 text-zinc-600">{{ $app->user->asesiProfile?->nama_perusahaan ?? 'Pribadi' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($app->ak02)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $app->ak02->rekomendasi_akhir === 'K' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $app->ak02->rekomendasi_akhir === 'K' ? 'Kompeten (K)' : 'Belum Kompeten (BK)' }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400 italic text-[11px]">Belum Dinilai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-zinc-400 italic">
                                        Belum ada peserta yang terdaftar pada jadwal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deskripsi Pelaksanaan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Aspek Positif Pelaksanaan</label>
                    <textarea name="aspek_positif" rows="4" placeholder="Hal-hal positif selama pelaksanaan asesmen..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm">{{ old('aspek_positif', $report->aspek_positif) }}</textarea>
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Kendala / Hambatan yang Dihadapi</label>
                    <textarea name="kendala_hambatan" rows="4" placeholder="Kendala sarana prasarana atau teknis..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm">{{ old('kendala_hambatan', $report->kendala_hambatan) }}</textarea>
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Saran Perbaikan Instrumen / MUK</label>
                    <textarea name="saran_perbaikan" rows="4" placeholder="Saran perbaikan untuk kaji ulang MUK..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm">{{ old('saran_perbaikan', $report->saran_perbaikan) }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between border-t border-zinc-200 pt-6">
                <a href="{{ route('admin.schedules.show', $schedule) }}" class="px-4 py-2 rounded-xl border border-zinc-300 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 transition">
                    Kembali ke Jadwal
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Laporan Asesmen (FR-AK-05)</span>
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
