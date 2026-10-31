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
                    Pantau status permohonan sertifikasi kompetensi, pengisian formulir APL-02, dan hasil uji kompetensi Anda di sini.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <span class="px-3 py-1.5 bg-white/10 rounded-xl text-xs font-medium text-white border border-white/20 backdrop-blur-sm">
                    NIK: {{ $asesi->nik ?? 'Belum diisi' }}
                </span>
            </div>
        </div>

        <!-- Alur Tahapan Sertifikasi (Progress Stepper) -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <h3 class="text-sm font-bold text-zinc-900">Alur Sertifikasi Kompetensi Anda</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Ikuti seluruh tahapan berikut sampai terbitnya sertifikat kompetensi resmi BNSP:</p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-6 gap-3">
                <!-- Step 1 -->
                <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span class="text-[10px] font-bold text-blue-700 uppercase">Selesai</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Pendaftaran</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Akun calon asesi telah aktif dan terdaftar.</p>
                </div>

                <!-- Step 2 -->
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-zinc-300 text-zinc-700 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Fase 4</span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900">Berkas APL-01</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-snug">Pilih skema & unggah berkas syarat.</p>
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
