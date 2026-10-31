<x-app-layout title="Portal Calon Asesi">
    <div class="space-y-6">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/30 text-emerald-200 border border-emerald-400/30 mb-2">
                    Portal Peserta Uji (Asesi)
                </span>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Halo, {{ $asesi->name }}</h2>
                <p class="text-xs sm:text-sm text-emerald-100 mt-1 max-w-xl">
                    Pantau status permohonan sertifikasi kompetensi, verifikasi berkas APL-01, asesmen mandiri, dan jadwal uji kompetensi Anda di sini.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('asesi.applications.create') }}" class="px-4 py-2 bg-white text-emerald-950 font-bold text-xs rounded-xl hover:bg-emerald-50 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Daftar Sertifikasi Baru</span>
                </a>
            </div>
        </div>

        <!-- Latest Application Card (If Any) -->
        @if ($latestApplication)
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-7 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-zinc-100 pb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Permohonan Aktif</span>
                        <span class="font-mono text-xs font-bold text-zinc-900 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200">
                            {{ $latestApplication->nomor_pendaftaran }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $latestApplication->status->badgeClasses() }}">
                            {{ $latestApplication->status->label() }}
                        </span>
                    </div>

                    <div class="text-xs text-zinc-400">
                        Diajukan: {{ $latestApplication->tgl_pengajuan?->format('d M Y H:i') ?? $latestApplication->created_at->format('d M Y') }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-xs text-zinc-400 block">Skema Sertifikasi:</span>
                        <h4 class="text-sm font-bold text-zinc-900 mt-0.5">{{ $latestApplication->scheme->nama_skema }}</h4>
                        <span class="text-xs text-zinc-500 font-mono">Kode: {{ $latestApplication->scheme->kode_skema }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-zinc-400 block">Jadwal & Lokasi Uji (TUK):</span>
                        @if ($latestApplication->schedule)
                            <h4 class="text-sm font-bold text-zinc-900 mt-0.5">{{ $latestApplication->schedule->nama_jadwal }}</h4>
                            <p class="text-xs text-zinc-500">{{ $latestApplication->schedule->formatted_tanggal }} di {{ $latestApplication->schedule->tuk->nama_tuk }}</p>
                        @else
                            <p class="text-xs text-zinc-500 italic mt-0.5">Menunggu penetapan jadwal oleh Admin LSP</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-start md:justify-end gap-2 pt-2 md:pt-0">
                        @if ($latestApplication->status->canEdit())
                            <a href="{{ route('asesi.applications.edit', $latestApplication) }}" class="px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Perbaiki Berkas</span>
                            </a>
                        @endif
                        <a href="{{ route('asesi.applications.show', $latestApplication) }}" class="px-3.5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                            <span>Buka Form APL-01</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                @if ($latestApplication->status === \App\Enums\ApplicationStatus::Revisi)
                    <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl text-xs text-orange-900 flex items-start gap-2">
                        <svg class="w-4 h-4 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <strong>Catatan Revisi dari LSP:</strong>
                            <p class="mt-0.5 text-orange-800">{{ $latestApplication->catatan_revisi ?: 'Terdapat dokumen yang perlu diperbaiki.' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Alur Tahapan Sertifikasi (Progress Stepper) -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <h3 class="text-sm font-bold text-zinc-900">Alur Sertifikasi Kompetensi Anda</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Ikuti seluruh tahapan berikut sampai terbitnya sertifikat kompetensi resmi BNSP:</p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-6 gap-3">
                <!-- Step 1 -->
                <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span class="text-[10px] font-bold text-emerald-700 uppercase">Selesai</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Pendaftaran</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Akun calon asesi telah aktif dan terdaftar.</p>
                </div>

                <!-- Step 2 -->
                <div class="p-3.5 rounded-xl border {{ $latestApplication ? ($latestApplication->status === \App\Enums\ApplicationStatus::Diterima ? 'border-emerald-200 bg-emerald-50/50' : 'border-blue-200 bg-blue-50/50') : 'border-zinc-200 bg-zinc-50' }} flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full {{ $latestApplication ? ($latestApplication->status === \App\Enums\ApplicationStatus::Diterima ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white') : 'bg-zinc-300 text-zinc-700' }} flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span class="text-[10px] font-bold {{ $latestApplication ? ($latestApplication->status === \App\Enums\ApplicationStatus::Diterima ? 'text-emerald-700' : 'text-blue-700') : 'text-zinc-400' }} uppercase">
                            {{ $latestApplication ? $latestApplication->status->label() : 'Belum Isi' }}
                        </span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Berkas APL-01</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">
                        {{ $latestApplication ? ($latestApplication->status === \App\Enums\ApplicationStatus::Diterima ? 'Berkas disetujui & terverifikasi.' : 'Menunggu verifikasi / revisi berkas.') : 'Pilih skema & unggah dokumen persyaratan.' }}
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-zinc-300 text-zinc-700 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Fase 5</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">APL-02 Mandiri</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Isi asesmen mandiri per kriteria unjuk kerja.</p>
                </div>

                <!-- Step 4 -->
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-zinc-300 text-zinc-700 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Fase 6</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Pelaksanaan Uji</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Tanda tangan AK-01 & asesmen di TUK.</p>
                </div>

                <!-- Step 5 -->
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-zinc-300 text-zinc-700 flex items-center justify-center text-xs font-bold shrink-0">5</span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Fase 7</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Rapat Pleno</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Penetapan keputusan kompetensi.</p>
                </div>

                <!-- Step 6 -->
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-zinc-300 text-zinc-700 flex items-center justify-center text-xs font-bold shrink-0">6</span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Sertifikat</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Sertifikat Terbit</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Unduh sertifikat kompetensi BNSP.</p>
                </div>
            </div>
        </div>

        <!-- Profil Cepat Asesi -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-zinc-900">Kelengkapan Data Calon Asesi</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Pastikan data kontak dan NIK Anda valid untuk penerbitan sertifikat resmi:</p>

            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <dt class="text-zinc-500">Nomor Induk Kependudukan (NIK)</dt>
                    <dd class="mt-1 font-semibold text-zinc-900 font-mono text-sm">{{ $asesi->nik ?? 'Belum terisi' }}</dd>
                </div>
                <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <dt class="text-zinc-500">Nomor Handphone / WhatsApp</dt>
                    <dd class="mt-1 font-semibold text-zinc-900 font-mono text-sm">{{ $asesi->phone ?? 'Belum terisi' }}</dd>
                </div>
                <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <dt class="text-zinc-500">Alamat Email Terverifikasi</dt>
                    <dd class="mt-1 font-semibold text-zinc-900 text-sm">{{ $asesi->email }}</dd>
                </div>
            </dl>
        </div>

    </div>
</x-app-layout>
