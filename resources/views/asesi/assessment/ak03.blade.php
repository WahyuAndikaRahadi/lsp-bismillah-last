<x-app-layout title="FR-AK-03: Umpan Balik Asesmen — {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6">

        <!-- Top Navigation -->
        <div>
            <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                <span>Portal Asesi</span>
                <span>&rsaquo;</span>
                <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                <span>&rsaquo;</span>
                <a href="{{ route('asesi.applications.show', $application) }}" class="hover:underline font-mono">{{ $application->nomor_pendaftaran }}</a>
                <span>&rsaquo;</span>
                <span class="text-zinc-900 font-medium">FR-AK-03</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                FR-AK-03: Formulir Umpan Balik dan Catatan Asesmen
            </h1>
            <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                Evaluasi pelaksanaan uji kompetensi untuk peningkatan mutu berkelanjutan LSP.
            </p>
        </div>

        <form action="{{ route('asesi.applications.ak03.store', $application) }}" method="POST" class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-8 font-sans">
            @csrf

            <!-- Kop Form -->
            <div class="border-b-2 border-zinc-900 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                    <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase">FR-AK-03. FORMULIR UMPAN BALIK ASESMEN</h2>
                </div>
                <div class="text-right text-xs">
                    <div class="font-mono font-bold text-zinc-900">{{ $application->nomor_pendaftaran }}</div>
                    <div class="text-zinc-500">Skema: {{ $application->scheme->nama_skema }}</div>
                </div>
            </div>

            <!-- Kuesioner Evaluasi 5 Poin Standar BNSP -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Kuesioner Evaluasi Pelaksanaan Asesmen</h3>

                <div class="border border-zinc-200 rounded-xl overflow-hidden text-xs">
                    <table class="min-w-full divide-y divide-zinc-200 text-left">
                        <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">Komponen Evaluasi Proses Asesmen</th>
                                <th class="px-4 py-3 w-36 text-center">Pilihan Jawaban</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            <!-- Q1 -->
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-zinc-500">1</td>
                                <td class="px-4 py-3 text-zinc-800">
                                    Apakah asesi diberikan penjelasan tentang proses asesmen, tujuan, dan bukti yang diperlukan sebelum pelaksanaan uji?
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="q1_penjelasan" value="1" {{ $ak03->q1_penjelasan ? 'checked' : '' }} class="text-blue-600">
                                            <span>Ya</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="q1_penjelasan" value="0" {{ !$ak03->q1_penjelasan && $ak03->exists ? 'checked' : '' }} class="text-blue-600">
                                            <span>Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            <!-- Q2 -->
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-zinc-500">2</td>
                                <td class="px-4 py-3 text-zinc-800">
                                    Apakah asesi menyepakati rencana asesmen, metode uji yang digunakan, dan instrumen penilaian?
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="q2_kesepakatan" value="1" {{ $ak03->q2_kesepakatan ? 'checked' : '' }} class="text-blue-600">
                                            <span>Ya</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="q2_kesepakatan" value="0" {{ !$ak03->q2_kesepakatan && $ak03->exists ? 'checked' : '' }} class="text-blue-600">
                                            <span>Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            <!-- Q3 -->
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-zinc-500">3</td>
                                <td class="px-4 py-3 text-zinc-800">
                                    Apakah asesor menjelaskan hak banding terhadap hasil asesmen kepada asesi?
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="q3_hak_banding" value="1" {{ $ak03->q3_hak_banding ? 'checked' : '' }} class="text-blue-600">
                                            <span>Ya</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="q3_hak_banding" value="0" {{ !$ak03->q3_hak_banding && $ak03->exists ? 'checked' : '' }} class="text-blue-600">
                                            <span>Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            <!-- Q4 -->
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-zinc-500">4</td>
                                <td class="px-4 py-3 text-zinc-800">
                                    Apakah asesor bersikap adil, objektif, tidak diskriminatif, dan menciptakan suasana uji yang kondusif?
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="q4_adil_objektif" value="1" {{ $ak03->q4_adil_objektif ? 'checked' : '' }} class="text-blue-600">
                                            <span>Ya</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="q4_adil_objektif" value="0" {{ !$ak03->q4_adil_objektif && $ak03->exists ? 'checked' : '' }} class="text-blue-600">
                                            <span>Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            <!-- Q5 -->
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-zinc-500">5</td>
                                <td class="px-4 py-3 text-zinc-800">
                                    Apakah asesor memberikan umpan balik yang konstruktif dan penjelasan yang memadai terkait pencapaian kompetensi?
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="q5_umpan_balik" value="1" {{ $ak03->q5_umpan_balik ? 'checked' : '' }} class="text-blue-600">
                                            <span>Ya</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="q5_umpan_balik" value="0" {{ !$ak03->q5_umpan_balik && $ak03->exists ? 'checked' : '' }} class="text-blue-600">
                                            <span>Tidak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Catatan & Saran Perbaikan -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-zinc-800">Catatan & Saran Perbaikan untuk LSP dan TUK (Opsional)</label>
                <textarea name="catatan_rekomendasi" rows="3" placeholder="Tuliskan saran atau masukan Anda..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan_rekomendasi', $ak03->catatan_rekomendasi) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between border-t border-zinc-200 pt-6">
                <a href="{{ route('asesi.applications.show', $application) }}" class="px-4 py-2 rounded-xl border border-zinc-300 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 transition">
                    Kembali
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Kirim Formulir Umpan Balik (FR-AK-03)</span>
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
