@extends('layouts.print-muk')

@section('title', 'FR.AK.04 Banding Asesmen - ' . ($asesi->full_name ?? 'Peserta'))
@section('doc-title', 'FR.AK.04 Banding Asesmen')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 25.7mm 11.56mm 15mm 25.58mm;
    }
    body {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.15;
        color: #000;
    }
    .doc-main-title {
        font-family: 'Calibri', 'Carlito', Arial, sans-serif;
        font-weight: bold;
        font-size: 12pt;
        margin-bottom: 4.2mm;
    }
    .table-ak04 {
        width: 172.5mm;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 11pt;
    }
    .table-ak04 td {
        border: 1px solid #000;
    }
    .checkbox-cell {
        text-align: center;
        vertical-align: middle;
        font-size: 11pt;
        font-family: 'MS Gothic', 'Calibri', sans-serif;
    }
</style>
@endpush

@section('content')
@php
    $isEmptyMode = request()->query('mode') === 'empty' || (isset($mode) && $mode === 'empty') || empty($asesi?->id);
    $asesiName = $isEmptyMode ? '' : ($asesi->full_name ?? '');
    $assessorName = $isEmptyMode ? '' : ($assessor?->full_name ?? $assessor?->name ?? '');
    $scheduleDate = $isEmptyMode ? '' : ($schedule->date ? \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') : '');

    $latestAppeal = (isset($appeals) && $appeals instanceof \Illuminate\Support\Collection) ? $appeals->first() : null;
    $isYes1 = true;
    $isYes2 = true;
    $isYes3 = false;
    $reasonText = (!$isEmptyMode && $latestAppeal && ($latestAppeal->appeal_reasons || $latestAppeal->reason))
        ? ($latestAppeal->appeal_reasons ?? $latestAppeal->reason)
        : 'Asesi mampu mengerjakannya';
@endphp

<div class="ak04-page">
    {{-- Main Document Title --}}
    <div class="doc-main-title">
        FR.AK.04. BANDING ASESMEN
    </div>

    {{-- Main Continuous Table --}}
    <table class="table-ak04">
        <colgroup>
            <col style="width: 134.43mm;">
            <col style="width: 17.44mm;">
            <col style="width: 20.63mm;">
        </colgroup>
        <tbody>
            {{-- Row 1: Nama Asesi --}}
            <tr style="height: 7.79mm;">
                <td colspan="3" style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Nama Asesi: {{ $asesiName }}
                </td>
            </tr>

            {{-- Row 2: Nama Asesor --}}
            <tr style="height: 7.57mm;">
                <td colspan="3" style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Nama Asesor: {{ $assessorName }}
                </td>
            </tr>

            {{-- Row 3: Tanggal Asesmen --}}
            <tr style="height: 7.62mm;">
                <td colspan="3" style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Tanggal Asesmen: {{ $scheduleDate }}
                </td>
            </tr>

            {{-- Row 4: Question Header --}}
            <tr style="height: 10.04mm;">
                <td style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Jawablah dengan Ya atau Tidak pertanyaan-pertanyaan berikut ini :
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    YA
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    TIDAK
                </td>
            </tr>

            {{-- Row 5: Question 1 --}}
            <tr style="height: 12.36mm;">
                <td style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Apakah Proses Banding telah dijelaskan kepada Anda?
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! $isYes1 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! !$isYes1 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
            </tr>

            {{-- Row 6: Question 2 --}}
            <tr style="height: 12.32mm;">
                <td style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Apakah Anda telah mendiskusikan Banding dengan Asesor?
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! $isYes2 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! !$isYes2 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
            </tr>

            {{-- Row 7: Question 3 --}}
            <tr style="height: 12.41mm;">
                <td style="padding: 1.0mm 2.5mm; vertical-align: middle;">
                    Apakah Anda mau melibatkan “orang lain” membantu Anda dalam Proses Banding?
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! $isYes3 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
                <td class="checkbox-cell">
                    @if($isEmptyMode)
                        &#9744;
                    @else
                        {!! !$isYes3 ? '&#9745;' : '&#9744;' !!}
                    @endif
                </td>
            </tr>

            {{-- Row 8: Scheme block --}}
            <tr style="height: 26.16mm;">
                <td colspan="3" style="padding: 1.5mm 2.5mm; vertical-align: top; line-height: 1.15;">
                    Banding ini diajukan atas Keputusan Asesmen yang dibuat terhadap Skema Sertifikasi (Kualifikasi/Klaster/Okupasi) berikut :<br>
                    <div style="margin-top: 1.5mm;">
                        Skema Sertifikasi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $isEmptyMode ? '' : ($scheme->name ?? '') }}<br>
                        No. Skema Sertifikasi : {{ $isEmptyMode ? '' : ($scheme->code ?? '') }}
                    </div>
                </td>
            </tr>

            {{-- Row 9: Reason block --}}
            <tr style="height: 29.09mm;">
                <td colspan="3" style="padding: 1.32mm 2.5mm; vertical-align: top; line-height: 1.15;">
                    Banding ini diajukan atas alasan sebagai berikut :<br>
                    <div style="margin-top: 5.5mm;">
                        {{ $reasonText }}
                    </div>
                </td>
            </tr>

            {{-- Row 10: Rights Notice --}}
            <tr style="height: 17.44mm;">
                <td colspan="3" style="padding: 1.25mm 2.5mm; vertical-align: top; line-height: 1.15;">
                    Anda mempunyai hak mengajukan banding jika Anda menilai Proses Asesmen tidak sesuai SOP dan tidak memenuhi Prinsip Asesmen.
                </td>
            </tr>

            {{-- Row 11: Signature line --}}
            <tr style="height: 28.37mm;">
                <td colspan="3" style="padding: 12.5mm 2.5mm 2.0mm 2.5mm; vertical-align: top; line-height: 1.15;">
                    Tanda tangan Asesi : …………………………………………. Tanggal : …………………………….
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
