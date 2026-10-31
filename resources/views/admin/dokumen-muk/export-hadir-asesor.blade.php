@extends('layouts.print-muk')

@section('title', 'Daftar Hadir Asesor - ' . $schedule->code)
@section('doc-title', 'Daftar Hadir Asesor')

@section('content')
<div style="font-size: 10pt; line-height: 1.4;">
    {{-- Header Title --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 12pt; font-weight: bold; text-decoration: underline; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            DAFTAR HADIR ASESOR
        </h2>
    </div>

    {{-- Meta List 1-5 --}}
    <div style="margin-bottom: 20px; line-height: 1.8;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
            <tr>
                <td style="width: 18%; vertical-align: top;">1. Skema</td>
                <td style="width: 3%; vertical-align: top; text-align: center;">:</td>
                <td style="width: 79%; vertical-align: top;"><strong>{{ $scheme->name }}</strong></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">2. TUK</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;"><strong>{{ $schedule->tuk_name }}</strong></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">3. Alamat</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $schedule->tuk_address ?? 'Tempat Uji Kompetensi Terverifikasi LSP PrO Indo' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">4. Tanggal Uji</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">5. Jumlah Asesi</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;"><strong>{{ $total_asesi }}</strong> orang</td>
            </tr>
        </table>
    </div>

    {{-- Assessor Table --}}
    <table class="bnsp-table" style="width: 100%; font-size: 9.5pt;">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th style="width: 42%;">Nama Asesor Kompetensi</th>
                <th style="width: 25%;">No. MET</th>
                <th style="width: 25%;">Tanda Tangan</th>
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
                <tr>
                    <td style="text-align: center; height: 42px; vertical-align: middle;">{{ $i + 1 }}.</td>
                    <td style="vertical-align: middle; font-weight: bold;">
                        {{ $assr?->full_name ?? $assr?->name ?? '' }}
                    </td>
                    <td style="vertical-align: middle; text-align: center; font-family: monospace;">
                        {{ $assr?->assessor_registration_number ?? $assr?->profile?->met_number ?? '' }}
                    </td>
                    <td style="vertical-align: middle; text-align: center;">
                        @if($assr?->profile?->signature_path)
                            <img src="{{ asset('storage/' . $assr->profile->signature_path) }}" style="max-height: 38px; max-width: 120px;" alt="TTD">
                        @elseif($assessments->first()?->assessor_signature && $i === 0)
                            <img src="{{ $assessments->first()->assessor_signature }}" style="max-height: 38px; max-width: 120px;" alt="TTD">
                        @else
                            <div style="height: 35px;"></div>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- PIC TUK Signature on Right Side --}}
    <div style="margin-top: 35px; width: 100%; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 45%; margin-left: auto;">
            <div style="margin-bottom: 10px;">
                {{ $schedule->tuk_city ?? 'Karawang' }}, {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
            </div>
            <div style="font-weight: bold; margin-bottom: 6px;">Penanggungjawab kegiatan</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt; line-height: 1.6;">
                <tr>
                    <td style="width: 32%;">Nama</td>
                    <td style="width: 5%;">:</td>
                    <td style="width: 63%;"><strong>{{ $schedule->pic_tuk_name ?? '........................................' }}</strong></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $schedule->pic_tuk_position ?? 'Penanggung Jawab TUK' }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top: 6px;">Tanda tangan :</td>
                </tr>
            </table>
            <div class="sig-box" style="margin-top: 8px;">
                @if($schedule->pic_tuk_signature)
                    <img src="{{ $schedule->pic_tuk_signature }}" class="sig-img" alt="TTD Penanggung Jawab">
                @else
                    <div style="height: 50px;"></div>
                @endif
                <div style="border-bottom: 1px dotted #000; width: 85%; margin-top: 4px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection
