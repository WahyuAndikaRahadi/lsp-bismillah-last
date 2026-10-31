@extends('pdf.layout', ['title' => 'FR-AK-02 Rekaman Asesmen - ' . $application->nomor_permohonan])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-AK-02. FORMULIR REKAMAN ASESMEN KOMPETENSI</div>
    <div class="doc-number">Nomor: {{ $application->nomor_permohonan }}/AK-02</div>
</div>

<table class="clean-table" style="font-size: 8.5pt; margin-bottom: 8px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->scheme->nama_skema ?? '-' }} ({{ $application->scheme->kode_skema ?? '-' }})</td>
        <td style="width: 120px; font-weight: bold;">Nama Asesi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->user->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td>:</td>
        <td>{{ $application->schedule->tuk->nama_tuk ?? 'TUK LSP' }}</td>
        <td style="font-weight: bold;">Nama Asesor</td>
        <td>:</td>
        <td>{{ $application->asesor->name ?? 'Tim Asesor LSP' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hari / Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($application->schedule->tgl_asesmen)->isoFormat('dddd, D MMMM Y') }}</td>
        <td style="font-weight: bold;">No. Registrasi Asesi</td>
        <td>:</td>
        <td style="font-family: monospace;">{{ $application->nomor_permohonan }}</td>
    </tr>
</table>

<p style="font-size: 9pt; font-weight: bold; margin-bottom: 4px;">Matriks Penilaian per Unit Kompetensi:</p>
<table class="data-table" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2" style="width: 25px;">No</th>
            <th rowspan="2" style="width: 110px;">Kode Unit</th>
            <th rowspan="2">Judul Unit Kompetensi</th>
            <th colspan="3" style="width: 100px;">Metode Asesmen</th>
            <th rowspan="2" style="width: 80px;">Rekomendasi</th>
        </tr>
        <tr>
            <th style="width: 33px;">L</th>
            <th style="width: 33px;">TL</th>
            <th style="width: 33px;">T</th>
        </tr>
    </thead>
    <tbody>
        @php $ak02 = $application->ak02; @endphp
        @foreach ($application->scheme->units ?? [] as $idx => $u)
            @php
                $uAss = $ak02 ? $ak02->unitAssessments->firstWhere('unit_id', $u->id) : null;
                $isK = $uAss && ($uAss->rekomendasi_unit ?? $uAss->rekomendasi) === 'K';
            @endphp
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-family: monospace;">{{ $u->kode_unit }}</td>
                <td>{{ $u->judul_unit }}</td>
                <td style="text-align: center; font-weight: bold;">{{ ($uAss && ($uAss->metode_observasi_langsung ?? $uAss->metode_l)) ? '✓' : '' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ ($uAss && ($uAss->metode_portofolio ?? $uAss->metode_tl)) ? '✓' : '' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ ($uAss && ($uAss->metode_pertanyaan ?? $uAss->metode_t)) ? '✓' : '' }}</td>
                <td style="text-align: center; font-weight: bold;">
                    @if ($isK)
                        <span class="badge badge-k">K</span>
                    @elseif ($uAss)
                        <span class="badge badge-bk">BK</span>
                    @else
                        <span>K</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="font-size: 7.5pt; color: #555; margin-top: 2px; margin-bottom: 10px;">
    * Keterangan Metode: <strong>L</strong> = Bukti Langsung (Observasi/Demonstrasi), <strong>TL</strong> = Bukti Tidak Langsung (Verifikasi Portofolio), <strong>T</strong> = Bukti Tambahan (Pertanyaan Lisan/Tertulis).
</div>

<!-- Rekomendasi Akhir -->
<div style="border: 1.5px solid #333; padding: 8px 12px; background-color: #fdfdfd; margin-bottom: 12px;">
    <div style="font-size: 9.5pt; font-weight: bold; margin-bottom: 4px;">Rekomendasi Akhir Asesor:</div>
    <div style="font-size: 10pt; margin-bottom: 6px;">
        Peserta dinyatakan: 
        @if ($ak02 && $ak02->rekomendasi_akhir === 'K')
            <strong style="color: #1e7e34; border: 1.5px solid #1e7e34; padding: 2px 8px; border-radius: 4px;">KOMPETEN (K)</strong>
        @else
            <strong style="color: #c81e1e; border: 1.5px solid #c81e1e; padding: 2px 8px; border-radius: 4px;">BELUM KOMPETEN (BK)</strong>
        @endif
    </div>
    <div style="font-size: 8.5pt; color: #333;">
        <strong>Tindak Lanjut yang Dibutuhkan:</strong> {{ $ak02->tindak_lanjut ?? 'Diusulkan untuk penerbitan sertifikat kompetensi kerja oleh BNSP.' }}
    </div>
    <div style="font-size: 8.5pt; color: #333; margin-top: 3px;">
        <strong>Komentar / Catatan Asesor:</strong> {{ $ak02->komentar_asesor ?? $ak02->catatan_asesor ?? 'Peserta telah mendemonstrasikan seluruh kompetensi sesuai standar unjuk kerja.' }}
    </div>
</div>

<table class="sign-table" style="margin-top: 15px;">
    <tr>
        <td>
            <div>Asesi,</div>
            <div style="font-weight: bold;">Menerima Hasil Rekomendasi</div>
            <div class="sign-space">
                @if(!empty($application->user->signature_image))
                    <img src="{{ $application->user->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->user->name ?? '-' }}</div>
            <div style="font-size: 8pt;">Tanggal: {{ \Carbon\Carbon::parse($application->schedule->tgl_asesmen)->format('d/m/Y') }}</div>
        </td>
        <td>
            <div>Asesor Kompetensi,</div>
            <div style="font-weight: bold;">Penguji Utama</div>
            <div class="sign-space">
                @if(!empty($application->asesor?->signature_image))
                    <img src="{{ $application->asesor->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->asesor->name ?? 'Asesor Kompetensi' }}</div>
            <div style="font-size: 8pt;">No. MET: {{ $application->asesor?->asesorProfile?->no_reg_met ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
