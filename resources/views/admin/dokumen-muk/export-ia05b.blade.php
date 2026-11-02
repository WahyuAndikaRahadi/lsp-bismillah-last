@extends('layouts.print-muk')

@section('title', 'FR.IA.05.B Lembar Kunci Jawaban - ' . ($scheme->name ?? ''))

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
        padding: 8.0mm 15.0mm 10.0mm 23.0mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .kop-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 3.5mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 32mm;
        height: 18mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 26mm;
        max-height: 16mm;
    }
    .kop-text {
        width: 140mm;
        text-align: center;
        font-size: 11pt;
        line-height: 1.25;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.2;
        margin-bottom: 3.0mm;
    }
    .meta-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 9.5pt;
        margin-bottom: 3.0mm;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.8mm 1.5mm;
        vertical-align: middle;
    }
    .key-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.15;
    }
    .key-table tr {
        height: 7.6mm;
    }
    .key-table td, .key-table th {
        border: 1pt solid #000;
        padding: 0.5mm 1.2mm;
        box-sizing: border-box;
    }
    .key-table th {
        font-weight: bold;
        text-align: center;
        background-color: #f1f5f9;
    }
    .center-cell {
        text-align: center;
        vertical-align: middle;
    }
    .sign-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.2;
        margin-top: 4.0mm;
    }
    .sign-table tr {
        height: 8.5mm;
    }
    .sign-table td, .sign-table th {
        border: 1pt solid #000;
        padding: 0.8mm 1.5mm;
        vertical-align: middle;
    }
    .sign-table th {
        font-weight: bold;
        text-align: center;
    }
</style>

@php
    $schemeName = $scheme->name ?? 'Mekanik Junior';
    $schemeCode = $scheme->code ?? '05/LSP PrO Indo/I/2026';
    $tukName = $schedule->tuk_name ?? 'Sewaktu/Tempat Kerja/Mandiri*';
    
    // Exact keys from master document
    $keys = [
        1 => 'C', 2 => 'B', 3 => 'C', 4 => 'C', 5 => 'B',
        6 => 'C', 7 => 'D', 8 => 'A', 9 => 'B', 10 => 'D',
        11 => 'C', 12 => 'D', 13 => 'C', 14 => 'B', 15 => 'A',
        16 => 'B', 17 => 'D', 18 => 'A', 19 => 'B', 20 => 'C',
        21 => 'D', 22 => 'B', 23 => 'D', 24 => 'B', 25 => 'C',
        26 => 'C', 27 => 'B', 28 => 'D', 29 => 'B', 30 => 'A',
        31 => 'D', 32 => 'B', 33 => 'D', 34 => 'D', 35 => 'C',
        36 => 'B', 37 => 'C', 38 => 'C', 39 => 'B', 40 => 'B',
        41 => 'A', 42 => 'D', 43 => 'A', 44 => 'B', 45 => 'A',
        46 => 'A', 47 => 'A', 48 => 'C', 49 => 'C', 50 => 'B',
        51 => 'C', 52 => 'C', 53 => 'C', 54 => 'D', 55 => 'A',
        56 => 'B', 57 => 'C', 58 => 'D', 59 => 'D', 60 => 'D',
        61 => 'A', 62 => 'B', 63 => 'A', 64 => 'B', 65 => 'B',
        66 => 'A', 67 => 'A', 68 => 'C', 69 => 'C', 70 => 'D',
        71 => 'B', 72 => 'B', 73 => 'C', 74 => 'C', 75 => 'C',
    ];
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
        FR.IA.05.B. DPT - LEMBAR KUNCI JAWABAN PERTANYAAN TERTULIS PILIHAN GANDA
    </div>

    <table class="meta-table">
        <colgroup>
            <col style="width: 48mm;">
            <col style="width: 18mm;">
            <col style="width: 5mm;">
            <col style="width: 101.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td rowspan="2">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
                <td>Judul</td>
                <td style="text-align: center;">:</td>
                <td style="font-style: italic;">{{ $schemeName }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td style="text-align: center;">:</td>
                <td>{{ $schemeCode }}</td>
            </tr>
            <tr>
                <td colspan="2">TUK</td>
                <td style="text-align: center;">:</td>
                <td>Sewaktu/Tempat Kerja/Mandiri*</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2.0mm;">
        Kunci Jawaban Pertanyaan Tertulis – Pilihan Ganda:
    </div>

    <table class="key-table">
        <colgroup>
            <col style="width: 15.0mm;">
            <col style="width: 71.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 71.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kunci Jawaban</th>
                <th>No.</th>
                <th>Kunci Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 25; $i++)
                @php $r = $i + 25; @endphp
                <tr>
                    <td class="center-cell">{{ $i }}.</td>
                    <td class="center-cell" style="font-weight: bold;">{{ $keys[$i] ?? '' }}</td>
                    <td class="center-cell">{{ $r }}.</td>
                    <td class="center-cell" style="font-weight: bold;">{{ $keys[$r] ?? '' }}</td>
                </tr>
            @endfor
            <tr>
                <th>No.</th>
                <th>Kunci Jawaban</th>
                <th>No.</th>
                <th>CKunci Jawaban</th>
            </tr>
            <tr>
                <td class="center-cell">51.</td>
                <td class="center-cell" style="font-weight: bold;">{{ $keys[51] ?? 'C' }}</td>
                <td class="center-cell">66.</td>
                <td class="center-cell" style="font-weight: bold;">{{ $keys[66] ?? 'A' }}</td>
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

    <table class="key-table">
        <colgroup>
            <col style="width: 15.0mm;">
            <col style="width: 71.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 71.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kunci Jawaban</th>
                <th>No.</th>
                <th>Kunci Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 52; $i <= 65; $i++)
                @php $r = $i + 15; @endphp
                <tr>
                    <td class="center-cell">{{ $i }}.</td>
                    <td class="center-cell" style="font-weight: bold;">{{ $keys[$i] ?? '' }}</td>
                    <td class="center-cell">{{ $r <= 75 ? $r . '.' : '' }}</td>
                    <td class="center-cell" style="font-weight: bold;">{{ $r <= 75 ? ($keys[$r] ?? '') : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div style="font-size: 9.5pt; font-weight: bold; margin-top: 5.0mm; margin-bottom: 2.0mm;">
        PENYUSUN DAN VALIDATOR
    </div>

    <table class="sign-table">
        <colgroup>
            <col style="width: 25.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 45.0mm;">
            <col style="width: 42.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th>STATUS</th>
                <th>NO</th>
                <th>NAMA</th>
                <th>NOMOR MET</th>
                <th>TANDA TANGAN<br>DAN TANGGAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="2" class="center-cell" style="font-weight: bold;">PENYUSUN</td>
                <td class="center-cell">1</td>
                <td>GUNADI</td>
                <td>000.000459 2007</td>
                <td>27 Januari 2026</td>
            </tr>
            <tr>
                <td class="center-cell">2</td>
                <td>SAPTONO</td>
                <td>000.003672 2016</td>
                <td>27 Januari 2026</td>
            </tr>
            <tr>
                <td rowspan="3" class="center-cell" style="font-weight: bold;">VALIDATOR</td>
                <td class="center-cell">1</td>
                <td>RANDI ARDIANSYAH</td>
                <td>000.002498 2013</td>
                <td>27 Januari 2026</td>
            </tr>
            <tr>
                <td class="center-cell">2</td>
                <td>KARYANTO</td>
                <td>000.008260 2025</td>
                <td>27 Januari 2026</td>
            </tr>
            <tr>
                <td class="center-cell">3</td>
                <td>RIDWAN</td>
                <td>000.008265 2025</td>
                <td>27 Januari 2026</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
