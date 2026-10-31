@extends('pdf.layout', ['title' => 'Daftar Hadir Asesmen - ' . $schedule->kode_jadwal])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">DAFTAR HADIR ASESMEN KOMPETENSI</div>
    <div class="doc-number">Jadwal: {{ $schedule->nama_jadwal }} ({{ $schedule->kode_jadwal }})</div>
</div>

<table class="clean-table" style="margin-bottom: 12px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->scheme->nama_skema ?? '-' }}</td>
        <td style="width: 120px; font-weight: bold;">Hari / Tanggal</td>
        <td style="width: 10px;">:</td>
        <td>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td>:</td>
        <td>{{ $schedule->tuk->nama_tuk ?? '-' }}</td>
        <td style="font-weight: bold;">Waktu / Jam</td>
        <td>:</td>
        <td>{{ $schedule->jam_mulai ?? '08:00' }} WIB s/d Selesai</td>
    </tr>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-bottom: 4px;">1. Presensi Kehadiran Peserta Asesi:</p>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th>Nama Lengkap Peserta</th>
            <th>NIK</th>
            <th>Instansi</th>
            <th style="width: 90px;">Tanda Tangan (Pagi)</th>
            <th style="width: 90px;">Tanda Tangan (Siang)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedule->applications as $idx => $app)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center; font-size: 8.5pt;">{{ $app->user->asesiProfile->nik ?? '-' }}</td>
                <td style="font-size: 8.5pt;">{{ $app->user->asesiProfile->nama_institusi ?? '-' }}</td>
                <td style="text-align: center; height: 32px;">
                    @if(!empty($app->user->signature_image))
                        <img src="{{ $app->user->signature_image }}" style="max-height: 25px; max-width: 70px;" alt="TTD">
                    @endif
                </td>
                <td style="text-align: center; height: 32px;">
                    @if(!empty($app->user->signature_image))
                        <img src="{{ $app->user->signature_image }}" style="max-height: 25px; max-width: 70px;" alt="TTD">
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-top: 15px; margin-bottom: 4px;">2. Presensi Kehadiran Asesor Kompetensi:</p>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th>Nama Asesor</th>
            <th>No. Registrasi MET</th>
            <th>Peran</th>
            <th style="width: 120px;">Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedule->scheduleAsesors as $idx => $sa)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $sa->asesor->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $sa->asesor->asesorProfile->no_reg_met ?? '-' }}</td>
                <td style="text-align: center;">{{ ucfirst($sa->peran ?? 'Penguji') }}</td>
                <td style="text-align: center; height: 35px;">
                    @if(!empty($sa->asesor->signature_image))
                        <img src="{{ $sa->asesor->signature_image }}" style="max-height: 30px; max-width: 90px;" alt="TTD">
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="sign-table" style="margin-top: 20px;">
    <tr>
        <td></td>
        <td>
            <div>Mengetahui,</div>
            <div style="font-weight: bold;">Penanggung Jawab TUK</div>
            <div class="sign-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $schedule->tuk->penanggung_jawab ?? 'Penanggung Jawab TUK' }}</div>
        </td>
    </tr>
</table>
@endsection
