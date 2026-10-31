<x-app-layout title="Verifikasi APL-01: {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6" x-data="documentVerificationDrawer()">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Admin LSP</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('admin.verifications.index') }}" class="hover:underline">Verifikasi Berkas</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium font-mono">{{ $application->nomor_pendaftaran }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        {{ $application->user->name }}
                    </h1>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-700">
                        {{ $application->nomor_pendaftaran }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $application->status->badgeClasses() }}">
                        {{ $application->status->label() }}
                    </span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Data Pemohon & Verifikasi Berkas Persyaratan -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Ringkasan Data Calon Asesi & Skema -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Data Pemohon & Sertifikasi</h2>
                        <span class="text-xs text-zinc-500">Diajukan: {{ $application->tgl_pengajuan?->format('d M Y H:i') ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-zinc-500 block">Nama Lengkap:</span>
                            <span class="font-bold text-zinc-900 text-sm">{{ $application->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block">No. KTP / NIK:</span>
                            <span class="font-mono font-bold text-zinc-900 text-sm">{{ $application->user->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block">Tempat / Tanggal Lahir:</span>
                            <span class="text-zinc-800">{{ $profile?->tempat_lahir ?? '-' }}, {{ $profile?->tanggal_lahir?->format('d M Y') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block">Kontak / WhatsApp:</span>
                            <span class="text-zinc-800">{{ $profile?->telepon ?: $application->user->phone }} / {{ $application->user->email }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-zinc-500 block">Alamat Rumah:</span>
                            <span class="text-zinc-800">{{ $profile?->alamat_lengkap ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block">Skema Sertifikasi:</span>
                            <span class="font-bold text-blue-700">{{ $application->scheme->nama_skema }}</span>
                            <span class="text-zinc-500 font-mono block">[{{ $application->scheme->kode_skema }}]</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block">Tujuan Asesmen:</span>
                            <span class="font-semibold text-zinc-800">{{ $application->tujuan_asesmen }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Lembar Verifikasi Bukti Kelengkapan Pemohon (Dokumen) -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Bukti Kelengkapan Pemohon (Persyaratan Dasar)</h2>
                            <p class="text-xs text-zinc-500 mt-0.5">Klik dokumen untuk pratinjau instan (Quick-View Drawer) dan tentukan status kelayakan.</p>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200">
                            {{ $application->documents->count() }} Berkas Diunggah
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($application->documents as $doc)
                            <div class="p-4 rounded-xl border {{ $doc->status_verifikasi === \App\Enums\DocumentVerificationStatus::TidakMemenuhi ? 'border-rose-300 bg-rose-50/30' : ($doc->status_verifikasi === \App\Enums\DocumentVerificationStatus::Memenuhi ? 'border-emerald-200 bg-emerald-50/20' : 'border-zinc-200 bg-zinc-50/40') }} flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition">
                                <div class="space-y-1 flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-bold text-zinc-900">{{ $doc->nama_dokumen }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $doc->status_verifikasi->badgeClasses() }}" id="docBadge_{{ $doc->id }}">
                                            {{ $doc->status_verifikasi->label() }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3 text-[11px] text-zinc-500">
                                        <span>File: <strong class="text-zinc-700">{{ $doc->file_name }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Ukuran: {{ $doc->formatted_size }}</span>
                                    </div>

                                    <div id="docNoteBox_{{ $doc->id }}" class="{{ $doc->catatan ? '' : 'hidden' }} text-xs text-rose-700 font-medium pt-1">
                                        Catatan: <span id="docNoteText_{{ $doc->id }}">{{ $doc->catatan }}</span>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-2">
                                    <!-- Tombol Buka Drawer Quick-View -->
                                    <button type="button" @click="openDrawer({{ $doc->id }}, '{{ addslashes($doc->nama_dokumen) }}', '{{ $doc->file_url }}', '{{ $doc->file_type }}', '{{ $doc->status_verifikasi->value }}', '{{ addslashes($doc->catatan ?? '') }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-semibold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Pratinjau & Verifikasi</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-zinc-400 text-xs italic">
                                Calon asesi belum mengunggah dokumen persyaratan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Tanda Tangan Asesi Pemohon -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-3">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Tanda Tangan Pemohon</h2>
                    <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-48 h-24 bg-white border border-zinc-300 rounded-lg flex items-center justify-center p-2">
                            @if ($application->ttd_asesi)
                                <img src="{{ $application->ttd_asesi }}" alt="Tanda Tangan Asesi" class="max-h-full max-w-full object-contain">
                            @else
                                <span class="text-xs text-zinc-400 italic">Belum bertanda tangan</span>
                            @endif
                        </div>
                        <div class="text-xs space-y-1">
                            <div class="font-bold text-zinc-900">{{ $application->user->name }}</div>
                            <div class="text-zinc-500">Waktu Bertanda Tangan: {{ $application->tgl_ttd_asesi?->format('d M Y H:i') ?? '-' }} WIB</div>
                            <div class="text-[11px] text-emerald-600 font-semibold">Telah menyetujui pernyataan kebenaran data formulir FR-APL-01.</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Col: Form Putusan Verifikasi & Plotting Jadwal -->
            <div class="space-y-6">

                <!-- Card Putusan Verifikasi -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-5 sticky top-6">
                    <div class="border-b border-zinc-100 pb-3">
                        <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Keputusan Verifikasi</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Tentukan rekomendasi kelayakan permohonan sertifikasi.</p>
                    </div>

                    <form action="{{ route('admin.verifications.update-status', $application) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Pilihan Status Akhir -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-2">Putusan Status <span class="text-rose-500">*</span></label>
                            <div class="space-y-2">
                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                    <input type="radio" name="status" value="diterima" {{ $application->status->value === 'diterima' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-emerald-700">Diterima (Terverifikasi)</div>
                                        <div class="text-zinc-500 text-[11px]">Seluruh berkas persyaratan telah sah dan lengkap.</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                    <input type="radio" name="status" value="revisi" {{ $application->status->value === 'revisi' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-orange-700">Perlu Revisi Dokumen</div>
                                        <div class="text-zinc-500 text-[11px]">Asesi diminta mengunggah ulang dokumen tertentu.</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                    <input type="radio" name="status" value="ditolak" {{ $application->status->value === 'ditolak' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-rose-700">Ditolak</div>
                                        <div class="text-zinc-500 text-[11px]">Tidak memenuhi persyaratan kualifikasi dasar.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Revisi / Alasan Tolak -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Catatan Verifikator untuk Asesi</label>
                            <textarea name="catatan_revisi" rows="3" placeholder="Tuliskan catatan perbaikan berkas atau rekomendasi..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan_revisi', $application->catatan_revisi) }}</textarea>
                            <p class="text-[10px] text-zinc-400 mt-1">Catatan ini akan dikirim otomatis ke email asesi.</p>
                        </div>

                        <!-- Plotting Jadwal Langsung -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Penempatan Jadwal Asesmen (TUK)</label>
                            <select name="schedule_id" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- {{ $application->schedule ? 'Pertahankan jadwal saat ini' : 'Pilih jadwal aktif' }} --</option>
                                @foreach ($availableSchedules as $sched)
                                    <option value="{{ $sched->id }}" {{ $application->schedule_id === $sched->id ? 'selected' : '' }}>
                                        {{ $sched->nama_jadwal }} &bull; {{ $sched->formatted_tanggal }} (Sisa: {{ $sched->sisa_kuota }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Putusan Verifikasi</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- DRAWER QUICK-VIEW DOKUMEN INTERAKTIF (SLIDE-OVER / MODAL PREVIEW)         --}}
        {{-- ========================================================================= --}}
        <div x-show="drawerOpen" class="fixed inset-0 z-50 overflow-hidden" style="display: none;" x-cloak>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-xs transition-opacity" @click="closeDrawer()"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-2xl bg-white shadow-2xl flex flex-col">
                    
                    <!-- Drawer Header -->
                    <div class="px-6 py-4 bg-lsp-navy text-white flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Pratinjau Cepat Dokumen</span>
                            <h3 class="text-sm font-bold truncate max-w-md" x-text="drawerDocName"></h3>
                        </div>
                        <button type="button" @click="closeDrawer()" class="text-zinc-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Drawer Preview Body -->
                    <div class="flex-1 overflow-y-auto p-4 bg-zinc-100 flex items-center justify-center">
                        <template x-if="drawerDocType === 'pdf'">
                            <iframe :src="drawerDocUrl" class="w-full h-full min-h-[500px] rounded-xl border border-zinc-300 shadow-inner bg-white"></iframe>
                        </template>

                        <template x-if="drawerDocType !== 'pdf'">
                            <div class="max-w-full max-h-full overflow-auto p-2 bg-white rounded-xl border border-zinc-300 shadow-sm">
                                <img :src="drawerDocUrl" :alt="drawerDocName" class="max-h-[500px] object-contain mx-auto">
                            </div>
                        </template>
                    </div>

                    <!-- Drawer Footer Review Controls -->
                    <div class="p-5 border-t border-zinc-200 bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900">Verifikasi Dokumen Ini:</span>
                            <a :href="drawerDocUrl" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                <span>Buka di Tab Baru</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </div>

                        <!-- Status Selection Buttons -->
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="drawerStatus = 'memenuhi'" :class="drawerStatus === 'memenuhi' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-600/30' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 border border-emerald-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>Memenuhi Syarat</span>
                            </button>

                            <button type="button" @click="drawerStatus = 'tidak_memenuhi'" :class="drawerStatus === 'tidak_memenuhi' ? 'bg-rose-600 text-white font-bold ring-2 ring-rose-600/30' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 border border-rose-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span>Tidak Memenuhi (Perlu Revisi)</span>
                            </button>
                        </div>

                        <!-- Note Field -->
                        <div>
                            <input type="text" x-model="drawerNote" placeholder="Tuliskan catatan koreksi untuk dokumen ini (jika tidak memenuhi)..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" @click="closeDrawer()" class="px-4 py-2 rounded-xl border border-zinc-300 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 transition">
                                Tutup
                            </button>
                            <button type="button" @click="saveDocumentStatus()" :disabled="isSaving" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition disabled:opacity-50">
                                <span x-show="!isSaving">Simpan Status Dokumen</span>
                                <span x-show="isSaving">Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Script Drawer Quick-View -->
    <script>
        function documentVerificationDrawer() {
            return {
                drawerOpen: false,
                drawerDocId: null,
                drawerDocName: '',
                drawerDocUrl: '',
                drawerDocType: '',
                drawerStatus: 'pending',
                drawerNote: '',
                isSaving: false,

                openDrawer(id, name, url, type, status, note) {
                    this.drawerDocId = id;
                    this.drawerDocName = name;
                    this.drawerDocUrl = url;
                    this.drawerDocType = (type || '').toLowerCase();
                    this.drawerStatus = status || 'pending';
                    this.drawerNote = note || '';
                    this.drawerOpen = true;
                },

                closeDrawer() {
                    this.drawerOpen = false;
                },

                async saveDocumentStatus() {
                    this.isSaving = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/verifications/documents/${this.drawerDocId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                status_verifikasi: this.drawerStatus,
                                catatan: this.drawerNote
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            // Update badge in DOM
                            const badge = document.getElementById(`docBadge_${this.drawerDocId}`);
                            if (badge) {
                                badge.textContent = data.document.status_label;
                                if (data.document.status === 'memenuhi') {
                                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20';
                                } else if (data.document.status === 'tidak_memenuhi') {
                                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 ring-1 ring-rose-600/20';
                                }
                            }

                            // Update note text in DOM
                            const noteBox = document.getElementById(`docNoteBox_${this.drawerDocId}`);
                            const noteText = document.getElementById(`docNoteText_${this.drawerDocId}`);
                            if (noteBox && noteText) {
                                if (data.document.catatan) {
                                    noteText.textContent = data.document.catatan;
                                    noteBox.classList.remove('hidden');
                                } else {
                                    noteBox.classList.add('hidden');
                                }
                            }

                            this.closeDrawer();
                        } else {
                            alert('Gagal memperbarui status dokumen.');
                        }
                    } catch (e) {
                        console.error('Error updating document status:', e);
                        alert('Terjadi kesalahan saat memperbarui status dokumen.');
                    } finally {
                        this.isSaving = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
