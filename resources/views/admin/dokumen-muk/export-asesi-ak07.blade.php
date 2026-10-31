@extends('layouts.print-muk')

@section('title', 'FR.AK.07 Penyesuaian yang Wajar - ' . $asesi->full_name)
@section('doc-title', 'FR.AK.07 Ceklis Penyesuaian yang Wajar dan Beralasan')

@section('content')
<div style="font-size: 9pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.07. CEKLIS PENYESUAIAN YANG WAJAR DAN BERALASAN</div>
    </div>

    {{-- Meta Grid --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 10px;">
        <tr>
            <td class="label" style="border-right: 1px solid #000; width: 25%; font-weight: bold;" rowspan="2">
                Skema Sertifikasi<br><span style="font-size: 8pt; font-weight: normal;">(KKNI/Okupasi/Klaster)</span>
            </td>
            <td style="width: 12%; padding-left: 6px;">Judul</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->name }}</td>
        </tr>
        <tr>
            <td style="padding-left: 6px;">Nomor</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->code }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">TUK</td>
            <td colspan="3" style="padding-left: 6px;">Sewaktu/Tempat Kerja/Mandiri* (<strong>{{ $schedule->tuk_name }}</strong>)</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Nama Asesor</td>
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Nama Asesi</td>
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $asesi->full_name }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Tanggal</td>
            <td colspan="3" style="padding-left: 6px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- Potensi Asesi Block --}}
    <div style="border: 1px solid #000; padding: 6px; margin-bottom: 10px; font-size: 8.5pt;">
        <strong>Potensi Asesi:</strong>
        <div style="margin-top: 4px; line-height: 1.6;">
            <div><span class="check-box">&radic;</span> Hasil pelatihan dan/atau pendidikan, dimana kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi</div>
            <div><span class="check-box">&nbsp;</span> Hasil pelatihan dan/atau pendidikan, dimana kurikulum belum berbasis kompetensi.</div>
            <div><span class="check-box">&nbsp;</span> Pekerja berpengalaman, berasal dari industri yang operasionalnya mampu telusur dengan standar kompetensi.</div>
            <div><span class="check-box">&nbsp;</span> Pelatihan / belajar mandiri atau otodidak.</div>
        </div>
    </div>

    {{-- Adjustment Checklist Table --}}
    @php
        $ak07 = $assessment->ak07Items->first();
        $hasAdjustment = !empty($ak07);
    @endphp
    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi (Karakteristik Asesi)</th>
                <th style="width: 15%;">Diperlukan Penyesuaian (Ya/Tidak)</th>
                <th style="width: 35%;">Keterangan &amp; Bentuk Penyesuaian</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; vertical-align: top;">1</td>
                <td style="vertical-align: top; padding: 4px 6px;">
                    <strong>Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi</strong>
                </td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                    Tidak
                </td>
                <td style="font-size: 8pt; vertical-align: top; padding: 4px 6px;">
                    Asesi mampu membaca manual book dan instruksi kerja dengan baik dalam bahasa Indonesia/Inggris teknis.
                </td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: top;">2</td>
                <td style="vertical-align: top; padding: 4px 6px;">
                    <strong>Penyediaan dukungan pembaca, penerjemah, pendamping teknis</strong>
                </td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                    Tidak
                </td>
                <td style="font-size: 8pt; vertical-align: top; padding: 4px 6px;">
                    Tidak memerlukan pendamping khusus.
                </td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: top;">3</td>
                <td style="vertical-align: top; padding: 4px 6px;">
                    <strong>Penyesuaian waktu pelaksanaan uji (kondisi keletihan, istirahat, atau lingkungan TUK)</strong>
                </td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                    {{ $hasAdjustment ? 'Ya' : 'Tidak' }}
                </td>
                <td style="font-size: 8pt; vertical-align: top; padding: 4px 6px;">
                    {{ $hasAdjustment ? ($ak07->adjustment_detail ?? 'Penyesuaian durasi kerja praktek diatur sesuai kondisi bengkel TUK.') : 'Waktu pelaksanaan normal sesuai jadwal.' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 15px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Asesi :</div>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    <strong>{{ $asesi->full_name }}</strong><br>
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Asesor :</div>
                <div class="sig-box">
                    @if($assessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $assessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessment->assessor_signature)
                        <img src="{{ $assessment->assessor_signature }}" class="sig-img" alt="TTD Asesor">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    <strong>{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</strong><br>
                    No. MET : {{ $assessor?->profile?->met_number ?? '-' }}<br>
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
