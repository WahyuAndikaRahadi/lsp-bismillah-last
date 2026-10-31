<x-app-layout title="Peninjauan APL-02: {{ $application->user->name }}">
    <div class="space-y-6" x-data="asesorApl02Review()">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesor</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesor.apl02.index') }}" class="hover:underline">Daftar APL-02</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">{{ $application->user->name }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        Peninjauan Bukti FR-APL-02
                    </h1>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-700">
                        {{ $application->nomor_pendaftaran }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $apl02->status->badgeClasses() }}">
                        {{ $apl02->status->label() }}
                    </span>
                </div>
            </div>

            <!-- Batch Action Button -->
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" @click="markAllVatm()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tandai Semua VATM (Valid, Asli, Terkini, Memadai)</span>
                </button>
            </div>
        </div>

        <form action="{{ route('asesor.apl02.update', $apl02) }}" method="POST" id="asesorReviewForm" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Identitas Asesi & Dokumen Portofolio -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Identitas Calon Asesi & Skema</h2>
                    <span class="text-xs text-zinc-500">TUK: {{ $application->schedule?->tuk->nama_tuk ?? '-' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-zinc-500 block">Nama Calon Asesi:</span>
                        <span class="font-bold text-zinc-900 text-sm">{{ $application->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">NIK & No. Telp:</span>
                        <span class="font-mono text-zinc-900">{{ $application->user->nik ?? '-' }} &bull; {{ $application->user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Skema Sertifikasi:</span>
                        <span class="font-bold text-blue-700">{{ $application->scheme->nama_skema }}</span>
                    </div>
                </div>

                <!-- Dokumen Lampiran Pemohon (Quick Access) -->
                <div class="pt-2 border-t border-zinc-100">
                    <span class="text-[11px] font-bold text-zinc-500 uppercase block mb-2">Dokumen Portofolio Terunggah:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($application->documents as $doc)
                            <a href="{{ $doc->file_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs border border-zinc-200 transition">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>{{ $doc->nama_dokumen }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tabel KUK & Verifikasi VATM -->
            <div class="space-y-6">
                @foreach ($application->scheme->competencyUnits as $unitIdx => $unit)
                    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
                        <div class="p-4 bg-lsp-navy text-white flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-blue-400 uppercase font-mono">Unit {{ $unitIdx + 1 }}: {{ $unit->kode_unit }}</span>
                                <h3 class="text-xs font-bold">{{ $unit->judul_unit }}</h3>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                            <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                                <tr>
                                    <th class="px-4 py-2.5 w-16 text-center">KUK</th>
                                    <th class="px-4 py-2.5">Kriteria Unjuk Kerja</th>
                                    <th class="px-4 py-2.5 w-20 text-center">Mandiri</th>
                                    <th class="px-4 py-2.5 w-48">Bukti Pendukung</th>
                                    <th class="px-4 py-2.5 w-44 text-center">Tinjauan Asesor (VATM)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 bg-white">
                                @foreach ($unit->elements as $elem)
                                    <tr class="bg-zinc-50/70 font-semibold text-zinc-700">
                                        <td colspan="5" class="px-4 py-1.5 text-[11px]">
                                            Elemen: {{ $elem->nama_elemen }}
                                        </td>
                                    </tr>
                                    @foreach ($elem->criterias as $kuk)
                                        @php
                                            $item = $itemsByKuk->get($kuk->id);
                                        @endphp
                                        @if ($item)
                                            <tr>
                                                <td class="px-4 py-3 font-mono font-bold text-blue-700 text-center">{{ $kuk->nomor_kuk }}</td>
                                                <td class="px-4 py-3 text-zinc-800">{{ $kuk->pernyataan }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->penilaian_mandiri === 'K' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                        {{ $item->penilaian_mandiri }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-zinc-600 text-[11px]">{{ $item->bukti_pendukung }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <label class="flex items-center gap-0.5 cursor-pointer text-[11px] font-mono font-bold" title="Valid">
                                                            <input type="checkbox" name="items[{{ $item->id }}][v]" value="1" x-model="vatm[{{ $item->id }}].v" class="rounded text-blue-600 border-zinc-300">
                                                            <span>V</span>
                                                        </label>
                                                        <label class="flex items-center gap-0.5 cursor-pointer text-[11px] font-mono font-bold" title="Asli">
                                                            <input type="checkbox" name="items[{{ $item->id }}][a]" value="1" x-model="vatm[{{ $item->id }}].a" class="rounded text-blue-600 border-zinc-300">
                                                            <span>A</span>
                                                        </label>
                                                        <label class="flex items-center gap-0.5 cursor-pointer text-[11px] font-mono font-bold" title="Terkini">
                                                            <input type="checkbox" name="items[{{ $item->id }}][t]" value="1" x-model="vatm[{{ $item->id }}].t" class="rounded text-blue-600 border-zinc-300">
                                                            <span>T</span>
                                                        </label>
                                                        <label class="flex items-center gap-0.5 cursor-pointer text-[11px] font-mono font-bold" title="Memadai">
                                                            <input type="checkbox" name="items[{{ $item->id }}][m]" value="1" x-model="vatm[{{ $item->id }}].m" class="rounded text-blue-600 border-zinc-300">
                                                            <span>M</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <!-- Rekomendasi & Putusan Asesor -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-zinc-100 pb-3">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Rekomendasi Asesor Penguji</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Tentukan apakah asesi direkomendasikan untuk lanjut ke tahap uji kompetensi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-2">Rekomendasi Kelanjutan Asesmen <span class="text-rose-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                <input type="radio" name="rekomendasi_asesor" value="Asesmen dapat dilanjutkan" {{ $apl02->rekomendasi_asesor === 'Asesmen dapat dilanjutkan' || !$apl02->rekomendasi_asesor ? 'checked' : '' }} class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-emerald-700">Asesmen dapat dilanjutkan</div>
                                    <div class="text-zinc-500 text-[11px]">Bukti portofolio memenuhi ketentuan VATM. Asesi siap uji kompetensi.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                <input type="radio" name="rekomendasi_asesor" value="Asesmen tidak dapat dilanjutkan" {{ $apl02->rekomendasi_asesor === 'Asesmen tidak dapat dilanjutkan' ? 'checked' : '' }} class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-rose-700">Asesmen tidak dapat dilanjutkan</div>
                                    <div class="text-zinc-500 text-[11px]">Bukti belum memadai atau masih terdapat kekurangan portofolio.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Status Verifikasi APL-02 <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="diverifikasi" {{ $apl02->status->value === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi (Disetujui Lanjut Uji)</option>
                                <option value="perlu_revisi" {{ $apl02->status->value === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi (Minta Perbaikan Bukti)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Catatan Asesor untuk Asesi</label>
                            <textarea name="catatan_asesor" rows="3" placeholder="Catatan kesiapan atau petunjuk pelaksanaan uji..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan_asesor', $apl02->catatan_asesor) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tanda Tangan Canvas Asesor -->
                <div class="border-t border-zinc-100 pt-5 space-y-3">
                    <label class="block text-xs font-bold text-zinc-800">
                        Tanda Tangan Digital Asesor Penguji <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-zinc-300 rounded-xl bg-zinc-50 p-2 inline-block">
                        <canvas id="asesorSignCanvas" width="450" height="150" class="bg-white rounded-lg border border-zinc-200 cursor-crosshair touch-none"></canvas>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span class="text-[11px] text-zinc-400">Tanda Tangan Digital Penguji Sah</span>
                            <button type="button" id="clearAsesorSignBtn" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition">
                                Bersihkan (Clear)
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="ttd_asesor" id="ttd_asesor_input">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <a href="{{ route('asesor.apl02.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    Kembali
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Hasil Peninjauan FR-APL-02</span>
                </button>
            </div>

        </form>

    </div>

    <script>
        function asesorApl02Review() {
            return {
                vatm: {
                    @foreach ($apl02->items as $item)
                        {{ $item->id }}: {
                            v: {{ $item->is_valid ? 'true' : 'false' }},
                            a: {{ $item->is_asli ? 'true' : 'false' }},
                            t: {{ $item->is_terkini ? 'true' : 'false' }},
                            m: {{ $item->is_memadai ? 'true' : 'false' }},
                        },
                    @endforeach
                },

                markAllVatm() {
                    for (const itemId in this.vatm) {
                        this.vatm[itemId].v = true;
                        this.vatm[itemId].a = true;
                        this.vatm[itemId].t = true;
                        this.vatm[itemId].m = true;
                    }
                }
            };
        }

        // Asesor Canvas
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('asesorSignCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearAsesorSignBtn');
            const hiddenInput = document.getElementById('ttd_asesor_input');
            const form = document.getElementById('asesorReviewForm');

            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - rect.left, y: clientY - rect.top };
            }

            function start(e) {
                isDrawing = true;
                const pos = getPos(e);
                lastX = pos.x; lastY = pos.y;
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.stroke();
                lastX = pos.x; lastY = pos.y;
            }

            function stop() {
                if (isDrawing) {
                    isDrawing = false;
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            }

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stop);
            canvas.addEventListener('mouseleave', stop);

            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stop);

            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
            });

            form.addEventListener('submit', () => {
                if (!hiddenInput.value) {
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            });
        });
    </script>
</x-app-layout>
