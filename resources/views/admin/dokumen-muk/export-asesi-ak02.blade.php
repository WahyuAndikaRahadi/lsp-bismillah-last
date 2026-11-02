@extends('layouts.print-muk')

@section('title', 'FR.AK.02 Rekaman Asesmen - ' . ($asesi->full_name ?? 'Peserta'))
@section('doc-title', 'FR.AK.02 Rekaman Asesmen Kompetensi')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 12.7mm 7.2mm 12.7mm 20.0mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .header-kop-table {
        width: 180.0mm;
        margin-left: 2.6mm;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 6.5mm;
    }
    .header-kop-table td {
        border: 1px solid #000;
    }
    .header-logo-cell {
        width: 33.7mm;
        height: 20.1mm;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }
    .header-title-cell {
        width: 146.3mm;
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
        margin-left: 2.5mm;
        margin-bottom: 5.7mm;
    }
    .table-meta {
        width: 182.8mm;
        margin-left: 0.3mm;
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
        margin-left: 0;
        margin-bottom: 2.5mm;
        line-height: 1.2;
    }
    .table-matrix {
        width: 182.8mm;
        margin-left: 0;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-bottom: 0;
    }
    .table-matrix th, .table-matrix td {
        border: 1px solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: middle;
    }
    .table-matrix th {
        font-weight: bold;
        text-align: center;
        background: #fff;
    }
    .th-vert {
        height: 21.5mm;
        vertical-align: top;
        padding: 1.2mm 0 0 0;
        text-align: center;
    }
    .th-vert span {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        display: inline-block;
        vertical-align: middle;
        font-family: 'Calibri', sans-serif;
        font-size: 11pt;
        white-space: nowrap;
        line-height: 1.0;
    }
    .table-rekomendasi {
        width: 182.8mm;
        margin-left: 0;
        border-collapse: collapse;
        border: 1px solid #000;
        border-top: none;
        font-size: 11pt;
    }
    .table-rekomendasi td {
        border: 1px solid #000;
        padding: 1.5mm 2.0mm;
        vertical-align: top;
    }
    .table-sig {
        width: 182.1mm;
        margin-left: 0.5mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
        margin-top: 22.7mm;
        margin-bottom: 0;
    }
    .table-sig td {
        border: 1px solid #000;
        padding: 1.2mm 2.0mm;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
@php
    $startDate = $schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '';
    $endDate = $schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '';
    $assessorName = $assessor?->full_name ?? $assessor?->name ?? '';
    $asesiName = $asesi->full_name ?? '';
    $regNo = $assessor?->profile?->no_reg_asesor ?? $assessor?->profile?->met_number ?? '';
    $isK = in_array($assessment->final_result, ['K', 'KOMPETEN']) || ($assessment->final_result === null && $assessment->status === 'COMPLETED');
    $isBK = in_array($assessment->final_result, ['BK', 'BELUM_KOMPETEN']);
@endphp

{{-- PAGE 1 --}}
<div class="ak02-page">
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
        FR.AK.02. REKAMAN ASESMEN KOMPETENSI
    </div>

    {{-- Meta Grid --}}
    <table class="table-meta">
        <colgroup>
            <col style="width: 45.5mm;">
            <col style="width: 18.2mm;">
            <col style="width: 3.0mm;">
            <col style="width: 116.1mm;">
        </colgroup>
        <tr>
            <td rowspan="2" style="vertical-align: middle;">
                Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)
            </td>
            <td style="padding: 0.5mm 1mm;">Judul</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 1.5mm;">{{ $scheme->name }}</td>
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

    <div class="instruction-text">
        Beri tanda centang (√) di kolom yang sesuai untuk mencerminkan bukti yang diperoleh untuk menentukan<br>
        Kompetensi Asesi untuk setiap Unit Kompetensi.
    </div>

    {{-- Matrix Table --}}
    @php
        $ak02Indexed = $ak02Items->keyBy('competency_unit_id');
        $units = $scheme->competencyUnits;
    @endphp
    <table class="table-matrix">
        <colgroup>
            <col style="width: 85.7mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 11.3mm;">
            <col style="width: 11.3mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.5mm;">
            <col style="width: 12.0mm;">
        </colgroup>
        <thead>
            <tr style="height: 2.8mm;">
                <th rowspan="2" style="text-align: center; vertical-align: middle; font-family: 'Calibri', sans-serif; font-size: 11pt; font-weight: bold;">Unit Kompetensi</th>
                <th colspan="8" style="font-size: 7.5pt; padding: 0; line-height: 2.8mm; height: 2.8mm; text-align: center; font-weight: bold;">Bukti yang Diperoleh</th>
            </tr>
            <tr style="height: 25.0mm;">
                <th class="th-vert"><span>Observasi</span><span>Demonstrasi</span></th>
                <th class="th-vert"><span>Portofolio</span></th>
                <th class="th-vert"><span>Pernyataan</span><span>Pihak Ketiga</span></th>
                <th class="th-vert"><span>Pertanyaan</span><span>Wawancara</span></th>
                <th class="th-vert"><span>Pertanyaan</span><span>Lisan</span></th>
                <th class="th-vert"><span>Pertanyaan</span><span>Tertulis</span></th>
                <th class="th-vert"><span>Proyek Kerja</span></th>
                <th class="th-vert"><span>Lainnya</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach($units as $unit)
                @php
                    $item = $ak02Indexed->get($unit->id);
                    $isObs = $item ? $item->is_observasi : true;
                    $isPorto = $item ? $item->is_portofolio : false;
                    $isPihakKetiga = $item ? $item->is_pihak_ketiga : false;
                    $isWawancara = $item ? $item->is_lisan : false;
                    $isLisan = $item ? $item->is_lisan : false;
                    $isTulis = $item ? $item->is_tulis : true;
                    $isProyek = $item ? $item->is_proyek : false;
                    $isLainnya = $item ? $item->is_lainnya : false;
                @endphp
                <tr style="height: 8.8mm;">
                    <td style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.15; padding: 0.8mm 1.5mm;">
                        {{ $unit->title }}
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isObs ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isPorto ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isPihakKetiga ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isWawancara ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isLisan ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isTulis ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isProyek ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">{!! $isLainnya ? '&radic;' : '' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Recommendation Table --}}
    <table class="table-rekomendasi">
        <colgroup>
            <col style="width: 85.7mm;">
            <col style="width: 97.1mm;">
        </colgroup>
        <tr style="height: 7.6mm;">
            <td style="font-weight: normal; vertical-align: middle;">Rekomendasi hasil asesmen</td>
            <td style="vertical-align: middle;">
                &#9633; Kompeten / &#9633; Belum kompeten
            </td>
        </tr>
        <tr style="height: 21.5mm;">
            <td style="font-weight: bold;">
                Tindak lanjut yang dibutuhkan<br>
                <span style="font-weight: normal; font-size: 11pt;">(Masukkan pekerjaan tambahan dan asesmen yang<br>diperlukan untuk mencapai kompetensi)</span>
            </td>
            <td>
                {{ $assessment->assessor_follow_up ?? '' }}
            </td>
        </tr>
        <tr style="height: 19.4mm;">
            <td style="font-weight: normal;">
                Komentar/ Observasi oleh asesor
            </td>
            <td>
                {{ $assessment->assessor_notes ?? '' }}
            </td>
        </tr>
    </table>
</div>

{{-- PAGE BREAK --}}
<div style="page-break-before: always; break-before: page;"></div>

{{-- PAGE 2 --}}
<div class="ak02-page">
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

    {{-- Signatures Table --}}
    <table class="table-sig">
        <colgroup>
            <col style="width: 60.0mm;">
            <col style="width: 3.0mm;">
            <col style="width: 119.1mm;">
        </colgroup>
        <tr style="height: 5.3mm;">
            <td colspan="3" style="font-weight: bold; padding: 0.5mm 2.0mm;">Asesi :</td>
        </tr>
        <tr style="height: 7.2mm;">
            <td style="padding: 0.5mm 2.0mm;">Nama</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 2.0mm;">{{ $asesiName }}</td>
        </tr>
        <tr style="height: 15.3mm;">
            <td style="vertical-align: top; padding: 1.0mm 2.0mm; line-height: 1.55;">
                Tanda tangan<br>dan Tanggal
            </td>
            <td style="vertical-align: top; text-align: center; padding: 1.0mm 0; line-height: 1.55;">:</td>
            <td style="vertical-align: bottom; padding: 0.5mm 2.0mm 1.0mm 2.0mm;">
                @if($assessment->asesi_signature)
                    <img src="{{ $assessment->asesi_signature }}" style="max-height: 10mm; display: block;" alt="TTD">
                @endif
                <span style="font-size: 11pt;">{{ $startDate }}</span>
            </td>
        </tr>
        <tr style="height: 4.9mm;">
            <td colspan="3" style="font-weight: bold; padding: 0.5mm 2.0mm;">Asesor :</td>
        </tr>
        <tr style="height: 6.7mm;">
            <td style="padding: 0.5mm 2.0mm;">Nama</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 2.0mm;">{{ $assessorName }}</td>
        </tr>
        <tr style="height: 6.6mm;">
            <td style="padding: 0.5mm 2.0mm;">No. Reg</td>
            <td style="text-align: center; padding: 0.5mm 0;">:</td>
            <td style="padding: 0.5mm 2.0mm;">{{ $regNo }}</td>
        </tr>
        <tr style="height: 14.3mm;">
            <td style="vertical-align: top; padding: 1.0mm 2.0mm; line-height: 1.55;">
                Tanda tangan<br>dan Tanggal
            </td>
            <td style="vertical-align: top; text-align: center; padding: 1.0mm 0; line-height: 1.55;">:</td>
            <td style="vertical-align: bottom; padding: 0.5mm 2.0mm 1.0mm 2.0mm;">
                @if($assessor?->profile?->signature_path)
                    <img src="{{ asset('storage/' . $assessor->profile->signature_path) }}" style="max-height: 10mm; display: block;" alt="TTD">
                @elseif($assessment->assessor_signature)
                    <img src="{{ $assessment->assessor_signature }}" style="max-height: 10mm; display: block;" alt="TTD">
                @endif
                <span style="font-size: 11pt;">{{ $startDate }}</span>
            </td>
        </tr>
    </table>

    {{-- Lampiran Dokumen --}}
    <div style="font-size: 11pt; line-height: 1.25; margin-left: -2.3mm; margin-top: 2.8mm;">
        <div style="margin-bottom: 1.2mm;">LAMPIRAN DOKUMEN:</div>
        <div style="padding-left: 7.3mm; line-height: 4.8mm;">
            1. Dokumen APL 01 peserta<br>
            2. Dokumen APL 02 peserta<br>
            3. Bukti-bukti berkualitas peserta<br>
            4. Tinjauan proses asesmen
        </div>
    </div>
</div>
@endsection
