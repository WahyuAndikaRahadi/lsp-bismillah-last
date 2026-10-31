<x-app-layout title="Verifikasi Permohonan Sertifikasi (FR-APL-01)">
    <div class="space-y-6" x-data="{ plotModalOpen: false, selectedAppId: null, selectedAppName: '', selectedSchemeId: null }">

        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Admin LSP</span>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">Verifikasi Berkas APL-01</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Verifikasi Permohonan Sertifikasi</h1>
                <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                    Tinjau berkas bukti kelengkapan calon asesi, verifikasi syarat dasar, dan tempatkan ke jadwal asesmen.
                </p>
            </div>
        </div>

        <!-- Status Filter Tabs / Stat Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <a href="{{ route('admin.verifications.index', ['status' => 'diajukan', 'scheme_id' => $currentSchemeId, 'search' => $search]) }}" class="p-3.5 rounded-2xl border transition {{ $currentStatus === 'diajukan' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                <div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Menunggu Verifikasi</div>
                <div class="text-2xl font-black text-amber-900 mt-1">{{ $counts['diajukan'] }}</div>
                <div class="text-[10px] text-amber-600 mt-0.5">Antrean permohonan baru</div>
            </a>

            <a href="{{ route('admin.verifications.index', ['status' => 'revisi', 'scheme_id' => $currentSchemeId, 'search' => $search]) }}" class="p-3.5 rounded-2xl border transition {{ $currentStatus === 'revisi' ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                <div class="text-[11px] font-bold text-orange-700 uppercase tracking-wider">Perlu Revisi</div>
                <div class="text-2xl font-black text-orange-900 mt-1">{{ $counts['revisi'] }}</div>
                <div class="text-[10px] text-orange-600 mt-0.5">Menunggu respon asesi</div>
            </a>

            <a href="{{ route('admin.verifications.index', ['status' => 'diterima', 'scheme_id' => $currentSchemeId, 'search' => $search]) }}" class="p-3.5 rounded-2xl border transition {{ $currentStatus === 'diterima' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Diterima</div>
                <div class="text-2xl font-black text-emerald-900 mt-1">{{ $counts['diterima'] }}</div>
                <div class="text-[10px] text-emerald-600 mt-0.5">Berkas lengkap & sah</div>
            </a>

            <a href="{{ route('admin.verifications.index', ['status' => 'ditolak', 'scheme_id' => $currentSchemeId, 'search' => $search]) }}" class="p-3.5 rounded-2xl border transition {{ $currentStatus === 'ditolak' ? 'border-rose-500 bg-rose-50/60 ring-2 ring-rose-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Ditolak</div>
                <div class="text-2xl font-black text-rose-900 mt-1">{{ $counts['ditolak'] }}</div>
                <div class="text-[10px] text-rose-600 mt-0.5">Tidak memenuhi syarat</div>
            </a>

            <a href="{{ route('admin.verifications.index', ['status' => 'all', 'scheme_id' => $currentSchemeId, 'search' => $search]) }}" class="p-3.5 rounded-2xl border transition {{ $currentStatus === 'all' ? 'border-blue-500 bg-blue-50/60 ring-2 ring-blue-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                <div class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider">Semua Data</div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ $counts['all'] }}</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Total seluruh pemohon</div>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-4">
            <form action="{{ route('admin.verifications.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <input type="hidden" name="status" value="{{ $currentStatus }}">

                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <!-- Scheme Filter -->
                    <select name="scheme_id" onchange="this.form.submit()" class="text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Semua Skema Sertifikasi --</option>
                        @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}" {{ (string)$currentSchemeId === (string)$scheme->id ? 'selected' : '' }}>
                                {{ $scheme->nama_skema }} ({{ $scheme->kode_skema }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Instant Search Input -->
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor reg, nama asesi, NIK..." class="w-full text-xs rounded-xl border-zinc-300 pl-9 pr-3 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- Table Data Verifikasi -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                    <thead class="bg-zinc-50 text-zinc-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">No. Registrasi & Tgl</th>
                            <th class="px-5 py-3.5">Calon Asesi (Pemohon)</th>
                            <th class="px-5 py-3.5">Skema Sertifikasi</th>
                            <th class="px-5 py-3.5">Jadwal Uji (TUK)</th>
                            <th class="px-5 py-3.5 text-center">Kelengkapan Berkas</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white">
                        @forelse ($applications as $app)
                            <tr class="hover:bg-zinc-50/60 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-mono font-bold text-zinc-900">{{ $app->nomor_pendaftaran }}</div>
                                    <div class="text-[11px] text-zinc-400 mt-0.5">
                                        {{ $app->tgl_pengajuan?->format('d M Y H:i') ?? $app->created_at->format('d M Y') }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-bold text-zinc-900">{{ $app->user->name }}</div>
                                    <div class="text-[11px] font-mono text-zinc-500">NIK: {{ $app->user->nik ?? '-' }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $app->user->phone ?? $app->user->email }}</div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-zinc-900">{{ $app->scheme->nama_skema }}</div>
                                    <div class="text-[11px] text-zinc-500 font-mono">{{ $app->scheme->kode_skema }}</div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 mt-1">
                                        {{ $app->tujuan_asesmen }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($app->schedule)
                                        <div class="font-semibold text-zinc-900">{{ $app->schedule->nama_jadwal }}</div>
                                        <div class="text-[11px] text-zinc-500">{{ $app->schedule->formatted_tanggal }}</div>
                                        <div class="text-[11px] text-blue-600 font-medium">{{ $app->schedule->tuk->nama_tuk }}</div>
                                    @else
                                        <span class="text-zinc-400 italic text-xs">Belum di-plot jadwal</span>
                                        @if ($app->status === \App\Enums\ApplicationStatus::Diterima)
                                            <button type="button" @click="selectedAppId = {{ $app->id }}; selectedAppName = '{{ addslashes($app->user->name) }}'; selectedSchemeId = {{ $app->scheme_id }}; plotModalOpen = true;" class="block mt-1 text-[11px] font-bold text-blue-600 hover:text-blue-700">
                                                + Plot ke Jadwal
                                            </button>
                                        @endif
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @php
                                        $validCount = $app->documents->where('status_verifikasi', \App\Enums\DocumentVerificationStatus::Memenuhi)->count();
                                        $totalDocs = $app->documents->count();
                                    @endphp
                                    <div class="font-bold text-zinc-900">{{ $validCount }} / {{ $totalDocs }} Berkas</div>
                                    @if($totalDocs > 0 && $validCount === $totalDocs)
                                        <span class="text-[10px] font-bold text-emerald-600">Semua Memenuhi</span>
                                    @elseif($app->hasInvalidDocument())
                                        <span class="text-[10px] font-bold text-rose-600">Ada Berkas Ditolak</span>
                                    @else
                                        <span class="text-[10px] text-zinc-400">Belum Lengkap Diperiksa</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $app->status->badgeClasses() }}">
                                        {{ $app->status->label() }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.verifications.show', $app) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Verifikasi Berkas</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-zinc-500">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-zinc-700">Tidak ada data permohonan sertifikasi</p>
                                    <p class="text-xs text-zinc-400 mt-1">Gunakan kata kunci atau filter status yang berbeda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200">
                {{ $applications->links() }}
            </div>
        </div>

        <!-- Quick Plot Modal -->
        <div x-show="plotModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-xs transition-opacity" @click="plotModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h3 class="text-sm font-bold text-zinc-900">Plotting Jadwal Asesmen</h3>
                        <button type="button" @click="plotModalOpen = false" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <p class="text-xs text-zinc-600">
                        Pilih jadwal asesmen aktif untuk peserta: <strong x-text="selectedAppName" class="text-zinc-900"></strong>
                    </p>

                    <form :action="'/admin/verifications/' + selectedAppId + '/plot'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Jadwal Asesmen Tersedia</label>
                            <select name="schedule_id" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Jadwal Asesmen --</option>
                                @foreach ($activeSchedules as $sched)
                                    <option value="{{ $sched->id }}">
                                        {{ $sched->nama_jadwal }} &bull; {{ $sched->formatted_tanggal }} di {{ $sched->tuk->nama_tuk }} (Sisa: {{ $sched->sisa_kuota }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="plotModalOpen = false" class="px-4 py-2 rounded-xl border border-zinc-300 text-xs font-semibold text-zinc-700 hover:bg-zinc-100">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm">
                                Simpan Plotting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
