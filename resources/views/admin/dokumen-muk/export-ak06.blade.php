@extends('layouts.print-muk')

@section('title', 'FR.AK.06 Meninjau Proses Asesmen - ' . ($schedule->code ?? 'Jadwal'))
@section('doc-title', 'FR.AK.06 Meninjau Proses Asesmen')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 9.49mm 7.68mm 12.7mm 25.2mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .header-kop-table {
        width: 176.6mm;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .header-kop-table td {
        border: 1px solid #000;
    }
    .header-logo-cell {
        width: 33.4mm;
        height: 18.7mm;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }
    .header-title-cell {
        width: 143.2mm;
        text-align: center;
        vertical-align: middle;
        font-family: 'Tahoma', sans-serif;
        font-weight: bold;
        font-size: 11pt;
        padding: 0 4mm;
        line-height: 1.2;
    }
    .doc-main-title {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-weight: bold;
        font-size: 12pt;
        margin-left: 0.2mm;
        margin-top: 6.51mm;
        margin-bottom: 4.80mm;
        height: 4.5mm;
        line-height: 1.0;
    }
    .table-meta {
        width: 177.0mm;
        table-layout: fixed;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 12pt;
        margin-bottom: 5.33mm;
    }
    .table-meta td {
        border: 1px solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: middle;
    }
    .box-penjelasan {
        width: 176.6mm;
        height: 36.38mm;
        border: 1px solid #000;
        font-size: 12pt;
        line-height: 1.15;
        padding: 1.6mm 2.6mm;
        margin-bottom: 5.72mm;
        box-sizing: border-box;
    }
    .table-prinsip {
        width: 176.42mm;
        table-layout: fixed;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 12pt;
        margin-bottom: 5.26mm;
    }
    .table-prinsip th, .table-prinsip td {
        border: 1px solid #000;
        padding: 0.5mm 1.0mm;
        vertical-align: middle;
    }
    .table-dimensi {
        width: 177.2mm;
        table-layout: fixed;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
    }
    .table-dimensi th, .table-dimensi td {
        border: 1px solid #000;
        padding: 0.5mm 1.0mm;
        vertical-align: middle;
    }
    .checkbox-cell {
        text-align: center;
        vertical-align: middle;
        font-size: 12pt;
        font-family: 'MS Gothic', 'Calibri', sans-serif;
        padding: 0;
    }
    .bullet-point {
        font-family: 'Times New Roman', serif;
        margin-right: 2.5mm;
    }
</style>
@endpush

@section('content')
@php
    $isEmptyMode = request()->query('mode') === 'empty' || (isset($mode) && $mode === 'empty');
    $leadAssessorName = $isEmptyMode ? '' : ($leadAssessor?->full_name ?? $leadAssessor?->name ?? '');
    $scheduleDate = $isEmptyMode ? '' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '');
    $ak06Data = is_array($schedule->ak06_review) ? $schedule->ak06_review : [];
    $comment = $isEmptyMode ? '' : ($schedule->ak06_assessor_comments ?? '');
@endphp

{{-- PAGE 1 --}}
<div class="ak06-page">
    {{-- Header Kop Table --}}
    <table class="header-kop-table">
        <tr style="height: 18.7mm;">
            <td class="header-logo-cell">
                <img src="{{ asset('images/muk/logo.png') }}" style="width: 18.9mm; height: 16.8mm; object-fit: contain; display: block; margin: 0 auto;" alt="Logo">
            </td>
            <td class="header-title-cell">
                Lembaga Sertifikasi Profesi Praktisi Otomotif  LSP-PrO Indo
            </td>
        </tr>
    </table>

    {{-- Title --}}
    <div class="doc-main-title">
        <span style="display: inline-block; width: 25.4mm;">FR.AK.06.</span><span>MENINJAU PROSES ASESMEN</span>
    </div>

    {{-- Meta Grid --}}
    <table class="table-meta">
        <colgroup>
            <col style="width: 46.9mm;">
            <col style="width: 20.0mm;">
            <col style="width: 5.0mm;">
            <col style="width: 105.1mm;">
        </colgroup>
        <tr style="height: 10.02mm;">
            <td rowspan="2" style="vertical-align: middle;">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td>Judul</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="font-size: 11pt; line-height: 1.1;"><em>Periodical Maintenance Electrified Vehicles</em> (Perawatan Berkala<br>Mobil Listrik)</td>
        </tr>
        <tr style="height: 8.54mm;">
            <td>Nomor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="font-size: 11pt;">{{ $scheme->code }}</td>
        </tr>
        <tr style="height: 8.19mm;">
            <td colspan="2">TUK</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td>Sewaktu/Tempat Kerja/Mandiri*</td>
        </tr>
        <tr style="height: 8.18mm;">
            <td colspan="2">Nama Asesor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td>{{ $leadAssessorName }}</td>
        </tr>
        <tr style="height: 8.18mm;">
            <td colspan="2">Tanggal</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td>{{ $scheduleDate }}</td>
        </tr>
    </table>

    {{-- Explanations (Penjelasan) --}}
    <div class="box-penjelasan">
        <div style="font-weight: bold; margin-bottom: 2.2mm;">Penjelasan:</div>
        <div style="display: flex; line-height: 1.25; margin-bottom: 0.5mm;">
            <div style="width: 6.3mm;">1.</div>
            <div>Peninjauan dapat dilakukan oleh lead asesor atau asesor yang melaksanakan asesmen.</div>
        </div>
        <div style="display: flex; line-height: 1.25; margin-bottom: 0.5mm;">
            <div style="width: 6.3mm;">2.</div>
            <div>Peninjauan dapat dilakukan secara terpadu dalam skema sertifikasi dan /<br>atau peserta kelompokyang homogen.</div>
        </div>
        <div style="display: flex; line-height: 1.25;">
            <div style="width: 6.3mm;">3.</div>
            <div>Isilah pemenuhan dimensi kompetensi dengan menuliskan jenis bukti dan instrumen yang<br>digunakan pada saat asesmen sebagai bukti terpenuhinya dimensi kompetensi.</div>
        </div>
    </div>

    {{-- Table 1: Principles of Assessment --}}
    <table class="table-prinsip">
        <colgroup>
            <col style="width: 91.92mm;">
            <col style="width: 25.9mm;">
            <col style="width: 20.0mm;">
            <col style="width: 21.3mm;">
            <col style="width: 17.3mm;">
        </colgroup>
        <thead>
            <tr style="height: 6.8mm;">
                <th rowspan="2" style="border: 1px solid #000; text-align: left; padding-left: 2.0mm; font-weight: bold;">Asepek yang ditinjau</th>
                <th colspan="4" style="border: 1px solid #000; text-align: center; font-weight: bold;">Kesesuaian dengan prinsip asesmen</th>
            </tr>
            <tr style="height: 6.95mm;">
                <th style="border: 1px solid #000; text-align: center; font-style: italic; font-weight: normal;">Validitas</th>
                <th style="border: 1px solid #000; text-align: center; font-style: italic; font-weight: normal;">Reliabel</th>
                <th style="border: 1px solid #000; text-align: center; font-style: italic; font-weight: normal;">Fleksibel</th>
                <th style="border: 1px solid #000; text-align: center; font-style: italic; font-weight: normal;">Adil</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 6.07mm;">
                <td style="padding-left: 2.0mm;">Prosedur asesmen:</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
            </tr>
            <tr style="height: 6.28mm;">
                <td style="padding-left: 2.0mm;"><span class="bullet-point">&#9679;</span> Rencana asesmen</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
            </tr>
            <tr style="height: 6.35mm;">
                <td style="padding-left: 2.0mm;"><span class="bullet-point">&#9679;</span> Persiapan asesmen</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
            </tr>
            <tr style="height: 6.32mm;">
                <td style="padding-left: 2.0mm;"><span class="bullet-point">&#9679;</span> Implementasi asesmen</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
            </tr>
            <tr style="height: 6.35mm;">
                <td style="padding-left: 2.0mm;"><span class="bullet-point">&#9679;</span> Keputusan asesmen</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td></td>
            </tr>
            <tr style="height: 6.32mm;">
                <td style="padding-left: 2.0mm;"><span class="bullet-point">&#9679;</span> Umpan balik asesmen</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td class="checkbox-cell">&#9744;</td>
                <td></td>
            </tr>
            <tr style="height: 8.64mm;">
                <td colspan="5" style="padding: 1.0mm 2.0mm; vertical-align: middle;">
                    Rekomendasi untuk peningkatan
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Table 2: Dimensions of Competency --}}
    <table class="table-dimensi">
        <colgroup>
            <col style="width: 45.2mm;">
            <col style="width: 23.8mm;">
            <col style="width: 25.4mm;">
            <col style="width: 27.0mm;">
            <col style="width: 24.2mm;">
            <col style="width: 31.6mm;">
        </colgroup>
        <thead>
            <tr style="height: 5.8mm;">
                <th rowspan="2" style="text-align: center; font-weight: bold;">Aspek yang ditinjau</th>
                <th colspan="5" style="text-align: center; font-weight: bold;">Pemenuhan dimensi kompetensi</th>
            </tr>
            <tr style="height: 14.3mm;">
                <th style="text-align: center; font-style: italic; font-weight: normal;">Task Skills</th>
                <th style="text-align: center; font-style: italic; font-weight: normal; line-height: 1.1;">Task<br>Management<br>Skills</th>
                <th style="text-align: center; font-style: italic; font-weight: normal; line-height: 1.1;">Contingency<br>Management<br>Skills</th>
                <th style="text-align: center; font-style: italic; font-weight: normal; line-height: 1.1;">Job Role/<br>Environment<br>Skills</th>
                <th style="text-align: center; font-style: italic; font-weight: normal;">Transfer Skills</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 54.3mm;">
                <td style="padding: 1.1mm 2.0mm; vertical-align: top; font-size: 12pt; line-height: 7.75mm;">
                    <strong>Konsistensi keputusan<br>asesmen</strong><br>
                    Bukti dari berbagai<br>asesmen diperiksa<br>untuk konsistensi<br>dimensi kompetensi
                </td>
                <td style="text-align: center; vertical-align: top; padding: 1.0mm 1.0mm; line-height: 7.1mm;">
                    L / CL<br>
                    FR. IA<br>
                    01,02,03,<br>
                    T / DPT<br>
                    IA 05
                </td>
                <td style="text-align: center; vertical-align: top; padding-top: 3.6mm; font-family: 'Times New Roman', serif;">
                    ………
                </td>
                <td style="text-align: center; vertical-align: top; padding-top: 3.6mm; font-family: 'Times New Roman', serif;">
                    ………
                </td>
                <td style="text-align: center; vertical-align: top; padding-top: 3.6mm; font-family: 'Times New Roman', serif;">
                    ………
                </td>
                <td style="text-align: center; vertical-align: top; padding-top: 3.6mm; font-family: 'Times New Roman', serif;">
                    ………
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- PAGE 2 --}}
<div style="page-break-before: always; break-before: page;"></div>

<div class="ak06-page">
    {{-- Header Kop Table --}}
    <table class="header-kop-table">
        <tr style="height: 18.7mm;">
            <td class="header-logo-cell">
                <img src="{{ asset('images/muk/logo.png') }}" style="width: 18.9mm; height: 16.8mm; object-fit: contain; display: block; margin: 0 auto;" alt="Logo">
            </td>
            <td class="header-title-cell">
                Lembaga Sertifikasi Profesi Praktisi Otomotif  LSP-PrO Indo
            </td>
        </tr>
    </table>

    {{-- Recommendation Box --}}
    <div style="width: 176.73mm; height: 12.53mm; border: 1px solid #000; border-bottom: none; font-size: 11pt; padding: 1.5mm 2.4mm; margin-top: 4.9mm; box-sizing: border-box;">
        Rekomendasi untuk peningkatan:
    </div>

    {{-- Assessor Review Table --}}
    <table style="width: 176.73mm; height: 38.38mm; border-collapse: collapse; border: 1px solid #000; font-size: 11pt; table-layout: fixed;">
        <colgroup>
            <col style="width: 45.0mm;">
            <col style="width: 37.0mm;">
            <col style="width: 94.73mm;">
        </colgroup>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; vertical-align: top; padding: 1.5mm 5.5mm; line-height: 1.15;">
                    Nama<br>Lead Asesor/Asesor
                    <div style="margin-top: 2.5mm; font-weight: bold;">
                        {{ $leadAssessorName }}
                    </div>
                </td>
                <td style="border: 1px solid #000; vertical-align: top; text-align: center; padding: 1.5mm 2.0mm; line-height: 1.15;">
                    Tanggal &amp; Tanda<br>Tangan
                    <div style="margin-top: 2.5mm;">
                        {{ $scheduleDate }}
                    </div>
                </td>
                <td style="border: 1px solid #000; vertical-align: top; padding: 1.5mm 2.4mm; line-height: 1.15;">
                    Komentar
                    <div style="margin-top: 2.5mm;">
                        {{ $comment }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
