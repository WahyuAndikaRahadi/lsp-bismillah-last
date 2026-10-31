@extends('pdf.layout', ['title' => 'Daftar Hadir Pleno - ' . ($pleno->nomor_ba_pleno ?? $schedule->kode_jadwal)])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">DAFTAR HADIR RAPAT PLENO KOMITE TEKNIS</div>
    <div class="doc-number">Nomor Rapat: {{ $pleno->nomor_ba_pleno ?? 'PLENO/' . date('Y') . '/001' }}</div>
</div>

<table class="clean-table" style="margin-bottom: 15px;">
    <tr>
        <td style="width: 150px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->scheme->nama_skema ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Jadwal Asesmen</td>
        <td>:</td>
        <td>{{ $schedule->nama_jadwal }} ({{ $schedule->kode_jadwal }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hari / Tanggal Sidang</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($pleno->tanggal_pleno ?? now())->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat / Ruang Sidang</td>
        <td>:</td>
        <td>{{ $pleno->tempat ?? 'Ruang Rapat Pleno LSP' }}</td>
    </tr>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-bottom: 4px;">Presensi Kehadiran Peserta Rapat Pleno:</p>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama Lengkap Peserta Rapat</th>
            <th>Jabatan / Unsur</th>
            <th style="width: 140px;">Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">1</td>
            <td style="font-weight: bold;">{{ $pleno->ketua_komite ?? 'Ketua Komite Teknis' }}</td>
            <td>Ketua Komite Teknis LSP</td>
            <td style="text-align: center; height: 40px;">
                @if(!empty($pleno->ttd_ketua))
                    <img src="{{ $pleno->ttd_ketua }}" style="max-height: 35px; max-width: 90px;" alt="TTD">
                @endif
            </td>
        </tr>
        @foreach ($schedule->scheduleAsesors as $idx => $sa)
            <tr>
                <td style="text-align: center;">{{ $idx + 2 }}</td>
                <td style="font-weight: bold;">{{ $sa->asesor->name ?? '-' }}</td>
                <td>Asesor Penguji (MET: {{ $sa->asesor->asesorProfile->no_reg_met ?? '-' }})</td>
                <td style="text-align: center; height: 40px;">
                    @if(!empty($sa->asesor->signature_image))
                        <img src="{{ $sa->asesor->signature_image }}" style="max-height: 35px; max-width: 90px;" alt="TTD">
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td style="text-align: center;">{{ $schedule->scheduleAsesors->count() + 2 }}</td>
            <td style="font-weight: bold;">{{ $lsp->nama_ketua ?? 'Direktur LSP' }}</td>
            <td>Pengarah / Direktur LSP</td>
            <td style="text-align: center; height: 40px;">
                @if(!empty($lsp->ttd_ketua) && file_exists(public_path('storage/' . $lsp->ttd_ketua)))
                    <img src="{{ public_path('storage/' . $lsp->ttd_ketua) }}" style="max-height: 35px; max-width: 90px;" alt="TTD">
                @endif
            </td>
        </tr>
    </tbody>
</table>

<table class="sign-table" style="margin-top: 30px;">
    <tr>
        <td></td>
        <td>
            <div>Disahkan oleh:</div>
            <div style="font-weight: bold;">Ketua Komite Teknis LSP</div>
            <div class="sign-space">
                @if(!empty($pleno->ttd_ketua))
                    <img src="{{ $pleno->ttd_ketua }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $pleno->ketua_komite ?? 'Ketua Komite Teknis' }}</div>
        </td>
    </tr>
</table>
@endsection
