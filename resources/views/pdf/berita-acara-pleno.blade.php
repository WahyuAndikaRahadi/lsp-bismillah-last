@extends('pdf.layout', ['title' => 'Berita Acara Pleno - ' . ($pleno->nomor_ba_pleno ?? $schedule->kode_jadwal)])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">BERITA ACARA RAPAT PLENO KOMITE TEKNIS</div>
    <div class="doc-number">Nomor: {{ $pleno->nomor_ba_pleno ?? 'BA-PLENO/' . date('Y') . '/001' }}</div>
</div>

<p style="text-align: justify; margin-bottom: 12px; font-size: 9.5pt;">
    Pada hari ini <strong>{{ \Carbon\Carbon::parse($pleno->tanggal_pleno ?? now())->isoFormat('dddd') }}</strong>, tanggal <strong>{{ \Carbon\Carbon::parse($pleno->tanggal_pleno ?? now())->isoFormat('D MMMM Y') }}</strong> bertempat di <strong>{{ $pleno->tempat ?? 'Kantor LSP' }}</strong>, telah dilaksanakan Rapat Pleno Komite Teknis Sertifikasi LSP guna memverifikasi dan menetapkan hasil rekomendasi asesmen kompetensi kerja:
</p>

<table class="clean-table" style="margin-left: 15px; margin-bottom: 12px;">
    <tr>
        <td style="width: 160px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->scheme->nama_skema ?? '-' }} ({{ $schedule->scheme->kode_skema ?? '-' }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Jadwal / Event</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->nama_jadwal }} ({{ $schedule->kode_jadwal }})</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td style="width: 10px;">:</td>
        <td>{{ $schedule->tuk->nama_tuk ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tanggal Asesmen</td>
        <td style="width: 10px;">:</td>
        <td>{{ \Carbon\Carbon::parse($schedule->tgl_asesmen)->isoFormat('D MMMM Y') }}</td>
    </tr>
</table>

<p style="text-align: justify; margin-bottom: 8px; font-size: 9.5pt;">
    Setelah menelaah berkas asesmen (FR-APL-01, FR-APL-02, FR-AK-01, FR-AK-02, FR-AK-03, dan FR-AK-05), Komite Teknis Sertifikasi memutuskan dan menetapkan status kelulusan peserta sebagai berikut:
</p>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;">No</th>
            <th>Nama Lengkap Peserta</th>
            <th>NIK</th>
            <th>Rekomendasi Asesor</th>
            <th style="width: 110px;">Keputusan Pleno</th>
            <th>Nomor Sertifikat BNSP</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedule->applications as $idx => $app)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? '-' }}</td>
                <td style="font-family: monospace; text-align: center; font-size: 8.5pt;">{{ $app->user->asesiProfile->nik ?? '-' }}</td>
                <td style="text-align: center;">
                    @if ($app->ak02 && $app->ak02->rekomendasi_akhir === 'K')
                        <span class="badge badge-k">KOMPETEN</span>
                    @elseif ($app->ak02 && $app->ak02->rekomendasi_akhir === 'BK')
                        <span class="badge badge-bk">BELUM KOMPETEN</span>
                    @else
                        <span>-</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold;">
                    @if ($app->status_kelulusan === 'kompeten')
                        <span class="badge badge-k">KOMPETEN (K)</span>
                    @elseif ($app->status_kelulusan === 'belum_kompeten')
                        <span class="badge badge-bk">BELUM KOMPETEN</span>
                    @else
                        <span style="color: #777;">-</span>
                    @endif
                </td>
                <td style="font-family: monospace; font-size: 8pt; text-align: center;">
                    {{ $app->no_sertifikat ?? '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if(!empty($pleno->catatan_pleno))
    <div style="margin-top: 10px; font-size: 9pt; background-color: #f9f9f9; padding: 6px 10px; border: 1px solid #ddd;">
        <strong>Catatan Pleno:</strong> {{ $pleno->catatan_pleno }}
    </div>
@endif

<p style="text-align: justify; margin-top: 12px; font-size: 9pt;">
    Bagi peserta yang dinyatakan <strong>KOMPETEN</strong>, diterbitkan Sertifikat Kompetensi Kerja Badan Nasional Sertifikasi Profesi (BNSP) dengan masa berlaku 3 (tiga) tahun terhitung sejak tanggal penetapan rapat pleno ini.
</p>

<table class="sign-table">
    <tr>
        <td>
            <div>Mengesahkan,</div>
            <div style="font-weight: bold;">Ketua Komite Teknis Sertifikasi</div>
            <div class="sign-space">
                @if(!empty($pleno->ttd_ketua))
                    <img src="{{ $pleno->ttd_ketua }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $pleno->ketua_komite ?? 'Ketua Komite Teknis' }}</div>
        </td>
        <td>
            <div>Mengetahui,</div>
            <div style="font-weight: bold;">Direktur / Ketua LSP</div>
            <div class="sign-space">
                @if(!empty($lsp->ttd_ketua) && file_exists(public_path('storage/' . $lsp->ttd_ketua)))
                    <img src="{{ public_path('storage/' . $lsp->ttd_ketua) }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $lsp->nama_ketua ?? 'Ketua LSP' }}</div>
        </td>
    </tr>
</table>
@endsection
