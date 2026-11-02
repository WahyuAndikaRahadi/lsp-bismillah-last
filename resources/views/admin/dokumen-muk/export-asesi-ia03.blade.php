@extends('layouts.print-muk')

@section('title', 'FR.IA.03 Pertanyaan Mendukung Observasi - ' . ($asesi->full_name ?? ''))

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
        font-size: 10pt;
        line-height: 1.15;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        max-height: 295mm;
        box-sizing: border-box;
        padding: 8.0mm 15.0mm 8.0mm 25.5mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .kop-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 34.4mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 30mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 135.1mm;
        text-align: center;
        font-family: Tahoma, sans-serif;
        font-size: 11pt;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.25;
        margin-bottom: 2.5mm;
    }
    .meta-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 9.5pt;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.8mm 1.5mm;
        vertical-align: top;
    }
    .panduan-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
        font-size: 9pt;
    }
    .panduan-table th, .panduan-table td {
        border: 1pt solid #000;
        padding: 1.0mm 1.8mm;
    }
    .panduan-table th {
        font-weight: bold;
        text-align: left;
    }
    .unit-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 9pt;
    }
    .unit-table th, .unit-table td {
        border: 1pt solid #000;
        padding: 0.6mm 1.5mm;
        vertical-align: middle;
    }
    .unit-table th {
        font-weight: bold;
        text-align: center;
    }
    .question-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 9pt;
    }
    .question-table th, .question-table td {
        border: 1pt solid #000;
        padding: 0.6mm 1.5mm;
        vertical-align: middle;
    }
    .question-table th {
        font-weight: bold;
        text-align: center;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
    .sig-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-top: 3.0mm;
        margin-bottom: 2.5mm;
        font-size: 9.5pt;
    }
    .sig-table td {
        border: 1pt solid #000;
        padding: 1.0mm 1.5mm;
        vertical-align: top;
    }
</style>

{{-- ==================== PAGE 1 ==================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <div class="doc-main-title">FR.IA.03. PMO - PERTANYAAN MENDUKUNG OBSERVASI</div>

    <table class="meta-table">
        <colgroup>
            <col style="width: 45.7mm;">
            <col style="width: 18.2mm;">
            <col style="width: 4.6mm;">
            <col style="width: 101.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" style="font-weight: bold;">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
            <td style="font-weight: bold;">Judul</td>
            <td class="text-center">:</td>
            <td>{{ $scheme->name ?? 'Mekanik Junior' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor</td>
            <td class="text-center">:</td>
            <td>{{ $scheme->code ?? '05/LSP PrO Indo/I/2026' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TUK</td>
            <td colspan="3">: Sewaktu/Tempat Kerja/Mandiri*</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Asesor</td>
            <td colspan="3">: {{ $leadAssessor->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Asesi</td>
            <td colspan="3">: {{ $asesi->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Tanggal</td>
            <td colspan="3">: {{ $assessmentDate ?? '' }}</td>
        </tr>
    </table>
    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 1.5mm;">*Coret yang tidak perlu</div>

    <table class="panduan-table">
        <tr>
            <th>PANDUAN BAGI ASESOR</th>
        </tr>
        <tr>
            <td style="line-height: 1.25;">
                <ul style="margin: 0; padding-left: 4.5mm;">
                    <li style="margin-bottom: 1.0mm;">Formulir ini di isi oleh asesor kompetensi dapat sebelum, pada saat atau setelah melakukan asesmen dengan metode observasi demonstrasi.</li>
                    <li style="margin-bottom: 1.0mm;">Pertanyaan dibuat dengan tujuan untuk menggali, dapat berisi pertanyaan yang berkaitan dengan dimensi kompetensi, batasan variabel dan aspek kritis yang relevan dengan skenario tugas dan praktik demonstrasi.</li>
                    <li style="margin-bottom: 1.0mm;">Jika pertanyaan disampaikan sebelum asesi melakukan praktik demonstrasi, maka pertanyaan dibuat berkaitan dengan aspek K3L, SOP, penggunaan peralatan dan perlengkapan.</li>
                    <li style="margin-bottom: 1.0mm;">Jika setelah asesi melakukan praktik demonstrasi terdapat item pertanyaan pendukung observasi telah terpenuhi, maka pertanyaan tersebut tidak perlu ditanyakan lagi dan cukup memberi catatan bahwa sudah terpenuhi pada saat tugas praktek demonstrasi pada kolom tanggapan</li>
                    <li style="margin-bottom: 1.0mm;">Jika pada saat observasi ada hal yang perlu dikonfirmasi sedangkan di instrumen daftar pertanyaan pendukung observasi tidak ada, maka asesor dapat memberikan pertanyaan dengan syarat pertanyaan harus berkaitan dengan tugas praktek demonstrasi. Jika dilakukan, asesor harus mencatat dalam instrumen pertanyaan pendukung observasi.</li>
                    <li>Tanggapan asesi ditulis pada kolom tanggapan.</li>
                </ul>
            </td>
        </tr>
    </table>

    <table class="unit-table">
        <colgroup>
            <col style="width: 54.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.0mm;">
            <col style="width: 72.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="10" style="vertical-align: middle; text-align: center; font-weight: bold; background-color: #fff;">
                    Kelompok Pekerjaan 1<br>Persiapan servis
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G.45TSM01.018.2</td>
                <td>Melakukan Perawatan Karburator</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45TSM01.020.2</td>
                <td>Melakukan Perawatan Renggang Klep</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45TSM01.021.2</td>
                <td>Melakukan Perawatan Free Play Throttle Gas</td>
            </tr>
            <tr>
                <td class="text-center">4.</td>
                <td class="nowrap">G.45TSM01.022.2</td>
                <td>Melakukan Perawatan Filter Udara</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td class="nowrap">G.45TSM01.023.2</td>
                <td>Melakukan Perawatan Sistem Clucth / Kopling</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td class="nowrap">G.45TSM01.024.2</td>
                <td>Melakukan pemeriksaan dan penggantian oli</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td class="nowrap">G.45TSM01.025.2</td>
                <td>Melakukan pemeriksaan sistem pelumasan mesin</td>
            </tr>
            <tr>
                <td class="text-center">8.</td>
                <td class="nowrap">G.45TSM01.027.2</td>
                <td>Melakukan Perawatan Sistem Continuosly Variable Tansmission (CVT)</td>
            </tr>
            <tr>
                <td class="text-center">9.</td>
                <td class="nowrap">G.45TSM01.028.2</td>
                <td>Melakukan Perawatan Sistem Pendinginan</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 2 ==================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="question-table">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="2" rowspan="2" style="vertical-align: middle;">Pertanyaan</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tdk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1.</td>
                <td>Apa yang terjadi jika setelan campuran udara dan bahan bakar (air-fuel mixture) pada karburator terlalu kaya (terlalu banyak bensin)?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2.</td>
                <td>Sebutkan Langkah-langkah yang dilakukan sebelum  melakukan penyetelan celah katup pada mesin sepeda motor ?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3.</td>
                <td>Pekerjaan dan pemeriksaan apa saja yang dilakukan saat perawatan kabel dan handel gas ?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">4.</td>
                <td>Pada jadwal perawatan berkala yang ditunjukan Buku Pedoman Reparasi tanda “G” untuk item perawatan saringan udara memiliki arti ?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">5.</td>
                <td>Sebutkan langkah-langkah penyetelan kopling sepeda motor tipe cub (bebek)?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">6.</td>
                <td>Sebutkan langkah-langkah penggantian oli pada sepeda motor tipe cub (bebek)?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">7.</td>
                <td>Prosedur pemeriksaan tinggi permukaan oli pada mesin sepeda motor adalah?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">8.</td>
                <td>Komponen utama pada CVT sepeda motor yang bergerak membuka dan menutup untuk mengubah diameter puli dan rasio putaran adalah?</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">9.</td>
                <td>Alat tepat yang dipergunakan untuk menguji kebocoran sistem pendinginan air pada sepeda motor adalah ?</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 3 ==================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="question-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 139.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td>Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    <table class="unit-table">
        <colgroup>
            <col style="width: 54.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.0mm;">
            <col style="width: 72.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4" style="vertical-align: middle; text-align: center; font-weight: bold; background-color: #fff;">
                    Kelompok Pekerjaan 2 Fuel<br>injection service
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="nowrap">G.45TSM01.019.2</td>
                <td>Melakukan Perawatan Sistem Injeksi</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td class="nowrap">G.45TSM01.053.2</td>
                <td>Melakukan Pembersihan Injector</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td class="nowrap">G.45TSM01.055.2</td>
                <td>Mengoperasikan diagnostic tools</td>
            </tr>
        </tbody>
    </table>

    <table class="question-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="2" rowspan="2" style="vertical-align: middle;">Pertanyaan</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tdk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1.</td>
                <td>Apa fungsi utama dari komponen injektor (injector) pada sistem bahan bakar sepeda motor injeksi ?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2.</td>
                <td>Metode pembersihan injektor yang paling umum dan dianjurkan dalam perawatan berkala di bengkel resmi adalah?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3.</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    <table class="unit-table">
        <colgroup>
            <col style="width: 54.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.0mm;">
            <col style="width: 72.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4" style="vertical-align: middle; text-align: center; font-weight: bold; background-color: #fff;">
                    Kelompok Pekerjaan 3<br>Electrik
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G .45TSM01.039.2</td>
                <td>Melakukan Perawatan Baterai</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G .45TSM01.026.2</td>
                <td>Melakukan Perawatan Busi</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G .45TSM01.040.2</td>
                <td>Melakukan perawatan instrumen kelistrikan (sistem sinyal dan penerangan)</td>
            </tr>
        </tbody>
    </table>

    <table class="question-table">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="2" rowspan="2" style="vertical-align: middle;">Pertanyaan</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tdk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1.</td>
                <td>Bagaimana cara mengganti baterai motor yang benar?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2.</td>
                <td>Apa dampak jika celah busi terlalu lebar dari spesifikasi pabrikan?</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 4 ==================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="question-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3.</td>
                <td>Apa yang dapat terjadi jika sistem penerangan tidak berfungsi dengan baik?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    <table class="unit-table">
        <colgroup>
            <col style="width: 54.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.0mm;">
            <col style="width: 72.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="8" style="vertical-align: middle; text-align: center; font-weight: bold; background-color: #fff;">
                    Kelompok Pekerjaan 4<br>Chassis
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G.45TSM01.029.2</td>
                <td>Melakukan Penggantian Rantai Roda</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45TSM01.030.2</td>
                <td>Melakukan Pemeriksaan Keolengan Roda</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45TSM01.031.2</td>
                <td>Melakukan Perawatan Sistem Pengereman</td>
            </tr>
            <tr>
                <td class="text-center">4.</td>
                <td class="nowrap">G.45TSM01.032.2</td>
                <td>Melakukan Perawatan Sistem Kemudi</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td class="nowrap">G.45TSM01.033.2</td>
                <td>Melakukan Perawatan Sistem Suspensi</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td class="nowrap">G.45TSM01.034.2</td>
                <td>Melakukan Bongkar Pasang Cover Body</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td class="nowrap">G.45TSM01.043.2</td>
                <td>Melakukan Pemeriksaan Chassis</td>
            </tr>
        </tbody>
    </table>

    <table class="question-table">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="2" rowspan="2" style="vertical-align: middle;">Pertanyaan</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tdk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1.</td>
                <td>Berapa Jarak main bebas rantai roda ?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2.</td>
                <td>Roda depan terasa bergoyang kemungkinan penyebab adalah?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3.</td>
                <td>Jarak main bebas penyetelan pedal rem belakang adalah ?</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">4.</td>
                <td>Apabila pengendalian setang/kemudi terasa berat untuk digerakkan, kemungkinan penyebabnya adalah?</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">5.</td>
                <td>Sebutkan ciri-ciri kerusakan pada shock absorber ?</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 5 ==================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="question-table" style="margin-bottom: 3.0mm;">
        <colgroup>
            <col style="width: 14.0mm;">
            <col style="width: 125.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">6.</td>
                <td>Apa yang harus diperiksa setelah melakukan pemasangan cover body?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">7.</td>
                <td>Apabila ditemukan retakan pada rangka (frame) chassis, tindakan perbaikan yang paling tepat adalah?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Tanggapan:</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    <table class="sig-table">
        <colgroup>
            <col style="width: 38.0mm;">
            <col style="width: 4.0mm;">
            <col style="width: 127.5mm;">
        </colgroup>
        <tr>
            <td colspan="3" style="font-weight: bold;">Asesi :</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td class="text-center">:</td>
            <td>{{ $asesi->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td>Tanda tangan/ Tanggal</td>
            <td class="text-center">:</td>
            <td style="height: 12.0mm; vertical-align: bottom;"></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">Asesor :</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td class="text-center">:</td>
            <td>{{ $leadAssessor->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td>No. Reg</td>
            <td class="text-center">:</td>
            <td>{{ $leadAssessor->profile?->no_reg_asesor ?? '' }}</td>
        </tr>
        <tr>
            <td>Tanda tangan / Tanggal</td>
            <td class="text-center">:</td>
            <td style="height: 12.0mm; vertical-align: bottom;"></td>
        </tr>
    </table>

    <div style="font-size: 8.5pt; font-style: italic; line-height: 1.25; margin-top: 4.0mm;">
        Diadaptasi dari template yang disediakan di Departemen Pendidikan dan Pelatihan, Australia. Merancang instrumen asesmen untuk hasil yang berkualitas di VET, 2008 di VET, 2008
    </div>
</div>
@endsection
