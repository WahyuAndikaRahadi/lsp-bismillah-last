<x-app-layout title="FR-AK-01: Persetujuan Asesmen & Kerahasiaan">
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
                <span class="text-zinc-900 font-medium">FR-AK-01</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                FR-AK-01: Persetujuan Asesmen dan Kerahasiaan
            </h1>
            <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                Konfirmasi persetujuan waktu pelaksanaan uji kompetensi dan klausul kerahasiaan materi uji.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-6 font-sans">
            <!-- Kop Form -->
            <div class="border-b-2 border-zinc-900 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                    <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase">FR-AK-01. PERSETUJUAN ASESMEN DAN KERAHASIAAN</h2>
                </div>
                <div class="text-right text-xs">
                    <div class="font-mono font-bold text-zinc-900">{{ $application->nomor_pendaftaran }}</div>
                    <div class="text-zinc-500">Skema: {{ $application->scheme->nama_skema }}</div>
                </div>
            </div>

            <!-- Tabel Informasi Pelaksanaan Uji -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs bg-zinc-50 p-4 rounded-xl border border-zinc-200">
                <div>
                    <span class="text-zinc-400 block">Nama Peserta (Asesi):</span>
                    <span class="font-bold text-zinc-900">{{ $application->user->name }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Nama Asesor Penguji:</span>
                    <span class="font-bold text-zinc-900">{{ $application->asesor?->name ?? 'Asesor Penguji LSP' }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Tempat Uji Kompetensi (TUK):</span>
                    <span class="font-bold text-zinc-900">{{ $ak01->tuk_nama }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Hari / Tanggal Uji:</span>
                    <span class="font-bold text-zinc-900">{{ $ak01->hari_tanggal->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Waktu / Durasi:</span>
                    <span class="font-bold text-zinc-900">{{ $ak01->waktu }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block">Status Dokumen:</span>
                    <span class="font-bold {{ $ak01->isFullySigned() ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $ak01->isFullySigned() ? 'Telah Ditandatangani Kedua Pihak' : 'Menunggu Pengesahan Lengkap' }}
                    </span>
                </div>
            </div>

            <!-- Pernyataan Kerahasiaan & Hak Kewajiban Sesuai Standar BNSP -->
            <div class="space-y-4 text-xs leading-relaxed text-zinc-700">
                <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 space-y-2">
                    <h3 class="font-bold text-zinc-900 uppercase tracking-wide">Pernyataan Asesor:</h3>
                    <p>
                        Saya menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai asesor serta tidak akan membocorkan setiap materi uji kompetensi kepada pihak mana pun, kecuali atas izin tertulis dari Lembaga Sertifikasi Profesi (LSP). Saya akan bersikap adil, objektif, dan tidak diskriminatif selama memandu uji kompetensi ini.
                    </p>
                </div>

                <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 space-y-2">
                    <h3 class="font-bold text-zinc-900 uppercase tracking-wide">Pernyataan Peserta (Asesi):</h3>
                    <p>
                        Saya menyatakan setuju untuk mengikuti seluruh proses asesmen dengan mematuhi ketentuan yang telah ditetapkan. Saya telah diberikan hak penjelasan mengenai prosedur uji kompetensi, instrumen penilaian yang digunakan, serta hak untuk mengajukan banding terhadap keputusan hasil asesmen.
                    </p>
                </div>
            </div>

            <!-- Area Tanda Tangan Digital -->
            <div class="border-t border-zinc-200 pt-6 grid grid-cols-1 md:grid-cols-2 gap-8 text-xs text-center">
                <!-- Sisi Asesi -->
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-3">
                    <span class="text-zinc-500 block">Tanda Tangan Asesi,</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($ak01->ttd_asesi)
                            <img src="{{ $ak01->ttd_asesi }}" alt="TTD Asesi" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Belum Bertanda Tangan]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $application->user->name }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $ak01->tgl_ttd_asesi?->format('d M Y H:i') ?? '-' }}</div>

                    @if (!$ak01->ttd_asesi)
                        <form action="{{ route('asesi.applications.ak01.sign', $application) }}" method="POST" class="pt-2">
                            @csrf
                            <input type="hidden" name="setuju" value="1">
                            <button type="submit" class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                                Tandatangani Persetujuan
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Sisi Asesor -->
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-3">
                    <span class="text-zinc-500 block">Tanda Tangan Asesor,</span>
                    <div class="h-24 flex items-center justify-center">
                        @if ($ak01->ttd_asesor)
                            <img src="{{ $ak01->ttd_asesor }}" alt="TTD Asesor" class="max-h-20 object-contain">
                        @else
                            <span class="text-zinc-400 italic">[Menunggu Tanda Tangan Asesor]</span>
                        @endif
                    </div>
                    <div class="font-bold text-zinc-900 border-t border-zinc-200 pt-2">{{ $application->asesor?->name ?? 'Asesor Penguji' }}</div>
                    <div class="text-[10px] text-zinc-400">Tgl: {{ $ak01->tgl_ttd_asesor?->format('d M Y H:i') ?? '-' }}</div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
