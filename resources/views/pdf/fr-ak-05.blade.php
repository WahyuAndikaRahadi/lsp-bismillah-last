@extends('pdf.layout', ['title' => 'FR-AK-05 Laporan Asesmen - ' . $schedule->kode_jadwal])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-AK-05. LAPORAN ASESMEN</div>
    <div class="doc-number">Nomor Jadwal: {{ $schedule->nama_jadwal }} ({{ $schedule->kode_jadwal }})</div>
</div>

<table class="clean-table" style="font-size: 8.5pt; margin-bottom: 10px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $schedule->scheme->nama_skema ?? '-' }} ({{ $schedule->scheme->kode_skema ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td>:</td>
        <td>{{ $schedule->tuk->nama_tuk ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tanggal Pelaksanaan</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
</table>

<p style="font-size: 9pt; font-weight: bold; margin-bottom: 4px;">1. Rekapitulasi Hasil Asesmen Peserta:</p>
<table class="data-table" style="font-size: 8pt;">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th>Nama Lengkap Peserta</th>
            <th>NIK</th>
            <th>Instansi</th>
            <th>Asesor Penguji</th>
            <th style="width: 90px;">Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedule->applications as $idx => $app)
            @php $isK = $app->ak02 && $app->ak02->rekomendasi_akhir === 'K'; @endphp
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $app->user->asesiProfile->nik ?? '-' }}</td>
                <td>{{ $app->user->asesiProfile->nama_institusi ?? '-' }}</td>
                <td>{{ $app->asesor->name ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">
                    @if ($isK)
                        <span class="badge badge-k">KOMPETEN</span>
                    @elseif ($app->ak02 && $app->ak02->rekomendasi_akhir === 'BK')
                        <span class="badge badge-bk">BELUM KOMPETEN</span>
                    @else
                        <span>-</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@php $report = $schedule->ak05Report; @endphp
<p style="font-size: 9pt; font-weight: bold; margin-top: 12px; margin-bottom: 4px;">2. Ringkasan Pelaksanaan Asesmen:</p>
<div style="border: 1px solid #333; padding: 8px 10px; background-color: #fdfdfd; font-size: 8.5pt;">
    <p style="margin: 0 0 4px 0;"><strong>a. Aspek Positif Pelaksanaan:</strong></p>
    <p style="margin: 0 0 8px 0; color: #444;">
        {{ $report->aspek_positif ?? 'Seluruh peserta antusias dan telah mempersiapkan portofolio bukti kerja dengan baik. Fasilitas perangkat TUK berfungsi normal.' }}
    </p>

    <p style="margin: 0 0 4px 0;"><strong>b. Kendala / Hambatan yang Dihadapi:</strong></p>
    <p style="margin: 0 0 8px 0; color: #444;">
        {{ $report->kendala_hambatan ?? $report->kendala_dihadapi ?? 'Tidak terdapat kendala teknis yang signifikan selama pelaksanaan uji berlangsung.' }}
    </p>

    <p style="margin: 0 0 4px 0;"><strong>c. Saran Perbaikan Instrumen Uji (Kaji Ulang MUK):</strong></p>
    <p style="margin: 0; color: #444;">
        {{ $report->saran_perbaikan ?? 'Direkomendasikan penambahan studi kasus baru pada materi pertanyaan tertulis untuk mengakomodasi perkembangan industri terkini.' }}
    </p>
</div>

<table class="sign-table" style="margin-top: 20px;">
    <tr>
        <td></td>
        <td>
            <div>Tim Asesor Kompetensi yang Melaporkan,</div>
            <div class="sign-space">
                @php $firstAsesor = $schedule->scheduleAsesors->first()?->asesor; @endphp
                @if($firstAsesor && !empty($firstAsesor->signature_image))
                    <img src="{{ $firstAsesor->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $firstAsesor->name ?? 'Tim Asesor LSP' }}</div>
            <div style="font-size: 8.5pt;">No. MET: {{ $firstAsesor->asesorProfile->no_reg_met ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
