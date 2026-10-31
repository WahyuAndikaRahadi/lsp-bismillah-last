<x-app-layout title="Peserta & Penilaian Jadwal {{ $schedule->schedule_code }}">
    <div class="space-y-6">

        <!-- Top Header Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('asesor.schedules.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-800 transition flex items-center gap-1.5">
                <span>&larr;</span> Kembali ke Daftar Jadwal
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('asesor.ak05.report', $schedule) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 transition">
                    Laporan Asesor (FR-AK-05)
                </a>
                <a href="{{ route('documents.berita-acara-asesmen', $schedule) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 transition">
                    Cetak Berita Acara
                </a>
            </div>
        </div>

        <!-- Schedule Overview Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-[11px] text-zinc-400 uppercase font-semibold">Kode Jadwal</span>
                    <p class="font-mono text-base font-bold text-zinc-900 dark:text-white mt-0.5">{{ $schedule->schedule_code }}</p>
                    <span class="text-xs text-zinc-500 block mt-1">Event: {{ $schedule->event?->title }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-zinc-400 uppercase font-semibold">Skema Sertifikasi</span>
                    <p class="text-xs font-bold text-zinc-900 dark:text-white mt-0.5">{{ $schedule->scheme?->name }}</p>
                    <span class="font-mono text-[11px] text-zinc-500 block">{{ $schedule->scheme?->code }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-zinc-400 uppercase font-semibold">Tempat Uji Kompetensi (TUK)</span>
                    <p class="text-xs font-bold text-zinc-900 dark:text-white mt-0.5">{{ $schedule->tuk?->name }}</p>
                    <span class="text-[11px] text-zinc-500 block">{{ $schedule->tuk?->address ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-zinc-400 uppercase font-semibold">Tanggal Asesmen</span>
                    <p class="text-xs font-bold text-zinc-900 dark:text-white mt-0.5">{{ $schedule->assessment_date?->translatedFormat('d F Y') }}</p>
                    <span class="text-[11px] text-zinc-500 block">Total Peserta: {{ $schedule->applications->count() }} Asesi</span>
                </div>
            </div>
        </div>

        <!-- Participants Table (Cockpit Penilaian Asesor) -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-white">Daftar Peserta Uji Kompetensi</h2>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Lakukan verifikasi APL-02, persetujuan AK-01, dan penilaian observasi AK-02 per asesi.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Asesi & NIK</th>
                            <th class="p-3.5 text-center">Berkas APL-01</th>
                            <th class="p-3.5 text-center">Mandiri APL-02</th>
                            <th class="p-3.5 text-center">Kerahasiaan AK-01</th>
                            <th class="p-3.5 text-center">Observasi AK-02</th>
                            <th class="p-3.5 text-right">Aksi Penilaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($schedule->applications as $idx => $app)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="p-3.5">
                                    <strong class="text-zinc-900 dark:text-zinc-100 block">{{ $app->asesi?->user?->name }}</strong>
                                    <span class="font-mono text-[11px] text-zinc-500 block">NIK: {{ $app->asesi?->nik }}</span>
                                    <span class="text-[10px] text-zinc-400 block">{{ $app->asesi?->organization ?? '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $app->documents->count() }} Dokumen Lengkap
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($app->apl02Assessment)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Diverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Review
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($app->ak01Agreement && $app->ak01Agreement->signed_by_asesor_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Ditandatangani
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('asesor.ak01.sign', $app) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="notes" value="Persetujuan asesmen terkonfirmasi">
                                            <button type="submit" class="px-2 py-1 rounded text-[10px] font-semibold bg-zinc-800 text-white hover:bg-zinc-700 transition">
                                                Tandatangan AK-01
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($app->ak02Assessment)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $app->ak02Assessment->recommendation === 'kompeten' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ strtoupper($app->ak02Assessment->recommendation) }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-zinc-400">Belum diuji</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                                    @if ($app->apl02Assessment)
                                        <a href="{{ route('asesor.apl02.review', $app->apl02Assessment) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 transition">
                                            APL-02
                                        </a>
                                    @endif
                                    <a href="{{ route('asesor.ak02.form', $app) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 active:scale-[0.98] transition">
                                        Penilaian AK-02
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-zinc-500">
                                    Belum ada peserta yang dijadwalkan pada sesi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
