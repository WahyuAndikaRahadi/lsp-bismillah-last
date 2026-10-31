@extends('pdf.layout', ['title' => 'FR-APL-01 Permohonan Sertifikasi - ' . $application->nomor_permohonan])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-APL-01. FORMULIR PERMOHONAN SERTIFIKASI KOMPETENSI</div>
    <div class="doc-number">Nomor Permohonan: {{ $application->nomor_permohonan }}</div>
</div>

<!-- Bagian 1: Data Pemohon -->
<div style="background-color: #f2f2f2; font-weight: bold; padding: 4px 6px; font-size: 9.5pt; border: 1px solid #333; margin-top: 10px;">
    Bagian 1 : Rincian Data Pemohon Sertifikasi
</div>

<p style="font-size: 9pt; font-weight: bold; margin: 6px 0 2px 0;">a. Data Pribadi</p>
<table class="clean-table" style="font-size: 8.5pt;">
    <tr>
        <td style="width: 140px;">Nama Lengkap</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->user->name ?? '-' }}</td>
        <td style="width: 130px;">No. KTP / NIK</td>
        <td style="width: 10px;">:</td>
        <td style="font-family: monospace;">{{ $profile->nik ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tempat / Tgl Lahir</td>
        <td>:</td>
        <td>{{ $profile->tempat_lahir ?? '-' }}, {{ $profile->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
        <td>Jenis Kelamin</td>
        <td>:</td>
        <td>{{ ($profile->jenis_kelamin ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
    </tr>
    <tr>
        <td>Kebangsaan</td>
        <td>:</td>
        <td>{{ $profile->kebangsaan ?? 'Indonesia' }}</td>
        <td>Pendidikan Terakhir</td>
        <td>:</td>
        <td>{{ $profile->pendidikan_terakhir ?? '-' }}</td>
    </tr>
    <tr>
        <td>Alamat Rumah</td>
        <td>:</td>
        <td colspan="4">{{ $profile->alamat_rumah ?? '-' }}</td>
    </tr>
    <tr>
        <td>No. Telepon / HP</td>
        <td>:</td>
        <td>{{ $profile->no_hp ?? '-' }}</td>
        <td>Email Pribadi</td>
        <td>:</td>
        <td>{{ $application->user->email ?? '-' }}</td>
    </tr>
</table>

<p style="font-size: 9pt; font-weight: bold; margin: 6px 0 2px 0;">b. Data Pekerjaan Sekarang</p>
<table class="clean-table" style="font-size: 8.5pt;">
    <tr>
        <td style="width: 140px;">Nama Lembaga / Perusahaan</td>
        <td style="width: 10px;">:</td>
        <td>{{ $profile->nama_institusi ?? '-' }}</td>
        <td style="width: 130px;">Jabatan / Posisi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $profile->jabatan ?? '-' }}</td>
    </tr>
    <tr>
        <td>Alamat Kantor</td>
        <td>:</td>
        <td colspan="4">{{ $profile->alamat_kantor ?? '-' }}</td>
    </tr>
</table>

<!-- Bagian 2: Data Sertifikasi -->
<div style="background-color: #f2f2f2; font-weight: bold; padding: 4px 6px; font-size: 9.5pt; border: 1px solid #333; margin-top: 10px;">
    Bagian 2 : Data Sertifikasi
</div>
<table class="clean-table" style="font-size: 8.5pt; margin-top: 4px;">
    <tr>
        <td style="width: 140px;">Judul Skema</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->scheme->nama_skema ?? '-' }}</td>
    </tr>
    <tr>
        <td>Nomor Skema</td>
        <td>:</td>
        <td style="font-family: monospace;">{{ $application->scheme->kode_skema ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tujuan Asesmen</td>
        <td>:</td>
        <td>[X] Sertifikasi Baru &nbsp;&nbsp; [ ] Sertifikasi Ulang (RCC) &nbsp;&nbsp; [ ] RPL</td>
    </tr>
</table>

<p style="font-size: 9pt; font-weight: bold; margin: 4px 0 2px 0;">Daftar Unit Kompetensi Kemasan:</p>
<table class="data-table" style="font-size: 8pt;">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th style="width: 120px;">Kode Unit</th>
            <th>Judul Unit Kompetensi</th>
            <th style="width: 80px;">Jenis Standar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($application->scheme->units ?? [] as $idx => $u)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-family: monospace;">{{ $u->kode_unit }}</td>
                <td>{{ $u->judul_unit }}</td>
                <td style="text-align: center;">{{ $u->standar ?? 'SKKNI' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Bagian 3: Bukti Kelengkapan -->
<div style="background-color: #f2f2f2; font-weight: bold; padding: 4px 6px; font-size: 9.5pt; border: 1px solid #333; margin-top: 8px;">
    Bagian 3 : Bukti Kelengkapan Pemohon
</div>

<table class="data-table" style="font-size: 8pt; margin-top: 4px;">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th>Dokumen Persyaratan Dasar</th>
            <th style="width: 80px;">Kelengkapan</th>
            <th style="width: 110px;">Verifikasi LSP</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($application->documents as $idx => $doc)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td>{{ $doc->requirement->nama_dokumen ?? $doc->nama_dokumen ?? 'Dokumen Pendukung' }}</td>
                <td style="text-align: center; color: #1e7e34; font-weight: bold;">Ada</td>
                <td style="text-align: center; font-weight: bold;">
                    @if ($doc->status === 'memenuhi')
                        <span style="color: #1e7e34;">Memenuhi</span>
                    @elseif ($doc->status === 'perbaikan')
                        <span style="color: #c81e1e;">Perbaikan</span>
                    @else
                        <span>Pending</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pengesahan -->
<table class="sign-table" style="margin-top: 15px;">
    <tr>
        <td>
            <div>Rekomendasi Verifikator:</div>
            <div style="font-weight: bold; margin-top: 2px;">
                @if ($application->status->value === 'diterima' || in_array($application->status->value, ['dijadwalkan', 'selesai']))
                    <span style="color: #1e7e34; border: 1px solid #1e7e34; padding: 2px 6px;">DITERIMA SEBAGAI PESERTA</span>
                @else
                    <span style="color: #c81e1e; border: 1px solid #c81e1e; padding: 2px 6px;">BELUM MEMENUHI SYARAT</span>
                @endif
            </div>
            <div style="margin-top: 6px;">Admin / Verifikator LSP</div>
            <div class="sign-space">
                @if(!empty($application->verifier?->signature_image))
                    <img src="{{ $application->verifier->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->verifier->name ?? 'Admin Verifikator' }}</div>
            <div style="font-size: 8.5pt;">Tanggal: {{ $application->tgl_verifikasi ? \Carbon\Carbon::parse($application->tgl_verifikasi)->format('d/m/Y') : date('d/m/Y') }}</div>
        </td>
        <td>
            <div>Pemohon Sertifikasi (Asesi),</div>
            <div style="font-weight: bold; margin-top: 2px;">Menyatakan data ini benar</div>
            <div class="sign-space" style="margin-top: 18px;">
                @if(!empty($application->user->signature_image))
                    <img src="{{ $application->user->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->user->name ?? '-' }}</div>
            <div style="font-size: 8.5pt;">Tanggal: {{ $application->created_at->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>
@endsection
