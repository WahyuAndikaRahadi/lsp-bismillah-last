@extends('layouts.print-muk')

@section('title', 'Cover MUK - ' . $scheme->name)
@section('doc-title', 'Cover Perangkat MUK')

@section('content')
<div style="text-align: center; padding: 40px 20px; min-height: 250mm; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #1e293b;">
            LEMBAGA SERTIFIKASI PROFESI PRAKTISI OTOMOTIF INDONESIA
        </div>
        <div style="font-size: 16pt; font-weight: 900; color: #1e3a8a; margin-top: 4px; letter-spacing: 2px;">
            LSP - PrO INDO
        </div>
        <div style="font-size: 9pt; color: #64748b; margin-top: 6px;">
            Lisensi Badan Nasional Sertifikasi Profesi (BNSP) &bull; SK Lisensi No. KEP.0543/BNSP/III/2021
        </div>
        <div style="border-bottom: 3px double #000; margin: 25px auto; width: 90%;"></div>
    </div>

    <div style="margin: 40px 0;">
        <div style="font-size: 13pt; font-weight: bold; text-transform: uppercase; color: #475569; letter-spacing: 1.5px; margin-bottom: 12px;">
            PERANGKAT ASESMEN KOMPETENSI
        </div>
        <div style="font-size: 20pt; font-weight: 900; text-transform: uppercase; color: #0f172a; line-height: 1.3; border: 2px solid #000; padding: 20px 25px; display: inline-block; max-width: 90%;">
            MATERI UJI KOMPETENSI (MUK)
        </div>
    </div>

    <div style="margin: 30px auto; width: 85%; text-align: left; font-size: 11pt;">
        <table style="width: 100%; border-collapse: collapse; line-height: 2;">
            <tr>
                <td style="width: 32%; font-weight: bold;">Skema Sertifikasi</td>
                <td style="width: 4%; text-align: center;">:</td>
                <td style="width: 64%; font-weight: bold; color: #1e3a8a;">{{ $scheme->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nomor Skema</td>
                <td style="text-align: center;">:</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $scheme->code }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tempat Uji Kompetensi</td>
                <td style="text-align: center;">:</td>
                <td>{{ $schedule->tuk_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Pelaksanaan</td>
                <td style="text-align: center;">:</td>
                <td>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Asesor Kompetensi</td>
                <td style="text-align: center;">:</td>
                <td>
                    @foreach($assessors as $assr)
                        <div>{{ $assr->full_name ?? $assr->name }} ({{ $assr->profile?->met_number ?? 'MET' }})</div>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <div style="font-weight: bold; font-size: 11pt; letter-spacing: 1px;">
            JAKARTA / BEKASI
        </div>
        <div style="font-size: 10pt; font-weight: bold; margin-top: 4px;">
            {{ \Carbon\Carbon::parse($schedule->date)->format('Y') }}
        </div>
    </div>
</div>
@endsection
