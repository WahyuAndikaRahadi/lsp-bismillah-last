@php
    $jadwal = $jadwal ?? $schedule;
    $assessments = $assessments ?? $jadwal->assessments ?? collect();
    $selectedAssessmentId = $selectedAssessmentId ?? request('assessment');

    $itemsDef = [
        1 => [
            'title' => 'Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi.',
            'options' => [
                'Memerlukan dukungan pembaca, penerjemah, pelayan, penulis untuk merekam jawaban asesi.',
                'Melakukan asesmen verbal (gunakan pertanyaan lisan/pertanyaan wawancara) dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
                'Menggunakan Hasil produksi.',
                'Menggunakan Ceklis observasi/demonstrasi.',
                'Menggunakan daftar instruksi terstruktur.',
            ],
        ],
        2 => [
            'title' => 'Penyediaan dukungan pembaca, penerjemah, pelayan, penulis.',
            'options' => [
                'Menggunakan pertanyaan lisan dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
                'Menggunakan pertanyaan wawancara dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
            ],
        ],
        3 => [
            'title' => 'Penggunaan teknologi adaptif atau peralatan khusus. (Tidak dapat menggunakan teknologi adaptif, misal: mengoperasikan komputer dan printer, peralatan digital dsb).',
            'options' => [
                'Ceklis observasi/demonstrasi',
                'Demonstrasi',
                'Pertanyaan lisan',
                'Pertanyaan tertulis',
                'Pertanyaan wawancara',
                'Daftar instruksi terstruktur',
                'Ceklis verifikasi portofolio',
                'Menggunakan dukungan operator komputer',
            ],
        ],
        4 => [
            'title' => 'Pelaksanaan asesmen secara fleksibel karena alasan keletihan atau keperluan pengobatan.',
            'options' => [
                'Menggunakan juru tulis.',
                'Menggunakan kameramen perekam video/audio.',
                'Memperbolehkan periode waktu yang lebih panjang untuk menyelesaikan tugas pekerjaan dalam asesmen.',
                'Melakukan tugas pekerjaan dalam asesmen dengan waktu lebih pendek.',
                'Menggunakan instruksi-instruksi spesifik pada proyek yang dapat dilakukan pada berbagai tingkatan.',
            ],
        ],
        5 => [
            'title' => 'Penyediaan peralatan asesmen berupa braille, audio/video-tape.',
            'options' => [
                'Menggunakan pertanyaan lisan.',
                'Menggunakan pertanyaan wawancara.',
            ],
        ],
        6 => [
            'title' => 'Penyesuaian tempat fisik/lingkungan asesmen.',
            'options' => [
                'Pertanyaan lisan.',
                'Pertanyaan tulis.',
                'Pertanyaan wawancara.',
                'Ceklis Verifikasi portofolio.',
                'Ceklis reviu produk.',
                'Daftar instruksi terstruktur.',
            ],
        ],
        7 => [
            'title' => 'Pertimbangan umur/usia lanjut/gender asesi. (Adanya perbedaan usia dengan asesor yang lebih muda).',
            'options' => [
                'Menggunakan studi kasus / daftar instruksi terstruktur.',
                'Menggunakan instrumen asesmen dengan huruf normal jangan terlalu kecil.',
                'Menggunakan asesor dengan jenis kelamin yang sama dengan asesi.',
                'Menggunakan instrumen asesmen yang sama walaupun berbeda jenis kelamin (tidak membedakan jenis kelamin).',
            ],
        ],
        8 => [
            'title' => 'Pertimbangan budaya/tradisi/agama.',
            'options' => [
                'Menggunakan studi kasus daftar instruksi terstruktur.',
                'Menggunakan asesor tanpa pertimbangan budaya/tradisi/agama.',
                'Menggunakan instrumen asesmen yang sama walaupun berbeda budaya/tradisi/agama.',
            ],
        ],
    ];
@endphp
@extends('layouts.app', ['title' => 'FR.AK.07 - Penyesuaian yang Wajar dan Beralasan'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Header Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <a href="{{ route('asesor.jadwal.asesi', $jadwal->id) }}" class="text-xs text-lsp-navy hover:text-lsp-navy-dark font-bold inline-flex items-center gap-1">&larr; Kembali ke Daftar Asesi</a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-lsp-title tracking-tight mt-1 font-heading">FR.AK.07 CEKLIS PENYESUAIAN YANG WAJAR DAN BERALASAN</h1>
            <p class="text-xs text-lsp-muted font-medium">Lembaga Sertifikasi Profesi Praktisi Otomotif Indonesia (LSP PrO Indo)</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('asesor.schedule.ruang-uji', $jadwal->id) }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-xs">
                Lewati & Langsung ke Ruang Uji &rarr;
            </a>
        </div>
    </div>

    {{-- Info Box (Pengisian Opsional) --}}
    <div class="bg-blue-50/70 border border-blue-200 rounded-xl p-4 text-xs text-blue-900 shadow-xs flex items-start gap-3">
        <span class="text-base text-blue-600 font-bold">ℹ</span>
        <div>
            <span class="font-bold">Formulir Bersifat Opsional:</span> Pengisian FR.AK07 hanya dilakukan apabila terdapat asesi yang membutuhkan penyesuaian wajar berdasarkan keterbatasan karakteristik atau kondisi khusus. Apabila tidak ada penyesuaian khusus, formulir dapat dibiarkan kosong (default) dan Anda dapat langsung klik tombol <strong>"Simpan FR.AK07 & Lanjut ke Ruang Uji"</strong> atau <strong>"Lewati & Langsung ke Ruang Uji"</strong>.
        </div>
    </div>

    <form action="{{ route('asesor.ak07.store', $jadwal->id) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Meta Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="bg-lsp-navy text-white px-6 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <span class="text-xs font-mono font-bold tracking-wider uppercase text-amber-200">Data Asesmen & Identifikasi Jadwal</span>
                <span class="text-xs text-slate-200">{{ $jadwal->code }}</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                        <span class="text-slate-500 font-semibold block mb-0.5">Skema Sertifikasi:</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $jadwal->scheme->name ?? '-' }}</div>
                        <div class="font-mono text-slate-600 mt-0.5">Nomor: {{ $jadwal->scheme->code ?? '-' }}</div>
                    </div>
                    <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                        <span class="text-slate-500 font-semibold block mb-0.5">Tempat Uji Kompetensi (TUK):</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $jadwal->tuk_name ?? 'Sewaktu / Tempat Kerja / Mandiri' }}</div>
                        <div class="text-slate-600 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($jadwal->date)->isoFormat('D MMMM Y') }}</div>
                    </div>
                </div>

                {{-- Pilih Asesi Sasaran (Batch) --}}
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Asesi yang Diberikan Penyesuaian (Pilih Asesi):
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($assessments as $assessment)
                            <label class="flex items-center space-x-2.5 p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" name="assessment_ids[]" value="{{ $assessment->id }}"
                                    class="rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy w-4 h-4"
                                    {{ (empty(old('assessment_ids')) || in_array($assessment->id, old('assessment_ids', [])) || (string)$selectedAssessmentId === (string)$assessment->id) ? 'checked' : '' }}>
                                <div class="truncate">
                                    <div class="font-bold text-slate-900 text-xs truncate">{{ $assessment->asesi->full_name }}</div>
                                    <div class="text-[10px] text-slate-500 truncate">{{ $assessment->asesi->email }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Panduan Bagi Asesor Box --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-3">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide border-b border-slate-200 pb-2">
                PANDUAN BAGI ASESOR
            </h2>
            <ul class="text-xs text-slate-700 space-y-1.5 list-disc list-inside leading-relaxed">
                <li>Formulir ini dapat digunakan (sebelum pra asesmen, saat pelaksanaan pra asesmen, setelah pra asesmen) jika ada asesi yang mempunyai keterbatasan sesuai karakteristik yang dimilikinya sehingga diperlukan penyesuaian yang wajar dan beralasan atau jika rencana asesmen dan perangkat asesmen tidak sesuai dengan acuan pembanding, potensi asesi dan konteks asesi atau jika asesi merasa keletihan, sakit, serta jika kondisi alam, listrik padam, dll.</li>
                <li>Berilah tanda centang pada kolom potensi asesi jika relevan.</li>
                <li>Jika diperlukan penyesuaian, pilih <strong>Ya</strong> pada kolom dan beri tanda centang pada opsi modifikasi di kolom keterangan (pilihan boleh lebih dari satu).</li>
                <li>Jika tidak diperlukan penyesuaian, biarkan opsi pada pilihan <strong>Tidak</strong> atau kosongkan.</li>
            </ul>

            {{-- Potensi Asesi --}}
            <div class="mt-4 pt-4 border-t border-slate-200">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block mb-2">Potensi Asesi:</span>
                <div class="space-y-2 text-xs">
                    @php
                        $potensiList = [
                            'Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi.',
                            'Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.',
                            'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi.',
                            'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.',
                            'Pelatihan / belajar mandiri atau otodidak.',
                        ];
                    @endphp
                    @foreach($potensiList as $pIndex => $pText)
                        <label class="flex items-start space-x-2.5 cursor-pointer">
                            <input type="checkbox" name="potensi[]" value="{{ $pText }}" class="mt-0.5 rounded border-slate-400 text-brand-600 focus:ring-brand-500 w-4 h-4">
                            <span class="text-slate-800">{{ $pText }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabel 8 Karakteristik Asesi Sesuai Dokumen MUK PDF --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="bg-lsp-navy text-white px-6 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-amber-200">
                    Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi (Karakteristik Asesi)
                </h2>
                <span class="text-xs text-slate-200 font-mono">8 Indikator Karakteristik</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-800 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center border-r border-slate-200">No</th>
                            <th class="px-4 py-3 w-2/5 border-r border-slate-200">Mengidentifikasi Persyaratan Modifikasi & Kontekstualisasi</th>
                            <th class="px-4 py-3 w-36 text-center border-r border-slate-200">Diperlukan Penyesuaian?</th>
                            <th class="px-4 py-3">Keterangan & Bentuk Penyesuaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($itemsDef as $idx => $def)
                            <tr class="hover:bg-slate-50/70 transition align-top">
                                <td class="px-4 py-4 text-center font-bold font-mono border-r border-slate-200 text-slate-700">
                                    {{ $idx }}
                                    <input type="hidden" name="items[{{ $idx }}][title]" value="{{ $def['title'] }}">
                                </td>
                                <td class="px-4 py-4 border-r border-slate-200 text-slate-900 font-medium leading-relaxed">
                                    {{ $def['title'] }}
                                </td>
                                <td class="px-4 py-4 border-r border-slate-200 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <label class="inline-flex items-center space-x-1 cursor-pointer">
                                            <input type="radio" name="items[{{ $idx }}][needed]" value="YA" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                            <span class="font-bold text-emerald-800">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-1 cursor-pointer">
                                            <input type="radio" name="items[{{ $idx }}][needed]" value="TIDAK" checked class="text-slate-600 focus:ring-slate-500 w-4 h-4">
                                            <span class="text-slate-700">Tidak</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-4 space-y-2">
                                    <div class="space-y-1.5">
                                        @foreach($def['options'] as $opt)
                                            <label class="flex items-start space-x-2 text-slate-700 cursor-pointer">
                                                <input type="checkbox" name="items[{{ $idx }}][options][]" value="{{ $opt }}" class="mt-0.5 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy w-3.5 h-3.5">
                                                <span>{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="pt-1">
                                        <input type="text" name="items[{{ $idx }}][custom_note]" placeholder="Keterangan lain / catatan penyesuaian spesifik..." class="w-full text-xs px-2.5 py-1.5 rounded border border-slate-300 focus:ring-1 focus:ring-lsp-navy focus:outline-none bg-white">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Hasil Kesepakatan Asesor dan Asesi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide border-b border-slate-200 pb-2">
                HASIL KESEPAKATAN ANTARA ASESOR DAN ASESI
            </h2>
            <p class="text-xs text-slate-600">
                Tentukan apakah diperlukan penyesuaian berdasarkan hasil identifikasi di atas. Jika diperlukan penyesuaian, tuliskan rincian kesepakatan:
            </p>

            <div class="flex items-center space-x-6 text-xs pt-1">
                <span class="font-bold text-slate-800">Status Kesepakatan Penyesuaian:</span>
                <label class="inline-flex items-center space-x-1.5 cursor-pointer">
                    <input type="radio" name="agreement_status" value="YA" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="font-bold text-emerald-800">Diperlukan Penyesuaian</span>
                </label>
                <label class="inline-flex items-center space-x-1.5 cursor-pointer">
                    <input type="radio" name="agreement_status" value="TIDAK" checked class="text-slate-600 focus:ring-slate-500 w-4 h-4">
                    <span class="font-medium text-slate-700">Tidak Diperlukan Penyesuaian (Standar Baku)</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs pt-2">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">1) Acuan Pembanding Asesmen:</label>
                    <input type="text" name="agreement_benchmark" placeholder="Contoh: SKKNI No. 097/2018..." class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-lsp-navy focus:outline-none bg-slate-50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">2) Metode Asesmen yang Disepakati:</label>
                    <input type="text" name="agreement_method" placeholder="Contoh: Observasi Demonstrasi & Wawancara..." class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-lsp-navy focus:outline-none bg-slate-50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">3) Instrumen Asesmen Disepakati:</label>
                    <input type="text" name="agreement_instrument" placeholder="Contoh: FR.IA.01, FR.IA.02..." class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-lsp-navy focus:outline-none bg-slate-50">
                </div>
            </div>

            <div class="pt-2 text-xs">
                <label class="block font-semibold text-slate-700 mb-1">4) Catatan Tambahan / Uraian Penyesuaian:</label>
                <textarea name="agreement_notes" rows="2" placeholder="Catatan kesepakatan penyesuaian khusus (jika ada)..." class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-lsp-navy focus:outline-none bg-slate-50"></textarea>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
            <a href="{{ route('asesor.schedule.ruang-uji', $jadwal->id) }}" class="w-full sm:w-auto text-center px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-xs">
                &larr; Lewati (Tanpa Penyesuaian) ke Ruang Uji
            </a>
            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('asesor.jadwal.asesi', $jadwal->id) }}" class="px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Kembali ke Daftar Asesi</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold uppercase tracking-wider bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-xs transition">
                    Simpan FR.AK07 & Lanjut ke Ruang Uji &rarr;
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
