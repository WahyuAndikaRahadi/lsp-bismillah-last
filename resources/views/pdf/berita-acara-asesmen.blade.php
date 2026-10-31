@extends('pdf.layout', ['title' => 'Berita Acara Asesmen - ' . $schedule->kode_jadwal])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">BERITA ACARA PELAKSANAAN ASESMEN KOMPETENSI</div>
    <div class="doc-number">Nomor: BA-UJI/{{ $schedule->kode_jadwal }}/{{ date('Y') }}</div>
</div>

<p style="text-align: justify; margin-bottom: 12px; font-size: 9.5pt;">
    Pada hari ini <strong>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('dddd') }}</strong>, tanggal <strong>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('D MMMM Y') }}</strong> bertempat di Tempat Uji Kompetensi (TUK) <strong>{{ $schedule->tuk->nama_tuk ?? '-' }}</strong>, telah dilaksanakan Asesmen dan Uji Kompetensi Profesi dengan rincian sebagai berikut:
</p>

<table class="clean-table" style="margin-left: 15px; margin-bottom: 15px;">
    <tr>
        <td style="width: 160px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->scheme->nama_skema ?? '-' }} ({{ $schedule->scheme->kode_skema ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji Kompetensi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->tuk->nama_tuk ?? '-' }} ({{ $schedule->tuk->jenis_tuk ?? 'Sewaktu' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Alamat TUK</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->tuk->alamat ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Waktu Pelaksanaan</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->jam_mulai ?? '08:00' }} WIB s/d Selesai</td>
    </tr>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-bottom: 4px;">Rekapitulasi Pelaksanaan Asesmen:</p>
@php
    $total = $schedule->applications->count();
    $k = $schedule->applications->filter(fn($a) => $a->ak02 && $a->ak02->rekomendasi_akhir === 'K')->count();
    $bk = $schedule->applications->filter(fn($a) => $a->ak02 && $a->ak02->rekomendasi_akhir === 'BK')->count();
@endphp
<table class="data-table" style="width: 70%; margin-left: 15px;">
    <tr>
        <td style="width: 65%; font-weight: bold;">Total Peserta Terdaftar</td>
        <td style="text-align: center; font-weight: bold;">{{ $total }} Orang</td>
    </tr>
    <tr>
        <td>Peserta Hadir Mengikuti Uji</td>
        <td style="text-align: center; font-weight: bold;">{{ $total }} Orang</td>
    </tr>
    <tr>
        <td>Direkomendasikan Kompeten (K)</td>
        <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ $k }} Orang</td>
    </tr>
    <tr>
        <td>Direkomendasikan Belum Kompeten (BK)</td>
        <td style="text-align: center; font-weight: bold; color: #c81e1e;">{{ $bk }} Orang</td>
    </tr>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-top: 15px; margin-bottom: 4px;">Daftar Rekomendasi Hasil Peserta:</p>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama Lengkap Peserta</th>
            <th>NIK</th>
            <th>Asesor Penguji</th>
            <th style="width: 100px;">Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedule->applications as $idx => $app)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $app->user->asesiProfile->nik ?? '-' }}</td>
                <td>{{ $app->asesor->name ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">
                    @if ($app->ak02 && $app->ak02->rekomendasi_akhir === 'K')
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

<p style="text-align: justify; margin-top: 15px; font-size: 9pt;">
    Demikian Berita Acara Pelaksanaan Asesmen Kompetensi ini dibuat dengan sebenarnya sesuai dengan kenyataan dan ketentuan teknis pelaksanaan asesmen BNSP.
</p>

<table class="sign-table">
    <tr>
        <td>
            <div>Penanggung Jawab TUK</div>
            <div style="font-weight: bold;">{{ $schedule->tuk->nama_tuk ?? 'TUK' }}</div>
            <div class="sign-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $schedule->tuk->penanggung_jawab ?? 'Penanggung Jawab TUK' }}</div>
        </td>
        <td>
            <div>Asesor Kompetensi Penguji</div>
            <div style="font-weight: bold;">Tim Asesor LSP</div>
            <div class="sign-space">
                @php $firstAsesor = $schedule->scheduleAsesors->first()?->asesor; @endphp
                @if($firstAsesor && !empty($firstAsesor->signature_image))
                    <img src="{{ $firstAsesor->signature_image }}" class="sign-img" alt="TTD Asesor">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $firstAsesor->name ?? 'Asesor Kompetensi' }}</div>
            <div style="font-size: 8.5pt;">No. MET: {{ $firstAsesor->asesorProfile->no_reg_met ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
