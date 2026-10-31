@extends('pdf.layout', ['title' => 'Surat Tugas Asesor - ' . ($scheduleAsesor->no_surat_tugas ?? $schedule->kode_jadwal)])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">SURAT PERINTAH TUGAS</div>
    <div class="doc-number">Nomor: {{ $scheduleAsesor->no_surat_tugas ?? '001/ST-LSP/' . date('m/Y') }}</div>
</div>

<p style="text-align: justify; margin-bottom: 12px; font-size: 9.5pt;">
    Dalam rangka pelaksanaan Asesmen dan Uji Kompetensi Kerja sesuai standar Badan Nasional Sertifikasi Profesi (BNSP), Direktur / Ketua Lembaga Sertifikasi Profesi dengan ini memberikan perintah tugas kepada:
</p>

<table class="clean-table" style="margin-left: 15px; margin-bottom: 12px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Nama Asesor</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $scheduleAsesor->asesor->name ?? '-' }}</td>
    </tr>
    <tr>
        <td>No. Registrasi MET</td>
        <td>:</td>
        <td style="font-family: monospace;">{{ $scheduleAsesor->asesor->asesorProfile->no_reg_met ?? '-' }}</td>
    </tr>
    <tr>
        <td>Peran Penugasan</td>
        <td>:</td>
        <td>{{ ucfirst($scheduleAsesor->peran ?? 'Penguji') }}</td>
    </tr>
    <tr>
        <td>Institusi / Asal</td>
        <td>:</td>
        <td>{{ $scheduleAsesor->asesor->asesorProfile->institusi_asal ?? 'LSP' }}</td>
    </tr>
</table>

<p style="text-align: justify; margin-bottom: 8px; font-size: 9.5pt;">
    Untuk melaksanakan tugas sebagai Asesor Kompetensi dalam asesmen sertifikasi dengan rincian kegiatan sebagai berikut:
</p>

<table class="clean-table" style="margin-left: 15px; margin-bottom: 15px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->scheme->nama_skema ?? '-' }} ({{ $schedule->scheme->kode_skema ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td>:</td>
        <td>{{ $schedule->tuk->nama_tuk ?? '-' }} - {{ $schedule->tuk->alamat ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hari / Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Waktu / Jam</td>
        <td>:</td>
        <td>{{ $schedule->jam_mulai ?? '08:00' }} s/d Selesai WIB</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Jenis Anggaran</td>
        <td>:</td>
        <td>{{ $schedule->jenis_anggaran ?? 'Mandiri' }}</td>
    </tr>
</table>

<p style="font-size: 9.5pt; font-weight: bold; margin-bottom: 4px;">Daftar Peserta Asesmen yang Ditugaskan:</p>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama Lengkap Peserta</th>
            <th>NIK</th>
            <th>Nomor Registrasi Uji</th>
            <th>Instansi / Asal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($applications as $idx => $app)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $app->user->asesiProfile->nik ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center;">{{ $app->nomor_permohonan }}</td>
                <td>{{ $app->user->asesiProfile->nama_institusi ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #777;">Belum ada peserta yang diplot untuk asesor ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p style="text-align: justify; margin-top: 10px; font-size: 9pt;">
    Demikian Surat Perintah Tugas ini dibuat untuk dilaksanakan dengan penuh rasa tanggung jawab serta mematuhi seluruh kode etik asesor dan regulasi BNSP yang berlaku.
</p>

<table class="sign-table">
    <tr>
        <td></td>
        <td>
            <div>Ditetapkan di: {{ $lsp->alamat ? explode(',', $lsp->alamat)[0] : 'Jakarta' }}</div>
            <div>Pada tanggal: {{ \Carbon\Carbon::parse($schedule->tgl_surat_tugas ?? now())->isoFormat('D MMMM Y') }}</div>
            <div style="margin-top: 6px; font-weight: bold;">Direktur / Ketua Lembaga Sertifikasi Profesi</div>
            <div class="sign-space">
                @if(!empty($lsp->ttd_ketua) && file_exists(public_path('storage/' . $lsp->ttd_ketua)))
                    <img src="{{ public_path('storage/' . $lsp->ttd_ketua) }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $lsp->nama_ketua ?? 'Ketua LSP' }}</div>
            <div style="font-size: 8.5pt;">Direktur Pelaksana</div>
        </td>
    </tr>
</table>
@endsection
