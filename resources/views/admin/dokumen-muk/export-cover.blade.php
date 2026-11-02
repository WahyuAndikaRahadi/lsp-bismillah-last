@extends('layouts.print-muk')

@section('title', 'Cover MUK - ' . $scheme->name)

@section('content')
<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Calibri, 'Carlito', sans-serif;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .cover-container {
        width: 210mm;
        height: 297mm;
        box-sizing: border-box;
        padding-top: 7.6mm;
        position: relative;
    }
    .kop-table {
        width: 174.6mm;
        margin: 0 auto;
        border-collapse: collapse;
        border: 1pt solid #000;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo-cell {
        width: 34.0mm;
        height: 30.5mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo-img {
        max-width: 28mm;
        max-height: 26mm;
    }
    .kop-title-cell {
        width: 140.6mm;
        text-align: center;
        padding-top: 5.5mm;
        vertical-align: top;
        font-size: 16pt;
        line-height: 1.35;
    }
    .main-box {
        width: 168.4mm;
        height: 79.0mm;
        margin: 51.3mm auto 0 auto;
        border: 1pt solid #000;
        box-sizing: border-box;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2mm 5mm;
    }
    .doc-word {
        font-size: 34pt;
        line-height: 1.05;
        font-weight: normal;
        margin-bottom: 2mm;
    }
    .muk-word {
        font-size: 36pt;
        line-height: 1.05;
        font-weight: bold;
        margin-bottom: 3mm;
    }
    .scheme-word {
        font-size: 35pt;
        line-height: 1.05;
        font-weight: bold;
    }
    .asesi-row {
        position: absolute;
        top: 231.0mm;
        left: 25.0mm;
        width: 168.4mm;
        font-family: Arial, 'Liberation Sans', sans-serif;
        font-size: 11.4pt;
        font-weight: bold;
    }
</style>

@php
    $mukNum = '3';
    if (preg_match('/(?:Skema\s*|0)?(\d+)/i', $scheme->code ?? '', $matches)) {
        $mukNum = (int)$matches[1];
    }
    $fullAsesi = (!empty($mode) && $mode !== 'empty' && !empty($asesiName)) ? $asesiName : '…………………………………………………………………………………';
@endphp

<div class="cover-container">
    {{-- Header Kop Table --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                <img src="{{ public_path('images/logo-lsp.png') }}" class="kop-logo-img" alt="Logo">
            </td>
            <td class="kop-title-cell">
                <div><strong>L</strong>embaga <strong>S</strong>ertifikasi <strong>P</strong>rofesi <strong>P</strong>raktisi <strong>O</strong>tomotif  Indonesia</div>
                <div style="font-weight: bold; margin-top: 5.2mm;">LSP-PrO Indo</div>
            </td>
        </tr>
    </table>

    {{-- Center Box --}}
    <div class="main-box">
        <div class="doc-word">DOKUMEN</div>
        <div class="muk-word">MUK {{ $mukNum }}</div>
        <div class="scheme-word">
            <em>Periodical Maintenance</em><br>
            <div style="margin-top: 0.8mm;"><em>Electrified Vehicles</em> (Perawatan</div>
            <div style="margin-top: 2.5mm;">Berkala Mobil Listrik)</div>
        </div>
    </div>

    {{-- Bottom Asesi Row --}}
    <div class="asesi-row">
        Nama  Asesi : {{ $fullAsesi }}
    </div>
</div>
@endsection
