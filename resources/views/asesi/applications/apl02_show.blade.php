<x-app-layout title="FR-APL-02: {{ $application->nomor_pendaftaran }}">
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
                    <span class="text-zinc-900 font-medium">FR-APL-02</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        FR-APL-02: Asesmen Mandiri
                    </h1>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $apl02->status->badgeClasses() }}">
                        {{ $apl02->status->label() }}
                    </span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                @if ($apl02->status === \App\Enums\Apl02Status::Draft || $apl02->status === \App\Enums\Apl02Status::PerluRevisi)
                    <a href="{{ route('asesi.applications.apl02.create', $application) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Ubah / Lengkapi APL-02</span>
                    </a>
                @endif
                <a href="{{ route('documents.fr-apl-02', $application) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Unduh PDF APL-02</span>
                </a>
            </div>
        </div>

        <!-- Rekomendasi Asesor Alert -->
        @if ($apl02->rekomendasi_asesor)
            <div class="p-5 rounded-2xl border {{ $apl02->rekomendasi_asesor === 'Asesmen dapat dilanjutkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }} space-y-1">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Rekomendasi Asesor Penguji: {{ $apl02->rekomendasi_asesor }}</h3>
                </div>
                @if ($apl02->catatan_asesor)
                    <p class="text-xs pl-7 mt-1">{{ $apl02->catatan_asesor }}</p>
                @endif
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- BLANGKO RESMI FR-APL-02 KEPATUHAN STANDAR BNSP                           --}}
        {{-- ========================================================================= --}}
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-6">
            <!-- Kop Form -->
            <div class="border-b-2 border-zinc-900 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                    <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase">FR-APL-02. ASESMEN MANDIRI</h2>
                </div>
                <div class="text-right text-xs">
                    <div class="font-mono font-bold text-zinc-900">{{ $application->nomor_pendaftaran }}</div>
                    <div class="text-zinc-500">Skema: {{ $application->scheme->nama_skema }}</div>
                </div>
            </div>

            <!-- Tabel Identitas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs bg-zinc-50 p-4 rounded-xl border border-zinc-200">
                <div>
                    <span class="text-zinc-400 block">Nama Peserta:</span>
                    <span class="font-bold text-zinc-900">{{ $application->user->name }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Nama Asesor:</span>
                    <span class="font-bold text-zinc-900">{{ $apl02->asesor?->name ?? ($application->asesor?->name ?? 'Belum Ditugaskan') }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">TUK:</span>
                    <span class="font-bold text-zinc-900">{{ $application->schedule?->tuk->nama_tuk ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Tanggal Peninjauan:</span>
                    <span class="font-bold text-zinc-900">{{ $apl02->tgl_ttd_asesor?->format('d M Y') ?? 'Menunggu Verifikasi' }}</span>
                </div>
            </div>

            <!-- Tabel Daftar KUK -->
            <div class="space-y-6">
                @foreach ($application->scheme->competencyUnits as $uIdx => $unit)
                    <div class="border border-zinc-200 rounded-xl overflow-hidden">
                        <div class="p-3 bg-zinc-100 border-b border-zinc-200">
                            <span class="text-[10px] font-bold text-blue-700 uppercase font-mono">Unit {{ $uIdx + 1 }}: {{ $unit->kode_unit }}</span>
                            <h3 class="text-xs font-bold text-zinc-900 mt-0.5">{{ $unit->judul_unit }}</h3>
                        </div>

                        <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                            <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                                <tr>
                                    <th class="px-4 py-2.5 w-16 text-center">KUK</th>
                                    <th class="px-4 py-2.5">Kriteria Unjuk Kerja</th>
                                    <th class="px-4 py-2.5 w-20 text-center">Mandiri</th>
                                    <th class="px-4 py-2.5 w-48">Bukti Pendukung</th>
                                    <th class="px-4 py-2.5 w-32 text-center">Tinjauan Asesor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 bg-white">
                                @foreach ($unit->elements as $elem)
                                    <tr class="bg-zinc-50/70 font-semibold text-zinc-700">
                                        <td colspan="5" class="px-4 py-2 text-[11px]">
                                            Elemen: {{ $elem->nama_elemen }}
                                        </td>
                                    </tr>
                                    @foreach ($elem->criterias as $kuk)
                                        @php
                                            $item = $itemsByKuk->get($kuk->id);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2.5 font-mono font-bold text-blue-700 text-center">{{ $kuk->nomor_kuk }}</td>
                                            <td class="px-4 py-2.5 text-zinc-800">{{ $kuk->pernyataan }}</td>
                                            <td class="px-4 py-2.5 text-center font-bold">
                                                @if ($item && $item->penilaian_mandiri === 'K')
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800">K</span>
                                                @elseif($item)
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-rose-100 text-rose-800">BK</span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5 text-zinc-600 text-[11px]">{{ $item?->bukti_pendukung ?? '-' }}</td>
                                            <td class="px-4 py-2.5 text-center">
                                                @if ($item && $apl02->tgl_ttd_asesor)
                                                    <div class="flex items-center justify-center gap-1 text-[10px] font-mono font-bold">
                                                        <span class="{{ $item->is_valid ? 'text-emerald-700' : 'text-zinc-300' }}">V</span>
                                                        <span class="{{ $item->is_asli ? 'text-emerald-700' : 'text-zinc-300' }}">A</span>
                                                        <span class="{{ $item->is_terkini ? 'text-emerald-700' : 'text-zinc-300' }}">T</span>
                                                        <span class="{{ $item->is_memadai ? 'text-emerald-700' : 'text-zinc-300' }}">M</span>
                                                    </div>
                                                @else
                                                    <span class="text-zinc-400 italic text-[11px]">Belum ditinjau</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <!-- Bagian Tanda Tangan -->
            <div class="border-t border-zinc-200 pt-6 grid grid-cols-1 md:grid-cols-2 gap-8 text-xs text-center">
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-2">
                    <span class="text-zinc-500">Asesi Pemohon,</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($apl02->ttd_asesi)
                            <img src="{{ $apl02->ttd_asesi }}" alt="TTD Asesi" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Tertanda Digital]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $application->user->name }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $apl02->tgl_ttd_asesi?->format('d M Y') ?? '-' }}</div>
                </div>

                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-2">
                    <span class="text-zinc-500">Asesor Penguji,</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($apl02->ttd_asesor)
                            <img src="{{ $apl02->ttd_asesor }}" alt="TTD Asesor" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Menunggu Verifikasi Asesor]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $apl02->asesor?->name ?? 'Asesor Penguji' }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $apl02->tgl_ttd_asesor?->format('d M Y') ?? '-' }}</div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
