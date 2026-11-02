@extends('layouts.print-muk')

@section('title', 'FR.IA.01 Ceklis Observasi Praktik - ' . ($asesi->full_name ?? ''))

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
        font-size: 8.5pt;
        line-height: 1.15;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        max-height: 295mm;
        box-sizing: border-box;
        padding: 5.0mm 15.0mm 5.0mm 25.5mm;
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
        margin-bottom: 2.0mm;
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
        margin-bottom: 1.5mm;
    }
    .meta-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 1.5mm;
        font-size: 9pt;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.6mm 1.5mm;
        vertical-align: top;
    }
    .panduan-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 8.5pt;
    }
    .panduan-table th, .panduan-table td {
        border: 1pt solid #000;
        padding: 0.8mm 1.5mm;
    }
    .kelompok-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 8.5pt;
    }
    .kelompok-table th, .kelompok-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.2mm;
        vertical-align: middle;
    }
    .unit-header-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 1.5mm;
        font-size: 8.5pt;
    }
    .unit-header-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.2mm;
        vertical-align: middle;
    }
    .obs-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 8pt;
        line-height: 1.12;
    }
    .obs-table th, .obs-table td {
        border: 1pt solid #000;
        padding: 0.4mm 0.8mm;
        vertical-align: middle;
    }
    .obs-table th {
        font-weight: bold;
        text-align: center;
    }
    .rekomendasi-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        margin-top: 3.0mm;
    }
    .rekomendasi-table td {
        border: 1pt solid #000;
        padding: 1.0mm 1.5mm;
        vertical-align: top;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
</style>

{-- ==================== PAGE 1 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="doc-main-title">FR.IA.01. CL - CEKLIS OBSERVASI AKTIVITAS DI TEMPAT KERJA ATAU TEMPAT KERJA SIMULASI</div>

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
            <td>&nbsp;{{ $scheme->name ?? 'Mekanik Junior' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor</td>
            <td class="text-center">:</td>
            <td>&nbsp;{{ $scheme->code ?? '05/LSP PrO Indo/I/2026' }}</td>
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
    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 1.0mm;">*Coret yang tidak perlu</div>

    <table class="panduan-table">
        <tr>
            <th>PANDUAN BAGI ASESOR</th>
        </tr>
        <tr>
            <td style="line-height: 1.25;">
                <ul style="margin: 0; padding-left: 4.5mm;">
                    <li style="margin-bottom: 0.8mm;">Lengkapi nama unit kompetensi, elemen, dan kriteria unjuk kerja sesuai kolom dalam tabel.</li>
                    <li style="margin-bottom: 0.8mm;">Isilah standar industri atau tempat kerja</li>
                    <li style="margin-bottom: 0.8mm;">Beri tanda centang (&radic;) pada kolom &ldquo;YA&rdquo; jika Anda yakin asesi dapat melakukan/ mendemonstrasikan tugas sesuai KUK, atau centang (&radic;) pada kolom &ldquo;Tidak&rdquo; bila sebaliknya.</li>
                    <li style="margin-bottom: 0.8mm;">Penilaian Lanjut diisi bila hasil belum dapat disimpulkan, untuk itu gunakan metode lain sehingga keputusan dapat dibuat.</li>
                    <li>Isilah kolom KUK sesuai dengan Unit Kompetensi/ SKKNI</li>
                </ul>
            </td>
        </tr>
    </table>

    <table class="kelompok-table">
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

{-- ==================== PAGE 2 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 1</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.018.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Karburator</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan<br>karburator</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan spesifikasi<br>komponen pada sistem<br>bahan bakar diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja karburator<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi dan cara<br>kerja bagianbagian<br>karburator diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit dari setiap<br>komponen bagian karburator<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Kondisi dan posisi<br>pelampung pada karburator<br>diidentifikasi sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 General tools, alat ukur, dan<br>bahan pembersih<br>karburator disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Supporting tools dan pit<br>area/stall disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan<br>dalam melaksanakan unit<br>kompetensi ini.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>sistem bahan bakar</td>
                <td style="vertical-align: top;">2.1 Aliran bahan bakar dari<br>tangka diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Komponen sistem bahan<br>bakar diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Karburator dilepas sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi dan kelengkapan<br>karburator diperiksa sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Buka-tutup throttle valve<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 3 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.6 Fungsi air cut valve diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>karburator sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen<br>karburator dibongkar sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>karburator dibersihkan<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Semua lubang pada<br>komponen-komponen<br>karburator dipastikan tidak<br>tersumbat.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 2</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.020.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Renggang Klep</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>Perawatan<br>Renggang Klep</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja dari komponen<br>pada sistem klep<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian dari sistem<br>klep diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada sistem klep<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit dari renggang<br>klep diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur penyetelan<br>renggang klep diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 General tools, alat ukur,<br>special service tools/special<br>tools diidentifikasi dan<br>disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pexlaksanaan proses<br>kerja</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pengujian pak</td>
                <td style="vertical-align: top;">2.1 Timing hole cap, crankshaft<br>hole cap, dan cover cylinder</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 4 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;">baterai Melakukan<br>Pemeriksaan<br>Renggang Klep</td>
                <td style="vertical-align: top;">head dilepas sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Posisi top langkah kompresi<br>dipastikan sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Renggang klep disetel sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan Kinerja<br>Klep Sesuai Standar</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen klep,<br>timing hole cap, crankshaft<br>hole cap, dan cover cylinder<br>head dipasang sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Semua oil seal dan o-ring<br>diganti sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Renggang klep dipastikan<br>sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Semua baut dan komponen<br>dipastikan kekencangannya<br>sesuai standar</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 3</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.021.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Free Play Throttle Gas</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan free play<br>throttle gas</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja komponen pada<br>sistem throttle gas<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian-bagian<br>sistem throttle gas<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi, dan cara<br>kerja bagianbagian throttle<br>gas diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Kondisi dan free play throttle<br>gas diidentifikasi sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 General tools dan bahan<br>pembersih disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur K3 diterapkan</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 5 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">dalam melaksanakan unit<br>kompetensi ini.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>sistem throttle gas</td>
                <td style="vertical-align: top;">2.1 Kelancaran buka dan tutup<br>throttle gas diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Kondisi dan kelengkapan<br>sistem throttle gas diperiksa<br>sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Jalur/routing kabel gas<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi kabel gas diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>throttle gas sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Sistem throttle gas dilumasi<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>throttle gas dirakit sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Mur penyetelan free play<br>kabel gas disetel sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Throttle gas dipastikan<br>berfungsi dengan normal.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 4</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.022.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Filter Udara</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>Perawatan filter<br>udara</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja sistem filter<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian komponen<br>filter diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi dari komponen-<br>komponen sistem filter<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit komponen<br>utama filter diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 General tools, supporting<br>tools dan bahan pembersih</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 6 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Rangkaian sistem filter<br>udara diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan sistem<br>filter udara</td>
                <td style="vertical-align: top;">2.1 Rangkaian sistem filter<br>udara diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Rangkaian sistem filter udara<br>dilepas dan dibuka sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Filter udara diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kinerja sistem filter udara<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>sistem filter udara<br>sesuai standar</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen<br>sistem filter udara<br>dibersihkan sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>sistem filter udara dirakit<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Rangkaian sistem filter<br>udara dipasang sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Bagian yang terhubung<br>dengan karburator/throttle<br>body diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Semua baut pada rangkaian<br>sistem filter udara dipastikan</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 5</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.023.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Clucth / Kopling</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>kopling</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja komponen pada<br>sistem clutch/kopling<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 7 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian dari sistem<br>clutch/kopling diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada sistem<br>clutch/kopling diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Kondisi dan free play handle</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 clutch/kopling diidentifikasi<br>sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 General tools, alat ukur,<br>special service tools/special<br>tools, supporting tools<br>(pressure cable lube) dan<br>bahan pembersih disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen sistem<br>kopling</td>
                <td style="vertical-align: top;">2.1 Kelancaran kinerja handle<br>clutch/kopling diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Kondisi dan kelengkapan<br>sistem clutch/kopling<br>diperiksa sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Alur/routing kabel<br>clutch/kopling diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi kabel clutch/kopling<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>sistem kopling sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Kabel clutch/kopling<br>dilumasi sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>sistem clutch/kopling dirakit<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Mur penyetelan kabel<br>clutch/kopling disetel sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Sistem clutch/kopling<br>dipastikan berfungsi normal.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 8 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 6</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.024.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan pemeriksaan dan penggantian oli</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pemeriksaan dan<br>penggantian oli<br>mesin, oli transmisi<br>dan filter oli</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja filter oli<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Jalur sistem pelumasan<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Prosedur pemeriksaan oli<br>mesin dan oli transmisi, dan<br>filter oli diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit oli mesin, oli<br>transmisi dan filter oli<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 General tools, alat ukur dan<br>bahan pembersih disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur penggantian oli<br>mesin, oli transmisi, dan<br>filter oli diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur bongkar pasang<br>cover cowling diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>dari oli mesin, oli<br>transmisi, dan filter<br>oli</td>
                <td style="vertical-align: top;">2.1 Oli mesin dan oli transmisi,<br>dan filter oli diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Oli mesin, oli transmisi, dan<br>filter oli diganti sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kebocoran oli dipastikan<br>tidak ada.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 9 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 7</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.025.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan pemeriksaan sistem pelumasan mesin</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pemeriksaan sistem<br>pelumasan mesin</td>
                <td style="vertical-align: top;">1.1 Fungsi dan jenis sistem<br>pelumasan mesin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, dan cara kerja<br>sistem pelumasan mesin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi, dan cara<br>kerja bagian-bagian sistem<br>pelumasan mesin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit dari setiap<br>komponen bagian sistem<br>pelumasan mesin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Kondisi posisi pelumas mesin<br>diidentifikasi sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 General tools, alat ukur,<br>supporting tools dan bahan<br>pembersih sistem<br>pelumasan mesin disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>sistem pelumasan<br>mesin</td>
                <td style="vertical-align: top;">2.1 Tekanan/aliran oli di sistem<br>pelumasan mesin diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Saringan oli (oil strainer<br>screen) saringan oli<br>sentrifugal dan gear pompa<br>oli dilepas sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi dan kelengkapan<br>sistem pelumasan mesin<br>diperiksa sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>sistem pelumasan<br>mesin</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen<br>sistem pelumasan mesin<br>dibongkar sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>sistem pelumasan mesin</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 10 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">dibersihkan sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Semua saluran di sistem<br>pelumasan mesin dipastikan<br>tidak tersumbat.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Komponen-komponen<br>sistem pelumasan mesin<br>dirakit sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Kekencangan semua baut<br>pada komponen sistem<br>pelumasan dipastikan sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.6 Kinerja sistem pelumasan<br>dipastikan sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 8</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.027.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Continuosly Variable
Tansmission (CVT)</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>CVTKriteria Unjuk<br>Kerja</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian dari sistem<br>CVT diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada sistem CVT<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Service limit dari setiap<br>komponen CVT diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Tools, alat ukur, special<br>service tools/special tools,<br>alat dan bahan pembersih<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>sistem CVT</td>
                <td style="vertical-align: top;">2.1 Fungsi sistem CVT diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 CVT dibongkar sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi dan kelengkapan</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 11 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">CVT diperiksa sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>CVT sesuai standar</td>
                <td style="vertical-align: top;">3.1 Semua o-ring dan gasket<br>diganti sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen CVT<br>dirakit dan dipasang sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Pengencangan baut<br>dilakukan secaraberurutan<br>menyilang.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 CVT dipastikan berfungsi<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 9</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.028.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Pendinginan</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>pendingin</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja dari komponen<br>pada sistem pendingin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian dari sistem<br>pendingin diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada sistem<br>pendingin diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Standar dari setiap<br>komponen sistem pendingin<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Tools, alat ukur, special<br>service tools/special tools<br>diidentifikasi dan disiapkan<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen sistem</td>
                <td style="vertical-align: top;">2.1 Fungsi sistem pendingin<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Bagian sistem pendingin</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 12 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;">pendingin</td>
                <td style="vertical-align: top;">dibongkar sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi dan kelengkapan<br>komponen bagian sistem<br>pendingin diperiksa sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>sistem pendingin<br>sesuai standar</td>
                <td style="vertical-align: top;">3.1 Semua seal oil, o-ring dan<br>gasket diganti sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen<br>bagian sistem pendingin<br>dirakit sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Bagian sistem pendingin<br>dipasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Semua baut pada sistem<br>pendingin dipastikan sudah<br>dikencangkan sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Bagian sistem pendingin<br>diperiksa kinerjanya saat<br>kondisi mesin dingin dan<br>panas sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.6 Sistem pendingin dipastikan<br>berfungsi sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="kelompok-table">
        <colgroup>
            <col style="width: 54.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 33.0mm;">
            <col style="width: 72.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4" style="vertical-align: middle; text-align: center; font-weight: bold; background-color: #fff;">
                    Kelompok Pekerjaan 2<br>Fuel injection service
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
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 1</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.019.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Injeksi</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>Perawatan Sistem<br>Injeksi</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan spesifikasi<br>dari komponen pada sistem<br>injeksi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi,<br>dan cara kerja bagian-<br>bagian sistem injeksi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 13 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi, dan cara<br>kerja bagianbagian sistem<br>injeksi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">.<br>BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit dari setiap<br>komponen bagian sistem<br>injeksi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 General tools, alat ukur,<br>dan bahan pembersih<br>injeksi disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Supporting tools disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>Pemeriksaan<br>Komponen dari<br>Sistem Injeksi</td>
                <td style="vertical-align: top;">2.1 Ketidaknormalan sistem<br>injeksi diperiksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Kelancaran buka-tutup<br>katup throttle body<br>diperiksa sesuai prosedur</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan Kinerja<br>Sistem Injeksi Sesuai<br>Standar</td>
                <td style="vertical-align: top;">3.1 Tekanan dan aliran bahan<br>bakar pompa injeksi<br>dipastikan sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Injektor dibersihkan sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Air screw disetel saat mesin<br>hidup sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Sistem injeksi di-reset<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Kinerja sistem injeksi<br>dipastikan sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 2</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.053.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Pembersihan Injector</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pembersihan<br>injector</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan cara kerja<br>sistem fuel injeksi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">Workshop<br>Manual<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, dan cara kerja<br>sistem bahan bakar<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, dan cara kerja<br>injector diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Fungsi, jenis, dan cara kerja<br>alat pembersih injector<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 14 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur deteksi<br>ketidaknormalan fungsi<br>injector diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur bongkar pasang<br>cover body, dan fuel tank<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur pelepasan dan<br>pemasangan alat pembersih<br>injector ke injector<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur pembersihan<br>injector diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>perawatan injector</td>
                <td style="vertical-align: top;">2.1 Pembongkaran cover body,<br>dan fuel tank dilakukan<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Alat pembersih injector<br>disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Pembersihan injector<br>dilakukan sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Injector dipastikan<br>berfungsi normal setelah<br>dibersihkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Pemasangan cover body,<br>dan fuel tank dilakukan<br>sesuai prosedur</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 3</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.055.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Mengoperasikan diagnostic tools</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pengoperasian<br>diagnostic tools</td>
                <td style="vertical-align: top;">1.1 Cara pengoperasian<br>diagnostic tools dan<br>tampilan data diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Jenis, fungsi dan cara kerja<br>sistem injeksi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal sistem<br>injeksi sepeda motor<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Kondisi ketidaknormalan<br>bagian dari sensor dan<br>actuator diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Tools dan alat ukur untuk<br>setiap ketidaknormalan<br>sistem injeksi sepeda motor<br>didentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 15 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur bongkar pasang<br>cover body diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur bongkar pasang<br>setiap bagian sistem injeksi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur deteksi ketidak<br>normalan sistem injeksi<br>sepeda motor diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur troubleshooting<br>ketidaknormalan sistem<br>injeksi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.10 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Mengidentifikasi<br>standar kondisi<br>normal sistem<br>injeksi</td>
                <td style="vertical-align: top;">2.1 Standar kondisi normal<br>pada bagian sensor<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Standar kondisi normal pada<br>bagian actuator<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Menentukan sistem<br>injeksi yang tidak<br>normal</td>
                <td style="vertical-align: top;">3.1 Gejala ketidaknormalan<br>sistem injeksi dianalisis<br>sesuai prosedur untuk<br>mencari bagian sensor dan<br>actuator yang tidak normal.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Cover body dibongkar<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Bagian sensor dan atau<br>actuator yang tidak normal<br>dibongkar sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Tegangan, Tahanan standar<br>dan servis Limit komponen<br>sensor diukur untuk<br>menentukan yang tidak<br>normal.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Penyebab ketidaknormalan<br>komponen dianalisis sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="kelompok-table">
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
                <td class="nowrap">G.45TSM01.039.2</td>
                <td>Melakukan Perawatan Baterai</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td class="nowrap">G.45TSM01.026.2</td>
                <td>Melakukan Perawatan Busi</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td class="nowrap">G.45TSM01.040.2</td>
                <td>Melakukan perawatan instrumen
kelistrikan (sistem sinyal dan penerangan)</td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 1</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.039.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Baterai</td>
        </tr>
    </table>
</div>

{-- ==================== PAGE 16 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan baterai</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan spesifikasi<br>baterai diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.<br>BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Kondisi normal dan<br>ketidaknormalan dari<br>baterai diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Service limit dari baterai<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur pengukuran<br>tegangan baterai<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur bongkar pasang<br>baterai dari sepeda motor<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 General tools dan alat ukur<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.<br>2.1 Baterai dilepas sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan baterai</td>
                <td style="vertical-align: top;"></td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Tegangan baterai diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>baterai sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Baterai diperiksa kinerjanya<br>saat sistem beban aktif.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">Workshop<br>Manual<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Baterai diperiksa pada saat<br>setelah sistem<br>pengisian/charging.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Baterai dipastikan berfungsi<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 2</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.026.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Busi</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan busi</td>
                <td style="vertical-align: top;">1.1 Jenis dan fungsi busi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, kondisi standar, dan<br>cara kerja bagian-bagian<br>busi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Prosedur perawatan busi</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 17 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Service limit busi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Tools, alat ukur, special<br>service tools/special tools,<br>alat dan bahan pembersih<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Memastikan busi<br>terpasang dengan<br>benar</td>
                <td style="vertical-align: top;">2.1 Prosedur bongkar pasang<br>busi dilakukan berdasarkan<br>metode/cara dan<br>perlengkapan yang sesuai<br>dengan spesifikasi pabrik.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Prosedur pembersihan busi<br>dilakukan sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Prosedur pemeriksaan busi<br>dilakukan sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Prosedur pemeriksaan cap<br>oise/tutup busi dilakukan<br>sesuai buku pedoman<br>reparasi/manual book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Prosedur pemeriksaan<br>percikkan bungan api<br>dilakukan sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.6 Pemasangan busi dilakukan<br>sesuai buku panduan<br>reparasi/manual book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.7 Prosedur menghidupkan<br>mesin dilakukan sesuai<br>standar</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 3</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.040.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan perawatan instrumen kelistrikan (sistem sinyal
dan penerangan)</td>
        </tr>
    </table>
</div>

{-- ==================== PAGE 18 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;">instrumen<br>kelistrikan (sistem<br>sinyal dan<br>penerangan)</td>
                <td style="vertical-align: top;">komponen pada instrumen<br>kelistrikan (sistem sinyal<br>dan penerangan)<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada istrumen<br>kelistrikan (sistem sinyal<br>dan penerangan)<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Standar kinerja dari setiap<br>komponen instrumen<br>kelistrikan (sistem sinyal<br>dan penerangan)<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur bongkar pasang<br>instrument kelistrikan<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur pemeriksaan<br>sistem kelistrikan<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 General tools, alat ukur,<br>dan alat pembersih<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>instrumen<br>kelistrikan (sistem<br>sinyal dan<br>penerangan)</td>
                <td style="vertical-align: top;">2.1 Fungsi instrumen kelistrikan<br>(sistem sinyal dan<br>penerangan) diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Komponen dilepas sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi dan kelengkapan<br>instrument kelistrikan<br>(sistem sinyal dan<br>penerangan) diperiksa<br>sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>instrumen<br>kelistrikan (sistem<br>sinyal dan<br>penerangan) sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen<br>Instrumen kelistrikan<br>(sistem sinyal dan<br>penerangan) dirakit dan<br>dipasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Instrumen kelistrikan<br>(sistem sinyal dan<br>penerangan) dipastikan<br>berfungsi sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 19 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="kelompok-table">
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
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 1</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.029.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Penggantian Rantai Roda</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>penggantian rantai<br>roda</td>
                <td style="vertical-align: top;">1.1 Fungsi, spesifikasi, dan cara<br>kerja bagian dari rantai roda<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Kondisi normal dan tidak<br>normal bagian dari rantai<br>roda diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Tools, alat ukur, special<br>service tools/special tools,<br>alat dan bahan pembersih<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan bagian<br>dari rantai roda</td>
                <td style="vertical-align: top;">2.1 Kondisi rantai roda diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Kondisi gear sprocket depan<br>dan belakang diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi coupling damper<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi penyetel rantai roda<br>diperiksa.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Rantai roda dibongkar sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 20 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>rantai roda sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Semua circlip gear sprocket<br>roda diganti sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Bagian rantai roda dirakit<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Semua baut yang<br>berhubungan dengan rantai<br>roda dipastikan sudah<br>dikencangkan sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Rantai roda disetel dan<br>dipastikan berfungsi sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 2</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.030.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Pemeriksaan Keolengan Roda</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pemeriksaan<br>keolengan roda</td>
                <td style="vertical-align: top;">1.1 Fungsi dan cara kerja bagian<br>roda diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, kondisi standar, dan<br>cara kerja bagian roda<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal dan tidak<br>normal bagian roda<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur bongkar pasang<br>roda diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur penyetelan velg<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur memastikan<br>kenormalan fungsi roda<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Tools, alat ukur, dan special<br>service tools/special tools<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan fungsi<br>roda</td>
                <td style="vertical-align: top;">2.1 Fungsi roda di periksa sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Roda di bongkar sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 21 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi dan kelengkapan<br>roda di periksa sesui<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi keolengan roda<br>dipastikan sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan fungsi<br>setiap bagian roda</td>
                <td style="vertical-align: top;">3.1 Bagian-bagian roda dirakit<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Kekencangan mur dan baut<br>as roda dipastikan sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Roda dipastikan berfungsi<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 3</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.031.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Pengereman</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>pengereman</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan spesifikasi<br>sistem pengereman<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja komponen dari<br>sistem pengereman<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi dan cara<br>kerja bagianbagian<br>komponen sistem<br>pengereman diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Kondisi normal atau tidak<br>normal diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Service limit dari setiap<br>komponen bagian sistem<br>pengereman diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Jenis-jenis minyak rem<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Standar penyetelan<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur bongkar pasang<br>komponen pengereman<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.10 Prosedur bongkar pasang<br>bagian-bagian komponen<br>pengereman diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 22 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.11 Tools, alat ukur, special<br>service tools/special tools,<br>alat dan bahan pembersih<br>kampas rem diidentifikasi<br>dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.12 Prosedur K3 diterapkan<br>pada pelaksanaan proses<br>kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan pada<br>sistem pengereman</td>
                <td style="vertical-align: top;">2.1 Sistem pengereman<br>diperiksa sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Komponen sistem<br>pengereman diperiksa<br>sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Bagian-bagian komponen<br>sistem pengereman<br>diperiksa sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi minyak rem<br>dipastikan sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>pengereman sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Komponen sistem<br>pengereman dibongkar<br>pasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Bagian komponen sistem<br>pengereman dibongkar<br>pasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Bagian-bagian sistem<br>pengereman dilumasi sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 4</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.032.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Kemudi</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>kemudi</td>
                <td style="vertical-align: top;">1.1 Fungsi dan cara kerja sistem<br>kemudi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Kondisi normal dan<br>ketidaknormalan sistem<br>kemudi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi, kondisi standar dan<br>cara kerja bagian-bagian<br>sistem kemudi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur bongkar pasang<br>headlight assy, cover body<br>dan cowl diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur bongkar pasang<br>sistem kemudi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 23 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur deteksi<br>ketidaknormalan setiap<br>bagian dari sistem kemudi<br>sepeda motor diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Prosedur penyetelan setiap<br>bagian dari sistem kemudi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 General tools, kunci torsi,<br>service special tools/special<br>tools diidentifikasi dan<br>disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Memastikan sistem<br>kemudi dalam<br>kondisi normal</td>
                <td style="vertical-align: top;">2.1 Identifikasi kenormalan dan<br>ketidaknormalan fungsi<br>sistem kemudi dilakukan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Pembongkaran sistem<br>kemudi dilakukan sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Bagian-bagian sistem kemudi<br>dilakukan penggantian<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Bagian-bagian dari sistem<br>kemudi dirakit sesuai<br>prosedur</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 5</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.033.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Perawatan Sistem Suspensi</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>perawatan sistem<br>suspensi</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, spesifikasi, dan<br>cara kerja dari komponen<br>pada sistem suspense<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi, jenis, spesifikasi, dan<br>cara kerja bagian bagian dari<br>sistem suspensi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Kondisi normal dan tidak<br>normal dari komponen-<br>komponen pada sistem<br>suspensi diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Kondisi dari bagian suspense<br>diidentifikasi sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Prosedur bongkar pasang<br>roda diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 24 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Prosedur bongkar pasang<br>sistem suspensi<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 General tools, alat ukur,<br>service special tools/special<br>tools, dan bahan pembersih<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>sistem suspensi</td>
                <td style="vertical-align: top;">2.1 Fungsi suspensi diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Kondisi dan kelengkapan<br>suspense diperiksa sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Kondisi oil seal/seal shock<br>diperiksa sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 Kondisi oli shock diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Kondisi inner tube diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.6 Kondisi spring diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.7 Kondisi bushing diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>suspensi sesuai<br>standar</td>
                <td style="vertical-align: top;">3.1 Bagian bagian sistem<br>suspense dibongkar sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Oil seal/seal shock, oli shock,<br>bushing diganti sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Komponen-komponen<br>suspense dibersihkan dan<br>dirakit sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 6</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.034.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Bongkar Pasang Cover Body</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>bongkarpasang<br>cover body</td>
                <td style="vertical-align: top;">1.1 Fungsi, jenis, dan spesifikasi<br>material dari komponen<br>pada cover body<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 25 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Fungsi dan kondisi dari<br>komponen pada cover body<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Bentuk dan ukuran dari<br>bagian cover body<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur bongkar pasang<br>cover body diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Perintah kerja diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Perlalatan yang sesuai<br>diidentifikasi dan disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Supporting tools dipastikan<br>ketersediaannya.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Pit area dan tempat<br>peletakan cover body<br>dipastikan kesiapannya.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pelepasan<br>komponen cover<br>body</td>
                <td style="vertical-align: top;">2.1 Cover body dilepas sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Ukuran, tipe baut, ring,<br>bushing dan damper cover<br>body diperiksa sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.3 Pengait cover body diperiksa<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.4 komponen penerangan<br>dilepas sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.5 Komponen spidometer<br>dilepas sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan cover<br>body terpasang<br>sesuai standar</td>
                <td style="vertical-align: top;">3.1. Komponen penerangan<br>dipasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen spidometer<br>dipasang sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Komponen-komponen cover<br>body dipasang sesuai<br>prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Baut pengikat, damper, ring,<br>clamp dipastikan terpasang<br>sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.5 Sistem kelistrikan dipastikan<br>berfungsi sesuai prosedur.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 26 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="unit-header-table">
        <colgroup>
            <col style="width: 43.0mm;">
            <col style="width: 22.0mm;">
            <col style="width: 4.5mm;">
            <col style="width: 100.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" class="text-bold text-center" style="vertical-align: middle;">Unit Kompetensi 7</td>
            <td class="text-bold">Kode Unit</td>
            <td class="text-center">:</td>
            <td class="nowrap">G.45TSM01.043.2</td>
        </tr>
        <tr>
            <td class="text-bold">Judul Unit</td>
            <td class="text-center">:</td>
            <td>Melakukan Pemeriksaan Chassis</td>
        </tr>
    </table>
    <table class="obs-table">
        <colgroup>
            <col style="width: 8.5mm;">
            <col style="width: 33.0mm;">
            <col style="width: 55.0mm;">
            <col style="width: 25.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 21.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Elemen</th>
                <th rowspan="2">Kriteria Unjuk Kerja</th>
                <th rowspan="2">Standar<br>Industri atau<br>Tempat Kerja</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">Penilaian<br>Lanjut</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">Menyiapkan<br>pemeriksaan chassis</td>
                <td style="vertical-align: top;">1.1 Fungsi dan jenis dari chassis<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;">BUKU<br>PEDOMMAN<br>REPARASI<br>.</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.2 Prosedur pemeriksaan fungsi an<br>jenis chassis diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.3 Fungsi dan kondisi komponen<br>chassis diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.4 Prosedur melepas dan merakit<br>komponen chassis<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.5 Service limit/batas toleransi dari<br>setiap komponen chassis<br>diidentifikasi.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.6 Kondisi posisi chassis<br>diidentifikasi sesuai standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.7 Tools, special tools, alat ukur,<br>alat dan bahan pembersih<br>chassis diidentifikasi dan<br>disiapkan.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">1.8 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">2</td>
                <td style="vertical-align: top;">Melakukan<br>pemeriksaan<br>komponen dari<br>chassis</td>
                <td style="vertical-align: top;">2.1 Kondisi chassis diperiksa sesuai<br>standar.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">2.2 Komponen-komponen dari<br>chassis diperiksa sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;">3</td>
                <td style="vertical-align: top;">Memastikan kinerja<br>chassis</td>
                <td style="vertical-align: top;">3.1 Komponen-komponen chassis<br>dibongkar sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.2 Komponen-komponen chassis<br>dibersihkan sesuai buku<br>pedoman reparasi/manual<br>book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.3 Komponen-komponen chassis<br>dirakit sesuai buku pedoman<br>reparasi/manual book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: top;"></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">3.4 Semua baut dan mur pada<br>komponen dari chassis<br>dipastikan kekencangannya<br>sesuai buku pedoman<br>reparasi/manual book.</td>
                <td class="text-center" style="vertical-align: top; font-size: 7.5pt;"></td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;">☐</td>
                <td class="text-center" style="vertical-align: middle;"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 27 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div style="font-weight: bold; margin-bottom: 2.0mm; font-size: 9.5pt;">Umpan Balik untuk asesi:</div>
    <div style="width: 169.5mm; height: 35.0mm; border: 1pt solid #000; margin-bottom: 3.0mm; padding: 2.0mm; font-size: 8.5pt;"></div>

    <table class="rekomendasi-table">
        <colgroup>
            <col style="width: 67.5mm;">
            <col style="width: 47.5mm;">
            <col style="width: 6.5mm;">
            <col style="width: 48.0mm;">
        </colgroup>
        <tr>
            <td rowspan="7" style="vertical-align: top; line-height: 1.25;">
                <strong>Rekomendasi:</strong><br><br>
                ☐ Asesi telah memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>KOMPETEN</strong><br><br>
                ☐ Asesi belum memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>BELUM KOMPETEN</strong><br>
                pada:<br>
                Kelompok Pekerjaan: …<br>
                Unit: …..<br>
                Elemen: …..<br>
                KUK: …….
            </td>
            <td colspan="3" style="font-weight: bold;">Asesi</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td class="text-center">:</td>
            <td>{{ $asesi->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td>Tanda tangan/ Tanggal</td>
            <td class="text-center">:</td>
            <td style="height: 10.0mm; vertical-align: bottom;"></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">Asesor</td>
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
            <td>Tanda tangan/ Tanggal</td>
            <td class="text-center">:</td>
            <td style="height: 10.0mm; vertical-align: bottom;"></td>
        </tr>
    </table>
</div>
@endsection
