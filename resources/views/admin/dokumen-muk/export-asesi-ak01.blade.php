@extends('layouts.print-muk')

@section('title', 'FR.AK.01 Persetujuan Asesmen - ' . ($asesi->full_name ?? 'Asesi'))
@section('doc-title', 'FR.AK.01 Persetujuan Asesmen dan Kerahasiaan')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 5.0mm 12.8mm 12.0mm 24.1mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .header-kop-table {
        width: 172.6mm;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .header-kop-table td {
        border: 1px solid #000;
    }
    .header-logo-cell {
        width: 35.8mm;
        height: 19.0mm;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }
    .header-title-cell {
        width: 136.6mm;
        text-align: center;
        vertical-align: middle;
        font-family: 'Tahoma', sans-serif;
        font-size: 11pt;
        padding: 0 4mm;
        line-height: 1.2;
    }
    .doc-main-title {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-weight: bold;
        font-size: 12pt;
        margin-top: 4.8mm;
        margin-bottom: 3.5mm;
        line-height: 1.0;
    }
    .table-ak01 {
        width: 172.6mm;
        table-layout: fixed;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        line-height: 1.15;
    }
    .table-ak01 td, .table-ak01 th {
        border: 1px solid #000;
        padding: 0.8mm 1.5mm;
        vertical-align: middle;
    }
    .checkbox-symbol {
        font-family: 'MS Gothic', 'Calibri', sans-serif;
        font-size: 16pt;
        display: inline-block;
        vertical-align: middle;
        margin-right: 2.5mm;
    }
    .check-wingdings {
        font-family: 'Wingdings 2', 'Wingdings', sans-serif;
        font-size: 16pt;
        display: inline-block;
        vertical-align: middle;
        margin-right: 2.5mm;
    }
</style>
@endpush

@section('content')
@php
    $isEmptyMode = request()->query('mode') === 'empty' || (isset($mode) && $mode === 'empty');
    $assessorName = $isEmptyMode ? '' : ($assessor?->full_name ?? $assessor?->name ?? ($leadAssessor?->full_name ?? $leadAssessor?->name ?? ''));
    $asesiName = $isEmptyMode ? '' : ($asesi->full_name ?? $asesi->name ?? '');
    $schemeName = $scheme->name ?? 'Mekanik Junior';
    $schemeCode = $scheme->code ?? '05/LSP PrO Indo/I/2026';
    $tukName = $isEmptyMode ? 'Sewaktu/Tempat Kerja/Mandiri*' : 'Sewaktu/Tempat Kerja/Mandiri*';
    $scheduleDate = $isEmptyMode ? '………………………………' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '………………………………');
    $scheduleDayDate = $isEmptyMode ? '………………………………' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d F Y') : '………………………………');
    $scheduleTime = $isEmptyMode ? '………………………………' : (($schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H.i') : '08.00') . ' s/d ' . ($schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H.i') : '16.00'));
    $tukActual = $isEmptyMode ? '………………………………' : ($schedule->tuk_name ?? 'Sewaktu/Tempat Kerja/Mandiri*');
@endphp

{{-- Header Kop Table --}}
<table class="header-kop-table">
    <tr style="height: 19.0mm;">
        <td class="header-logo-cell">
            <img src="{{ asset('images/muk/logo.png') }}" style="width: 18.9mm; height: 16.8mm; object-fit: contain; display: block; margin: 0 auto;" alt="Logo">
        </td>
        <td class="header-title-cell">
            Lembaga Sertifikasi Profesi Praktisi Otomotif <strong>LSP-PrO Indo</strong>
        </td>
    </tr>
</table>

{{-- Title --}}
<div class="doc-main-title">
    FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN
</div>

{{-- Main Table --}}
<table class="table-ak01">
    <colgroup>
        <col style="width: 42.8mm;">
        <col style="width: 16.4mm;">
        <col style="width: 7.0mm;">
        <col style="width: 52.4mm;">
        <col style="width: 54.0mm;">
    </colgroup>
    <tbody>
        {{-- Intro row --}}
        <tr style="height: 15.2mm;">
            <td colspan="5" style="vertical-align: middle; padding: 1.5mm 2.0mm;">
                Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen
            </td>
        </tr>

        {{-- Scheme Title --}}
        <tr style="height: 7.3mm;">
            <td rowspan="2" style="vertical-align: middle;">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td>Judul</td>
            <td colspan="3" style="padding-left: 2.2mm;">
                <span style="display: inline-block; width: 5.0mm; text-align: center;">:</span><span style="font-style: italic;">{{ $schemeName }}</span>
            </td>
        </tr>

        {{-- Scheme Code --}}
        <tr style="height: 7.3mm;">
            <td>Nomor</td>
            <td colspan="3" style="padding-left: 2.2mm;">
                <span style="display: inline-block; width: 5.0mm; text-align: center;">:</span><span>{{ $schemeCode }}</span>
            </td>
        </tr>

        {{-- TUK --}}
        <tr style="height: 7.2mm;">
            <td colspan="2">TUK</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td colspan="2" style="padding-left: 3.5mm;">{{ $tukName }}</td>
        </tr>

        {{-- Nama Asesor --}}
        <tr style="height: 8.7mm;">
            <td colspan="2">Nama Asesor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td colspan="2" style="padding-left: 3.5mm;">{{ $assessorName }}</td>
        </tr>

        {{-- Nama Asesi --}}
        <tr style="height: 7.5mm;">
            <td colspan="2">Nama Asesi</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td colspan="2" style="padding-left: 3.5mm;">{{ $asesiName }}</td>
        </tr>

        {{-- Bukti rows --}}
        <tr style="height: 10.6mm;">
            <td rowspan="4" colspan="2" style="vertical-align: top; padding-top: 11.5mm;">
                Bukti yang akan dikumpulkan :
            </td>
            <td style="text-align: center; padding: 1.0mm 0 0 0; vertical-align: top;">:</td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.5mm;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Verifikasi Portofolio
            </td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.6mm;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Reviu Produk
            </td>
        </tr>
        <tr style="height: 10.6mm;">
            <td style="text-align: center; padding: 1.0mm 0 0 0; vertical-align: top;">:</td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.5mm;">
                <span class="check-wingdings">&#61522;</span> Hasil Observasi Langsung
            </td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.6mm;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Kegiatan Terstruktur
            </td>
        </tr>
        <tr style="height: 10.6mm;">
            <td style="text-align: center; padding: 1.0mm 0 0 0; vertical-align: top;">:</td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.5mm;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Pertanyaan Lisan
            </td>
            <td style="vertical-align: top; padding: 3.8mm 0 0 0.6mm;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Pertanyaan Tertulis
            </td>
        </tr>
        <tr style="height: 18.4mm;">
            <td style="text-align: center; padding: 1.0mm 0 0 0; vertical-align: top;">:</td>
            <td style="vertical-align: top; padding: 3.5mm 0 0 0.5mm;">
                <span class="checkbox-symbol">&#9744;</span> Lainnya ……
            </td>
            <td style="vertical-align: top; padding: 3.5mm 0 0 0.6mm; line-height: 1.15;">
                <span class="checkbox-symbol">&#9744;</span> Hasil Pertanyaan<br><span style="display: inline-block; padding-left: 9.2mm;">Wawancara</span>
            </td>
        </tr>

        {{-- Pelaksanaan rows --}}
        <tr style="height: 8.0mm;">
            <td rowspan="3" colspan="3" style="vertical-align: top; padding-top: 8.5mm;">
                Pelaksanaan asesmen disepakati<br>pada:
            </td>
            <td style="border-right: none;">
                <span style="display: inline-block; width: 27.0mm;">Hari/ Tanggal</span><span>:</span>
            </td>
            <td style="border-left: none;">{{ $scheduleDayDate }}</td>
        </tr>
        <tr style="height: 8.0mm;">
            <td style="border-right: none;">
                <span style="display: inline-block; width: 27.0mm;">Waktu</span><span>:</span>
            </td>
            <td style="border-left: none;">{{ $scheduleTime }}</td>
        </tr>
        <tr style="height: 8.0mm;">
            <td style="border-right: none;">
                <span style="display: inline-block; width: 27.0mm;">TUK</span><span>:</span>
            </td>
            <td style="border-left: none;">{{ $tukActual }}</td>
        </tr>

        {{-- Statement clauses --}}
        <tr style="height: 13.5mm;">
            <td colspan="5" style="vertical-align: top; padding: 1.5mm 2.0mm;">
                <strong>Asesi :</strong>
                <div style="margin-top: 2.2mm;">Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor.</div>
            </td>
        </tr>
        <tr style="height: 26.0mm;">
            <td colspan="5" style="vertical-align: top; padding: 1.5mm 2.0mm; line-height: 1.25;">
                <strong>Asesor :</strong>
                <div style="margin-top: 2.2mm;">Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor dalam pekerjaan <em>Asesmen</em> kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.</div>
            </td>
        </tr>
        <tr style="height: 19.6mm;">
            <td colspan="5" style="vertical-align: top; padding: 1.5mm 2.0mm; line-height: 1.25;">
                <strong>Asesi :</strong>
                <div style="margin-top: 2.2mm;">Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.</div>
            </td>
        </tr>

        {{-- Signatures --}}
        <tr style="height: 32.6mm;">
            <td colspan="5" style="vertical-align: top; padding: 5.4mm 2.0mm 2.0mm 2.0mm; line-height: 1.4;">
                <div style="margin-bottom: 5.5mm;">
                    <span style="display: inline-block; width: 76.3mm;">Tanda tangan Asesor : ……………………………</span><span>Tanggal : ………………………………</span>
                </div>
                <div>
                    <span style="display: inline-block; width: 76.3mm;">Tanda tangan Asesi &nbsp;&nbsp;: ……………………………</span><span>Tanggal : ………………………………</span>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<div style="margin-top: 1.5mm; font-size: 11pt; font-style: italic;">
    * Coret yang tidak perlu
</div>
@endsection
