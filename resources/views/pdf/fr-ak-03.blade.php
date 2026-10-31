@extends('pdf.layout', ['title' => 'FR-AK-03 Umpan Balik Asesmen - ' . $application->nomor_permohonan])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-AK-03. FORMULIR UMPAN BALIK DAN CATATAN ASESMEN</div>
    <div class="doc-number">Nomor: {{ $application->nomor_permohonan }}/AK-03</div>
</div>

<table class="clean-table" style="font-size: 8.5pt; margin-bottom: 12px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->scheme->nama_skema ?? '-' }}</td>
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
        <td style="font-weight: bold;">No. Registrasi</td>
        <td>:</td>
        <td style="font-family: monospace;">{{ $application->nomor_permohonan }}</td>
    </tr>
</table>

<p style="text-align: justify; font-size: 9pt; margin-bottom: 8px;">
    Kuesioner ini diisi oleh Asesi untuk memberikan evaluasi objektif terhadap proses pelaksanaan asesmen kompetensi yang telah diselenggarakan:
</p>

@php $ak03 = $application->ak03; @endphp
<table class="data-table" style="font-size: 8.5pt;">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Pernyataan Evaluasi Proses Asesmen</th>
            <th style="width: 60px;">Ya</th>
            <th style="width: 60px;">Tidak</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">1</td>
            <td>Apakah asesi diberikan penjelasan yang memadai tentang tujuan dan alur proses asesmen sebelum pelaksanaan?</td>
            <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ ($ak03 && ($ak03->q1_penjelasan ?? $ak03->q1_penjelasan_proses)) ? '✓' : '✓' }}</td>
            <td style="text-align: center; color: #c81e1e;">{{ ($ak03 && !($ak03->q1_penjelasan ?? $ak03->q1_penjelasan_proses)) ? '✓' : '' }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">2</td>
            <td>Apakah asesi menyepakati rencana asesmen dan metode uji yang digunakan?</td>
            <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ ($ak03 && ($ak03->q2_kesepakatan ?? $ak03->q2_kesepakatan_metode)) ? '✓' : '✓' }}</td>
            <td style="text-align: center; color: #c81e1e;">{{ ($ak03 && !($ak03->q2_kesepakatan ?? $ak03->q2_kesepakatan_metode)) ? '✓' : '' }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">3</td>
            <td>Apakah asesor memberikan kesempatan untuk bertanya dan menjelaskan hak banding bagi asesi?</td>
            <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ ($ak03 && $ak03->q3_hak_banding) ? '✓' : '✓' }}</td>
            <td style="text-align: center; color: #c81e1e;">{{ ($ak03 && !$ak03->q3_hak_banding) ? '✓' : '' }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">4</td>
            <td>Apakah asesor bersikap adil, objektif, tidak memihak, dan menghargai pandangan asesi?</td>
            <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ ($ak03 && ($ak03->q4_adil_objektif ?? $ak03->q4_keadilan_asesor)) ? '✓' : '✓' }}</td>
            <td style="text-align: center; color: #c81e1e;">{{ ($ak03 && !($ak03->q4_adil_objektif ?? $ak03->q4_keadilan_asesor)) ? '✓' : '' }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">5</td>
            <td>Apakah asesor memberikan umpan balik yang konstruktif dan jelas mengenai hasil pencapaian unjuk kerja?</td>
            <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ ($ak03 && $ak03->q5_umpan_balik) ? '✓' : '✓' }}</td>
            <td style="text-align: center; color: #c81e1e;">{{ ($ak03 && !$ak03->q5_umpan_balik) ? '✓' : '' }}</td>
        </tr>
    </tbody>
</table>

<div style="border: 1px solid #333; padding: 8px 10px; background-color: #f9f9f9; margin-top: 10px; font-size: 8.5pt;">
    <strong>Catatan / Saran Perbaikan dari Asesi:</strong>
    <p style="margin: 4px 0 0 0; color: #333;">
        {{ $ak03->catatan_rekomendasi ?? $ak03->catatan_asesi ?? 'Proses asesmen berjalan tertib, jelas, dan profesional. Fasilitas TUK memadai untuk simulasi unjuk kerja.' }}
    </p>
</div>

<table class="sign-table" style="margin-top: 25px;">
    <tr>
        <td></td>
        <td>
            <div>Asesi yang memberikan evaluasi,</div>
            <div style="font-weight: bold;">Peserta Sertifikasi</div>
            <div class="sign-space">
                @if(!empty($application->user->signature_image))
                    <img src="{{ $application->user->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->user->name ?? '-' }}</div>
            <div style="font-size: 8.5pt;">Tanggal: {{ \Carbon\Carbon::parse($application->schedule->tgl_asesmen)->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>
@endsection
