<x-app-layout title="Sidang Pleno Kelulusan Asesmen">
    <div class="space-y-6">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.pleno.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Pleno</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Rapat Pleno Penetapan Hasil Asesmen</h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-zinc-500">
                    <span class="font-mono font-semibold text-zinc-700">{{ $schedule->kode_jadwal }}</span>
                    <span>•</span>
                    <span class="font-medium text-zinc-800">{{ $schedule->nama_jadwal }}</span>
                    <span>•</span>
                    <span>{{ $schedule->scheme->nama_skema ?? '-' }}</span>
                </div>
            </div>

            <!-- PDF Download Links if already authorized -->
            @if ($schedule->plenoMeeting && $schedule->plenoMeeting->status === 'disahkan')
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('documents.berita-acara-pleno', $schedule) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Cetak BA Pleno</span>
                    </a>
                    <a
                        href="{{ route('documents.daftar-hadir-pleno', $schedule) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 hover:bg-lsp-navy text-white font-semibold rounded-xl text-xs shadow-xs transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Presensi Pleno</span>
                    </a>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.pleno.store', $schedule) }}" class="space-y-6">
            @csrf

            <!-- Pleno Information Card -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-4 mb-4">
                    <h3 class="text-sm font-bold text-zinc-900">1. Data Berita Acara Rapat Pleno Komite Teknis</h3>
                    <span class="text-xs text-zinc-500 font-mono">Format Dokumen Resmi BNSP</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor Berita Acara Pleno <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="nomor_ba_pleno"
                            value="{{ old('nomor_ba_pleno', $pleno->nomor_ba_pleno) }}"
                            required
                            class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Tanggal Sidang Pleno <span class="text-rose-500">*</span></label>
                        <input
                            type="date"
                            name="tanggal_pleno"
                            value="{{ old('tanggal_pleno', $pleno->tanggal_pleno ? \Carbon\Carbon::parse($pleno->tanggal_pleno)->format('Y-m-d') : date('Y-m-d')) }}"
                            required
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Tempat / Lokasi Sidang <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="tempat"
                            value="{{ old('tempat', $pleno->tempat) }}"
                            required
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Ketua Komite Teknis / Pengesah <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="ketua_komite"
                            value="{{ old('ketua_komite', $pleno->ketua_komite) }}"
                            required
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Daftar Anggota Hadir Pleno</label>
                        <textarea
                            name="anggota_komite"
                            rows="2"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        >{{ old('anggota_komite', $pleno->anggota_komite) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Catatan Tambahan / Keputusan Khusus Sidang</label>
                        <textarea
                            name="catatan_pleno"
                            rows="2"
                            placeholder="Catatan rekomendasi pleno terkait integritas uji, tindak lanjut asesi BK, atau pengajuan blanko sertifikat..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        >{{ old('catatan_pleno', $pleno->catatan_pleno) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Participant Assessment Matrix & Final Pleno Decision -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden" x-data="{
                setAll(status) {
                    const radios = document.querySelectorAll(`input[type='radio'][value='${status}']`);
                    radios.forEach(r => r.checked = true);
                }
            }">
                <div class="px-6 py-4 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900">2. Penetapan Status Kelulusan Peserta Asesmen</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">Tinjau rekomendasi asesor dan tetapkan status akhir kelulusan untuk penerbitan sertifikat BNSP.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="setAll('kompeten')"
                            class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition"
                        >
                            Tandai Semua Kompeten (K)
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-600">
                        <thead class="bg-zinc-50/75 text-zinc-500 font-semibold border-b border-zinc-200">
                            <tr>
                                <th class="px-6 py-3.5 w-12 text-center">No</th>
                                <th class="px-6 py-3.5">Peserta Asesi</th>
                                <th class="px-6 py-3.5">Asesor Penguji</th>
                                <th class="px-6 py-3.5 text-center">Rekomendasi Asesor (FR-AK-02)</th>
                                <th class="px-6 py-3.5 text-center">Keputusan Pleno</th>
                                <th class="px-6 py-3.5">Nomor Sertifikat BNSP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @forelse ($schedule->applications as $idx => $app)
                                @php
                                    $ak02Rec = $app->ak02 ? $app->ak02->rekomendasi_akhir : null;
                                    $defaultStatus = $app->status_kelulusan ?? ($ak02Rec === 'K' ? 'kompeten' : ($ak02Rec === 'BK' ? 'belum_kompeten' : 'kompeten'));
                                @endphp
                                <tr class="hover:bg-zinc-50/50 transition">
                                    <td class="px-6 py-4 text-center font-semibold text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-zinc-900">{{ $app->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-zinc-500 flex items-center gap-2 mt-0.5">
                                            <span class="font-mono">NIK: {{ $app->user->asesiProfile->nik ?? '-' }}</span>
                                            <span>•</span>
                                            <span>Reg: {{ $app->nomor_permohonan }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-zinc-800 font-medium">{{ $app->asesor->name ?? 'Belum Diplot' }}</div>
                                        <div class="text-[11px] text-zinc-400">
                                            MET: {{ $app->asesor->asesorProfile->no_reg_met ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($ak02Rec === 'K')
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[11px]">
                                                KOMPETEN (K)
                                            </span>
                                        @elseif ($ak02Rec === 'BK')
                                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-md font-bold text-[11px]">
                                                BELUM KOMPETEN (BK)
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-zinc-100 text-zinc-500 rounded text-[11px]">
                                                Belum Ada Nilai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="kelulusan[{{ $app->id }}]"
                                                    value="kompeten"
                                                    {{ $defaultStatus === 'kompeten' ? 'checked' : '' }}
                                                    class="text-blue-600 focus:ring-blue-600 h-4 w-4"
                                                />
                                                <span class="text-xs font-semibold text-emerald-700">Kompeten</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="kelulusan[{{ $app->id }}]"
                                                    value="belum_kompeten"
                                                    {{ $defaultStatus === 'belum_kompeten' ? 'checked' : '' }}
                                                    class="text-rose-600 focus:ring-rose-600 h-4 w-4"
                                                />
                                                <span class="text-xs font-semibold text-rose-700">Belum Kompeten</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($app->no_sertifikat)
                                            <div class="font-mono text-xs font-bold text-blue-700 bg-blue-50/70 border border-blue-200 px-2 py-1 rounded-md inline-block">
                                                {{ $app->no_sertifikat }}
                                            </div>
                                            <div class="text-[10px] text-zinc-400 mt-0.5">
                                                Berlaku s/d: {{ \Carbon\Carbon::parse($app->masa_berlaku_sertifikat)->format('d M Y') }}
                                            </div>
                                        @else
                                            <span class="text-zinc-400 italic text-[11px]">Diterbitkan otomatis saat disahkan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-zinc-400">
                                        Tidak ada peserta yang terdaftar pada jadwal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-between bg-white rounded-2xl border border-zinc-200 shadow-sm p-4">
                <p class="text-xs text-zinc-500">
                    Dengan menekan tombol pengesahan, keputusan rapat pleno akan bersifat final dan nomor sertifikat resmi BNSP akan diterbitkan untuk seluruh peserta yang kompeten.
                </p>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Sahkan Keputusan Pleno & Terbitkan Sertifikat</span>
                    </button>
                </div>
            </div>

        </form>

    </div>
</x-app-layout>
