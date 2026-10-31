<x-app-layout title="Ubah Jadwal Asesmen">
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.schedules.show', $schedule) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Detail Jadwal</span>
                </a>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Ubah Data Jadwal Uji Kompetensi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">{{ $schedule->nama_jadwal }} ({{ $schedule->kode_jadwal }})</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="event_id" class="block text-xs font-semibold text-zinc-700">Event / Program Sertifikasi <span class="text-rose-500">*</span></label>
                        <select
                            name="event_id"
                            id="event_id"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            @foreach ($events as $ev)
                                <option value="{{ $ev->id }}" {{ old('event_id', $schedule->event_id) == $ev->id ? 'selected' : '' }}>
                                    {{ $ev->nama_event }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="scheme_id" class="block text-xs font-semibold text-zinc-700">Skema Sertifikasi <span class="text-rose-500">*</span></label>
                        <select
                            name="scheme_id"
                            id="scheme_id"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            @foreach ($schemes as $sch)
                                <option value="{{ $sch->id }}" {{ old('scheme_id', $schedule->scheme_id) == $sch->id ? 'selected' : '' }}>
                                    {{ $sch->nama_skema }} ({{ $sch->kode_skema }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tuk_id" class="block text-xs font-semibold text-zinc-700">Tempat Uji Kompetensi (TUK) <span class="text-rose-500">*</span></label>
                        <select
                            name="tuk_id"
                            id="tuk_id"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            @foreach ($tuks as $tk)
                                <option value="{{ $tk->id }}" {{ old('tuk_id', $schedule->tuk_id) == $tk->id ? 'selected' : '' }}>
                                    {{ $tk->nama_tuk }} ({{ $tk->jenis_tuk }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kode_jadwal" class="block text-xs font-semibold text-zinc-700">Kode Jadwal Unik <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="kode_jadwal"
                            id="kode_jadwal"
                            required
                            value="{{ old('kode_jadwal', $schedule->kode_jadwal) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>
                </div>

                <div>
                    <label for="nama_jadwal" class="block text-xs font-semibold text-zinc-700">Nama / Judul Jadwal Asesmen <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        name="nama_jadwal"
                        id="nama_jadwal"
                        required
                        value="{{ old('nama_jadwal', $schedule->nama_jadwal) }}"
                        class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tgl_pra_asesmen" class="block text-xs font-semibold text-zinc-700">Tanggal Pra-Asesmen</label>
                        <input
                            type="date"
                            name="tgl_pra_asesmen"
                            id="tgl_pra_asesmen"
                            value="{{ old('tgl_pra_asesmen', $schedule->tgl_pra_asesmen?->format('Y-m-d')) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label for="tgl_asesmen" class="block text-xs font-semibold text-zinc-700">Tanggal Pelaksanaan Uji <span class="text-rose-500">*</span></label>
                        <input
                            type="date"
                            name="tgl_asesmen"
                            id="tgl_asesmen"
                            required
                            value="{{ old('tgl_asesmen', $schedule->tgl_asesmen->format('Y-m-d')) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label for="tgl_selesai" class="block text-xs font-semibold text-zinc-700">Tanggal Selesai Asesmen</label>
                        <input
                            type="date"
                            name="tgl_selesai"
                            id="tgl_selesai"
                            value="{{ old('tgl_selesai', $schedule->tgl_selesai?->format('Y-m-d')) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tgl_buka_daftar" class="block text-xs font-semibold text-zinc-700">Buka Pendaftaran</label>
                        <input
                            type="date"
                            name="tgl_buka_daftar"
                            id="tgl_buka_daftar"
                            value="{{ old('tgl_buka_daftar', $schedule->tgl_buka_daftar?->format('Y-m-d')) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label for="tgl_tutup_daftar" class="block text-xs font-semibold text-zinc-700">Tutup Pendaftaran</label>
                        <input
                            type="date"
                            name="tgl_tutup_daftar"
                            id="tgl_tutup_daftar"
                            value="{{ old('tgl_tutup_daftar', $schedule->tgl_tutup_daftar?->format('Y-m-d')) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>

                    <div>
                        <label for="kuota_peserta" class="block text-xs font-semibold text-zinc-700">Kuota Peserta <span class="text-rose-500">*</span></label>
                        <input
                            type="number"
                            name="kuota_peserta"
                            id="kuota_peserta"
                            required
                            min="1"
                            value="{{ old('kuota_peserta', $schedule->kuota_peserta) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-xs font-semibold text-zinc-700">Status Siklus Asesmen <span class="text-rose-500">*</span></label>
                        <select
                            name="status"
                            id="status"
                            required
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        >
                            @foreach (\App\Enums\ScheduleStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ old('status', $schedule->status->value) === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_jadwal_bnsp" class="block text-xs font-semibold text-zinc-700">ID Jadwal Sisfo BNSP</label>
                        <input
                            type="text"
                            name="id_jadwal_bnsp"
                            id="id_jadwal_bnsp"
                            value="{{ old('id_jadwal_bnsp', $schedule->id_jadwal_bnsp) }}"
                            class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                        />
                    </div>
                </div>

                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-zinc-700">Keterangan / Catatan</label>
                    <textarea
                        name="keterangan"
                        id="keterangan"
                        rows="2"
                        class="mt-1.5 w-full px-3.5 py-2 text-sm bg-white border border-zinc-300 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition"
                    >{{ old('keterangan', $schedule->keterangan) }}</textarea>
                </div>

                <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.schedules.show', $schedule) }}" class="px-4 py-2 text-xs text-zinc-600 hover:bg-zinc-100 rounded-xl transition">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                    >
                        Simpan Perubahan Jadwal
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
