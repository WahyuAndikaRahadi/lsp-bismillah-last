@extends('layouts.print-muk')

@section('title', 'FR.MAPA.02 Peta Instrumen Asesmen - ' . ($scheme->name ?? ''))

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
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        height: 297mm;
        box-sizing: border-box;
        padding: 5.0mm 15.0mm 5.0mm 23.6mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .page-number {
        position: absolute;
        bottom: 8.0mm;
        left: 21.2mm;
        font-size: 11pt;
    }
    .kop-table {
        width: 177.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 7.5mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 34.5mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 30mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 143.0mm;
        text-align: center;
        font-family: Tahoma, sans-serif;
        font-size: 11pt;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.25;
        margin-bottom: 6.5mm;
    }
    .meta-table {
        width: 106.0mm;
        border-collapse: collapse;
        margin-bottom: 4.5mm;
        font-size: 11pt;
    }
    .meta-table td {
        padding: 0.5mm 1.0mm;
        vertical-align: top;
    }
    .meta-table td.col-left {
        width: 38.0mm;
        font-weight: bold;
    }
    .meta-table td.col-mid {
        width: 16.0mm;
    }
    .meta-table td.col-colon {
        width: 4.0mm;
        text-align: center;
    }
    .meta-table td.col-val {
        font-weight: normal;
    }
    .content-table {
        width: 177.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 3.5mm;
        font-size: 11pt;
    }
    .content-table th, .content-table td {
        border: 1pt solid #000;
        padding: 0.4mm 1.0mm;
        vertical-align: middle;
    }
    .content-table th {
        font-weight: bold;
        text-align: center;
    }
    .nowrap {
        white-space: nowrap;
    }
    .text-center {
        text-align: center;
    }
    .text-bold {
        font-weight: bold;
    }
</style>

{{-- ==================== PAGE 1 ==================== --}}
<div class="page-sheet">
    <div class="page-number">1</div>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo">
            </td>
            <td class="kop-text">
                Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo
            </td>
        </tr>
    </table>

    <div class="doc-main-title">
        FR.MAPA.02. PETA INSTRUMEN ASESSMEN HASIL PENDEKATAN ASESMEN DAN PERENCANAAN ASESMEN*
    </div>

    {{-- META TABLE --}}
    <table class="meta-table">
        <tr>
            <td class="col-left" rowspan="2">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td class="col-mid">Judul</td>
            <td class="col-colon">:</td>
            <td class="col-val">&nbsp;{{ $scheme->name ?? 'Engine Junior Technician' }}</td>
        </tr>
        <tr>
            <td class="col-mid">Nomor</td>
            <td class="col-colon">:</td>
            <td class="col-val">&nbsp;{{ $scheme->code ?? '01/LSP PrO Indo/I/2026' }}</td>
        </tr>
    </table>

    {{-- UNIT TABLE KELOMPOK 1 --}}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.5mm;">
            <col style="width: 106.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="9" style="vertical-align: middle; padding: 2.0mm; font-weight: bold;">
                    Kelompok<br>Pekerjaan 1<br><br>General<br>Service
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G.45OTO01.001.2</td>
                <td>Melaksanakan Keselamatan dan Kesehatan Kerja</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45OTO01.002.2</td>
                <td>Menggunakan Peralatan dan Perlengkapan Tempat Kerja</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45OTO01.003.2</td>
                <td>Melaksanakan Komunikasi di Tempat Kerja</td>
            </tr>
            <tr>
                <td class="text-center">4.</td>
                <td class="nowrap">G.45OTO01.007.2</td>
                <td>Membaca Gambar Teknik</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td class="nowrap">G.45OTO01.008.2</td>
                <td>Menggunakan Alat Ukur</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td class="nowrap">G.45OTO01.004.2</td>
                <td>Melaksanakan Pemeliharaan Komponen</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td class="nowrap">G.45OTO01.006.2</td>
                <td>Melaksanakan Teknik Pematrian</td>
            </tr>
            <tr>
                <td class="text-center">8.</td>
                <td class="nowrap">G.45OTO01.005.2</td>
                <td>Memperbaiki Sistem Hidrolik</td>
            </tr>
        </tbody>
    </table>

    {{-- INSTRUMENT TABLE KELOMPOK 1 (ROWS 1..9) --}}
    <table class="content-table">
        <colgroup>
            <col style="width: 10.0mm;">
            <col style="width: 105.0mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">INSTRUMEN ASESMEN</th>
                <th colspan="5">Potensi Asesi **</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td>FR.IA.01. &nbsp;CL - Ceklis Observasi Aktivitas Di Tempat Kerja atau Tempat Kerja Simulasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td>FR.IA.02. &nbsp;TPD - Tugas Praktik Demonstrasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td>FR.IA.03. PMO - Pertanyaan Untuk Mendukung Observasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" rowspan="2">4.</td>
                <td>4. a FR.IA.04A. &nbsp;&nbsp;DIT - &nbsp;Daftar Instruksi Terstruktur &nbsp;(Penjelasan Singkat Proyek Terkait Pekerjaan/ Kegiatan Terstruktur Lainnya )</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td>4.b FR.IA.04B. &nbsp;DIT – Penilaian Proyek Singkat atau Kegiatan Terstruktur Lainnya</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td>FR.IA.05. DPT - Daftar Pertanyaan Tertulis Pilihan Ganda</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td>FR.IA.06. DPT – Daftar Pertanyaan Tertulis Pilihan Esai</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td>FR.IA.07. DPL – Daftar Pertanyaan Lisan</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">8.</td>
                <td>FR.IA.08. CVP - Ceklis Verifikasi Portofolio</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">9.</td>
                <td>FR.IA.09. PW - Pertanyaan Wawancara</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 2 ==================== --}}
<div class="page-sheet">
    <div class="page-number">2</div>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo">
            </td>
            <td class="kop-text">
                Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo
            </td>
        </tr>
    </table>

    {{-- CONTINUATION OF TABLE 1 (ROWS 10..11) --}}
    <table class="content-table" style="margin-bottom: 3.0mm;">
        <colgroup>
            <col style="width: 10.0mm;">
            <col style="width: 105.0mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="width: 10.0mm;">10.</td>
                <td style="width: 105.0mm;">FR.IA.10. VPK - Verifikasi Pihak Ketiga</td>
                <td class="text-center" style="width: 12.5mm;">☐</td>
                <td class="text-center" style="width: 12.5mm;">☐</td>
                <td class="text-center" style="width: 12.5mm;">☐</td>
                <td class="text-center" style="width: 12.5mm;">☐</td>
                <td class="text-center" style="width: 12.5mm;">☐</td>
            </tr>
            <tr>
                <td class="text-center">11.</td>
                <td>FR.IA.11. CRP - Ceklis Reviu Produk</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    {{-- UNIT TABLE KELOMPOK 2 --}}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.5mm;">
            <col style="width: 106.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="7" style="vertical-align: middle; padding: 2.0mm; font-weight: bold;">
                    Kelompok<br>Pekerjaan 2<br><br>Engine
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G.45OTO01.010.2</td>
                <td>Melakukan Perawatan Berkala Engine</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45OTO01.020.2</td>
                <td>Memelihara Sistem Pelumasan Engine</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45OTO01.022.2</td>
                <td>Memelihara Sistem Pendinginan Engine</td>
            </tr>
            <tr>
                <td class="text-center">4.</td>
                <td class="nowrap">G.45OTO01.024.2</td>
                <td>Mengganti Cairan pada Sistem Pendinginan Engine</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td class="nowrap">G.45OTO01.026.2</td>
                <td>Memelihara Sistem Bahan Bakar Bensin</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td class="nowrap">G.45OTO01.032.2</td>
                <td>Memelihara Sistem Kontrol Emisi</td>
            </tr>
        </tbody>
    </table>

    {{-- INSTRUMENT TABLE KELOMPOK 2 --}}
    <table class="content-table" style="margin-bottom: 3.0mm;">
        <colgroup>
            <col style="width: 10.0mm;">
            <col style="width: 105.0mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">INSTRUMEN &nbsp;ASESMEN</th>
                <th colspan="5">Potensi Asesi **</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td>FR.IA.01. &nbsp;CL - Ceklis Observasi Aktivitas Di Tempat Kerja atau Tempat Kerja Simulasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td>FR.IA.02. &nbsp;TPD - Tugas Praktik Demonstrasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td>FR.IA.03. PMO - Pertanyaan Untuk Mendukung Observasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" rowspan="2">4.</td>
                <td>4. a FR.IA.0A. &nbsp;&nbsp;DIT - Daftar Instruksi Terstruktur &nbsp;( Penjelasan Singkat Proyek Terkait Pekerjaan / Kegiatan Terstruktur Lainnya )</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td>4.b FR.IA.04B. &nbsp;DIT - Daftar Instruksi Terstruktur &nbsp;( Penjelasan &nbsp;Kegiatan Terstruktur Lainnya &nbsp;)</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td>FR.IA.05. DPT - Daftar Pertanyaan Tertulis Pilihan Ganda</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td>FR.IA.06. DPT – Daftar Pertanyaan Tertulis Pilihan Esai</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td>FR.IA.07. DPL – Daftar Pertanyaan Lisan</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">8.</td>
                <td>FR.IA.08. CVP - Ceklis Verifikasi Portofolio</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">9.</td>
                <td>FR.IA.09. PW - Pertanyaan Wawancara</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">10.</td>
                <td>FR.IA.10. VPK - Verifikasi Pihak Ketiga</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">11.</td>
                <td>FR.IA.11. CRP - Ceklis Reviu Produk</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 11pt; padding-left: 5.0mm;">
        *) diisi berdasarkan hasil penentuan pendekatan asesmen dan perencanaan asesmen
    </div>
</div>

{{-- ==================== PAGE 3 ==================== --}}
<div class="page-sheet">
    <div class="page-number">3</div>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo">
            </td>
            <td class="kop-text">
                Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo
            </td>
        </tr>
    </table>

    {{-- UNIT TABLE KELOMPOK 3 --}}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.5mm;">
            <col style="width: 106.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="5" style="vertical-align: middle; padding: 2.0mm; font-weight: bold;">
                    Kelompok<br>Pekerjaan 3<br><br>Electrical
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td class="nowrap">G.45OTO01.065.2</td>
                <td>Memelihara Baterai</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45OTO01.066.2</td>
                <td>Mengganti Baterai Kendaraan Ringan</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45OTO01.067.2</td>
                <td>Melepas Komponen Kelistrikan</td>
            </tr>
            <tr>
                <td class="text-center">4.</td>
                <td class="nowrap">G.45OTO01.084.2</td>
                <td>Memelihara Sistem Air Conditioner</td>
            </tr>
        </tbody>
    </table>

    {{-- INSTRUMENT TABLE KELOMPOK 3 --}}
    <table class="content-table">
        <colgroup>
            <col style="width: 10.0mm;">
            <col style="width: 105.0mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">INSTRUMEN &nbsp;ASESMEN</th>
                <th colspan="5">Potensi Asesi **</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td>FR.IA.01. &nbsp;CL - Ceklis Observasi Aktivitas Di Tempat Kerja atau Tempat Kerja Simulasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td>FR.IA.02. &nbsp;TPD - Tugas Praktik Demonstrasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td>FR.IA.03. PMO - Pertanyaan Untuk Mendukung Observasi</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center" rowspan="2">4.</td>
                <td>4. a FR.IA.0A. &nbsp;&nbsp;DIT - Daftar Instruksi Terstruktur &nbsp;( Penjelasan Singkat Proyek Terkait Pekerjaan / Kegiatan Terstruktur Lainnya )</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td>4.b FR.IA.04B. &nbsp;DIT - Daftar Instruksi Terstruktur &nbsp;( Penjelasan &nbsp;Kegiatan Terstruktur Lainnya &nbsp;)</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">5.</td>
                <td>FR.IA.05. DPT - Daftar Pertanyaan Tertulis Pilihan Ganda</td>
                <td class="text-center">✓</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">6.</td>
                <td>FR.IA.06. DPT – Daftar Pertanyaan Tertulis Pilihan Esai</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">7.</td>
                <td>FR.IA.07. DPL – Daftar Pertanyaan Lisan</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">8.</td>
                <td>FR.IA.08. CVP - Ceklis Verifikasi Portofolio</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">9.</td>
                <td>FR.IA.09. PW - Pertanyaan Wawancara</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">10.</td>
                <td>FR.IA.10. VPK - Verifikasi Pihak Ketiga</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
            <tr>
                <td class="text-center">11.</td>
                <td>FR.IA.11. CRP - Ceklis Reviu Produk</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
                <td class="text-center">☐</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 4 ==================== --}}
<div class="page-sheet">
    <div class="page-number">4</div>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo">
            </td>
            <td class="kop-text">
                Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo
            </td>
        </tr>
    </table>

    <div class="doc-main-title" style="margin-top: 4.0mm; margin-bottom: 4.0mm;">
        &nbsp;&nbsp;Penyusun dan Validator
    </div>

    {{-- VALIDATOR TABLE --}}
    <table class="content-table" style="margin-bottom: 5.0mm;">
        <colgroup>
            <col style="width: 70.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 57.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td rowspan="2" style="font-weight: bold; vertical-align: top; padding: 3.0mm;">
                    Nama Penyusun
                </td>
                <td style="padding: 2.5mm;">
                    <strong>RIDWAN</strong><br>
                    No. Reg. 000.008265 2025
                </td>
                <td class="text-center" style="vertical-align: middle;">
                    05 Januari 2026
                </td>
            </tr>
            <tr>
                <td style="padding: 2.5mm;">
                    <strong>RANDI ARDIANSYAH</strong><br>
                    No. Reg . 000.002498 2013
                </td>
                <td class="text-center" style="vertical-align: middle;">
                    05 Januari 2026
                </td>
            </tr>
            <tr>
                <td rowspan="3" style="font-weight: bold; vertical-align: top; padding: 3.0mm;">
                    Nama Validator
                </td>
                <td style="padding: 2.5mm;">
                    <strong>SAPTONO</strong><br>
                    No. Reg. 000.003672 2016
                </td>
                <td class="text-center" style="vertical-align: middle;">
                    05 Januari 2026
                </td>
            </tr>
            <tr>
                <td style="padding: 2.5mm;">
                    <strong>KARYANTO</strong><br>
                    No. Reg. 000.008260 2025
                </td>
                <td class="text-center" style="vertical-align: middle;">
                    05 Januari 2026
                </td>
            </tr>
            <tr>
                <td style="padding: 2.5mm;">
                    <strong>GUNADI</strong><br>
                    No. Reg  . 000.000459 2007
                </td>
                <td class="text-center" style="vertical-align: middle;">
                    05 Januari 2026
                </td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 11pt; line-height: 1.35;">
        <p style="font-weight: bold; margin: 0 0 2.0mm 0;">**) Keterangan</p>
        <ol style="margin: 0; padding-left: 5.0mm;">
            <li style="margin-bottom: 1.5mm;">Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi.</li>
            <li style="margin-bottom: 1.5mm;">Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.</li>
            <li style="margin-bottom: 1.5mm;">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi.</li>
            <li style="margin-bottom: 1.5mm;">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</li>
            <li style="margin-bottom: 1.5mm;">Pelatihan / belajar mandiri atau otodidak</li>
        </ol>
    </div>
</div>
@endsection
