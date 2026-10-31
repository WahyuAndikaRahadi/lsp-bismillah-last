@extends('layouts.print-muk')

@section('title', 'FR.AK.01 Persetujuan Asesmen - ' . $asesi->full_name)
@section('doc-title', 'FR.AK.01 Persetujuan Asesmen dan Kerahasiaan')

@section('content')
<div style="font-size: 9.5pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN</div>
    </div>

    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 12px; text-align: center; color: #475569;">
        Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen
    </div>

    {{-- Meta Grid --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 12px;">
        <tr>
            <td class="label" style="border-right: 1px solid #000; width: 25%; font-weight: bold;" rowspan="2">
                Skema Sertifikasi<br><span style="font-size: 8.5pt; font-weight: normal;">(KKNI/Okupasi/Klaster)</span>
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
    </table>

    {{-- Evidence Checkbox Block --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 12px;">
        <tr>
            <td style="width: 25%; font-weight: bold; border-right: 1px solid #000; padding: 6px; vertical-align: middle;">
                Bukti yang akan dikumpulkan
            </td>
            <td style="padding: 6px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; font-size: 9pt;">
                    <div><span class="check-box">&radic;</span> Hasil Verifikasi Portofolio</div>
                    <div><span class="check-box">&nbsp;</span> Hasil Reviu Produk</div>
                    <div><span class="check-box">&radic;</span> Hasil Observasi Langsung</div>
                    <div><span class="check-box">&nbsp;</span> Hasil Kegiatan Terstruktur</div>
                    <div><span class="check-box">&radic;</span> Hasil Pertanyaan Lisan</div>
                    <div><span class="check-box">&radic;</span> Hasil Pertanyaan Tertulis</div>
                    <div><span class="check-box">&radic;</span> Hasil Pertanyaan Wawancara</div>
                    <div><span class="check-box">&nbsp;</span> Lainnya: ....................</div>
                </div>
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td style="font-weight: bold; border-right: 1px solid #000; padding: 6px; vertical-align: top;">
                Pelaksanaan asesmen disepakati pada:
            </td>
            <td style="padding: 6px;">
                <table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
                    <tr>
                        <td style="width: 22%;">Hari/ Tanggal</td>
                        <td style="width: 3%;">:</td>
                        <td><strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>:</td>
                        <td><strong>Pkl. {{ \Carbon\Carbon::parse($schedule->start_time)->format('H.i') }} s/d {{ \Carbon\Carbon::parse($schedule->end_time)->format('H.i') }}</strong></td>
                    </tr>
                    <tr>
                        <td>TUK</td>
                        <td>:</td>
                        <td><strong>{{ $schedule->tuk_name }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Statement clauses --}}
    <div style="border: 1px solid #000; padding: 8px 12px; margin-bottom: 15px; font-size: 8.5pt; line-height: 1.45;">
        <div style="margin-bottom: 8px;">
            <strong>Asesi :</strong><br>
            Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor.
        </div>
        <div style="margin-bottom: 8px;">
            <strong>Asesor :</strong><br>
            Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
        </div>
        <div>
            <strong>Asesi :</strong><br>
            Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
        </div>
    </div>

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 20px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-size: 9pt; font-weight: bold; margin-bottom: 4px;">Tanda tangan Asesi :</div>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 9pt;">
                    <strong>{{ $asesi->full_name }}</strong><br>
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-size: 9pt; font-weight: bold; margin-bottom: 4px;">Tanda tangan Asesor :</div>
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
                <div style="font-size: 9pt;">
                    <strong>{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</strong><br>
                    No. MET : {{ $assessor?->profile?->met_number ?? '-' }}<br>
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
