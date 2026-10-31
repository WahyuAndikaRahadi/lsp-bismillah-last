@extends('pdf.layout', ['title' => 'FR-AK-01 Persetujuan Asesmen - ' . $application->nomor_permohonan])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-AK-01. PERSETUJUAN ASESMEN DAN KERAHASIAAN</div>
    <div class="doc-number">Nomor: {{ $application->nomor_permohonan }}/AK-01</div>
</div>

<p style="text-align: justify; font-size: 9.5pt; margin-bottom: 12px;">
    Persetujuan ini dibuat antara Asesi dan Asesor Kompetensi sebelum proses asesmen dilaksanakan:
</p>

<table class="clean-table" style="margin-left: 10px; margin-bottom: 15px;">
    <tr>
        <td style="width: 160px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->scheme->nama_skema ?? '-' }} ({{ $application->scheme->kode_skema ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Nama Asesi</td>
        <td>:</td>
        <td>{{ $application->user->name ?? '-' }} (NIK: {{ $application->user->asesiProfile->nik ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Nama Asesor</td>
        <td>:</td>
        <td>{{ $application->asesor->name ?? 'Tim Asesor LSP' }} (No. MET: {{ $application->asesor->asesorProfile->no_reg_met ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji Kompetensi</td>
        <td>:</td>
        <td>{{ $application->schedule->tuk->nama_tuk ?? 'TUK LSP' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hari / Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($application->schedule->tgl_asesmen)->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
</table>

<div style="border: 1px solid #333; padding: 10px; background-color: #fdfdfd; margin-bottom: 15px; font-size: 9pt;">
    <p style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Pernyataan Asesi:</p>
    <p style="text-align: justify; margin: 0 0 8px 0;">
        Saya menyatakan bahwa saya telah mendapatkan penjelasan lengkap mengenai tujuan, proses, bukti asesmen, kriteria penilaian, dan hak banding yang dimiliki. Saya menyetujui untuk mengikuti seluruh proses asesmen kompetensi ini dengan sungguh-sungguh dan jujur.
    </p>

    <p style="font-weight: bold; margin: 8px 0 6px 0;">Pernyataan Asesor:</p>
    <p style="text-align: justify; margin: 0;">
        Saya menyatakan bahwa saya akan melaksanakan asesmen secara objektif, adil, profesional, dan bebas dari benturan kepentingan. Saya menjamin kerahasiaan seluruh dokumen, data pribadi asesi, dan hasil asesmen sesuai dengan kode etik asesor dan regulasi BNSP.
    </p>
</div>

<table class="sign-table" style="margin-top: 25px;">
    <tr>
        <td>
            <div>Asesi,</div>
            <div style="font-weight: bold;">Pemberi Persetujuan</div>
            <div class="sign-space">
                @if(!empty($application->user->signature_image))
                    <img src="{{ $application->user->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->user->name ?? '-' }}</div>
            <div style="font-size: 8.5pt;">Tanggal: {{ \Carbon\Carbon::parse($application->schedule->tgl_asesmen)->format('d/m/Y') }}</div>
        </td>
        <td>
            <div>Asesor Kompetensi,</div>
            <div style="font-weight: bold;">Penjamin Kerahasiaan</div>
            <div class="sign-space">
                @if(!empty($application->asesor?->signature_image))
                    <img src="{{ $application->asesor->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->asesor->name ?? 'Asesor Kompetensi' }}</div>
            <div style="font-size: 8.5pt;">No. MET: {{ $application->asesor?->asesorProfile?->no_reg_met ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
