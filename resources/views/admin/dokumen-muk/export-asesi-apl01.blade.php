@extends('layouts.print-muk')

@section('title', 'FR.APL.01 - ' . $asesi->full_name)
@section('doc-title', 'FR.APL.01 Permohonan Sertifikasi Kompetensi')

@section('content')
<div style="font-size: 9pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI</div>
    </div>

    {{-- Bagian 1: Data Pribadi --}}
    <div style="font-weight: bold; font-size: 9.5pt; margin-top: 10px; margin-bottom: 4px;">
        Bagian 1 : Rincian Data Pemohon Sertifikasi
    </div>
    <div style="font-size: 8pt; color: #475569; margin-bottom: 6px;">
        Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data pekerjaan anda pada saat ini.
    </div>

    <div style="font-weight: bold; margin-bottom: 4px;">a. Data Pribadi</div>
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 8px;">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama lengkap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%; font-weight: bold;">{{ $asesi->full_name }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">No. KTP/NIK</td>
            <td>:</td>
            <td style="font-family: monospace;">{{ $profile?->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Tempat / tgl. Lahir</td>
            <td>:</td>
            <td>{{ $profile?->birth_place ?? '-' }} / {{ $profile?->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jenis kelamin</td>
            <td>:</td>
            <td>{{ ($profile?->gender ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Kebangsaan</td>
            <td>:</td>
            <td>{{ $profile?->nationality ?? 'Indonesia' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat rumah</td>
            <td>:</td>
            <td>{{ $profile?->address ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">No. HP / E-mail</td>
            <td>:</td>
            <td>{{ $asesi->phone ?? $profile?->phone ?? '-' }} &nbsp;/&nbsp; {{ $asesi->email }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Kualifikasi Pendidikan</td>
            <td>:</td>
            <td>{{ $profile?->education_level ?? 'SMK / Sederajat' }}</td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-top: 8px; margin-bottom: 4px;">b. Data Pekerjaan Sekarang</div>
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 12px;">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Institusi / Perusahaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">{{ $profile?->company ?? $profile?->institution ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jabatan</td>
            <td>:</td>
            <td>{{ $profile?->job_title ?? $profile?->occupation ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat Kantor</td>
            <td>:</td>
            <td>{{ $profile?->company_address ?? '-' }}</td>
        </tr>
    </table>

    {{-- Bagian 2: Data Sertifikasi --}}
    <div style="font-weight: bold; font-size: 9.5pt; margin-top: 12px; margin-bottom: 4px;">
        Bagian 2 : Data Sertifikasi
    </div>
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 8px;">
        <tr>
            <td style="width: 25%; font-weight: bold;" rowspan="2">Skema Sertifikasi</td>
            <td style="width: 8%;">Judul</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->name }}</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->code }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td style="font-weight: bold;">Tujuan Asesmen</td>
            <td colspan="3" style="padding-left: 6px;">
                <span class="check-box">&radic;</span> Sertifikasi &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">&nbsp;</span> PKT &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">&nbsp;</span> RPL &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">&nbsp;</span> Lainnya
            </td>
        </tr>
    </table>

    {{-- Units Table --}}
    <div style="font-weight: bold; margin-bottom: 4px;">Daftar Unit Kompetensi sesuai kemasan:</div>
    <table class="bnsp-table" style="font-size: 8pt;">
        <thead>
            <tr>
                <th style="width: 6%;">No.</th>
                <th style="width: 25%;">Kode Unit</th>
                <th style="width: 45%;">Judul Unit</th>
                <th style="width: 24%;">Standar Kompetensi Kerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scheme->competencyUnits as $idx => $u)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $u->code }}</td>
                    <td>{{ $u->title }}</td>
                    <td style="font-size: 7.5pt; color: #475569;">SKKNI Otomotif</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Bagian 3: Bukti Kelengkapan --}}
    <div style="font-weight: bold; font-size: 9.5pt; margin-top: 12px; margin-bottom: 4px;" class="avoid-break">
        Bagian 3 : Bukti Kelengkapan Pemohon
    </div>
    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 6%;" rowspan="2">No.</th>
                <th style="width: 54%;" rowspan="2">Bukti Persyaratan Dasar</th>
                <th colspan="2" style="width: 20%;">Ada</th>
                <th style="width: 20%;" rowspan="2">Tidak Ada</th>
            </tr>
            <tr style="font-size: 7.5pt;">
                <th style="width: 10%;">Memenuhi Syarat</th>
                <th style="width: 10%;">Tidak Memenuhi Syarat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Fotocopy KTP / Identitas Diri Asesi</td>
                <td style="text-align: center; font-weight: bold;">&radic;</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Fotocopy Ijazah / Surat Keterangan Siswa SMK / Surat Pengalaman Kerja</td>
                <td style="text-align: center; font-weight: bold;">&radic;</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Pas Foto berwarna 3x4 latar belakang merah</td>
                <td style="text-align: center; font-weight: bold;">&radic;</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Recommendation & Signatures --}}
    <table class="meta-grid avoid-break" style="border: 1px solid #000; margin-top: 10px;">
        <tr>
            <td style="width: 50%; border-right: 1px solid #000; padding: 6px; vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 4px;">Rekomendasi Admin LSP:</div>
                <div style="font-size: 8.5pt;">
                    Pemohon <strong>DITERIMA / MEMENUHI SYARAT</strong> sebagai peserta sertifikasi kompetensi.
                </div>
            </td>
            <td style="width: 50%; padding: 6px; vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 4px;">Pemohon / Asesi:</div>
                <div style="font-size: 8.5pt;">
                    Menyatakan bahwa data dan dokumen yang saya sampaikan adalah sah dan benar.
                </div>
            </td>
        </tr>
    </table>

    <div class="sig-container avoid-break" style="margin-top: 15px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-size: 8.5pt; font-weight: bold;">Admin LSP :</div>
                <div class="sig-box">
                    <div style="height: 40px;"></div>
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-size: 8.5pt; font-weight: bold;">Tanda tangan Pemohon :</div>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 40px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    <strong>{{ $asesi->full_name }}</strong><br>
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
