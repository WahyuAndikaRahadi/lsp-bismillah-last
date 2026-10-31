<x-app-layout title="Permohonan Sertifikasi Saya (FR-APL-01)">
    <div class="space-y-6">

        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesi</span>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">Permohonan Sertifikasi (APL-01)</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Permohonan Sertifikasi Saya</h1>
                <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                    Kelola formulir permohonan sertifikasi kompetensi (FR-APL-01), pemantauan verifikasi berkas, dan jadwal asesmen.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('asesi.applications.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Daftar Sertifikasi Baru</span>
                </a>
            </div>
        </div>

        @if($applications->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-dashed border-zinc-300 p-12 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-zinc-900">Belum Ada Permohonan Sertifikasi</h3>
                <p class="text-xs sm:text-sm text-zinc-500 mt-1 max-w-md mx-auto">
                    Anda belum mengajukan permohonan sertifikasi kompetensi. Silakan pilih skema sertifikasi yang tersedia dan isi formulir FR-APL-01.
                </p>
                <div class="mt-6">
                    <a href="{{ route('asesi.applications.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Mulai Pendaftaran Sekarang</span>
                    </a>
                </div>
            </div>
        @else
            <!-- List Permohonan Cards -->
            <div class="grid grid-cols-1 gap-4">
                @foreach ($applications as $app)
                    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-5 sm:p-6 hover:border-zinc-300 transition flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div class="space-y-3 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-bold text-zinc-900 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                                    {{ $app->nomor_pendaftaran }}
                                </span>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $app->status->badgeClasses() }}">
                                    {{ $app->status->label() }}
                                </span>
                                <span class="text-xs text-zinc-400">
                                    Diajukan: {{ $app->tgl_pengajuan?->format('d M Y H:i') ?? $app->created_at->format('d M Y') }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-zinc-900 hover:text-blue-600 transition">
                                    <a href="{{ route('asesi.applications.show', $app) }}">
                                        {{ $app->scheme->nama_skema }}
                                    </a>
                                </h3>
                                <p class="text-xs text-zinc-500 mt-0.5">
                                    Kode Skema: <span class="font-medium text-zinc-700">{{ $app->scheme->kode_skema }}</span> &bull; 
                                    Tujuan: <span class="font-medium text-zinc-700">{{ $app->tujuan_asesmen }}</span>
                                </p>
                            </div>

                            <!-- Jadwal & TUK Info -->
                            <div class="flex flex-wrap items-center gap-4 text-xs text-zinc-600 pt-1">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @if ($app->schedule)
                                        <span>Jadwal: <strong>{{ $app->schedule->nama_jadwal }}</strong> ({{ $app->schedule->formatted_tanggal }})</span>
                                    @else
                                        <span class="italic text-zinc-400">Jadwal: Menunggu plotting admin LSP</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Dokumen Terunggah: <strong>{{ $app->documents->count() }} berkas</strong></span>
                                </div>
                            </div>

                            <!-- Revisi Banner if Status is Revisi -->
                            @if ($app->status === \App\Enums\ApplicationStatus::Revisi)
                                <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl text-xs text-orange-900 flex items-start gap-2.5">
                                    <svg class="w-4 h-4 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <span class="font-bold">Catatan Perbaikan dari Verifikator:</span>
                                        <p class="mt-0.5">{{ $app->catatan_revisi ?: 'Mohon periksa dan unggah ulang berkas yang belum memenuhi ketentuan.' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="shrink-0 flex sm:flex-col md:flex-row items-center gap-2 border-t md:border-t-0 pt-3 md:pt-0 border-zinc-100">
                            @if ($app->status->canEdit())
                                <a href="{{ route('asesi.applications.edit', $app) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Perbaiki Berkas</span>
                                </a>
                            @endif

                            <a href="{{ route('asesi.applications.show', $app) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-semibold shadow-sm transition">
                                <span>Lihat Detail FR-APL-01</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $applications->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
