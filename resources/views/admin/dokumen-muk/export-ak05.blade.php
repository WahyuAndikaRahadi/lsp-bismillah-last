@extends('layouts.print-muk')

@section('title', 'FR.AK.05 Laporan Asesmen - ' . ($schedule->code ?? 'Jadwal'))
@section('doc-title', 'FR.AK.05 Laporan Asesmen')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 12.7mm 10.78mm 12.7mm 25.0mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .header-kop-table {
        width: 174.5mm;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .header-kop-table td {
        border: 1px solid #000;
    }
    .header-logo-cell {
        width: 34.1mm;
        height: 23.04mm;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }
    .header-title-cell {
        width: 140.4mm;
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
        margin-left: 2.1mm;
        margin-top: 5.46mm;
        margin-bottom: 2.75mm;
        height: 4.5mm;
        line-height: 1.0;
    }
    .table-meta {
        width: 174.3mm;
        table-layout: fixed;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-bottom: 5.29mm;
    }
    .table-meta td {
        border: 1px solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: middle;
    }
    .table-candidates {
        width: 174.6mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
    }
    .table-candidates th, .table-candidates td {
        border: 1px solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: middle;
        line-height: 1.0;
    }
    .table-candidates th {
        font-weight: bold;
        text-align: center;
        background: #fff;
    }
    .table-findings {
        width: 174.2mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-bottom: 4.91mm;
    }
    .table-findings td {
        border: 1px solid #000;
    }
    .table-bottom {
        width: 174.2mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
    }
    .table-bottom td {
        border: 1px solid #000;
    }
    .checkbox-cell {
        text-align: center;
        vertical-align: middle;
        font-size: 12pt;
        font-family: 'MS Gothic', 'Calibri', sans-serif;
        padding: 0;
    }
</style>
@endpush

@section('content')
@php
    $isEmptyMode = request()->query('mode') === 'empty' || (isset($mode) && $mode === 'empty');
    $leadAssessorName = $isEmptyMode ? '' : ($leadAssessor?->full_name ?? $leadAssessor?->name ?? '');
    $regNo = $isEmptyMode ? '' : ($leadAssessor?->profile?->no_reg_asesor ?? $leadAssessor?->profile?->met_number ?? '');
    $scheduleDate = $isEmptyMode ? '' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '');
    $ak05Data = is_array($schedule->ak05_report) ? $schedule->ak05_report : [];
    $negativeAspects = $isEmptyMode ? '' : ($ak05Data['negative_aspects'] ?? '');
    $positiveAspects = $isEmptyMode ? '' : ($ak05Data['positive_aspects'] ?? '');
    $rejectionNotes = $isEmptyMode ? '' : ($ak05Data['rejection_notes'] ?? '');
    $suggestions = $isEmptyMode ? '' : ($ak05Data['improvement_suggestions'] ?? '');
    $notes = $isEmptyMode ? '' : ($schedule->notes ?? '');

    $candidateRows = $isEmptyMode ? 3 : max(3, count($assessments));
@endphp

<div class="ak05-page">
    {{-- Header Kop Table --}}
    <table class="header-kop-table">
        <tr style="height: 23.04mm;">
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
        <span style="display: inline-block; width: 23.7mm;">FR.AK.05.</span><span>LAPORAN ASESMEN</span>
    </div>

    {{-- Meta Grid --}}
    <table class="table-meta">
        <colgroup>
            <col style="width: 46.9mm;">
            <col style="width: 20.0mm;">
            <col style="width: 5.0mm;">
            <col style="width: 102.4mm;">
        </colgroup>
        <tr style="height: 9.45mm;">
            <td rowspan="2" style="vertical-align: middle;">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td style="padding: 0.5mm 1.5mm;">Judul</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm; font-style: italic;">{{ $scheme->name }}</td>
        </tr>
        <tr style="height: 8.50mm;">
            <td style="padding: 0.5mm 1.5mm;">Nomor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $scheme->code }}</td>
        </tr>
        <tr style="height: 7.48mm;">
            <td colspan="2">TUK</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">Sewaktu/Tempat Kerja/Mandiri*</td>
        </tr>
        <tr style="height: 7.58mm;">
            <td colspan="2">Nama Asesor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $leadAssessorName }}</td>
        </tr>
        <tr style="height: 7.66mm;">
            <td colspan="2">Tanggal</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $scheduleDate }}</td>
        </tr>
    </table>

    {{-- Candidates Table --}}
    <table class="table-candidates">
        <colgroup>
            <col style="width: 12.1mm;">
            <col style="width: 62.6mm;">
            <col style="width: 15.0mm;">
            <col style="width: 17.5mm;">
            <col style="width: 67.4mm;">
        </colgroup>
        <thead>
            <tr style="height: 7.79mm;">
                <th rowspan="2" style="vertical-align: middle;">No.</th>
                <th rowspan="2" style="vertical-align: middle;">Nama Asesi</th>
                <th colspan="2" style="vertical-align: middle;">Rekomendasi</th>
                <th rowspan="2" style="vertical-align: middle;">Keterangan**</th>
            </tr>
            <tr style="height: 10.35mm;">
                <th style="vertical-align: middle;">K</th>
                <th style="vertical-align: middle;">BK</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < $candidateRows; $i++)
                @php
                    $a = (!$isEmptyMode && isset($assessments[$i])) ? $assessments[$i] : null;
                    $isAbsent = $a && $a->final_result === 'TIDAK_HADIR';
                    $isK = ($a && ! $isAbsent) ? (in_array($a->final_result, ['K', 'KOMPETEN'], true) || ($a->final_result === null && $a->status === 'COMPLETED')) : false;
                    $isBK = ($a && ! $isAbsent) ? in_array($a->final_result, ['BK', 'BELUM_KOMPETEN'], true) : false;
                @endphp
                <tr style="height: 12.10mm;">
                    <td style="text-align: center;">{{ $i + 1 }}.</td>
                    <td>{{ $a?->asesi?->full_name ?? '' }}</td>
                    <td class="checkbox-cell">
                        @if($isEmptyMode)
                            &#9744;
                        @else
                            {!! $isK ? '&#9745;' : '&#9744;' !!}
                        @endif
                    </td>
                    <td class="checkbox-cell">
                        @if($isEmptyMode)
                            &#9744;
                        @else
                            {!! $isBK ? '&#9745;' : '&#9744;' !!}
                        @endif
                    </td>
                    <td style="font-size: 10pt;">
                        @if($isAbsent)
                            <span style="font-style: italic;">Tidak Hadir</span>
                        @elseif($a && $isBK)
                            Belum Kompeten
                        @elseif($a && $isK)
                            Kompeten
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Footnote --}}
    <div style="font-size: 11pt; margin-top: 1.35mm; margin-bottom: 0.58mm; line-height: 1.0;">
        ** tuliskan Kode dan Judul Unit Kompetensi yang dinyatakan BK bila mengases satu skema
    </div>

    {{-- Report Findings Table --}}
    <table class="table-findings">
        <colgroup>
            <col style="width: 69.5mm;">
            <col style="width: 104.7mm;">
        </colgroup>
        <tbody>
            <tr style="height: 33.05mm;">
                <td style="vertical-align: top; padding: 1.5mm 2.0mm; line-height: 1.15;">
                    Aspek Negatif dan Positif dalam<br>Asesemen
                </td>
                <td style="vertical-align: top; padding: 0.9mm 2.0mm; line-height: 1.15;">
                    Aspek Negatif :
                    <div style="min-height: 8.5mm; margin-top: 0.5mm;">{{ $negativeAspects }}</div>
                    Aspek Positif :
                    <div style="min-height: 8.5mm; margin-top: 0.5mm;">{{ $positiveAspects }}</div>
                </td>
            </tr>
            <tr style="height: 14.12mm;">
                <td style="vertical-align: middle; padding: 1.5mm 2.0mm; line-height: 1.15;">
                    Pencatatan Penolakan Hasil Asesmen
                </td>
                <td style="vertical-align: top; padding: 1.0mm 2.0mm; line-height: 1.15;">
                    Ada Penolakan :
                    <div style="margin-top: 0.5mm;">{{ $rejectionNotes }}</div>
                </td>
            </tr>
            <tr style="height: 13.58mm;">
                <td style="vertical-align: middle; padding: 1.5mm 2.0mm; line-height: 1.15;">
                    Saran Perbaikan :<br>
                    <span style="font-weight: normal;">(Asesor/Personil Terkait)</span>
                </td>
                <td style="vertical-align: top; padding: 1.5mm 2.0mm; line-height: 1.15;">
                    {{ $suggestions }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Bottom Notes & Assessor Table --}}
    <table class="table-bottom">
        <colgroup>
            <col style="width: 91.86mm;">
            <col style="width: 82.34mm;">
        </colgroup>
        <tbody>
            <tr style="height: 49.42mm;">
                <td style="vertical-align: top; padding: 2.0mm 2.5mm;">
                    <div style="font-weight: bold; margin-bottom: 2mm;">Catatan :</div>
                    <div>{{ $notes }}</div>
                </td>
                <td style="vertical-align: top; padding: 0;">
                    <table style="width: 100%; height: 100%; border-collapse: collapse; font-size: 11pt;">
                        <colgroup>
                            <col style="width: 27.8mm;">
                            <col style="width: 54.54mm;">
                        </colgroup>
                        <tr style="height: 8.08mm;">
                            <td colspan="2" style="border-bottom: 1px solid #000; font-weight: bold; padding: 1.5mm 2.0mm;">
                                Asesor :
                            </td>
                        </tr>
                        <tr style="height: 6.77mm;">
                            <td style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 1.0mm 2.0mm;">
                                Nama
                            </td>
                            <td style="border-bottom: 1px solid #000; padding: 1.0mm 2.0mm;">
                                {{ $leadAssessorName }}
                            </td>
                        </tr>
                        <tr style="height: 6.77mm;">
                            <td style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 1.0mm 2.0mm;">
                                No. Reg
                            </td>
                            <td style="border-bottom: 1px solid #000; padding: 1.0mm 2.0mm;">
                                {{ $regNo }}
                            </td>
                        </tr>
                        <tr style="height: 27.80mm;">
                            <td style="border-right: 1px solid #000; padding: 0 2.0mm; vertical-align: middle; line-height: 1.15;">
                                Tanda tangan/<br>Tanggal
                            </td>
                            <td style="padding: 1.0mm 2.0mm; vertical-align: top;">
                                @if(!$isEmptyMode)
                                    @if($leadAssessor?->profile?->signature_path)
                                        <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" style="max-height: 20mm; max-width: 48mm; object-fit: contain; display: block;" alt="TTD">
                                    @endif
                                    <div style="font-size: 9pt; margin-top: 1mm;">{{ $scheduleDate }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
