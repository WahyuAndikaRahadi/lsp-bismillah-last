@extends('layouts.print-muk')

@section('title', 'Surat Pernyataan Pemegang Sertifikat - ' . $asesi->full_name)
@section('doc-title', 'Surat Pernyataan Pemegang Sertifikat')

@section('content')
<div style="font-size: 10pt; line-height: 1.5; padding: 10px 15px;">
    {{-- Header Kop --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 12pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            SURAT PERNYATAAN PEMEGANG SERTIFIKAT KOMPETENSI
        </h2>
        <div style="font-size: 10.5pt; font-weight: bold; margin-top: 4px;">
            LEMBAGA SERTIFIKASI PROFESI PIHAK KETIGA<br>PRAKTISI OTOMOTIF INDONESIA (LSP P3 PRO INDO)
        </div>
        <div style="border-bottom: 2px solid #000; margin-top: 15px; width: 100%;"></div>
    </div>

    {{-- Opening clause --}}
    <div style="margin-bottom: 20px; text-align: justify;">
        Pada hari ini <strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l') }}</strong> Tanggal <strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d') }}</strong> Bulan <strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('F') }}</strong> Tahun <strong>{{ \Carbon\Carbon::parse($schedule->date)->format('Y') }}</strong> bertempat di <strong>{{ $schedule->tuk_name }}</strong>.
    </div>

    <div style="margin-bottom: 15px;">
        Saya bertanda tangan dibawah ini :
    </div>

    {{-- Meta Grid --}}
    <div style="margin-left: 20px; margin-bottom: 25px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt; line-height: 2;">
            <tr>
                <td style="width: 25%;">Nama</td>
                <td style="width: 4%;">:</td>
                <td style="width: 71%; font-weight: bold;">{{ $asesi->full_name }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td style="font-family: monospace;">{{ $profile?->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td>Skema</td>
                <td>:</td>
                <td style="font-weight: bold;">{{ $scheme->name }}</td>
            </tr>
            <tr>
                <td>No. Blanko</td>
                <td>:</td>
                <td>{{ $assessment->blanko_number ?? '........................................' }}</td>
            </tr>
            <tr>
                <td>No. Sertifikat</td>
                <td>:</td>
                <td>{{ $assessment->certificate_number ?? '........................................' }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 12px;">
        Dengan ini menyatakan bahwa saya akan:
    </div>

    {{-- Statement points 1 to 4 matching master verbatim --}}
    <ol style="margin-left: 20px; padding-left: 10px; line-height: 1.6; text-align: justify; margin-bottom: 25px;">
        <li style="margin-bottom: 8px;">
            Mematuhi ketentuan yang relevan dalam skema sertifikasi.
        </li>
        <li style="margin-bottom: 8px;">
            Menggunakan sertifikat hanya untuk ruang lingkup sertifikasi yang diberikan.
        </li>
        <li style="margin-bottom: 8px;">
            Tidak menggunakan sertifikat yang dapat mencemarkan / merugikan Lembaga Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo) dan tidak memberikan pernyataan terkait sertifikasi yang oleh Lembaga Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo) dianggap dapat menyesatkan atau tidak dapat dipertanggungjawabkan.
        </li>
        <li style="margin-bottom: 8px;">
            Menghentikan penggunaan atau pengakuan sertifikat setelah sertifikat dibekukan atau dicabut oleh Lembaga Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo) dan mengembalikan sertifikat kepada Lembaga Sertifikasi Profesi Pihak Ketiga Praktisi Otomotif Indonesia (LSP P3 PrO Indo).
        </li>
    </ol>

    <div style="margin-bottom: 35px; text-align: justify;">
        Demikian pernyataan ini saya buat dengan sebenar-benarnya dan penuh tanggung jawab.
    </div>

    {{-- Signature Section on Right Side --}}
    <div style="width: 100%; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 48%; text-align: center; margin-left: auto;">
            <div style="margin-bottom: 10px;">
                {{ $schedule->tuk_city ?? 'Karawang' }}, {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
            </div>
            <div style="font-weight: bold; margin-bottom: 6px;">Yang membuat pernyataan,</div>
            <div class="sig-box" style="margin-top: 10px;">
                @if($assessment->asesi_signature)
                    <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                @else
                    <div style="height: 55px;"></div>
                @endif
                <div style="border-bottom: 1px solid #000; width: 75%; margin: 6px auto;"></div>
            </div>
            <div style="font-weight: bold; font-size: 10pt;">
                ( {{ $asesi->full_name }} )
            </div>
        </div>
    </div>
</div>
@endsection
