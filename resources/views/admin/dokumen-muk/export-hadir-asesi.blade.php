@extends('layouts.print-muk')

@section('title', 'Daftar Hadir Asesi - ' . ($schedule->code ?? ''))
@section('doc-title', 'Daftar Hadir Asesi')
@section('sheet-class', 'landscape')

@push('styles')
<style>
    @page {
        size: A4 landscape;
        margin: 10.53mm 21.08mm 10.0mm 21.87mm;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, 'Liberation Sans', sans-serif;
    }
    .sheet {
        margin: 0 !important;
        padding: 0 !important;
        width: 254.0mm !important;
    }
</style>
@endpush

@section('content')
<div style="width: 254.0mm; font-family: Arial, 'Liberation Sans', sans-serif; font-size: 10pt; color: #000;">
    {{-- Kop Surat Landscape at y = 10.53mm, x = 25.93mm (offset 4.06mm from container left) --}}
    <div style="margin-left: 4.06mm; margin-bottom: 2.94mm; height: 24.13mm;">
        <img src="/images/muk/kop_surat.png" alt="Kop Surat LSP ProIndo" style="width: 154.13mm; height: 24.13mm; display: block;">
    </div>

    {{-- Title at y = 37.6mm, centered --}}
    <div style="text-align: center; height: 4.0mm; margin-bottom: 3.5mm;">
        <span style="font-size: 10pt; font-weight: bold; line-height: 1;">DAFTAR HADIR</span>
    </div>

    {{-- Meta Info at y = 45.1mm, x = 25.4mm (offset 3.53mm) --}}
    <div style="margin-left: 3.53mm; margin-bottom: 3.3mm; font-size: 10pt;">
        <table style="width: 240mm; border-collapse: collapse; font-size: 10pt;">
            <tr style="height: 6.8mm;">
                <td style="width: 24.0mm; padding: 0; vertical-align: top;">Hari / tanggal</td>
                <td style="width: 3.53mm; text-align: center; padding: 0; vertical-align: top;">:</td>
                <td style="padding: 0; vertical-align: top;">{{ ($mode ?? '') === 'empty' ? '……………………/ ………………………. 2026' : \Carbon\Carbon::parse($schedule->date)->translatedFormat('l / d F Y') }}</td>
            </tr>
            <tr style="height: 5.5mm;">
                <td style="padding: 0; vertical-align: top;">Tempat</td>
                <td style="text-align: center; padding: 0; vertical-align: top;">:</td>
                <td style="padding: 0; vertical-align: top;">{{ ($mode ?? '') === 'empty' ? '.......................................……………..' : ($schedule->tuk_name ?: '.......................................……………..') }}</td>
            </tr>
        </table>
    </div>

    {{-- Attendance Table at y = 62.4mm, width = 254.0mm --}}
    <table class="bnsp-table" style="width: 254.0mm; border-collapse: collapse; font-size: 10pt; margin: 0 0 10.9mm 0;">
        <colgroup>
            <col style="width: 11.11mm;">
            <col style="width: 46.30mm;">
            <col style="width: 36.25mm;">
            <col style="width: 69.85mm;">
            <col style="width: 23.81mm;">
            <col style="width: 28.58mm;">
            <col style="width: 38.10mm;">
        </colgroup>
        <thead>
            <tr style="height: 7.5mm;">
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">No</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Nama Peserta</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Institusi</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Alamat</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Pekerjaan</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">No. Telp/HP</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 2px; background: #fff;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = max(10, count($assessments));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $num = $i + 1;
                    $asm = $assessments[$i] ?? null;
                    $asesi = $asm?->asesi;
                    $profile = $asesi?->profile;
                    $isOdd = ($num % 2 !== 0);
                @endphp
                <tr style="height: 6.4mm;">
                    <td style="border: 1px solid #000; text-align: center; padding: 0 2px;">{{ $num }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $asesi?->full_name ?? $asesi?->name ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $profile?->institution ?? $profile?->company ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px; font-size: 9pt;">{{ $profile?->address ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $profile?->occupation ?? $profile?->job_title ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $asesi?->phone ?? $profile?->phone ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 2px;">
                        <div style="display: flex; justify-content: {{ $isOdd ? 'flex-start' : 'flex-end' }}; align-items: center; width: 100%;">
                            <span style="display: inline-block; width: 19mm; text-align: {{ $isOdd ? 'left' : 'right' }}; padding-left: 2mm; padding-right: 2mm;">
                                {{ $num }}
                                @if(($mode ?? '') !== 'empty' && $asm?->asesi_signature)
                                    <img src="{{ $asm->asesi_signature }}" style="max-height: 5.5mm; vertical-align: middle;" alt="TTD">
                                @endif
                            </span>
                        </div>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Assessor Signature on Left Side matching master at y = 143.7mm --}}
    <div style="margin-left: 11.43mm; font-size: 10pt;">
        <div style="height: 4.7mm;">
            {{ ($mode ?? '') === 'empty' ? '……………………….. , …………………………………. 2026' : (($schedule->tuk_city ?? 'Karawang') . ' , ' . \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y')) }}
        </div>
        <div style="margin-left: -9.5mm; height: 7.4mm;">Asesor :</div>
        <div style="margin-left: 1.6mm;">
            <div style="height: 4.7mm;">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($leadAssessor?->full_name ?? $leadAssessor?->name ?? '') }}</div>
            <div style="height: 4.7mm;">No. Reg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($leadAssessor?->assessor_registration_number ?? $leadAssessor?->profile?->met_number ?? '') }}</div>
            <div style="height: 4.7mm;">Tanda tangan &nbsp;:</div>
            <div style="height: 9.5mm;">
                @if(($mode ?? '') !== 'empty' && $leadAssessor?->profile?->signature_path)
                    <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" style="max-height: 8.5mm;" alt="TTD">
                @endif
            </div>
            <div>………………………………….</div>
        </div>
    </div>
</div>
@endsection
