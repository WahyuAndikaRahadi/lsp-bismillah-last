<x-app-layout title="FR-AK-02: Rekaman Asesmen — {{ $application->user->name }}">
    <div class="space-y-6" x-data="ak02Matrix()">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesor</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesor.apl02.index') }}" class="hover:underline">Penilaian</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium font-mono">{{ $application->nomor_pendaftaran }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        FR-AK-02: Formulir Rekaman Asesmen Kompetensi
                    </h1>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-700">
                        {{ $application->user->name }}
                    </span>
                </div>
            </div>

            <!-- Batch Evaluator Button -->
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" @click="markAllKompeten()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tandai Semua Unit Kompeten (K)</span>
                </button>
            </div>
        </div>

        <form action="{{ route('asesor.ak02.store', $application) }}" method="POST" id="ak02Form" class="space-y-6">
            @csrf

            <!-- Identitas Asesmen Card -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Informasi Penilaian Uji Kompetensi</h2>
                    <span class="text-xs text-zinc-500">TUK: {{ $application->schedule?->tuk->nama_tuk ?? '-' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-zinc-500 block">Nama Peserta (Asesi):</span>
                        <span class="font-bold text-zinc-900 text-sm">{{ $application->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Skema Sertifikasi:</span>
                        <span class="font-bold text-blue-700">{{ $application->scheme->nama_skema }}</span>
                        <span class="text-zinc-400 font-mono block">[{{ $application->scheme->kode_skema }}]</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Jadwal & Tanggal Uji:</span>
                        <span class="font-medium text-zinc-900">{{ $application->schedule?->formatted_tanggal ?? date('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Matriks Penilaian per Unit Kompetensi -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-lsp-navy text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Matriks Hasil Asesmen</span>
                        <h3 class="text-xs font-bold mt-0.5">Penilaian Kompetensi per Unit Kemasan</h3>
                    </div>
                    <span class="text-xs text-zinc-300 font-mono">Total: {{ $application->scheme->competencyUnits->count() }} Unit</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                        <thead class="bg-zinc-50 text-[10px] uppercase font-bold text-zinc-600">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3 w-40">Kode Unit</th>
                                <th class="px-4 py-3">Judul Unit Kompetensi</th>
                                <th class="px-4 py-3 w-48 text-center">Metode Asesmen Digunakan</th>
                                <th class="px-4 py-3 w-40 text-center">Rekomendasi Unit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($application->scheme->competencyUnits as $idx => $unit)
                                @php
                                    $unitAssess = $unitsAssessed->get($unit->id);
                                    $currentRek = $unitAssess?->rekomendasi_unit ?? 'K';
                                @endphp
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="px-4 py-4 text-center font-bold text-zinc-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-4 font-mono font-bold text-zinc-900">{{ $unit->kode_unit }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-zinc-900">{{ $unit->judul_unit }}</div>
                                        <div class="text-[11px] text-zinc-400 mt-0.5">{{ $unit->elements->count() }} Elemen &bull; Standar {{ $unit->jenis_standar }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3 text-xs">
                                            <label class="flex items-center gap-1 cursor-pointer font-bold" title="Observasi Demonstrasi Langsung">
                                                <input type="checkbox" name="units[{{ $unit->id }}][metode_l]" value="1" {{ $unitAssess?->metode_observasi_langsung ?? true ? 'checked' : '' }} class="rounded text-blue-600 border-zinc-300">
                                                <span>L</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer font-bold" title="Verifikasi Portofolio (Tidak Langsung)">
                                                <input type="checkbox" name="units[{{ $unit->id }}][metode_tl]" value="1" {{ $unitAssess?->metode_portofolio ?? false ? 'checked' : '' }} class="rounded text-blue-600 border-zinc-300">
                                                <span>TL</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer font-bold" title="Pertanyaan Lisan/Tertulis (Tambahan)">
                                                <input type="checkbox" name="units[{{ $unit->id }}][metode_t]" value="1" {{ $unitAssess?->metode_pertanyaan ?? true ? 'checked' : '' }} class="rounded text-blue-600 border-zinc-300">
                                                <span>T</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="inline-flex items-center gap-1 bg-zinc-100 p-1 rounded-xl border border-zinc-200">
                                            <label class="px-3 py-1 rounded-lg font-bold text-xs cursor-pointer transition" :class="unitRek[{{ $unit->id }}] === 'K' ? 'bg-emerald-600 text-white shadow-xs' : 'text-zinc-600 hover:text-zinc-900'">
                                                <input type="radio" name="units[{{ $unit->id }}][rekomendasi_unit]" value="K" x-model="unitRek[{{ $unit->id }}]" class="sr-only">
                                                <span>K</span>
                                            </label>
                                            <label class="px-3 py-1 rounded-lg font-bold text-xs cursor-pointer transition" :class="unitRek[{{ $unit->id }}] === 'BK' ? 'bg-rose-600 text-white shadow-xs' : 'text-zinc-600 hover:text-zinc-900'">
                                                <input type="radio" name="units[{{ $unit->id }}][rekomendasi_unit]" value="BK" x-model="unitRek[{{ $unit->id }}]" class="sr-only">
                                                <span>BK</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rekomendasi Keseluruhan Asesmen -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-zinc-100 pb-3">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Keputusan Rekomendasi Akhir Asesor</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Tetapkan rekomendasi kompetensi peserta untuk keseluruhan skema.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-2">Rekomendasi Akhir Hasil Uji <span class="text-rose-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                <input type="radio" name="rekomendasi_akhir" value="K" x-model="rekomendasiAkhir" class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-emerald-700">Kompeten (K)</div>
                                    <div class="text-zinc-500 text-[11px]">Seluruh kriteria unjuk kerja telah didemonstrasikan dengan memuaskan.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                                <input type="radio" name="rekomendasi_akhir" value="BK" x-model="rekomendasiAkhir" class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-rose-700">Belum Kompeten (BK)</div>
                                    <div class="text-zinc-500 text-[11px]">Terdapat kriteria unjuk kerja yang belum terpenuhi. Memerlukan asesmen ulang.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Komentar & Catatan Asesor Penguji</label>
                            <textarea name="komentar_asesor" rows="2" placeholder="Catatan jalannya asesmen..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('komentar_asesor', $ak02->komentar_asesor) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Tindak Lanjut yang Dibutuhkan (Jika BK)</label>
                            <textarea name="tindak_lanjut" rows="2" placeholder="Materi yang perlu dipelajari kembali..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('tindak_lanjut', $ak02->tindak_lanjut) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tanda Tangan Canvas Asesor -->
                <div class="border-t border-zinc-100 pt-5 space-y-3">
                    <label class="block text-xs font-bold text-zinc-800">
                        Tanda Tangan Digital Asesor Penguji <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-zinc-300 rounded-xl bg-zinc-50 p-2 inline-block">
                        <canvas id="ak02AsesorSignCanvas" width="450" height="150" class="bg-white rounded-lg border border-zinc-200 cursor-crosshair touch-none"></canvas>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span class="text-[11px] text-zinc-400">Pengesahan Hasil Rekaman Asesmen Resmi</span>
                            <button type="button" id="clearAk02SignBtn" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition">
                                Bersihkan (Clear)
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="ttd_asesor" id="ak02_ttd_asesor_input">
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
                    <span>Simpan Rekaman Asesmen (FR-AK-02)</span>
                </button>
            </div>

        </form>

    </div>

    <script>
        function ak02Matrix() {
            return {
                rekomendasiAkhir: '{{ old('rekomendasi_akhir', $ak02->rekomendasi_akhir ?: 'K') }}',
                unitRek: {
                    @foreach ($application->scheme->competencyUnits as $unit)
                        {{ $unit->id }}: '{{ $unitsAssessed->get($unit->id)?->rekomendasi_unit ?? 'K' }}',
                    @endforeach
                },

                markAllKompeten() {
                    this.rekomendasiAkhir = 'K';
                    for (const unitId in this.unitRek) {
                        this.unitRek[unitId] = 'K';
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('ak02AsesorSignCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearAk02SignBtn');
            const hiddenInput = document.getElementById('ak02_ttd_asesor_input');
            const form = document.getElementById('ak02Form');

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
