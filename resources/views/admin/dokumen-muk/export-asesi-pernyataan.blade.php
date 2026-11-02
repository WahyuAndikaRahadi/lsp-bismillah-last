@extends('layouts.print-muk')

@section('title', 'Surat Pernyataan Pemegang Sertifikat - ' . ($asesi->full_name ?? ''))

@section('content')
<style>
    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, 'Liberation Sans', sans-serif;
        font-size: 11pt;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-container {
        width: 210mm;
        box-sizing: border-box;
        padding-top: 37.1mm;
        padding-left: 25.4mm;
        padding-right: 18.0mm;
    }
    .doc-title {
        text-align: center;
        font-weight: bold;
        line-height: 8.0mm;
        margin-bottom: 7.9mm;
    }
    .opening-clause {
        line-height: 8.0mm;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .under-signed {
        line-height: 8.0mm;
        margin-bottom: 0;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11pt;
        margin-bottom: 0;
    }
    .meta-table tr {
        height: 8.0mm;
    }
    .meta-table td {
        vertical-align: middle;
        padding: 0;
    }
    .promise-intro {
        margin-top: 8.0mm;
        line-height: 8.0mm;
        margin-bottom: 0;
    }
    .list-item {
        padding-left: 12.7mm;
        text-indent: -6.35mm;
        line-height: 8.0mm;
        margin: 0;
    }
    .list-item-multi {
        padding-left: 12.7mm;
        text-indent: -6.35mm;
        line-height: 5.16mm;
        margin: 0;
    }
    .closing-clause {
        margin-top: 2.8mm;
        line-height: 8.0mm;
        margin-bottom: 0;
    }
    .sig-date {
        position: absolute;
        top: 235.0mm;
        left: 114.3mm;
    }
    .sig-name {
        position: absolute;
        top: 264.1mm;
        left: 127.0mm;
    }
</style>

@php
    $isFilled = (!empty($mode) && $mode !== 'empty');
    if ($isFilled && !empty($schedule->date)) {
        $cDate = \Carbon\Carbon::parse($schedule->date);
        $dayStr = $cDate->translatedFormat('l');
        $dateStr = $cDate->translatedFormat('d');
        $monthStr = $cDate->translatedFormat('F');
        $yearStr = $cDate->format('Y');
        $tukStr = $schedule->tuk_name ?? 'LSP PrO Indo';
        $openDateLine = "Pada hari ini {$dayStr} <span style=\"display: inline-block; width: 7.5mm;\"></span> Tanggal {$dateStr} Bulan {$monthStr} Tahun {$yearStr} bertempat di {$tukStr}.";
    } else {
        $openDateLine = 'Pada hari ini ___ <span style="display: inline-block; width: 7.5mm;"></span> Tanggal ___ Bulan ___ Tahun ___ bertempat di ___________________.';
    }

    $namaVal = $isFilled && !empty($asesiName) ? $asesiName : '__________';
    $nikVal = $isFilled && !empty($profile?->nik) ? $profile->nik : '__________';
    $skemaVal = $isFilled && !empty($scheme->name) ? $scheme->name : '__________';
    $blankoVal = $isFilled && !empty($assessment->blanko_number) ? $assessment->blanko_number : '__________';
    $certVal = $isFilled && !empty($assessment->certificate_number) ? $assessment->certificate_number : '__________';

    $sigDate = $isFilled && !empty($schedule->date) ? ($schedule->tuk_city ?? 'Bekasi') . ', ' . \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '…………………,………………………';
    $sigName = $isFilled && !empty($asesiName) ? '( ' . $asesiName . ' )' : '(………………………….)';
@endphp

<div class="page-container">
    <div class="doc-title">
        <div>SURAT PERNYATAAN PEMEGANG SERTIFIKAT KOMPETENSI</div>
        <div>LEMBAGA SERTIFIKASI PROFESI PIHAK KETIGA</div>
        <div>PRAKTISI OTOMOTIF INDONESIA</div>
        <div>(LSP P3 PRO INDO)</div>
    </div>

    <div class="opening-clause">
        Pada hari ini ___ <span style="display: inline-block; width: 7.5mm;"></span> Tanggal ___ Bulan ___ Tahun ___ bertempat di ___________________.
    </div>

    <div class="under-signed">
        Saya bertanda tangan dibawah ini :
    </div>

    <table class="meta-table">
        <colgroup>
            <col style="width: 38.1mm;">
            <col style="width: 121.1mm;">
        </colgroup>
        <tbody>
            <tr>
                <td>Nama</td>
                <td>: {{ $namaVal }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>: {{ $nikVal }}</td>
            </tr>
            <tr>
                <td>Skema</td>
                <td>: {{ $skemaVal }}</td>
            </tr>
            <tr>
                <td>No.Blanko</td>
                <td>: {{ $blankoVal }}</td>
            </tr>
            <tr>
                <td>No. Sertifikat</td>
                <td>: {{ $certVal }}</td>
            </tr>
        </tbody>
    </table>

    <div class="promise-intro">
        Dengan ini mengatakan bahwa saya akan:
    </div>

    <div class="list-item">
        1. Mematuhi ketentuan yang relevan dalam skema sertifikasi.
    </div>
    <div class="list-item">
        2. Menggunakan sertifikat hanya untuk ruang lingkup sertifikasi yang diberikan.
    </div>

    <div class="list-item-multi" style="margin-top: 1.4mm;">
        3. Tidak menggunakan sertifikat yang dapat mencemarkan / merugikan Lembaga<br>
        Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo) dan tidak<br>
        memberikan pernyataan terkait sertifikasi yang oleh Lembaga Sertifikasi Profesi Pihak<br>
        Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo) dianggap dapat menyesatkan<br>
        atau tidak dapat dipertanggungjawabkan.
    </div>

    <div class="list-item-multi" style="margin-top: 2.8mm;">
        4. Menghentikan penggunaan atau pengakuan sertifikat setelah sertifikat dibekukan atau<br>
        dicabut oleh Lembaga Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP<br>
        P3 PrO Indo) dan mengembalikan sertifikat kepada Lembaga Sertifikasi Profesi Pihak<br>
        Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo).
    </div>

    <div class="closing-clause">
        Demikian pernyataan ini saya buat dengan sebenar-benarnya dan penuh tanggung jawab.
    </div>

    <div class="sig-date">{{ $sigDate }}</div>
    <div class="sig-name">{{ $sigName }}</div>
</div>
@endsection
