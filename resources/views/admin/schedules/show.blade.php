<x-app-layout title="Kelola Jadwal Asesmen">
    <div class="space-y-6">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.schedules.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Jadwal</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">{{ $schedule->nama_jadwal }}</h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-zinc-500">
                    <span class="font-mono font-semibold text-zinc-700">{{ $schedule->kode_jadwal }}</span>
                    <span>•</span>
                    <span class="font-medium text-zinc-800">{{ $schedule->scheme->nama_skema ?? '-' }}</span>
                    <span>•</span>
                    <span>{{ $schedule->tuk->nama_tuk ?? '-' }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('documents.berita-acara-asesmen', $schedule) }}"
                    target="_blank"
                    class="px-3 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-300 shadow-xs transition"
                >
                    Cetak BA Uji
                </a>
                <a
                    href="{{ route('documents.daftar-hadir-asesmen', $schedule) }}"
                    target="_blank"
                    class="px-3 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-300 shadow-xs transition"
                >
                    Cetak Presensi
                </a>
                <a
                    href="{{ route('admin.pleno.show', $schedule) }}"
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition"
                >
                    Sidang Pleno
                </a>
                <a
                    href="{{ route('admin.schedules.edit', $schedule) }}"
                    class="px-3.5 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-300 shadow-xs transition"
                >
                    Ubah Data Jadwal
                </a>

                <!-- Quick Status Transition Form -->
                <form method="POST" action="{{ route('admin.schedules.update-status', $schedule) }}" class="flex items-center gap-1.5">
                    @csrf
                    @method('PATCH')
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="px-3 py-2 text-xs font-semibold rounded-xl border border-zinc-300 bg-white text-zinc-800 shadow-xs focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer"
                    >
                        @foreach (\App\Enums\ScheduleStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ $schedule->status === $st ? 'selected' : '' }}>
                                Status: {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Lifecycle Stepper Indicator -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-4">Siklus Tahapan Pelaksanaan Asesmen</h3>

            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                @php
                    $steps = [
                        ['id' => 'direncanakan', 'label' => '1. Direncanakan'],
                        ['id' => 'pendaftaran', 'label' => '2. Pendaftaran'],
                        ['id' => 'pra_asesmen', 'label' => '3. Pra-Asesmen'],
                        ['id' => 'asesmen', 'label' => '4. Pelaksanaan Uji'],
                        ['id' => 'pleno', 'label' => '5. Rapat Pleno'],
                        ['id' => 'selesai', 'label' => '6. Selesai'],
                    ];
                    $currentStatus = $schedule->status->value;
                    $statusOrder = array_column($steps, 'id');
                    $currentIndex = array_search($currentStatus, $statusOrder);
                @endphp

                @foreach ($steps as $index => $step)
                    @php
                        $isCurrent = $step['id'] === $currentStatus;
                        $isPassed = $currentIndex !== false && $index < $currentIndex;
                    @endphp
                    <div class="p-3 rounded-xl border text-center transition {{ $isCurrent ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : ($isPassed ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-zinc-50 text-zinc-500 border-zinc-200/80') }}">
                        <span class="text-[11px] font-bold block">{{ $step['label'] }}</span>
                        @if ($isCurrent)
                            <span class="text-[9px] font-semibold text-blue-100 uppercase tracking-wider block mt-0.5">Sedang Berjalan</span>
                        @elseif ($isPassed)
                            <span class="text-[9px] font-semibold text-emerald-600 uppercase tracking-wider block mt-0.5">Selesai</span>
                        @else
                            <span class="text-[9px] text-zinc-400 uppercase tracking-wider block mt-0.5">Menunggu</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Info Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Event & Waktu -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-3">
                <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                    Waktu & Program
                </h4>
                <div>
                    <span class="text-zinc-500 text-xs block">Event Acuan:</span>
                    <span class="font-bold text-zinc-900 text-xs block mt-0.5">{{ $schedule->event->nama_event }}</span>
                    <span class="text-[11px] text-blue-600 font-semibold">{{ $schedule->event->sumber_anggaran }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Tanggal Pelaksanaan Asesmen:</span>
                    <span class="font-bold text-zinc-900 text-sm block mt-0.5">{{ $schedule->formatted_tanggal }}</span>
                </div>
                @if ($schedule->tgl_pra_asesmen)
                    <div>
                        <span class="text-zinc-500 text-xs block">Tanggal Pra-Asesmen (APL-02):</span>
                        <span class="font-medium text-zinc-800 text-xs block mt-0.5">{{ $schedule->tgl_pra_asesmen->format('d M Y') }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-zinc-500 text-xs block">Batas Waktu Pendaftaran:</span>
                    <span class="font-medium text-zinc-800 text-xs block mt-0.5">
                        {{ $schedule->tgl_buka_daftar ? $schedule->tgl_buka_daftar->format('d M') : '-' }} s.d. {{ $schedule->tgl_tutup_daftar ? $schedule->tgl_tutup_daftar->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            <!-- Card 2: Skema & Kuota -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-3">
                <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                    Skema & Kapasitas Peserta
                </h4>
                <div>
                    <span class="text-zinc-500 text-xs block">Skema Sertifikasi:</span>
                    <span class="font-bold text-zinc-900 text-xs block mt-0.5">{{ $schedule->scheme->nama_skema }}</span>
                    <span class="text-[11px] font-mono text-zinc-500">{{ $schedule->scheme->kode_skema }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Biaya Asesmen per Peserta:</span>
                    <span class="font-bold text-zinc-900 text-sm font-mono block mt-0.5">{{ $schedule->scheme->formatted_biaya }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Kuota Maksimum Uji:</span>
                    <span class="font-bold text-blue-600 text-sm block mt-0.5">{{ $schedule->kuota_peserta }} Calon Asesi</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">ID Jadwal Sisfo BNSP:</span>
                    <span class="font-mono font-medium text-zinc-800 text-xs block mt-0.5">{{ $schedule->id_jadwal_bnsp ?? 'Belum terhubung' }}</span>
                </div>
            </div>

            <!-- Card 3: Tempat Uji (TUK) -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-3">
                <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider pb-2 border-b border-zinc-100">
                    Tempat Uji Kompetensi (TUK)
                </h4>
                <div>
                    <span class="text-zinc-500 text-xs block">Nama Tempat Uji:</span>
                    <span class="font-bold text-zinc-900 text-xs block mt-0.5">{{ $schedule->tuk->nama_tuk }}</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $schedule->tuk->jenis_badge_classes }} mt-1">
                        TUK {{ $schedule->tuk->jenis_tuk }}
                    </span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Alamat Lokasi:</span>
                    <p class="text-xs text-zinc-700 leading-relaxed mt-0.5">{{ $schedule->tuk->alamat ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Penanggung Jawab (PIC):</span>
                    <span class="font-medium text-zinc-800 text-xs block mt-0.5">{{ $schedule->tuk->penanggung_jawab ?? '-' }} ({{ $schedule->tuk->pic_phone ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- MUK Preview (Unit Kompetensi yang Diuji) -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-zinc-900 mb-1">Kemasan Unit Kompetensi yang Diujikan (MUK)</h3>
            <p class="text-xs text-zinc-500 mb-4">Unit-unit kompetensi standar SKKNI yang wajib dipenuhi asesi pada pelaksanaan jadwal ini.</p>

            <div class="divide-y divide-zinc-100 border border-zinc-200 rounded-xl overflow-hidden">
                @forelse ($schedule->scheme->units as $uIdx => $unit)
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-zinc-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-700 font-bold flex items-center justify-center text-[11px]">
                                {{ $uIdx + 1 }}
                            </span>
                            <div>
                                <span class="font-bold text-zinc-900 block">{{ $unit->judul_unit }}</span>
                                <span class="text-[11px] font-mono text-zinc-500">{{ $unit->kode_unit }}</span>
                            </div>
                        </div>
                        <span class="text-zinc-400 font-medium">{{ $unit->elements->count() }} Elemen Kompetensi</span>
                    </div>
                @empty
                    <p class="text-center text-zinc-400 text-xs py-4">Belum ada unit kompetensi pada skema ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Section Plotting Asesor & Asesi (Fase 4 & 5 Aktif Penuh) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 1. Penugasan Asesor Penguji (Surat Tugas) -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Tim Asesor Penguji (Surat Tugas)</h4>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Asesor yang ditugaskan menguji pada jadwal ini.</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $schedule->scheduleAsesors->count() }} Asesor
                    </span>
                </div>

                <!-- Form Tambah Asesor -->
                <form action="{{ route('admin.schedules.asesors.assign', $schedule) }}" method="POST" class="p-3 bg-zinc-50 rounded-xl border border-zinc-200 space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Pilih Asesor</label>
                            <select name="asesor_id" required class="w-full text-xs rounded-lg border-zinc-300">
                                <option value="">-- Pilih Asesor --</option>
                                @foreach ($availableAsesors as $asr)
                                    <option value="{{ $asr->id }}">
                                        {{ $asr->name }} (No. MET: {{ $asr->asesorProfile?->no_met ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Peran Penugasan</label>
                            <select name="peran" required class="w-full text-xs rounded-lg border-zinc-300">
                                <option value="Lead Asesor">Lead Asesor (Ketua)</option>
                                <option value="Anggota" selected>Anggota Asesor</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex items-end gap-2">
                            <div class="flex-1">
                                <label class="block font-bold text-zinc-700 mb-1">No. Surat Tugas (Auto/Manual)</label>
                                <input type="text" name="no_surat_tugas" placeholder="Kosongkan untuk nomor otomatis" class="w-full text-xs rounded-lg border-zinc-300">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shrink-0">
                                + Tugaskan Asesor
                            </button>
                        </div>
                    </div>
                </form>

                <!-- List Asesor Ditugaskan -->
                <div class="space-y-2">
                    @forelse ($schedule->scheduleAsesors as $sa)
                        <div class="p-3 bg-white border border-zinc-200 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-zinc-900 flex items-center gap-2">
                                    <span>{{ $sa->asesor->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $sa->peran === 'Lead Asesor' ? 'bg-purple-100 text-purple-800' : 'bg-zinc-100 text-zinc-700' }}">
                                        {{ $sa->peran }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-zinc-500 mt-0.5 flex items-center gap-1.5">
                                    <span>No. MET: <span class="font-mono">{{ $sa->asesor->asesorProfile?->no_met ?? '-' }}</span></span>
                                    <span>&bull;</span>
                                    <span>Surat Tugas: <span class="font-mono text-zinc-700">{{ $sa->no_surat_tugas }}</span></span>
                                    <a href="{{ route('documents.surat-tugas', [$schedule, $sa]) }}" target="_blank" class="text-blue-600 hover:underline font-semibold ml-1">
                                        [Cetak PDF]
                                    </a>
                                </div>
                            </div>
                            <form action="{{ route('admin.schedules.asesors.remove', [$schedule, $sa]) }}" method="POST" onsubmit="return confirm('Hapus penugasan asesor ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-center text-zinc-400 text-xs py-4">Belum ada asesor yang ditugaskan pada jadwal ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Daftar Peserta Terdaftar & Plotting Asesi ke Asesor -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Peserta Terdaftar (Plotting Asesor)</h4>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Pasangkan setiap peserta dengan asesor penguji.</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $schedule->applications->count() }} Peserta
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse ($schedule->applications as $app)
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="font-bold text-zinc-900">{{ $app->user->name }}</div>
                                <div class="text-[11px] text-zinc-500 font-mono">{{ $app->nomor_pendaftaran }} &bull; NIK: {{ $app->user->nik ?? '-' }}</div>
                            </div>

                            <form action="{{ route('admin.schedules.plot-asesor', $schedule) }}" method="POST" class="flex items-center gap-1.5 shrink-0">
                                @csrf
                                <input type="hidden" name="application_id" value="{{ $app->id }}">
                                <select name="asesor_id" onchange="this.form.submit()" class="text-xs rounded-lg border-zinc-300 py-1">
                                    <option value="">-- Pilih Asesor Penguji --</option>
                                    @foreach ($schedule->scheduleAsesors as $sa)
                                        <option value="{{ $sa->asesor_id }}" {{ $app->asesor_id === $sa->asesor_id ? 'selected' : '' }}>
                                            {{ $sa->asesor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-center text-zinc-400 text-xs py-4">Belum ada peserta yang di-plot ke jadwal ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
