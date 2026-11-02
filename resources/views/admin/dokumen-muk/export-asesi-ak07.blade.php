@extends('layouts.print-muk')

@section('title', 'FR.AK.07 Penyesuaian yang Wajar - ' . ($asesi->full_name ?? ''))

@section('content')
<style>
    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Calibri, 'Carlito', sans-serif;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        height: 297mm;
        box-sizing: border-box;
        padding: 7.5mm 11.5mm 0 24.0mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .kop-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 32mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 26mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 138.9mm;
        text-align: center;
        font-size: 11pt;
        line-height: 1.25;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.2;
        margin-bottom: 2.0mm;
    }
    .meta-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 9.5pt;
        margin-bottom: 1.0mm;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: middle;
    }
    .box-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.2;
        margin-bottom: 2.0mm;
    }
    .box-table td, .box-table th {
        border: 1pt solid #000;
        padding: 0.6mm 1.5mm;
        vertical-align: top;
    }
    .check-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8pt;
        line-height: 1.15;
    }
    .check-table td, .check-table th {
        border: 1pt solid #000;
        padding: 0.4mm 1.0mm;
        box-sizing: border-box;
        vertical-align: middle;
    }
    .check-table th {
        font-weight: bold;
        text-align: center;
    }
    .center-cell {
        text-align: center;
        vertical-align: middle;
    }
    .box-sym {
        font-family: 'MS Gothic', 'DejaVu Sans', sans-serif;
        font-size: 9pt;
    }
</style>

@php
    $schemeName = $scheme->name ?? 'Mekanik Junior';
    $schemeCode = $scheme->code ?? '05/LSP PrO Indo/I/2026';
    $tukName = $schedule->tuk_name ?? 'Sewaktu/Tempat Kerja/Mandiri*';
    $assessorName = $assessor->full_name ?? ($assessor->name ?? ($leadAssessor->full_name ?? ''));
    $asesiName = $asesi->full_name ?? '';
    $dateStr = !empty($schedule->date) ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '';
@endphp

{{-- ========================================== PAGE 1 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <div class="doc-main-title">
        FR.AK.07.  CEKLIS PENYESUAIAN YANG WAJAR dan BERALASAN
    </div>

    <table class="meta-table">
        <colgroup>
            <col style="width: 48mm;">
            <col style="width: 18mm;">
            <col style="width: 5mm;">
            <col style="width: 99.9mm;">
        </colgroup>
        <tbody>
            <tr style="height: 6.5mm;">
                <td rowspan="2">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
                <td>Judul</td>
                <td style="text-align: center;">:</td>
                <td style="font-style: italic;">{{ $schemeName }}</td>
            </tr>
            <tr style="height: 6.2mm;">
                <td>Nomor</td>
                <td style="text-align: center;">:</td>
                <td>{{ $schemeCode }}</td>
            </tr>
            <tr style="height: 6.1mm;">
                <td colspan="2">TUK</td>
                <td style="text-align: center;">:</td>
                <td>Sewaktu/Tempat Kerja/Mandiri*</td>
            </tr>
            <tr style="height: 6.1mm;">
                <td colspan="2">Nama Asesor</td>
                <td style="text-align: center;">:</td>
                <td>{{ $assessorName }}</td>
            </tr>
            <tr style="height: 6.2mm;">
                <td colspan="2">Nama Asesi</td>
                <td style="text-align: center;">:</td>
                <td>{{ $asesiName }}</td>
            </tr>
            <tr style="height: 6.2mm;">
                <td colspan="2">Tanggal</td>
                <td style="text-align: center;">:</td>
                <td>{{ $dateStr }}</td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 8pt; font-style: italic; margin-bottom: 1.5mm;">*Coret yang tidak perlu</div>

    <table class="box-table">
        <tr>
            <th style="font-weight: bold; text-align: left; height: 9.7mm;">PANDUAN BAGI ASESOR</th>
        </tr>
        <tr>
            <td style="height: 66.4mm;">
                •  Formulir ini dapat digunakan (sebelum pra asesmen, saat pelaksanaan pra asesmen, setelah pra asesmen)* jika ada asesi yang mempunyai keterbatasan sesuai karakteristik yang dimilikinya sehinga diperlukan penyesuaian yang wajar dan beralasan atau jika rencana asesmen dan perangkat asesmen tidak sesuai dengan acuan pembanding, potensi asesi dan konteks asesi atau ika asesi merasa keletihan, sakit, serta jika kondisi alam, listrik padam,……..<br>
                •  Coretlah pada tanda * yang tidak sesuai.<br>
                •  Berilah tanda √ pada kotak ‘☐ ‘ pada kolom potensi asesi<br>
                •  Jika jawaban Ya pada tanda ** berilah tanda √ di kotak ‘☐‘ kolom Ya dan selanjutanya pada kolom keterangan berilah tanda √ di kotak ‘☐‘ yang tersedia, pilihan boleh lebih dari satu.<br>
                •  atau jika jawaban Tidak pada tanda ** berilah tanda √ di kotak ‘☐ ‘ kolom tidak, dan tidak perlu memberi tanda tanda √ di kotak ‘☐ ‘ kolom keterangan.
            </td>
        </tr>
    </table>

    <table class="box-table" style="margin-bottom: 2mm;">
        <colgroup>
            <col style="width: 35mm;">
            <col style="width: 7mm;">
            <col style="width: 128.9mm;">
        </colgroup>
        <tbody>
            <tr style="height: 9.7mm;">
                <td rowspan="5" style="font-weight: bold; vertical-align: middle;">Potensi Asesi</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi</td>
            </tr>
            <tr style="height: 9.6mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pelatihan / belajar mandiri atau otodidak.</td>
            </tr>
        </tbody>
    </table>

    <table class="check-table">
        <colgroup>
            <col style="width: 12.5mm;">
            <col style="width: 57.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 20.0mm;">
            <col style="width: 5.0mm;">
            <col style="width: 67.0mm;">
        </colgroup>
        <thead>
            <tr style="height: 7.0mm;">
                <th rowspan="2">No</th>
                <th rowspan="2">Mengidentifikasi Persyaratan<br>Modifikasi dan Kontekstualisasi<br>(karaktersitik asesi):</th>
                <th colspan="2">Diperlukan<br>penyesuaian)**</th>
                <th colspan="2" rowspan="2">Keterangan</th>
            </tr>
            <tr style="height: 7.0mm;">
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 14.4mm;">
                <td rowspan="2" class="center-cell" style="vertical-align: middle;">1</td>
                <td rowspan="2" style="vertical-align: middle;">Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi.</td>
                <td rowspan="2" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="2" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Memerlukan dukungan pembaca, penerjemah, pelayan, penulis. untuk merekam jawaban asesi.</td>
            </tr>
            <tr style="height: 19.1mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Melakukan asesmen verbal (gunakan pertanyaan lisan/pertanyaan wawancara) dengan dilengkapi gambar diagram dan bentuk-benbentuk visual.</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ========================================== PAGE 2 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="check-table">
        <colgroup>
            <col style="width: 12.5mm;">
            <col style="width: 57.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 20.0mm;">
            <col style="width: 5.0mm;">
            <col style="width: 67.0mm;">
        </colgroup>
        <tbody>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan Hasil produksi</td>
            </tr>
            <tr style="height: 9.6mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Mengunakan Ceklis observasi/demonstrasi. <span class="box-sym">&#9744;</span></td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan daftar instruksi terstruktur. <span class="box-sym">&#9744;</span>………………………………….</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td rowspan="3" class="center-cell" style="vertical-align: middle;">2</td>
                <td rowspan="3" style="vertical-align: middle;">Penyediaan dukungan pembaca, penerjemah, pelayan, penulis.</td>
                <td rowspan="3" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="3" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan pertanyaan lisan dengan dilengkapi gambar diagram dan bentuk-bentuk visual.</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan pertanyaan wawancara dengan dilengkapi gambar diagram dan bentuk-bentuk visual.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>……………………………………..</td>
            </tr>
            <tr style="height: 9.6mm;">
                <td rowspan="7" class="center-cell" style="vertical-align: middle;">3</td>
                <td rowspan="7" style="vertical-align: middle;">Penggunaan teknologi adaptif atau peralatan khusus. (Tidak dapat menggunakan teknologi adaptif (misal: mengoperasikan komputer dan printer, peralatan digital dsb).</td>
                <td rowspan="7" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="7" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Ceklis observasi/demonstrasi Demonstrasi. <span class="box-sym">&#9744;</span>Pertanyaan lisan</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pertanyaan tertulis.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pertanyaan wawancara.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Daftar instruksi terstruktur.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Ceklis verifikasi portofolio.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan dukungan operator komputer.</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>………………………………………..</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td rowspan="6" class="center-cell" style="vertical-align: middle;">4</td>
                <td rowspan="6" style="vertical-align: middle;">Pelaksanaan asesmen secara fleksibel karena alasan keletihan atau keperluan pengobatan.</td>
                <td rowspan="6" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="6" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan juru tulis.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan kamaramen perekam vidio/ataudio.</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Memperbolehkan periode waktu yang lebih panjang untuk menyelesaikan tugas pekrejaan dalam asesmen.</td>
            </tr>
            <tr style="height: 19.1mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Melakukan tugas pekerjaan dalam asesmen dengan waktu lebih pendek.</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan instruksi-instruksi spesifik pada proyek yang dapat dilakukan pada berbagai tingkatan.</td>
            </tr>
            <tr style="height: 19.2mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>…………………………………………..</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td rowspan="3" class="center-cell" style="vertical-align: middle;">5</td>
                <td rowspan="3" style="vertical-align: middle;">Penyediaan peralatan asesmen berupa braille, audio/video-tape.</td>
                <td rowspan="3" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="3" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan pertanyaan lisan.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan pertanyaan wawancara.</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>……………………………………..</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell">6</td>
                <td>Penyesuaian tempat</td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pertanyaan lisan.</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ========================================== PAGE 3 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="check-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 12.5mm;">
            <col style="width: 57.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 20.0mm;">
            <col style="width: 5.0mm;">
            <col style="width: 67.0mm;">
        </colgroup>
        <tbody>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td>fisik/lingkungan asesmen</td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pertanyaan tulis.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Pertanyaan wawancara.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Ceklis Verifikasi portofolio.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Ceklis reviu produk.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Daftar instruksi terstruktur.</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"></td>
                <td></td>
                <td class="center-cell"></td>
                <td class="center-cell"></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>……………………………………..</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td rowspan="5" class="center-cell" style="vertical-align: middle;">7</td>
                <td rowspan="5" style="vertical-align: middle;">Pertimbangan umur/usia lanjut/gender asesi. (Adanya perbedaan usia dengan asesor yang lebih muda).</td>
                <td rowspan="5" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="5" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan studi kasus/daftar instruksi terstrukut</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan instrumen asesmen dengan huruf normal jangan terlalu kecil. <span class="box-sym">&#9744;</span></td>
            </tr>
            <tr style="height: 9.7mm;">
                <td class="center-cell"></td>
                <td>Menggunakan asesor dengan jenis kelamin yang sama dengan asesi.</td>
            </tr>
            <tr style="height: 28.6mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan instrumen asesmen yang sama walaupun berbeda jenis kelamain (tidak boleh memberi tanda tambahan pada instrumen asesmen yang digunakan dengan tujuan untuk membedakan jenis kelamin).</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>……………………………………..</td>
            </tr>
            <tr style="height: 9.7mm;">
                <td rowspan="4" class="center-cell" style="vertical-align: middle;">8</td>
                <td rowspan="4" style="vertical-align: middle;">Pertimbangan budaya/tradisi/agama.</td>
                <td rowspan="4" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td rowspan="4" class="center-cell" style="vertical-align: middle;"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan studi kasus daftar instruksi terstrukut <span class="box-sym">&#9744;</span></td>
            </tr>
            <tr style="height: 9.6mm;">
                <td class="center-cell"></td>
                <td>Menggunakan asesor tanpa pertimbangan budaya/tradisi/agama.</td>
            </tr>
            <tr style="height: 14.4mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>Menggunakan instrumen asesmen yang sama walaupun berbeda budaya/tradisi/agama).</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td>……………………………………..</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 8.5pt; line-height: 1.35; margin-bottom: 2mm; margin-top: 5mm;">
        Hasil kesepakatan antara asesi dengan asesor (Diperlukan penyesuaian atau tidak)*<br>
        Jika diperlukan penyesuaian maka, tuliskan kesepakatan dibawah ini:<br><br>
        1) Acuan Pembanding  Asesmen: ( tuliskan nama acuan pembanding yang disepakati)<br><br>
        2) Metode Asesmen: ( tuliskan nama metode yang disepakati )<br><br>
        3) Instrumen Asesmen: (kode formulir instrumen asesmen yang disepakati )
    </div>

    <table class="box-table" style="margin-top: 3mm;">
        <colgroup>
            <col style="width: 85mm;">
            <col style="width: 85.9mm;">
        </colgroup>
        <tbody>
            <tr style="height: 9.7mm;">
                <td>Nama Asesor : {{ $assessorName }}</td>
                <td>Tanggal dan Tanda Tangan Asesor:</td>
            </tr>
            <tr style="height: 17.1mm;">
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ========================================== PAGE 4 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="box-table" style="margin-top: 5mm; margin-bottom: 8mm;">
        <colgroup>
            <col style="width: 85mm;">
            <col style="width: 85.9mm;">
        </colgroup>
        <tbody>
            <tr style="height: 6.9mm;">
                <td>Nama Asesi: {{ $asesiName }}</td>
                <td>Tanggal dan Tanda Tangan Asesi:</td>
            </tr>
            <tr style="height: 17.1mm;">
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 8.5pt; font-style: italic;">
        Modul Melaksanakan Asesmen hal 32-35
    </div>
</div>
@endsection
