<x-app-layout title="FR-APL-01: {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6">

        <!-- Top Navigation & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesi</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium font-mono">{{ $application->nomor_pendaftaran }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        {{ $application->nomor_pendaftaran }}
                    </h1>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $application->status->badgeClasses() }}">
                        {{ $application->status->label() }}
                    </span>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                @if ($application->status === \App\Enums\ApplicationStatus::Diterima)
                    <a href="{{ route('asesi.applications.apl02.create', $application) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                        <span>FR-APL-02 (Mandiri)</span>
                    </a>
                @endif
                @if ($application->status->canEdit())
                    <a href="{{ route('asesi.applications.edit', $application) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Perbaiki Berkas Revisi</span>
                    </a>
                @endif
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Form</span>
                </button>
            </div>
        </div>

        <!-- Revisi Alert Banner -->
        @if ($application->status === \App\Enums\ApplicationStatus::Revisi)
            <div class="p-5 bg-orange-50 border border-orange-200 rounded-2xl text-orange-900 space-y-2">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-sm font-bold">Permohonan Memerlukan Perbaikan Dokumen</h3>
                </div>
                <p class="text-xs text-orange-800 leading-relaxed pl-7">
                    {{ $application->catatan_revisi ?: 'Admin LSP menandai beberapa berkas belum memenuhi persyaratan. Silakan periksa kolom catatan di Bagian 3 dan unggah ulang berkas yang diminta.' }}
                </p>
                <div class="pl-7 pt-1">
                    <a href="{{ route('asesi.applications.edit', $application) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-orange-600 text-white text-xs font-semibold hover:bg-orange-700 transition">
                        <span>Buka Form Perbaikan Sekarang</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Diterima Alert Banner -->
        @if ($application->status === \App\Enums\ApplicationStatus::Diterima)
            <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 space-y-2">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Permohonan Telah Diterima (Berkas Lengkap & Memenuhi Syarat)</h3>
                </div>
                <p class="text-xs text-emerald-800 leading-relaxed pl-7">
                    Selamat! Permohonan sertifikasi Anda telah disetujui oleh Tim Verifikator LSP.
                    @if ($application->schedule)
                        Anda telah dijadwalkan pada event: <strong>{{ $application->schedule->nama_jadwal }}</strong> (Tgl Asesmen: {{ $application->schedule->tgl_asesmen->format('d M Y') }} di {{ $application->schedule->tuk->nama_tuk }}).
                    @else
                        Jadwal asesmen sedang diproses oleh admin LSP. Anda akan menerima notifikasi segera setelah jadwal ditetapkan.
                    @endif
                </p>
                <div class="pl-7 pt-2">
                    <a href="{{ route('asesi.applications.apl02.create', $application) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                        <span>Lanjut ke Formulir Asesmen Mandiri (FR-APL-02)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- BLANGKO RESMI FR-APL-01 KEPATUHAN STANDAR BNSP                           --}}
        {{-- ========================================================================= --}}
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-10 space-y-8 font-sans">
            
            <!-- Dokumen Header / Kop Standar -->
            <div class="border-b-2 border-zinc-900 pb-5">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold tracking-widest text-zinc-500 uppercase">Badan Nasional Sertifikasi Profesi (BNSP)</span>
                        <h2 class="text-base sm:text-lg font-black text-zinc-900 uppercase tracking-tight">
                            FR-APL-01. FORMULIR PERMOHONAN SERTIFIKASI KOMPETENSI
                        </h2>
                    </div>
                    <div class="text-right text-xs space-y-1">
                        <div class="font-mono text-zinc-900 font-bold">No. Reg: {{ $application->nomor_pendaftaran }}</div>
                        <div class="text-zinc-500">Tanggal: {{ $application->tgl_pengajuan?->format('d/m/Y') ?? date('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: RINCIAN DATA PEMOHON SERTIFIKASI -->
            <div class="space-y-4">
                <div class="bg-zinc-100 px-4 py-2 rounded-lg font-bold text-xs text-zinc-900 uppercase tracking-wider flex items-center justify-between">
                    <span>Bagian 1 : Rincian Data Pemohon Sertifikasi</span>
                </div>

                <!-- A. Data Pribadi -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">a. Data Pribadi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-xs border border-zinc-200 rounded-xl p-4 bg-zinc-50/30">
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Nama Lengkap</span>
                            <span class="font-bold text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->nama_lengkap ?: $application->user->name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">No. KTP / NIK</span>
                            <span class="font-mono font-bold text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->nik ?: $application->user->nik }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Tempat / Tgl Lahir</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->tempat_lahir ?? '-' }}, {{ $profile?->tanggal_lahir?->format('d F Y') ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Jenis Kelamin</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Kebangsaan</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->kebangsaan ?? 'Indonesia' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Pendidikan Terakhir</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->pendidikan_terakhir ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">No. Telepon / HP</span>
                            <span class="font-mono text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->telepon ?: $application->user->phone }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Email Pribadi</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $application->user->email }}</span>
                        </div>
                        <div class="md:col-span-2 flex flex-col sm:flex-row justify-between py-1">
                            <span class="text-zinc-500 w-44 shrink-0">Alamat Rumah</span>
                            <span class="text-zinc-900 flex-1 text-left">{{ $profile?->alamat_lengkap ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- B. Data Pekerjaan Sekarang -->
                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">b. Data Pekerjaan Sekarang</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-xs border border-zinc-200 rounded-xl p-4 bg-zinc-50/30">
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Profesi / Status</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->pekerjaan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Nama Perusahaan/Lembaga</span>
                            <span class="font-bold text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->nama_perusahaan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Jabatan</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->jabatan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-zinc-100">
                            <span class="text-zinc-500 w-44">Telp / Email Kantor</span>
                            <span class="text-zinc-900 flex-1 text-right sm:text-left">{{ $profile?->telp_kantor ?? '-' }} / {{ $profile?->email_kantor ?? '-' }}</span>
                        </div>
                        <div class="md:col-span-2 flex flex-col sm:flex-row justify-between py-1">
                            <span class="text-zinc-500 w-44 shrink-0">Alamat Kantor</span>
                            <span class="text-zinc-900 flex-1 text-left">{{ $profile?->alamat_kantor ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: DATA SERTIFIKASI -->
            <div class="space-y-4">
                <div class="bg-zinc-100 px-4 py-2 rounded-lg font-bold text-xs text-zinc-900 uppercase tracking-wider flex items-center justify-between">
                    <span>Bagian 2 : Data Sertifikasi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border border-zinc-200 rounded-xl p-4 bg-zinc-50/30">
                    <div>
                        <span class="text-zinc-500 block mb-0.5">Judul Skema Sertifikasi:</span>
                        <span class="font-bold text-zinc-900 text-sm">{{ $application->scheme->nama_skema }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block mb-0.5">Nomor Skema Sertifikasi:</span>
                        <span class="font-mono font-bold text-zinc-900 text-sm">{{ $application->scheme->kode_skema }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block mb-0.5">Tujuan Asesmen:</span>
                        <span class="font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200 inline-block">
                            {{ $application->tujuan_asesmen }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block mb-0.5">Jadwal Asesmen (TUK):</span>
                        @if ($application->schedule)
                            <span class="font-semibold text-zinc-900">{{ $application->schedule->nama_jadwal }} &bull; {{ $application->schedule->formatted_tanggal }} ({{ $application->schedule->tuk->nama_tuk }})</span>
                        @else
                            <span class="text-zinc-400 italic">Belum di-plot oleh Admin</span>
                        @endif
                    </div>
                </div>

                <!-- Tabel Unit Kompetensi Kemasan -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Daftar Unit Kompetensi Sesuai Kemasan:</h4>
                    <div class="border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                            <thead class="bg-zinc-100 text-zinc-600 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="px-4 py-2.5 w-12 text-center">No</th>
                                    <th class="px-4 py-2.5 w-48">Kode Unit</th>
                                    <th class="px-4 py-2.5">Judul Unit Kompetensi</th>
                                    <th class="px-4 py-2.5 w-28 text-center">Standar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 bg-white">
                                @forelse ($application->scheme->competencyUnits as $idx => $unit)
                                    <tr>
                                        <td class="px-4 py-2.5 text-center text-zinc-500 font-medium">{{ $idx + 1 }}</td>
                                        <td class="px-4 py-2.5 font-mono font-bold text-zinc-900">{{ $unit->kode_unit }}</td>
                                        <td class="px-4 py-2.5 text-zinc-800 font-medium">{{ $unit->judul_unit }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 text-zinc-700">
                                                {{ $unit->jenis_standar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-zinc-500 italic">
                                            Tidak ada unit kompetensi pada skema ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: BUKTI KELENGKAPAN PEMOHON -->
            <div class="space-y-4">
                <div class="bg-zinc-100 px-4 py-2 rounded-lg font-bold text-xs text-zinc-900 uppercase tracking-wider flex items-center justify-between">
                    <span>Bagian 3 : Bukti Kelengkapan Pemohon</span>
                </div>

                <div class="border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                        <thead class="bg-zinc-100 text-zinc-600 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="px-4 py-2.5 w-12 text-center">No</th>
                                <th class="px-4 py-2.5">Nama Dokumen Persyaratan</th>
                                <th class="px-4 py-2.5 w-40">Status Unggahan</th>
                                <th class="px-4 py-2.5 w-36 text-center">Verifikasi LSP</th>
                                <th class="px-4 py-2.5">Catatan Verifikator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @forelse ($application->documents as $idx => $doc)
                                <tr class="{{ $doc->status_verifikasi === \App\Enums\DocumentVerificationStatus::TidakMemenuhi ? 'bg-rose-50/30' : '' }}">
                                    <td class="px-4 py-3 text-center text-zinc-500 font-medium">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-zinc-900">{{ $doc->nama_dokumen }}</div>
                                        <div class="text-[11px] text-zinc-400 mt-0.5">
                                            {{ $doc->file_name }} &bull; {{ $doc->formatted_size }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ $doc->file_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold border border-blue-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Berkas</span>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $doc->status_verifikasi->badgeClasses() }}">
                                            {{ $doc->status_verifikasi->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        @if($doc->catatan)
                                            <span class="text-rose-700 font-medium">{{ $doc->catatan }}</span>
                                        @else
                                            <span class="text-zinc-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-zinc-500 italic">
                                        Belum ada dokumen yang diunggah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BAGIAN PENGESAHAN & REKOMENDASI -->
            <div class="space-y-4 border-t border-zinc-200 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs">
                    
                    <!-- Sisi Asesi Pemohon -->
                    <div class="border border-zinc-200 rounded-xl p-5 bg-zinc-50/40 text-center space-y-3">
                        <span class="text-zinc-500 block">Pemohon Sertifikasi,</span>
                        <div class="h-28 flex items-center justify-center">
                            @if ($application->ttd_asesi)
                                <img src="{{ $application->ttd_asesi }}" alt="TTD Asesi" class="max-h-24 max-w-full object-contain">
                            @else
                                <span class="text-zinc-400 italic">[Tertanda Digital Saat Pengajuan]</span>
                            @endif
                        </div>
                        <div class="border-t border-zinc-200 pt-2 font-bold text-zinc-900">
                            {{ $profile?->nama_lengkap ?: $application->user->name }}
                        </div>
                        <div class="text-[11px] text-zinc-500">
                            Tgl: {{ $application->tgl_ttd_asesi?->format('d F Y') ?? '-' }}
                        </div>
                    </div>

                    <!-- Sisi Verifikator LSP -->
                    <div class="border border-zinc-200 rounded-xl p-5 bg-zinc-50/40 text-center space-y-3">
                        <span class="text-zinc-500 block">Admin / Verifikator LSP,</span>
                        <div class="h-28 flex items-center justify-center">
                            @if ($application->admin_verified_at)
                                <div class="space-y-1 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $application->status->badgeClasses() }}">
                                        {{ $application->rekomendasi_admin ?: $application->status->label() }}
                                    </span>
                                    <div class="text-[10px] text-zinc-400">Terverifikasi Sistem LSP</div>
                                </div>
                            @else
                                <span class="text-zinc-400 italic">[Menunggu Verifikasi Admin LSP]</span>
                            @endif
                        </div>
                        <div class="border-t border-zinc-200 pt-2 font-bold text-zinc-900">
                            {{ $application->admin?->name ?? 'Admin Verifikator LSP' }}
                        </div>
                        <div class="text-[11px] text-zinc-500">
                            Tgl: {{ $application->admin_verified_at?->format('d F Y') ?? 'Belum diverifikasi' }}
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-app-layout>
