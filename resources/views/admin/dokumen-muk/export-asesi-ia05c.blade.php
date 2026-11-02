@extends('layouts.print-muk')

@section('title', 'FR.IA.05.C Lembar Jawaban Pilihan Ganda - ' . ($asesi->full_name ?? ''))

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
        margin-bottom: 2.0mm;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.8mm 1.5mm;
        vertical-align: middle;
    }
    .ans-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.15;
    }
    .ans-table tr {
        height: 7.2mm;
    }
    .ans-table td, .ans-table th {
        border: 1pt solid #000;
        padding: 0.4mm 1.0mm;
        box-sizing: border-box;
    }
    .ans-table th {
        font-weight: bold;
        text-align: center;
        background-color: #f1f5f9;
    }
    .center-cell {
        text-align: center;
        vertical-align: middle;
    }
    .box-sym {
        font-family: 'MS Gothic', 'DejaVu Sans', sans-serif;
        font-size: 9pt;
    }
    .feedback-box {
        width: 172.0mm;
        border: 1pt solid #000;
        padding: 2.0mm 3.0mm;
        box-sizing: border-box;
        font-size: 8.5pt;
        line-height: 1.25;
        margin-top: 4.0mm;
        margin-bottom: 4.0mm;
    }
    .sign-table {
        width: 172.0mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.25;
    }
    .sign-table td {
        border: 1pt solid #000;
        padding: 2.0mm 3.0mm;
        vertical-align: top;
        height: 25.0mm;
    }
</style>

@php
    $schemeName = $scheme->name ?? 'Mekanik Junior';
    $schemeCode = $scheme->code ?? '05/LSP PrO Indo/I/2026';
    $tukName = $schedule->tuk_name ?? 'Sewaktu/Tempat Kerja/Mandiri*';
    $assessorName = $assessor->full_name ?? ($assessor->name ?? ($leadAssessor->full_name ?? ''));
    $asesiName = $asesi->full_name ?? '';
    $dateStr = !empty($schedule->date) ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '';
    $cbtResponse = $iaResponses->firstWhere('instrument_code', 'IA05');
    $answers = $cbtResponse?->data_json['answers'] ?? [];
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
        FR.IA.05.C. DPT - LEMBAR JAWABAN PILIHAN GANDA
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
            <tr>
                <td colspan="2">Nama Asesor</td>
                <td style="text-align: center;">:</td>
                <td>{{ $assessorName }}</td>
            </tr>
            <tr>
                <td colspan="2">Nama Asesi</td>
                <td style="text-align: center;">:</td>
                <td>{{ $asesiName }}</td>
            </tr>
            <tr>
                <td colspan="2">Tanggal</td>
                <td style="text-align: center;">:</td>
                <td>{{ $dateStr }}</td>
            </tr>
            <tr>
                <td colspan="2">Waktu</td>
                <td style="text-align: center;">:</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 8pt; font-style: italic; margin-bottom: 1.5mm;">*Coret yang tidak perlu</div>
    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2.0mm;">
        Lembar Jawaban Pertanyaan Tertulis – Pilihan Ganda:
    </div>

    <table class="ans-table">
        <colgroup>
            <col style="width: 12.0mm;">
            <col style="width: 44.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 44.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Jawaban</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">No.</th>
                <th rowspan="2">Jawaban</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 20; $i++)
                @php $r = $i + 20; @endphp
                <tr>
                    <td class="center-cell">{{ $i }}.</td>
                    <td class="center-cell">{{ $answers[$i] ?? '' }}</td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                    <td class="center-cell">{{ $r }}.</td>
                    <td class="center-cell">{{ $answers[$r] ?? '' }}</td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                </tr>
            @endfor
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Jawaban</th>
                <th colspan="2">Pencapaian</th>
                <th rowspan="2">No.</th>
                <th rowspan="2">Jawaban</th>
                <th colspan="2">Pencapaian</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
            <tr>
                <td class="center-cell">41.</td>
                <td class="center-cell">{{ $answers[41] ?? '' }}</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell">61.</td>
                <td class="center-cell">{{ $answers[61] ?? '' }}</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
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

    <table class="ans-table">
        <colgroup>
            <col style="width: 12.0mm;">
            <col style="width: 44.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 44.0mm;">
            <col style="width: 15.0mm;">
            <col style="width: 15.0mm;">
        </colgroup>
        <tbody>
            @for($i = 42; $i <= 60; $i++)
                @php $r = $i + 19; @endphp
                <tr>
                    <td class="center-cell">{{ $i }}.</td>
                    <td class="center-cell">{{ $answers[$i] ?? '' }}</td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                    <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                    <td class="center-cell">{{ $r <= 75 ? $r . '.' : '' }}</td>
                    <td class="center-cell">{{ $r <= 75 ? ($answers[$r] ?? '') : '' }}</td>
                    <td class="center-cell">{!! $r <= 75 ? '<span class="box-sym">&#9744;</span>' : '' !!}</td>
                    <td class="center-cell">{!! $r <= 75 ? '<span class="box-sym">&#9744;</span>' : '' !!}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="feedback-box">
        <strong>Umpan balik untuk asesi :</strong><br>
        Aspek pengetahuan seluruh unit pada kelompok pekerjaan yang diujikan telah dinilai melalui pertanyaan tertulis pilihan ganda.
    </div>

    <table class="sign-table">
        <colgroup>
            <col style="width: 86.0mm;">
            <col style="width: 86.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td>
                    <strong>Asesi :</strong><br><br>
                    Nama : {{ $asesiName }}<br><br>
                    Tanda tangan/ Tanggal :
                </td>
                <td>
                    <strong>Asesor :</strong><br><br>
                    Nama : {{ $assessorName }}<br>
                    No. Reg : <br><br>
                    Tanda tangan/ Tanggal :
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
