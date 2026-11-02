@extends('layouts.print-muk')

@section('title', 'Daftar Hadir Asesor - ' . ($schedule->code ?? ''))
@section('doc-title', 'Daftar Hadir Asesor')

@push('styles')
<style>
    @page {
        size: 210.26mm 297.04mm;
        margin: 5.29mm 12.0mm 15.0mm 10.05mm;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, 'Liberation Sans', sans-serif;
    }
    .sheet {
        margin: 0 !important;
        padding: 0 !important;
        width: 190.16mm !important;
    }
</style>
@endpush

@section('content')
<div style="width: 190.16mm; font-family: Arial, 'Liberation Sans', sans-serif; font-size: 11pt; color: #000;">
    {{-- Master Kop Surat at y = 5.29mm --}}
    <div style="margin: 0; padding: 0; height: 34.66mm;">
        <img src="/images/muk/kop_surat.png" alt="Kop Surat LSP ProIndo" style="width: 190.16mm; height: 34.66mm; display: block;">
    </div>

    {{-- Title at y = 42.8mm, x = 88.2mm (offset 78.15mm) --}}
    <div style="text-align: left; padding-left: 78.15mm; margin-top: 2.85mm; height: 4.3mm; margin-bottom: 10.2mm;">
        <span style="font-size: 11pt; font-weight: bold; line-height: 1;">DAFTAR HADIR ASESOR</span>
    </div>

    {{-- Meta List 1-5 at y = 57.3mm, x = 30.7mm (offset 20.65mm) --}}
    <div style="margin-left: 20.65mm; margin-bottom: 6.3mm; font-size: 11pt;">
        <table style="width: 169.5mm; border-collapse: collapse; font-size: 11pt; line-height: 1.18;">
            <tr style="height: 5.2mm;">
                <td style="width: 31.7mm; vertical-align: top; padding: 0;">1. Skema</td>
                <td style="width: 3.5mm; text-align: center; vertical-align: top; padding: 0;">:</td>
                <td style="vertical-align: top; padding: 0;">{{ $scheme->name ?: 'Mekanik Junior' }}</td>
            </tr>
            <tr style="height: 5.2mm;">
                <td style="vertical-align: top; padding: 0;">2. TUK</td>
                <td style="text-align: center; vertical-align: top; padding: 0;">:</td>
                <td style="vertical-align: top; padding: 0;">{{ $schedule->tuk_name ?: 'SMK Negeri 3 Karawang' }}</td>
            </tr>
            <tr style="height: 7.7mm;">
                <td style="vertical-align: top; padding: 0;">3. &nbsp;Alamat</td>
                <td style="text-align: center; vertical-align: top; padding: 0;">:</td>
                <td style="vertical-align: top; padding: 0; line-height: 1.05;">
                    Perumahan Griya Kondang Asri, Kelurahan Kondangjaya, Kecamatan Karawang<br>
                    <span style="font-family: 'Calibri', 'Carlito', sans-serif; font-size: 12pt; padding-left: 3.5mm;">Timur, Kabupaten Karawang, Jawa Barat (Kode Pos 41317)</span>
                </td>
            </tr>
            <tr style="height: 5.2mm;">
                <td style="vertical-align: top; padding: 0;">4. Tanggal Uji</td>
                <td style="text-align: center; vertical-align: top; padding: 0;">:</td>
                <td style="vertical-align: top; padding: 0;">{{ ($mode ?? '') === 'empty' ? '.......................................' : \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr style="height: 5.2mm;">
                <td style="vertical-align: top; padding: 0;">5. Jumlah Asesi</td>
                <td style="text-align: center; vertical-align: top; padding: 0;">:</td>
                <td style="vertical-align: top; padding: 0;">{{ ($mode ?? '') === 'empty' ? '........................................' : ($total_asesi . ' orang') }}</td>
            </tr>
        </table>
    </div>

    {{-- Assessor Table at y = 96.5mm, x = 17.73mm (offset 7.68mm) --}}
    <table class="bnsp-table" style="width: 179.92mm; margin-left: 7.68mm; border-collapse: collapse; font-size: 11pt; margin-top: 0; margin-bottom: 8.3mm;">
        <colgroup>
            <col style="width: 9.26mm;">
            <col style="width: 58.47mm;">
            <col style="width: 52.65mm;">
            <col style="width: 59.53mm;">
        </colgroup>
        <thead>
            <tr style="height: 7.0mm;">
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">No.</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Nama Asesor Kompetensi</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">No.MET</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $assessorList = $assessors;
                $rowCount = max(5, count($assessorList));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $assr = $assessorList[$i] ?? null;
                @endphp
                <tr style="height: 8.2mm;">
                    <td style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px;">{{ $i + 1 }}.</td>
                    <td style="border: 1px solid #000; padding: 1px 4px;">{{ $assr?->full_name ?? $assr?->name ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; padding: 1px 2px;">{{ $assr?->assessor_registration_number ?? $assr?->profile?->met_number ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; padding: 1px 2px;">
                        @if(($mode ?? '') !== 'empty' && $assr?->profile?->signature_path)
                            <img src="{{ asset('storage/' . $assr->profile->signature_path) }}" style="max-height: 7mm; max-width: 45mm;" alt="TTD">
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- PIC TUK Signature on Right Side at x = 114.3mm (offset 104.25mm) --}}
    <div style="margin-left: 104.25mm; font-size: 11pt;">
        <div style="margin-bottom: 4.5mm;">
            {{ ($mode ?? '') === 'empty' ? '……………….., ………………….2026' : (($schedule->tuk_city ?? 'Karawang') . ', ' . \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y')) }}
        </div>
        <div style="font-family: 'Calibri', 'Carlito', sans-serif; font-size: 12pt; line-height: 1.15;">
            <div style="height: 4.5mm;">Penanggungjawab kegiatan</div>
            <div style="height: 4.5mm;">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($schedule->pic_tuk_name ?? '') }}</div>
            <div style="height: 4.5mm;">Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($schedule->pic_tuk_position ?? '') }}</div>
            <div style="height: 4.5mm;">Tanda tangan &nbsp;:</div>
            <div style="height: 16.0mm;">
                @if(($mode ?? '') !== 'empty' && $schedule->pic_tuk_signature)
                    <img src="{{ $schedule->pic_tuk_signature }}" style="max-height: 14mm;" alt="TTD">
                @endif
            </div>
            <div style="font-family: Arial, 'Liberation Sans', sans-serif; font-size: 12pt;">………………………………….</div>
        </div>
    </div>
</div>
@endsection
