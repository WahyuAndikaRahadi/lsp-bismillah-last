@extends('layouts.print-muk')

@section('title', 'FR.APL.02 Asesmen Mandiri - ' . ($asesi->full_name ?? ''))

@section('content')
<style>
    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, "Liberation Sans", Helvetica, sans-serif;
        font-size: 9.5pt;
        line-height: 1.15;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        max-height: 295mm;
        box-sizing: border-box;
        padding: 5.0mm 19.1mm 5.0mm 18.9mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .page-number {
        position: absolute;
        bottom: 8.0mm;
        left: 25.4mm;
        font-size: 10pt;
    }
    .kop-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
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
        width: 137.5mm;
        text-align: center;
        font-family: Tahoma, sans-serif;
        font-size: 12pt;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 10pt;
        font-weight: bold;
        line-height: 1.2;
        margin-bottom: 2.0mm;
    }
    .panduan-box {
        border: 1pt solid #000;
        padding: 1.5mm 2.5mm;
        margin-bottom: 2.0mm;
        font-size: 9.5pt;
        line-height: 1.2;
    }
    .panduan-box ul {
        margin: 1.0mm 0 0 4.0mm;
        padding: 0;
        list-style-type: disc;
    }
    .panduan-box li {
        margin-bottom: 0.5mm;
    }
    .meta-table {
        width: 110.0mm;
        border-collapse: collapse;
        margin-bottom: 2.0mm;
        font-size: 9.5pt;
    }
    .meta-table td {
        padding: 0.5mm 1.0mm;
        vertical-align: top;
    }
    .meta-table td.col-left {
        width: 42.0mm;
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
    .unit-box {
        border: 1pt solid #000;
        border-bottom: none;
        padding: 0.8mm 1.5mm;
        font-size: 9.5pt;
        background: #fff;
    }
    .apl-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 9pt;
        line-height: 1.15;
    }
    .apl-table th, .apl-table td {
        border: 1pt solid #000;
        padding: 0.4mm 1.0mm;
        vertical-align: top;
    }
    .apl-table th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        font-size: 9.5pt;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
</style>

{{-- ==================== PAGE 1 ==================== --}}
<div class="page-sheet">
    <div class="page-number">1</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="doc-main-title">FR.APL.02. ASESMEN MANDIRI</div>

    <table class="meta-table">
        <tr>
            <td class="col-left" rowspan="2">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
            <td class="col-mid">Judul</td>
            <td class="col-colon">:</td>
            <td class="col-val">{{ $scheme->name ?? 'Mekanik Junior' }}</td>
        </tr>
        <tr>
            <td class="col-mid">Nomor</td>
            <td class="col-colon">:</td>
            <td class="col-val">{{ $scheme->code ?? '05/LSP PrO Indo/I/2026' }}</td>
        </tr>
    </table>

    <div class="panduan-box">
        <strong>PANDUAN ASESMEN MANDIRI</strong><br>
        <strong>Instruksi:</strong>
        <ul>
            <li>Baca setiap pertanyaan di kolom sebelah kiri</li>
            <li>Beri tanda centang () pada kotak jika Anda yakin dapat melakukan tugas yang dijelaskan.</li>
            <li>Isi kolom di sebelah kanan dengan menuliskan bukti yang relevan anda miliki untuk menunjukkan bahwa anda melakukan pekerjaan</li>
        </ul>
    </div>
    <div class="unit-box">
        <strong>Unit Kompetensi 1</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.018.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Karburator</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2. Elemen:      Melakukan pemeriksaan</strong><br>
                    komponen dari sistem bahan bakar<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Aliran bahan bakar dari tangka<br>diperiksa sesuai prosedur.<br>2.2 2.2 Komponen sistem bahan bakar<br>diperiksa sesuai prosedur.<br>2.3 2.3 Karburator dilepas sesuai prosedur.<br>2.4 2.4 Kondisi dan kelengkapan karburator<br>diperiksa sesuai standar.<br>2.5 2.5 Buka-tutup throttle valve diperiksa<br>sesuai prosedur.<br>2.6 2.6 Fungsi air cut valve diperiksa sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 2 ==================== --}}
<div class="page-sheet">
    <div class="page-number">2</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja karburator</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen karburator<br>dibongkar sesuai prosedur.<br>3.2 Komponen-komponen karburator<br>dibersihkan sesuai prosedur.<br>3.3 Semua lubang pada komponen-<br>komponen karburator dipastikan tidak<br>tersumbat.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 2</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.020.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Renggang Klep</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, spesifikasi, dan cara<br>kerja dari komponen pada sistem<br>klep diidentifikasi.<br>1.2 Fungsi, jenis, spesifikasi, dan cara<br>kerja bagian dari sistem klep<br>diidentifikasi.<br>1.3 Kondisi normal dan tidak normal dari<br>komponen-komponen pada sistem<br>klep diidentifikasi.<br>1.4 Service limit dari renggang klep<br>diidentifikasi.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 Prosedur penyetelan renggang klep<br>diidentifikasi.<br>1.7 General tools, alat ukur, special<br>service tools/special tools<br>diidentifikasi dan disiapkan.<br>1.8 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:    Melakukan pengujian pak baterai</strong><br>
                    Melakukan Pemeriksaan Renggang Klep<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Timing hole cap, crankshaft hole cap,<br>dan cover cylinder head dilepas<br>sesuai prosedur.<br>2.2 Posisi top langkah kompresi<br>dipastikan sesuai prosedur.<br>2.3 Renggang klep disetel sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:     Memastikan Kinerja Klep Sesuai</strong><br>
                    Standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen klep, timing<br>hole cap, crankshaft hole cap, dan<br>cover cylinder head dipasang sesuai<br>prosedur.<br>3.2 Semua oil seal dan o-ring diganti<br>sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 3 ==================== --}}
<div class="page-sheet">
    <div class="page-number">3</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 3</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>:</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>:</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, spesifikasi, dan cara<br>kerja komponen pada sistem throttle<br>gas diidentifikasi.<br>1.2 Fungsi, jenis, spesifikasi, dan cara<br>kerja bagian-bagian sistem throttle<br>gas diidentifikasi.<br>1.3 Fungsi, kondisi, dan cara kerja<br>bagianbagian throttle gas<br>diidentifikasi.<br>1.4 Kondisi dan free play throttle gas<br>diidentifikasi sesuai standar.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 General tools dan bahan pembersih<br>disiapkan.<br>1.7 Prosedur K3 diterapkan dalam<br>melaksanakan unit kompetensi ini.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:  Melakukan pemeriksaan komponen dari</strong><br>
                    sistem throttle gas<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Kelancaran buka dan tutup throttle<br>gas diperiksa sesuai prosedur.<br>2.2 Kondisi dan kelengkapan sistem<br>throttle gas diperiksa sesuai standar.<br>2.3 Jalur/routing kabel gas diperiksa<br>sesuai prosedur.<br>2.4 Kondisi kabel gas diperiksa sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:  Memastikan kinerja throttle gas sesuai</strong><br>
                    standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Sistem throttle gas dilumasi sesuai<br>prosedur.<br>3.2 Komponen-komponen throttle gas<br>dirakit sesuai prosedur.<br>3.3 Mur penyetelan free play kabel gas<br>disetel sesuai standar.<br>3.4 Throttle gas dipastikan berfungsi<br>dengan normal.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 4</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>:</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>:</td></tr>
        </table>
    </div>
</div>

{{-- ==================== PAGE 4 ==================== --}}
<div class="page-sheet">
    <div class="page-number">4</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2. Elemen:  Melakukan pemeriksaan sistem filter</strong><br>
                    udara<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Rangkaian sistem filter udara<br>diperiksa sesuai prosedur.<br>2.2 Rangkaian sistem filter udara dilepas<br>dan dibuka sesuai prosedur.<br>2.3 Filter udara diperiksa sesuai<br>prosedur.<br>2.4 Kinerja sistem filter udara diperiksa<br>sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:  Memastikan kinerja sistem filter udara</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen sistem filter<br>udara dibersihkan sesuai prosedur.<br>3.2 3.2 Komponen-komponen sistem<br>filter udara dirakit sesuai prosedur.<br>3.3 3.3 Rangkaian sistem filter udara<br>dipasang sesuai prosedur.<br>3.4 3.4 Bagian yang terhubung dengan<br>karburator/throttle body diperiksa<br>sesuai prosedur.<br>3.5 3.5 Semua baut pada rangkaian<br>sistem filter udara dipastikan
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 5</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.023.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Sistem Clutch/Kopling</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 5 ==================== --}}
<div class="page-sheet">
    <div class="page-number">5</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.2 Fungsi, jenis, spesifikasi, dan cara</strong><br>
                    kerja bagian dari sistem<br>clutch/kopling diidentifikasi.<br>1.3 Kondisi normal dan tidak normal dari<br>komponen-komponen pada sistem<br>clutch/kopling diidentifikasi.<br>1.4 Kondisi dan free play handle<br>1.5 clutch/kopling diidentifikasi sesuai<br>standar.<br>1.6 Perintah kerja diidentifikasi.<br>1.7 General tools, alat ukur, special<br>service tools/special tools, supporting<br>tools (pressure cable lube) dan<br>bahan pembersih disiapkan.<br>1.8 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:     Melakukan pemeriksaan komponen<br>sistem kopling<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Kelancaran<br>kinerja<br>handle<br>clutch/kopling<br>diperiksa<br>sesuai<br>prosedur.<br>2.2 Kondisi dan kelengkapan sistem<br>clutch/kopling<br>diperiksa<br>sesuai<br>standar.<br>2.3 Alur/routing<br>kabel<br>clutch/kopling<br>diperiksa sesuai prosedur.<br>2.4 Kondisi<br>kabel<br>clutch/kopling<br>diperiksa sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:       Memastikan kinerja sistem kopling</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Kabel clutch/kopling dilumasi sesuai<br>prosedur.<br>3.2 Komponen-komponen<br>sistem<br>clutch/kopling dirakit sesuai prosedur.<br>3.3 Mur penyetelan kabel clutch/kopling<br>disetel sesuai prosedur.<br>3.4 Sistem<br>clutch/kopling<br>dipastikan<br>berfungsi normal.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.024.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 6</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td></td></tr>
        </table>
    </div>
    <div class="unit-box">
        <strong>Unit Kompetensi</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan pemeriksaan dan penggantian oli</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, spesifikasi, dan cara<br>kerja filter oli diidentifikasi.<br>1.2 1.2 Jalur sistem pelumasan<br>diidentifikasi.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 6 ==================== --}}
<div class="page-sheet">
    <div class="page-number">6</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.3 1.3 Prosedur pemeriksaan oli mesin</strong><br>
                    dan oli transmisi, dan filter oli<br>diidentifikasi.<br>1.4 Service limit oli mesin, oli transmisi<br>dan filter oli diidentifikasi.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 General tools, alat ukur dan bahan<br>pembersih disiapkan.<br>1.7 Prosedur penggantian oli mesin, oli<br>transmisi, dan filter oli diidentifikasi.<br>1.8 Prosedur bongkar pasang cover<br>cowling diidentifikasi.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:     Memastikan kinerja dari oli mesin, oli<br>transmisi, dan filter oli<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Oli mesin dan oli transmisi, dan filter<br>oli diperiksa sesuai prosedur.<br>2.2 Oli mesin, oli transmisi, dan filter oli<br>diganti sesuai prosedur.<br>2.3 Kebocoran oli dipastikan tidak ada.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.025.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 7</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Pemeriksaan Sistem Pelumasan Mesin</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi dan jenis sistem pelumasan<br>mesin diidentifikasi.<br>1.2 Fungsi, jenis, dan cara kerja sistem<br>pelumasan mesin diidentifikasi.<br>1.3 Fungsi, kondisi, dan cara kerja<br>bagian-bagian sistem pelumasan<br>mesin diidentifikasi.<br>1.4 Service limit dari setiap komponen<br>bagian sistem pelumasan mesin<br>diidentifikasi.<br>1.5 Kondisi posisi pelumas mesin<br>diidentifikasi sesuai standar.<br>1.6 General tools, alat ukur, supporting<br>tools dan bahan pembersih sistem<br>pelumasan mesin disiapkan.<br>1.7 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:     Melakukan pemeriksaan komponen</strong><br>
                    dari sistem pelumasan mesin<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Tekanan/aliran oli di sistem<br>pelumasan mesin diperiksa sesuai<br>prosedur.<br>2.2 Saringan oli (oil strainer screen)<br>saringan oli sentrifugal dan gear<br>pompa oli dilepas sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 7 ==================== --}}
<div class="page-sheet">
    <div class="page-number">7</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2.3 Kondisi dan kelengkapan sistem</strong><br>
                    pelumasan mesin diperiksa sesuai<br>standar.<br>3. Elemen:     Memastikan kinerja sistem pelumasan<br>mesin<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen sistem<br>pelumasan mesin dibongkar sesuai<br>prosedur.<br>3.2 Komponen-komponen sistem<br>pelumasan mesin dibersihkan<br>sesuai prosedur.<br>3.3 Semua saluran di sistem pelumasan<br>mesin dipastikan tidak tersumbat.<br>3.4 Komponen-komponen sistem<br>pelumasan mesin dirakit sesuai<br>prosedur.<br>3.5 Kekencangan semua baut pada<br>komponen sistem pelumasan<br>dipastikan sesuai standar.<br>3.6 Kinerja sistem pelumasan dipastikan<br>sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.026.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 8</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Busi</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Jenis dan fungsi busi diidentifikasi.<br>1.2 Fungsi, kondisi standar, dan cara<br>kerja bagian-bagian busi<br>diidentifikasi.<br>1.3 Prosedur perawatan busi<br>diidentifikasi.<br>1.4 Service limit busi diidentifikasi.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 Tools, alat ukur, special service<br>tools/special tools, alat dan bahan<br>pembersih diidentifikasi dan<br>disiapkan.<br>1.7 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2</strong><br>
                    Elemen:      Memastikan busi terpasang dengan<br>benar<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Prosedur bongkar pasang busi<br>dilakukan berdasarkan metode/cara<br>dan perlengkapan yang sesuai<br>dengan spesifikasi pabrik.<br>2.2 Prosedur pembersihan busi<br>dilakukan sesuai buku pedoman<br>reparasi/manual book.<br>2.3 Prosedur pemeriksaan busi<br>dilakukan sesuai buku pedoman<br>reparasi/manual book.<br>2.4 Prosedur pemeriksaan cap<br>oise/tutup busi dilakukan sesuai
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 8 ==================== --}}
<div class="page-sheet">
    <div class="page-number">8</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 9</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.029.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Penggantian Rantai Roda</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2. Elemen:      Melakukan pemeriksaan bagian dari</strong><br>
                    rantai roda<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Kondisi rantai roda diperiksa sesuai<br>prosedur.<br>2.2 Kondisi gear sprocket depan dan<br>belakang diperiksa sesuai prosedur.<br>2.3 Kondisi coupling damper diperiksa<br>sesuai prosedur.<br>2.4 Kondisi penyetel rantai roda<br>diperiksa.<br>2.5 Rantai roda dibongkar sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja rantai roda</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Semua circlip gear sprocket roda<br>diganti sesuai prosedur.<br>3.2 Bagian rantai roda dirakit sesuai<br>prosedur.<br>3.3 Semua baut yang berhubungan<br>dengan rantai roda dipastikan sudah<br>dikencangkan sesuai prosedur.<br>3.4 Rantai roda disetel dan dipastikan<br>berfungsi sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 9 ==================== --}}
<div class="page-sheet">
    <div class="page-number">9</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 10</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.030.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Pemeriksaan Keolengan Roda</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi dan cara kerja bagian roda<br>diidentifikasi.<br>1.2 Fungsi, kondisi standar, dan cara<br>kerja bagian roda diidentifikasi.<br>1.3 Kondisi normal dan tidak normal<br>bagian roda diidentifikasi.<br>1.4 Prosedur bongkar pasang roda<br>diidentifikasi.<br>1.5 Prosedur penyetelan velg<br>diidentifikasi.<br>1.6 Prosedur memastikan kenormalan<br>fungsi roda diidentifikasi.<br>1.7 Perintah kerja diidentifikasi.<br>1.8 Tools, alat ukur, dan special service<br>tools/special tools diidentifikasi dan<br>disiapkan.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:     Melakukan pemeriksaan fungsi roda</strong><br>
                    •<br>Kriteria Unjuk Kerja:<br>2.1 Fungsi roda di periksa sesuai<br>prosedur.<br>2.2 Roda di bongkar sesuai prosedur.<br>2.3 Kondisi dan kelengkapan roda di<br>periksa sesui standar.<br>2.4 Kondisi keolengan roda dipastikan<br>sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:     Memastikan fungsi setiap bagian roda</strong><br>
                    •<br>Kriteria Unjuk Kerja:<br>3.1 Bagian-bagian roda dirakit sesuai<br>prosedur.<br>3.2 Kekencangan mur dan baut as roda<br>dipastikan sesuai prosedur.<br>3.3 Roda dipastikan berfungsi sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 11</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.031.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan sistem pengereman</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, dan spesifikasi sistem<br>pengereman diidentifikasi.<br>1.2 Fungsi, jenis, spesifikasi, dan cara<br>kerja komponen dari sistem<br>pengereman diidentifikasi.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 10 ==================== --}}
<div class="page-sheet">
    <div class="page-number">10</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.3 Fungsi, kondisi dan cara kerja</strong><br>
                    bagianbagian komponen sistem<br>pengereman diidentifikasi.<br>1.4 Kondisi normal atau tidak normal<br>diidentifikasi.<br>1.5 Service limit dari setiap komponen<br>bagian sistem pengereman<br>diidentifikasi.<br>1.6 Jenis-jenis minyak rem diidentifikasi.<br>1.7 Standar penyetelan diidentifikasi.<br>1.8 Perintah kerja diidentifikasi.<br>1.9 Prosedur bongkar pasang komponen<br>pengereman diidentifikasi.<br>1.10 Prosedur bongkar pasang bagian-<br>bagian komponen pengereman<br>diidentifikasi.<br>1.11 Tools, alat ukur, special service<br>tools/special tools, alat dan bahan<br>pembersih kampas rem diidentifikasi<br>dan disiapkan.<br>1.12 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:    Melakukan pemeriksaan pada sistem<br>pengereman<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Sistem pengereman diperiksa sesuai<br>standar.<br>2.2 Komponen sistem pengereman<br>diperiksa sesuai standar.<br>2.3 Bagian-bagian komponen sistem<br>pengereman diperiksa sesuai<br>standar.<br>2.4 Kondisi minyak rem dipastikan sesuai<br>standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:    Memastikan kinerja pengereman</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen sistem pengereman<br>dibongkar pasang sesuai prosedur.<br>3.2 Bagian komponen sistem<br>pengereman dibongkar pasang<br>sesuai prosedur.<br>3.3 Bagian-bagian sistem pengereman<br>dilumasi sesuai prosedur.<br>3.4 Free play tuas atau pedal rem disetel<br>sesuai standar.<br>3.5 Kinerja sistem pengereman<br>dipastikan berfungsi sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.032.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 12</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan sistem kemudi</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 11 ==================== --}}
<div class="page-sheet">
    <div class="page-number">11</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.2 Kondisi normal dan ketidaknormalan</strong><br>
                    sistem kemudi diidentifikasi.<br>1.3 Fungsi, kondisi standar dan cara<br>kerja bagian-bagian sistem kemudi<br>diidentifikasi.<br>1.4 Prosedur bongkar pasang headlight<br>assy, cover body dan cowl<br>diidentifikasi.<br>1.5 Prosedur bongkar pasang sistem<br>kemudi diidentifikasi.<br>1.6 Prosedur deteksi ketidaknormalan<br>setiap bagian dari sistem kemudi<br>sepeda motor diidentifikasi.<br>1.7 Prosedur penyetelan setiap bagian<br>dari sistem kemudi diidentifikasi.<br>1.8 General tools, kunci torsi, service<br>special tools/special tools<br>diidentifikasi dan disiapkan.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:    Memastikan sistem  kemudi dalam<br>kondisi normal<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Identifikasi kenormalan dan<br>ketidaknormalan fungsi sistem<br>kemudi dilakukan.<br>2.2 Pembongkaran sistem kemudi<br>dilakukan sesuai prosedur.<br>2.3 Bagian-bagian sistem kemudi<br>dilakukan penggantian sesuai<br>prosedur.<br>2.4 Bagian-bagian dari sistem kemudi<br>dirakit sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 13</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.033.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan sistem suspensi</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, spesifikasi, dan cara<br>kerja dari komponen pada sistem<br>suspense diidentifikasi.<br>1.2 Fungsi, jenis, spesifikasi, dan cara<br>kerja bagian bagian dari sistem<br>suspensi diidentifikasi.<br>1.3 Kondisi normal dan tidak normal dari<br>komponen-komponen pada sistem<br>suspensi diidentifikasi.<br>1.4 Kondisi dari bagian suspense<br>diidentifikasi sesuai standar.<br>1.5 Prosedur bongkar pasang roda<br>diidentifikasi.<br>1.6 Prosedur bongkar pasang sistem<br>suspensi diidentifikasi.<br>1.7 Perintah kerja diidentifikasi.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 12 ==================== --}}
<div class="page-sheet">
    <div class="page-number">12</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.8 General tools, alat ukur, service</strong><br>
                    special tools/special tools, dan bahan<br>pembersih diidentifikasi dan<br>disiapkan.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:    Melakukan pemeriksaan komponen<br>dari sistem suspensi<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Fungsi suspensi diperiksa sesuai<br>prosedur.<br>2.2 Kondisi dan kelengkapan suspense<br>diperiksa sesuai standar.<br>2.3 Kondisi oil seal/seal shock diperiksa<br>sesuai prosedur.<br>2.4 Kondisi oli shock diperiksa sesuai<br>prosedur.<br>2.5 Kondisi inner tube diperiksa sesuai<br>prosedur.<br>2.6 Kondisi spring diperiksa sesuai<br>prosedur.<br>2.7 Kondisi bushing diperiksa sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:     Memastikan kinerja suspensi sesuai</strong><br>
                    standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Bagian bagian sistem suspense<br>dibongkar sesuai prosedur.<br>3.2 Oil seal/seal shock, oli shock,<br>bushing diganti sesuai prosedur.<br>3.3 Komponen-komponen suspense<br>dibersihkan dan dirakit sesuai<br>prosedur.<br>3.4 Baut dan mur pada sistem suspense<br>dipastikan terpasang sesuai<br>prosedur.<br>Kode Unit<br>: G.45TSM01.034.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 14</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan sistem suspensi</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, dan spesifikasi material<br>dari komponen pada cover body<br>diidentifikasi.<br>1.2 Fungsi dan kondisi dari komponen<br>pada cover body diidentifikasi.<br>1.3 Bentuk dan ukuran dari bagian cover<br>body diidentifikasi.<br>1.4 Prosedur bongkar pasang cover<br>body diidentifikasi.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 Perlalatan yang sesuai diidentifikasi<br>dan disiapkan.<br>1.7 Supporting tools dipastikan<br>ketersediaannya.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 13 ==================== --}}
<div class="page-sheet">
    <div class="page-number">13</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.8 Pit area dan tempat peletakan cover</strong><br>
                    body dipastikan kesiapannya.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:    Melakukan pelepasan komponen<br>cover body<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Cover body dilepas sesuai prosedur.<br>2.2 Ukuran, tipe baut, ring, bushing dan<br>damper cover body diperiksa sesuai<br>standar.<br>2.3 Pengait cover body diperiksa sesuai<br>prosedur.<br>2.4 komponen penerangan dilepas<br>sesuai prosedur.<br>2.5 Komponen spidometer dilepas<br>sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:     Memastikan cover body terpasang</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen penerangan dipasang<br>sesuai prosedur.<br>3.2 Komponen spidometer dipasang<br>sesuai prosedur.<br>3.3 Komponen-komponen cover body<br>dipasang sesuai prosedur.<br>3.4 Baut pengikat, damper, ring, clamp<br>dipastikan terpasang sesuai<br>prosedur.<br>3.5 Sistem kelistrikan dipastikan<br>berfungsi sesuai prosedur.<br>Kode Unit<br>: G.45TSM01.039.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 15</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan Baterai</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2. Elemen:     Melakukan pemeriksaan baterai</strong><br>
                    •<br>Kriteria Unjuk Kerja:<br>2.1 Baterai dilepas sesuai prosedur.<br>☐<br>☐
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 14 ==================== --}}
<div class="page-sheet">
    <div class="page-number">14</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja baterai sesuai</strong><br>
                    standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Baterai diperiksa kinerjanya saat<br>sistem beban aktif.<br>3.2 Baterai diperiksa pada saat setelah<br>sistem pengisian/charging.<br>3.3 Baterai dipastikan berfungsi sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.019.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 16</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Sistem Injeksi</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>2. Elemen:     Melakukan Pemeriksaan Komponen</strong><br>
                    dari Sistem Injeksi<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Ketidaknormalan sistem injeksi<br>diperiksa sesuai prosedur.<br>2.2 2.2 Kelancaran buka-tutup katup<br>throttle body diperiksa sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan Kinerja Sistem Injeksi</strong><br>
                    Sesuai Standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Tekanan dan aliran bahan bakar<br>pompa injeksi dipastikan sesuai<br>standar.<br>3.2 Injektor dibersihkan sesuai<br>prosedur.<br>3.3 Air screw disetel saat mesin hidup<br>sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 15 ==================== --}}
<div class="page-sheet">
    <div class="page-number">15</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 17</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G45.TSM01.027.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Sistem Continuosly Variable Tansmission</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria unjuk Kerja<br>1.1 Fungsi, jenis, spesifikasi, dan cara<br>kerja bagian dari sistem CVT<br>diidentifikasi.<br>1.2 Kondisi normal dan tidak normal dari<br>komponen-komponen pada sistem<br>CVT diidentifikasi.<br>1.3 Service limit dari setiap komponen<br>CVT diidentifikasi.<br>1.4 Perintah kerja diidentifikasi.<br>1.5 Tools, alat ukur, special service<br>tools/special tools, alat dan bahan<br>pembersih diidentifikasi dan<br>disiapkan.<br>1.6 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:     Melakukan pemeriksaan komponen</strong><br>
                    dari sistem CVT<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Fungsi sistem CVT diperiksa sesuai<br>prosedur.<br>2.2 CVT dibongkar sesuai prosedur.<br>2.3 Kondisi dan kelengkapan CVT<br>diperiksa sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja CVT sesuai</strong><br>
                    standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Semua o-ring dan gasket diganti<br>sesuai prosedur.<br>3.2 Komponen-komponen CVT dirakit<br>dan dipasang sesuai prosedur.<br>3.3 Pengencangan baut dilakukan<br>secaraberurutan menyilang.<br>3.4 CVT dipastikan berfungsi sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.028.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 18</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Perawatan Sistem Pendinginan</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 16 ==================== --}}
<div class="page-sheet">
    <div class="page-number">16</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.1 Fungsi, jenis, spesifikasi, dan cara</strong><br>
                    kerja dari komponen pada sistem<br>pendingin diidentifikasi.<br>1.2 Fungsi, jenis, spesifikasi, dan cara<br>kerja bagian dari sistem pendingin<br>diidentifikasi.<br>1.3 Kondisi normal dan tidak normal dari<br>komponen-komponen pada sistem<br>pendingin diidentifikasi.<br>1.4 Standar dari setiap komponen<br>sistem pendingin diidentifikasi.<br>1.5 Perintah kerja diidentifikasi.<br>1.6 Tools, alat ukur, special service<br>tools/special tools diidentifikasi dan<br>disiapkan sesuai prosedur.<br>1.7 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:     Melakukan pemeriksaan komponen<br>sistem pendingin<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Fungsi sistem pendingin diperiksa<br>sesuai prosedur.<br>2.2 Bagian sistem pendingin dibongkar<br>sesuai prosedur.<br>2.3 Kondisi dan kelengkapan komponen<br>bagian sistem pendingin diperiksa<br>sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja sistem pendingin</strong><br>
                    sesuai standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Semua seal oil, o-ring dan gasket<br>diganti sesuai prosedur.<br>3.2 Komponen-komponen bagian sistem<br>pendingin dirakit sesuai prosedur.<br>3.3 Bagian sistem pendingin dipasang<br>sesuai prosedur.<br>3.4 Semua baut pada sistem pendingin<br>dipastikan sudah dikencangkan<br>sesuai prosedur.<br>3.5 Bagian sistem pendingin diperiksa<br>kinerjanya saat kondisi mesin dingin<br>dan panas sesuai prosedur.<br>3.6 Sistem pendingin dipastikan<br>berfungsi sesuai  prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 19</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.040.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan perawatan instrumen kelistrikan (sistem sinyal dan</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi, jenis, spesifikasi, wiring, dan<br>cara kerja dari komponen pada
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 17 ==================== --}}
<div class="page-sheet">
    <div class="page-number">17</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>instrumen kelistrikan (sistem sinyal</strong><br>
                    dan penerangan) diidentifikasi.<br>1.2 Kondisi normal dan tidak normal dari<br>komponen-komponen pada istrumen<br>kelistrikan (sistem sinyal dan<br>penerangan) diidentifikasi.<br>1.3 Standar kinerja dari setiap<br>komponen instrumen kelistrikan<br>(sistem sinyal dan penerangan)<br>diidentifikasi.<br>1.4 Prosedur bongkar pasang<br>instrument kelistrikan diidentifikasi.<br>1.5 Prosedur pemeriksaan sistem<br>kelistrikan diidentifikasi.<br>1.6 Perintah kerja diidentifikasi.<br>1.7 General tools, alat ukur, dan alat<br>pembersih diidentifikasi dan<br>disiapkan.<br>1.8 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:     Melakukan pemeriksaan komponen<br>dari instrumen kelistrikan (sistem sinyal dan<br>penerangan)<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Fungsi instrumen kelistrikan (sistem<br>sinyal dan penerangan) diperiksa<br>sesuai prosedur.<br>2.2 Komponen dilepas sesuai prosedur.<br>2.3 Kondisi dan kelengkapan instrument<br>kelistrikan (sistem sinyal dan<br>penerangan) diperiksa sesuai<br>standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>3. Elemen:      Memastikan kinerja instrumen</strong><br>
                    kelistrikan (sistem sinyal dan penerangan) sesuai<br>standar<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen Instrumen<br>kelistrikan (sistem sinyal dan<br>penerangan) dirakit dan dipasang<br>sesuai prosedur.<br>3.2 Instrumen kelistrikan (sistem sinyal<br>dan penerangan) dipastikan<br>berfungsi sesuai standar.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.043.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 20</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Pemeriksaan Chassis</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Fungsi dan jenis dari chassis<br>diidentifikasi.<br>1.2 Prosedur pemeriksaan fungsi an<br>jenis chassis diidentifikasi.<br>1.3 Fungsi dan kondisi komponen<br>chassis diidentifikasi.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 18 ==================== --}}
<div class="page-sheet">
    <div class="page-number">18</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.4 Prosedur melepas dan merakit</strong><br>
                    komponen chassis diidentifikasi.<br>1.5 Service limit/batas toleransi dari<br>setiap komponen chassis<br>diidentifikasi.<br>1.6 Kondisi posisi chassis diidentifikasi<br>sesuai standar.<br>1.7 Tools, special tools, alat ukur, alat<br>dan bahan pembersih chassis<br>diidentifikasi dan disiapkan.<br>1.8 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>•<br>Elemen:    Melakukan pemeriksaan komponen<br>dari chassis<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Kondisi chassis diperiksa sesuai<br>standar.<br>2.2 Komponen-komponen dari chassis<br>diperiksa sesuai buku pedoman<br>reparasi/manual book.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Elemen:     Memastikan kinerja chassis<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Komponen-komponen chassis<br>dibongkar sesuai buku pedoman<br>reparasi/manual book.<br>3.2 Komponen-komponen chassis<br>dibersihkan sesuai buku pedoman<br>reparasi/manual book.<br>3.3 Komponen-komponen chassis<br>dirakit sesuai buku pedoman<br>reparasi/manual book.<br>3.4 Semua baut dan mur pada<br>komponen dari chassis dipastikan<br>kekencangannya sesuai buku<br>pedoman reparasi/manual book.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 21</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td>: G.45TSM01.053.2</td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Melakukan Pembersihan Injector</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 19 ==================== --}}
<div class="page-sheet">
    <div class="page-number">19</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>1.7 Prosedur pelepasan dan</strong><br>
                    pemasangan alat pembersih injector<br>ke injector diidentifikasi.<br>1.8 Prosedur pembersihan injector<br>diidentifikasi.<br>1.9 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.<br>2. Elemen:     Melakukan perawatan injector<br>• Kriteria Unjuk Kerja:<br>2.1 Pembongkaran cover body, dan fuel<br>tank dilakukan sesuai prosedur.<br>2.2 Alat pembersih injector disiapkan.<br>2.3 Pembersihan injector dilakukan<br>sesuai prosedur.<br>2.4 Injector dipastikan berfungsi normal<br>setelah dibersihkan.<br>2.5 Pemasangan cover body, dan fuel<br>tank dilakukan sesuai prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>Kode Unit</strong><br>
                    : G.45TSM01.055.2
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="unit-box">
        <strong>Unit Kompetensi 22</strong><br>
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
            <tr><td style="width: 25.0mm;">Kode Unit</td><td style="width: 4.0mm;">:</td><td></td></tr>
            <tr><td>Judul Unit</td><td>:</td><td>: Mengoperasikan Diagnostic Tools</td></tr>
        </table>
    </div>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>•</strong><br>
                    Kriteria Unjuk Kerja:<br>1.1 Cara pengoperasian diagnostic tools<br>dan tampilan data diidentifikasi.<br>1.2 Jenis, fungsi dan cara kerja sistem<br>injeksi diidentifikasi.<br>1.3  Kondisi normal sistem injeksi<br>sepeda motor diidentifikasi.<br>1.4 Kondisi ketidaknormalan bagian dari<br>sensor dan actuator diidentifikasi.<br>1.5 Tools dan alat ukur untuk setiap<br>ketidaknormalan sistem injeksi<br>sepeda motor didentifikasi.<br>1.6 Prosedur bongkar pasang cover<br>body diidentifikasi.<br>1.7 Prosedur bongkar pasang setiap<br>bagian sistem injeksi diidentifikasi.<br>1.8 Prosedur deteksi ketidak normalan<br>sistem injeksi sepeda motor<br>diidentifikasi.<br>1.9 Prosedur troubleshooting<br>ketidaknormalan sistem injeksi<br>diidentifikasi.<br>1.10 Prosedur K3 diterapkan pada<br>pelaksanaan proses kerja.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <strong>2. Elemen:     Mengidentifikasi standar kondisi</strong><br>
                    normal sistem injeksi<br>•<br>Kriteria Unjuk Kerja:<br>2.1 Standar kondisi normal pada bagian<br>sensor diidentifikasi.<br>2.2 Standar kondisi normal pada bagian<br>actuator diidentifikasi.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ==================== PAGE 20 ==================== --}}
<div class="page-sheet">
    <div class="page-number">20</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <table class="apl-table">
        <colgroup>
            <col style="width: 94.4mm;">
            <col style="width: 9.5mm;">
            <col style="width: 9.5mm;">
            <col style="width: 58.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Dapatkah Saya ................?</th>
                <th>K</th>
                <th>BK</th>
                <th>Bukti yang relevan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>3. Elemen:      Menentukan sistem injeksi yang tidak</strong><br>
                    normal<br>•<br>Kriteria Unjuk Kerja:<br>3.1 Gejala ketidaknormalan sistem<br>injeksi dianalisis sesuai prosedur<br>untuk mencari bagian sensor dan<br>actuator yang tidak normal.<br>3.2 3.2 Cover body dibongkar sesuai<br>prosedur.<br>3.3 3.3 Bagian sensor dan atau actuator<br>yang tidak normal dibongkar sesuai<br>prosedur.<br>3.4 3.4 Tegangan, Tahanan standar dan<br>servis Limit komponen sensor diukur<br>untuk menentukan yang tidak<br>normal.<br>3.5 3.5 Penyebab ketidaknormalan<br>komponen dianalisis sesuai<br>prosedur.
                </td>
                <td class="text-center font-bold">☐</td>
                <td class="text-center font-bold">☐</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div style="font-weight: bold; margin-top: 3.0mm; margin-bottom: 2.0mm;">
        Asesmen dapat / tidak dapat dilanjutkan
    </div>
    <table class="apl-table" style="width: 172.0mm;">
        <tr>
            <td style="width: 25.0mm; font-weight: bold;">Nama</td>
            <td style="width: 60.0mm;">{{ $asesi->full_name ?? '' }}</td>
            <td style="width: 35.0mm; font-weight: bold;">Tanda tangan/<br>Tanggal</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4" style="background: #f1f5f9; font-weight: bold;">Ditinjau Oleh Asesor :</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama :</td>
            <td>{{ $leadAssessor->full_name ?? '' }}</td>
            <td rowspan="2" style="font-weight: bold; vertical-align: middle;">Tanda tangan/<br>Tanggal</td>
            <td rowspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">No. Reg:</td>
            <td>{{ $leadAssessor->profile?->no_reg_asesor ?? '' }}</td>
        </tr>
    </table>
</div>
@endsection
