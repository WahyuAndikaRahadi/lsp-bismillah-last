@extends('layouts.print-muk')

@section('title', 'FR.AK.03 Umpan Balik - ' . ($asesi->full_name ?? 'Peserta'))
@section('doc-title', 'FR.AK.03 Umpan Balik dan Catatan Asesmen')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 12.7mm 12.1mm 12.7mm 25.8mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .header-kop-table {
        width: 172.1mm;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 5.0mm;
    }
    .header-kop-table td {
        border: 1px solid #000;
    }
    .header-logo-cell {
        width: 33.7mm;
        height: 17.5mm;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }
    .header-title-cell {
        width: 138.4mm;
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
        margin-bottom: 4.8mm;
    }
    .table-meta {
        width: 172.1mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-bottom: 5.5mm;
    }
    .table-meta td {
        border: 1px solid #000;
        padding: 0.8mm 2mm;
        height: 5.8mm;
        vertical-align: middle;
    }
    .instruction-text {
        font-size: 11pt;
        margin-bottom: 2.5mm;
        line-height: 1.2;
    }
    .table-feedback {
        width: 172.1mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-bottom: 0;
    }
    .table-feedback th, .table-feedback td {
        border: 1px solid #000;
        padding: 0.2mm 1.5mm 0.1mm 1.5mm;
        vertical-align: middle;
        line-height: 9.5pt;
    }
    .table-feedback th {
        font-weight: normal;
        text-align: center;
        background: #fff;
    }
    .checkbox-cell {
        text-align: center;
        vertical-align: top;
        padding-top: 1.0mm;
        font-size: 12pt;
        font-family: 'MS Gothic', 'Calibri', sans-serif;
    }
</style>
@endpush

@section('content')
@php
    $isEmptyMode = request()->query('mode') === 'empty' || (isset($mode) && $mode === 'empty') || empty($asesi?->id);
    $startDate = $isEmptyMode ? '' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '');
    $endDate = $isEmptyMode ? '' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '');
    $assessorName = $isEmptyMode ? '' : ($assessor?->full_name ?? $assessor?->name ?? '');
    $asesiName = $isEmptyMode ? '' : ($asesi->full_name ?? '');

    $defaultComponents = [
        1 => 'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi',
        2 => 'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya',
        3 => 'Asesor memberikan kesempatan untuk mendiskusikan/menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen',
        4 => 'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman yang saya miliki',
        5 => 'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen',
        6 => 'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen',
        7 => 'Asesor memberikan umpan balik yang mendukung setelah asesmen serta tindak lanjutnya',
        8 => 'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya',
        9 => 'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen',
        10 => 'Asesor menggunakan keterampilan komunikasi yang efektif selama asesmen',
    ];
    $ak03Record = $assessment?->ak03Item;
    $ratings = $ak03Record?->ratings_json ?? [];
    $itemsByNumber = (isset($ak03Items) && $ak03Items instanceof \Illuminate\Support\Collection && $ak03Items->isNotEmpty()) ? $ak03Items->keyBy('component_number') : collect();
    $rowHeights = [
        1 => 11.98,
        2 => 12.87,
        3 => 12.87,
        4 => 12.88,
        5 => 12.91,
        6 => 12.11,
        7 => 12.06,
        8 => 12.15,
        9 => 12.12,
        10 => 12.11,
    ];
@endphp

<div class="ak03-page">
    {{-- Header Kop Table --}}
    <table class="header-kop-table">
        <tr>
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
        FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN
    </div>

    {{-- Meta Grid --}}
    <table class="table-meta">
        <colgroup>
            <col style="width: 42.68mm;">
            <col style="width: 18.80mm;">
            <col style="width: 5.00mm;">
            <col style="width: 106.07mm;">
        </colgroup>
        <tr>
            <td rowspan="2" style="vertical-align: middle;">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td style="padding: 0.5mm 1mm;">Judul</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm; font-style: italic;">{{ $scheme->name }}</td>
        </tr>
        <tr>
            <td style="padding: 0.5mm 1mm;">Nomor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $scheme->code }}</td>
        </tr>
        <tr>
            <td colspan="2">TUK</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">Sewaktu/Tempat Kerja/Mandiri*</td>
        </tr>
        <tr>
            <td colspan="2">Nama Asesor</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $assessorName }}</td>
        </tr>
        <tr>
            <td colspan="2">Nama Asesi</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $asesiName }}</td>
        </tr>
        <tr>
            <td rowspan="2" style="vertical-align: middle;">Tanggal Asesmen</td>
            <td style="padding: 0.5mm 1mm;">Mulai</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $startDate }}</td>
        </tr>
        <tr>
            <td style="padding: 0.5mm 1mm;">Selesai</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $endDate }}</td>
        </tr>
    </table>

    {{-- Instruction text --}}
    <div class="instruction-text">
        Umpan balik dari Asesi (diisi oleh Asesi setelah pengambilan keputusan):
    </div>

    {{-- Table Feedback --}}
    <table class="table-feedback">
        <colgroup>
            <col style="width: 106.7mm;">
            <col style="width: 9.5mm;">
            <col style="width: 11.3mm;">
            <col style="width: 44.6mm;">
        </colgroup>
        <thead>
            <tr style="height: 7.2mm;">
                <th rowspan="2" style="vertical-align: middle;">KOMPONEN</th>
                <th colspan="2" style="vertical-align: middle;">Hasil</th>
                <th rowspan="2" style="vertical-align: middle;">Catatan/Komentar<br>Asesi</th>
            </tr>
            <tr style="height: 7.2mm;">
                <th style="vertical-align: middle;">Ya</th>
                <th style="vertical-align: middle;">Tidak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defaultComponents as $num => $compText)
                @php
                    $savedItem = $itemsByNumber->get($num);
                    if ($savedItem) {
                        $isYes = (bool) $savedItem->result;
                        $comment = $savedItem->comment ?? '';
                    } elseif (isset($ratings[$num])) {
                        $isYes = is_array($ratings[$num]) ? ($ratings[$num]['result'] ?? true) : (bool) $ratings[$num];
                        $comment = is_array($ratings[$num]) ? ($ratings[$num]['comment'] ?? '') : '';
                    } else {
                        $isYes = true;
                        $comment = '';
                    }
                @endphp
                <tr style="height: {{ $rowHeights[$num] }}mm;">
                    <td style="vertical-align: middle;">
                        {{ $compText }}
                    </td>
                    <td class="checkbox-cell">
                        @if($isEmptyMode)
                            &#9744;
                        @else
                            {!! $isYes ? '&#9745;' : '&#9744;' !!}
                        @endif
                    </td>
                    <td class="checkbox-cell">
                        @if($isEmptyMode)
                            &#9744;
                        @else
                            {!! !$isYes ? '&#9745;' : '&#9744;' !!}
                        @endif
                    </td>
                    <td style="vertical-align: middle; padding: 0.8mm 1.5mm; font-size: 10pt;">
                        {{ $isEmptyMode ? '' : $comment }}
                    </td>
                </tr>
            @endforeach
            <tr style="height: 19.0mm;">
                <td colspan="4" style="vertical-align: top; padding: 1.8mm 2.0mm;">
                    Catatan/komentar lainnya (apabila ada) :
                    @if(!$isEmptyMode && !empty($assessment?->ak03_notes))
                        <div style="margin-top: 1.5mm; font-size: 10pt;">
                            {{ $assessment->ak03_notes }}
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
