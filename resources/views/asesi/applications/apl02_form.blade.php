<x-app-layout title="FR-APL-02: Asesmen Mandiri — {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6" x-data="apl02SelfAssessment()">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                    <span>Portal Asesi</span>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('asesi.applications.show', $application) }}" class="hover:underline font-mono">{{ $application->nomor_pendaftaran }}</a>
                    <span>&rsaquo;</span>
                    <span class="text-zinc-900 font-medium">Pengisian FR-APL-02</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                        FR-APL-02: Asesmen Mandiri
                    </h1>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-700">
                        {{ $application->nomor_pendaftaran }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        Skema: {{ $application->scheme->nama_skema }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-zinc-500 mt-1">
                    Lakukan penilaian mandiri secara jujur atas seluruh Kriteria Unjuk Kerja (KUK) pada skema sertifikasi yang Anda ajukan.
                </p>
            </div>

            <!-- Batch Evaluator Button -->
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" @click="markAllKompeten()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tandai Semua Kompeten (K)</span>
                </button>
            </div>
        </div>

        <form action="{{ route('asesi.applications.apl02.store', $application) }}" method="POST" id="apl02Form" class="space-y-8">
            @csrf

            <!-- Header Info Blangko APL-02 -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-7 space-y-4">
                <div class="border-b border-zinc-100 pb-3">
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">Panduan Pengisian Asesmen Mandiri</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Baca setiap Kriteria Unjuk Kerja (KUK) dengan seksama. Pilih <strong>K (Kompeten)</strong> jika Anda telah mampu dan memiliki bukti kompetensi, atau pilih <strong>BK (Belum Kompeten)</strong> jika Anda belum menguasai kriteria tersebut.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-zinc-500 block">Nama Peserta (Asesi):</span>
                        <span class="font-bold text-zinc-900">{{ $application->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Asesor Penguji:</span>
                        <span class="font-bold text-zinc-900">{{ $application->asesor?->name ?? 'Belum ditentukan oleh Admin' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Tempat Uji Kompetensi (TUK):</span>
                        <span class="font-bold text-zinc-900">{{ $application->schedule?->tuk->nama_tuk ?? 'TUK Terverifikasi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabel Unit Kompetensi, Elemen, dan KUK -->
            @foreach ($application->scheme->competencyUnits as $unitIdx => $unit)
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
                    <!-- Unit Header -->
                    <div class="p-5 bg-lsp-navy text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Unit Kompetensi #{{ $unitIdx + 1 }}</span>
                            <h3 class="text-sm font-bold mt-0.5">{{ $unit->judul_unit }}</h3>
                            <span class="font-mono text-xs text-zinc-300">Kode: {{ $unit->kode_unit }} &bull; Standar: {{ $unit->jenis_standar }}</span>
                        </div>
                        <div class="shrink-0">
                            <span class="text-xs px-2.5 py-1 rounded-full bg-white/10 text-zinc-200 border border-white/20 font-medium">
                                {{ $unit->elements->count() }} Elemen Kompetensi
                            </span>
                        </div>
                    </div>

                    <!-- Elemen & KUK List -->
                    <div class="divide-y divide-zinc-200">
                        @foreach ($unit->elements as $elemIdx => $elem)
                            <div class="p-5 bg-zinc-50/50">
                                <div class="mb-3">
                                    <span class="text-[11px] font-bold text-zinc-500 uppercase">Elemen {{ $elemIdx + 1 }}</span>
                                    <h4 class="text-xs font-bold text-zinc-900">{{ $elem->nama_elemen }}</h4>
                                </div>

                                <div class="space-y-3">
                                    @foreach ($elem->criterias as $kuk)
                                        @php
                                            $existingVal = $existingItems->get($kuk->id)?->penilaian_mandiri ?? 'K';
                                            $existingProof = $existingItems->get($kuk->id)?->bukti_pendukung ?? 'Portofolio / Dokumen APL-01';
                                        @endphp
                                        <div class="p-3.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition flex flex-col md:flex-row md:items-center justify-between gap-4 kuk-row" id="row_kuk_{{ $kuk->id }}">
                                            <div class="flex-1 space-y-1">
                                                <div class="flex items-start gap-2">
                                                    <span class="font-mono text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 shrink-0">
                                                        KUK {{ $kuk->nomor_kuk }}
                                                    </span>
                                                    <p class="text-xs text-zinc-800 leading-relaxed font-medium">
                                                        {{ $kuk->pernyataan }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="shrink-0 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                                <!-- Pilihan Mandiri: K vs BK -->
                                                <div class="flex items-center gap-2 bg-zinc-100 p-1 rounded-xl border border-zinc-200">
                                                    <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition" :class="penilaian[{{ $kuk->id }}] === 'K' ? 'bg-emerald-600 text-white shadow-xs' : 'text-zinc-600 hover:text-zinc-900'">
                                                        <input type="radio" name="items[{{ $kuk->id }}][penilaian_mandiri]" value="K" x-model="penilaian[{{ $kuk->id }}]" class="sr-only">
                                                        <span>K (Kompeten)</span>
                                                    </label>

                                                    <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition" :class="penilaian[{{ $kuk->id }}] === 'BK' ? 'bg-rose-600 text-white shadow-xs' : 'text-zinc-600 hover:text-zinc-900'">
                                                        <input type="radio" name="items[{{ $kuk->id }}][penilaian_mandiri]" value="BK" x-model="penilaian[{{ $kuk->id }}]" class="sr-only">
                                                        <span>BK (Belum)</span>
                                                    </label>
                                                </div>

                                                <!-- Bukti Pendukung -->
                                                <div class="w-full sm:w-56">
                                                    <input type="text" name="items[{{ $kuk->id }}][bukti_pendukung]" value="{{ $existingProof }}" placeholder="Bukti portofolio..." class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Pengesahan & Tanda Tangan Digital Asesi -->
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-zinc-100 pb-4">
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Pengesahan Asesmen Mandiri</span>
                    <h2 class="text-base font-bold text-zinc-900">Pernyataan & Tanda Tangan Asesi</h2>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-bold text-zinc-800">
                        Tanda Tangan Digital Asesi <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-zinc-300 rounded-xl bg-zinc-50 p-2 inline-block">
                        <canvas id="apl02SignatureCanvas" width="450" height="160" class="bg-white rounded-lg border border-zinc-200 cursor-crosshair touch-none"></canvas>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span class="text-[11px] text-zinc-400">Bubuhkan tanda tangan Anda di atas</span>
                            <button type="button" id="clearApl02SignBtn" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition">
                                Bersihkan (Clear)
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="ttd_asesi" id="apl02_ttd_input" value="{{ $application->ttd_asesi }}">
                </div>

                <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-xl">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="pernyataan_asesmen" value="1" required checked class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-zinc-300">
                        <span class="text-xs text-zinc-700 leading-relaxed">
                            Saya menyatakan bahwa data penilaian mandiri (FR-APL-02) yang saya isi di atas adalah benar dan sesuai dengan kompetensi serta portofolio yang saya miliki. Saya siap membuktikan kompetensi tersebut pada saat pelaksanaan uji kompetensi.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Action Submit Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('asesi.applications.show', $application) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <div class="flex items-center gap-3">
                    <button type="submit" name="action_draft" value="1" class="px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                        Simpan Sebagai Draft
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Submit Asesmen Mandiri (FR-APL-02)</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script>
        function apl02SelfAssessment() {
            return {
                penilaian: {
                    @foreach ($application->scheme->competencyUnits as $unit)
                        @foreach ($unit->elements as $elem)
                            @foreach ($elem->criterias as $kuk)
                                {{ $kuk->id }}: '{{ $existingItems->get($kuk->id)?->penilaian_mandiri ?? 'K' }}',
                            @endforeach
                        @endforeach
                    @endforeach
                },

                markAllKompeten() {
                    for (const kukId in this.penilaian) {
                        this.penilaian[kukId] = 'K';
                    }
                }
            };
        }

        // Signature Canvas
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('apl02SignatureCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearApl02SignBtn');
            const hiddenInput = document.getElementById('apl02_ttd_input');
            const form = document.getElementById('apl02Form');

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
