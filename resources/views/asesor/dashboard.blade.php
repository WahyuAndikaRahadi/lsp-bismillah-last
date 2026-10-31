<x-app-layout title="Portal Asesor Kompetensi">
    <div class="space-y-6">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-blue-800 to-sky-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/30 text-blue-200 border border-blue-400/30 mb-2">
                    Portal Asesor Kompetensi
                </span>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ $asesor->name }}</h2>
                <p class="text-xs sm:text-sm text-blue-100 mt-1 max-w-xl">
                    Portal kerja asesor untuk verifikasi berkas asesmen mandiri (FR-APL-02), lembar observasi uji (FR-AK-02), dan rekomendasi kelulusan asesi.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <span class="px-3 py-1.5 bg-white/10 rounded-xl text-xs font-medium text-white border border-white/20 backdrop-blur-sm">
                    Status Lisensi: Terdaftar
                </span>
            </div>
        </div>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm">
                <span class="text-xs font-medium text-zinc-500">Penugasan Aktif</span>
                <p class="text-2xl font-bold text-zinc-900 mt-1">0</p>
                <span class="text-[11px] text-zinc-400 mt-1 block">Jadwal asesmen berjalan</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm">
                <span class="text-xs font-medium text-zinc-500">Asesi Belum Dinilai</span>
                <p class="text-2xl font-bold text-zinc-900 mt-1">0</p>
                <span class="text-[11px] text-zinc-400 mt-1 block">Menunggu verifikasi APL-02 & AK-02</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm">
                <span class="text-xs font-medium text-zinc-500">Asesmen Selesai</span>
                <p class="text-2xl font-bold text-zinc-900 mt-1">0</p>
                <span class="text-[11px] text-zinc-400 mt-1 block">Laporan AK-05 diterbitkan</span>
            </div>
        </div>

        <!-- Information Card -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-zinc-900">Alur Kerja Asesor (Standar BNSP)</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Tahapan kerja digital yang akan Anda lalui pada setiap penugasan asesmen:</p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold mb-2">1</span>
                    <h4 class="text-xs font-bold text-zinc-900">Surat Tugas & MAPA</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">Menerima surat tugas resmi dari LSP dan merencanakan aktivitas asesmen.</p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold mb-2">2</span>
                    <h4 class="text-xs font-bold text-zinc-900">Verifikasi APL-02</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">Meninjau asesmen mandiri dan bukti portofolio asesi berdasarkan kaidah V-A-T-M.</p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold mb-2">3</span>
                    <h4 class="text-xs font-bold text-zinc-900">AK-01 & Penilaian AK-02</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">Penandatanganan klausul kerahasiaan dan pengisian lembar rekaman observasi uji.</p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold mb-2">4</span>
                    <h4 class="text-xs font-bold text-zinc-900">Rekomendasi AK-05</h4>
                    <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">Memberikan rekomendasi Kompeten (K) / Belum Kompeten (BK) dan laporan ke LSP.</p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
