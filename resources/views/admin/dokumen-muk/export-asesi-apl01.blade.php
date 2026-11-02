@extends('layouts.print-muk')

@section('title', 'FR.APL.01 Permohonan Sertifikasi Kompetensi - ' . ($asesi->full_name ?? ''))

@section('content')
<style>
    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Calibri, 'Carlito', sans-serif;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        height: 297mm;
        box-sizing: border-box;
        padding: 5.0mm 15.8mm 10.0mm 32.1mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .page-number {
        position: absolute;
        bottom: 8.0mm;
        left: 32.1mm;
        font-size: 10pt;
    }
    .kop-table {
        width: 162.1mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 5.0mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 33.7mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 28mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 128.4mm;
        text-align: center;
        font-size: 11pt;
        line-height: 1.25;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.2;
        margin-bottom: 4.0mm;
    }
    .section-title {
        font-size: 10pt;
        font-weight: bold;
        margin-bottom: 2.0mm;
    }
    .section-desc {
        font-size: 9pt;
        line-height: 1.25;
        margin-bottom: 4.0mm;
    }
    .sub-section-title {
        font-size: 9.5pt;
        font-weight: bold;
        margin-bottom: 2.0mm;
    }
    .data-table {
        width: 162.1mm;
        border-collapse: collapse;
        font-size: 9pt;
        line-height: 1.35;
        margin-bottom: 4.0mm;
    }
    .data-table td {
        padding: 1.0mm 1.0mm;
        vertical-align: top;
    }
    .border-table {
        width: 162.1mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 8.5pt;
        line-height: 1.2;
    }
    .border-table td, .border-table th {
        border: 1pt solid #000;
        padding: 0.8mm 1.2mm;
        box-sizing: border-box;
        vertical-align: middle;
    }
    .border-table th {
        font-weight: bold;
        text-align: center;
    }
    .center-cell {
        text-align: center;
        vertical-align: middle;
    }
    .box-sym {
        font-family: 'MS Gothic', 'DejaVu Sans', sans-serif;
        font-size: 9pt;
    }
</style>

@php
    $schemeName = $scheme->name ?? 'Mekanik Junior';
    $schemeCode = $scheme->code ?? '05/LSP PrO Indo/I/2026';
    $asesiName = $asesi->full_name ?? ($asesi->name ?? '');
    $nik = $profile->nik ?? '';
    $birthPlace = $profile->birth_place ?? '';
    $birthDate = !empty($profile->birth_date) ? \Carbon\Carbon::parse($profile->birth_date)->translatedFormat('d F Y') : '';
    $gender = $profile->gender ?? '';
    $nationality = $profile->nationality ?? 'Indonesia';
    $address = $profile->address ?? '';
    $postalCode = $profile->postal_code ?? '';
    $phone = $asesi->phone ?? ($profile->phone ?? '');
    $email = $asesi->email ?? '';
    $education = $profile->education_level ?? 'SMK';
    $companyName = $profile->company ?? ($profile->institution ?? '');
    $jobTitle = $profile->job_title ?? ($profile->occupation ?? '');
    $companyAddress = $profile->company_address ?? '';
    $companyPostalCode = $profile->company_postal_code ?? '';
    $companyPhone = $profile->company_phone ?? '';
    $companyEmail = $profile->company_email ?? '';
    $units = $scheme->competencyUnits ?? collect();
@endphp

{{-- ========================================== PAGE 1 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <div class="doc-main-title">
        FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI
    </div>

    <div class="section-title">
        Bagian 1 : Rincian Data Pemohon Sertifikasi
    </div>
    <div class="section-desc">
        Pada bagian ini, cantumlan data pribadi, data pendidikan formal serta data pekerjaan anda pada saat ini.
    </div>

    <div class="sub-section-title">a. Data Pribadi</div>
    <table class="data-table">
        <colgroup>
            <col style="width: 48mm;">
            <col style="width: 4mm;">
            <col style="width: 110.1mm;">
        </colgroup>
        <tbody>
            <tr>
                <td>Nama lengkap</td>
                <td>:</td>
                <td>{{ $asesiName }}</td>
            </tr>
            <tr>
                <td>No. KTP/NIK/Paspor</td>
                <td>:</td>
                <td>{{ $nik }}</td>
            </tr>
            <tr>
                <td>Tempat / tgl. Lahir</td>
                <td>:</td>
                <td>{{ $birthPlace }}{{ $birthPlace && $birthDate ? ', ' : '' }}{{ $birthDate }}</td>
            </tr>
            <tr>
                <td>Jenis kelamin</td>
                <td>:</td>
                <td>{{ $gender == 'L' ? 'Laki-laki' : ($gender == 'P' ? 'Wanita' : 'Laki-laki / Wanita *)') }}</td>
            </tr>
            <tr>
                <td>Kebangsaan</td>
                <td>:</td>
                <td>{{ $nationality }}</td>
            </tr>
            <tr>
                <td>Alamat rumah</td>
                <td>:</td>
                <td>
                    {{ $address }}
                    <span style="float: right; margin-right: 5mm;">Kode pos : {{ $postalCode }}</span>
                </td>
            </tr>
            <tr>
                <td>No. Telepon/E-mail</td>
                <td>:</td>
                <td>
                    Rumah : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kantor : <br>
                    HP : {{ $phone }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; E-mail : {{ $email }}
                </td>
            </tr>
            <tr>
                <td>Kualifikasi Pendidikan</td>
                <td>:</td>
                <td>{{ $education }}</td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 8pt; font-style: italic; margin-bottom: 3.0mm;">*Coret yang tidak perlu</div>

    <div class="sub-section-title">b. Data Pekerjaan Sekarang</div>
    <table class="data-table">
        <colgroup>
            <col style="width: 48mm;">
            <col style="width: 4mm;">
            <col style="width: 110.1mm;">
        </colgroup>
        <tbody>
            <tr>
                <td>Nama Institusi / Perusahaan</td>
                <td>:</td>
                <td>{{ $companyName }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $jobTitle }}</td>
            </tr>
            <tr>
                <td>Alamat Kantor</td>
                <td>:</td>
                <td>
                    {{ $companyAddress }}
                    <span style="float: right; margin-right: 5mm;">Kode pos : {{ $companyPostalCode }}</span>
                </td>
            </tr>
            <tr>
                <td>No. Telp/Fax/E-mail</td>
                <td>:</td>
                <td>
                    Telp : {{ $companyPhone }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fax : <br>
                    E-mail : {{ $companyEmail }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-number">1</div>
</div>

{{-- ========================================== PAGE 2 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <div class="section-title">
        Bagian 2 : Data Sertifikasi
    </div>
    <div class="section-desc">
        Tuliskan Judul dan Nomor Skema Sertifikasi yang anda ajukan berikut Daftar Unit Kompetensi sesuai kemasan pada skema sertifikasi untuk mendapatkan pengakuan sesuai dengan latar belakang pendidikan, pelatihan serta pengalaman kerja yang anda miliki.
    </div>

    <table class="border-table" style="margin-bottom: 3.5mm;">
        <colgroup>
            <col style="width: 45.0mm;">
            <col style="width: 20.1mm;">
            <col style="width: 97.0mm;">
        </colgroup>
        <tbody>
            <tr>
                <td rowspan="2">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
                <td>Judul</td>
                <td>: &nbsp;{{ $schemeName }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>: &nbsp;{{ $schemeCode }}</td>
            </tr>
            <tr>
                <td colspan="2">Tujuan Asesmen</td>
                <td>
                    : &nbsp;<span class="box-sym">&#9744;</span> Sertifikasi<br>
                    &nbsp;&nbsp;<span class="box-sym">&#9744;</span> Pengakuan Kompetensi Terkini (PKT)<br>
                    &nbsp;&nbsp;<span class="box-sym">&#9744;</span> Rekognisi Pembelajaran Lampau (RPL)<br>
                    &nbsp;&nbsp;<span class="box-sym">&#9744;</span> Lainnya
                </td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 1.5mm;">Daftar Unit Kompetensi sesuai kemasan:</div>
    <table class="border-table">
        <colgroup>
            <col style="width: 11.9mm;">
            <col style="width: 35.7mm;">
            <col style="width: 82.3mm;">
            <col style="width: 31.3mm;">
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
                <th>Standar<br>Kompetensi Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $idx => $u)
                <tr>
                    <td class="center-cell">{{ $idx + 1 }}.</td>
                    <td>{{ $u->code }}</td>
                    <td>{{ $u->name }}</td>
                    @if($idx === 0)
                        <td rowspan="{{ count($units) }}" class="center-cell" style="vertical-align: middle;">
                            SKKNI Nomor 147<br>Tahun 2019
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="center-cell">1.</td>
                    <td>G.45TSM01.018.2</td>
                    <td>Melakukan Perawatan Karburator</td>
                    <td class="center-cell">SKKNI Nomor 147<br>Tahun 2019</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-number">2</div>
</div>

{{-- ========================================== PAGE 3 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <div class="section-title">
        Bagian 3 : Bukti Kelengkapan Pemohon
    </div>
    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 1.5mm;">
        3.1 Bukti Persyaratan Dasar Pemohon
    </div>

    <table class="border-table" style="margin-bottom: 4.0mm;">
        <colgroup>
            <col style="width: 10.5mm;">
            <col style="width: 78.7mm;">
            <col style="width: 22.5mm;">
            <col style="width: 25.0mm;">
            <col style="width: 24.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Bukti Persyaratan Dasar</th>
                <th colspan="2">Ada</th>
                <th rowspan="2">Tidak Ada</th>
            </tr>
            <tr>
                <th>Memenuhi<br>Syarat</th>
                <th>Tidak<br>Memenuhi<br>Syarat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center-cell" style="vertical-align: top;">1.</td>
                <td>Fotocopy Ijazah SMK otomotif; atau</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
            <tr>
                <td class="center-cell" style="vertical-align: top;">2.</td>
                <td>Surat Keterangan Peserta didik SMK Jurusan Otomotif yang telah menyelesaikan mata pelajaran bidang Engine Junior Technician sesuai unit kompetensi skema dengan dibuktikan nilai raport semester 1-4 dan fotocopy Surat Keterangan/Sertifikat mengikuti prakerin; atau</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
            <tr>
                <td class="center-cell" style="vertical-align: top;">3.</td>
                <td>Fotocopy Ijazah SMA sederajat dan fotocopy sertifikat pelatihan berbasis kompetensi Engine Junior Technician dari lembaga pelatihan kerja yang Terakreditasi/Kredibel; atau</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
            <tr>
                <td class="center-cell" style="vertical-align: top;">4.</td>
                <td>Surat keterangan pengalaman kerja sesuai bidang Engine Junior Technician minimal 1 tahun</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 1.5mm;">
        3.2 Bukti Administratif
    </div>
    <table class="border-table">
        <colgroup>
            <col style="width: 10.5mm;">
            <col style="width: 78.7mm;">
            <col style="width: 22.5mm;">
            <col style="width: 25.0mm;">
            <col style="width: 24.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Bukti Administratif</th>
                <th colspan="2">Ada</th>
                <th rowspan="2">Tidak Ada</th>
            </tr>
            <tr>
                <th>Memenuhi<br>Syarat</th>
                <th>Tidak<br>Memenuhi<br>Syarat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center-cell">1.</td>
                <td>Fotocopy Kartu Tanda Penduduk (KTP),</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
            <tr>
                <td class="center-cell">2.</td>
                <td>Pas photo ukuran 3x4 sebanyak 2 lembar dengan background warna merah.</td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
            <tr>
                <td class="center-cell">3.</td>
                <td></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="box-sym">&#9744;</span></td>
            </tr>
        </tbody>
    </table>

    <div class="page-number">3</div>
</div>

{{-- ========================================== PAGE 4 ========================================== --}}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ public_path('images/logo-lsp.png') }}" alt="Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="border-table" style="margin-top: 5.0mm;">
        <colgroup>
            <col style="width: 88.4mm;">
            <col style="width: 73.8mm;">
        </colgroup>
        <tbody>
            <tr>
                <td style="vertical-align: top; padding: 2.0mm 3.0mm; height: 35.0mm;">
                    <strong>Rekomendasi (diisi oleh LSP):</strong><br>
                    Berdasarkan ketentuan persyaratan dasar, maka pemohon:<br>
                    <strong>Diterima/ Tidak diterima *)</strong> sebagai peserta sertifikasi<br><br>
                    <span style="font-size: 8pt; font-style: italic;">* coret yang tidak sesuai</span>
                </td>
                <td style="vertical-align: top; padding: 2.0mm 3.0mm; height: 35.0mm;">
                    <strong>Pemohon/ Kandidat :</strong><br><br>
                    Nama : {{ $asesiName }}<br><br>
                    Tanda tangan/ Tanggal :
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding: 2.0mm 3.0mm; height: 35.0mm;">
                    <strong>Catatan :</strong>
                </td>
                <td style="vertical-align: top; padding: 2.0mm 3.0mm; height: 35.0mm;">
                    <strong>Admin LSP :</strong><br><br>
                    Nama :<br><br>
                    Tanda tangan/ Tanggal :
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-number">4</div>
</div>
@endsection
