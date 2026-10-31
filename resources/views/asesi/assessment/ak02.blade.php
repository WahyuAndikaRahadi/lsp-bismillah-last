<x-app-layout title="FR-AK-02: Hasil Rekaman Asesmen — {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6">

        <!-- Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesi</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesi.applications.show', $application) }}" class="hover:underline font-mono">{{ $application->nomor_pendaftaran }}</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">Hasil FR-AK-02</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        FR-AK-02: Rekaman Hasil Asesmen Kompetensi
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $ak02->rekomendasi_akhir === 'K' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                        Rekomendasi: {{ $ak02->rekomendasi_akhir === 'K' ? 'Kompeten (K)' : 'Belum Kompeten (BK)' }}
                    </span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('asesi.applications.ak03', $application) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    <span>Isi Umpan Balik (FR-AK-03)</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-6 font-sans">
            <!-- Kop Form -->
            <div class="border-b-2 border-zinc-900 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                    <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase">FR-AK-02. REKAMAN ASESMEN KOMPETENSI</h2>
                </div>
                <div class="text-right text-xs">
                    <div class="font-mono font-bold text-zinc-900">{{ $application->nomor_pendaftaran }}</div>
                    <div class="text-zinc-500">Skema: {{ $application->scheme->nama_skema }}</div>
                </div>
            </div>

            <!-- Tabel Hasil per Unit -->
            <div class="border border-zinc-200 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                    <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3 w-40">Kode Unit</th>
                            <th class="px-4 py-3">Judul Unit Kompetensi</th>
                            <th class="px-4 py-3 w-32 text-center">Metode Uji</th>
                            <th class="px-4 py-3 w-28 text-center">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white">
                        @foreach ($ak02->units as $idx => $u)
                            <tr>
                                <td class="px-4 py-3 text-center text-zinc-500">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $u->unit->kode_unit }}</td>
                                <td class="px-4 py-3 text-zinc-800">{{ $u->unit->judul_unit }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-[11px]">
                                    {{ $u->metode_observasi_langsung ? 'L ' : '' }}
                                    {{ $u->metode_portofolio ? 'TL ' : '' }}
                                    {{ $u->metode_pertanyaan ? 'T' : '' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->rekomendasi_unit === 'K' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $u->rekomendasi_unit === 'K' ? 'Kompeten' : 'Belum Kompeten' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Catatan Asesor -->
            @if ($ak02->komentar_asesor || $ak02->tindak_lanjut)
                <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 text-xs space-y-2">
                    @if ($ak02->komentar_asesor)
                        <div>
                            <span class="font-bold text-zinc-800">Komentar Asesor:</span>
                            <p class="text-zinc-600 mt-0.5">{{ $ak02->komentar_asesor }}</p>
                        </div>
                    @endif
                    @if ($ak02->tindak_lanjut)
                        <div>
                            <span class="font-bold text-zinc-800">Tindak Lanjut yang Dibutuhkan:</span>
                            <p class="text-zinc-600 mt-0.5">{{ $ak02->tindak_lanjut }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Area Tanda Tangan Penerimaan Hasil -->
            <div class="border-t border-zinc-200 pt-6 grid grid-cols-1 md:grid-cols-2 gap-8 text-xs text-center">
                <!-- Sisi Asesi -->
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-3">
                    <span class="text-zinc-500 block">Asesi (Menerima Rekomendasi),</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($ak02->ttd_asesi)
                            <img src="{{ $ak02->ttd_asesi }}" alt="TTD Asesi" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Belum Konfirmasi Tanda Tangan]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $application->user->name }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $ak02->tgl_ttd_asesi?->format('d M Y H:i') ?? '-' }}</div>

                    @if (!$ak02->ttd_asesi)
                        <form action="{{ route('asesi.applications.ak02.sign', $application) }}" method="POST" class="pt-2">
                            @csrf
                            <input type="hidden" name="konfirmasi" value="1">
                            <button type="submit" class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                                Tanda Tangani Penerimaan Hasil
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Sisi Asesor -->
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-3">
                    <span class="text-zinc-500 block">Asesor Penguji,</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($ak02->ttd_asesor)
                            <img src="{{ $ak02->ttd_asesor }}" alt="TTD Asesor" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Tertanda Digital]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $ak02->asesor?->name ?? 'Asesor Penguji' }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $ak02->tgl_ttd_asesor?->format('d M Y H:i') ?? '-' }}</div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
