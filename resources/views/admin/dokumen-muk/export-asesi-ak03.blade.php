@extends('layouts.print-muk')

@section('title', 'FR.AK.03 Umpan Balik - ' . $asesi->full_name)
@section('doc-title', 'FR.AK.03 Umpan Balik dan Catatan Asesmen')

@section('content')
<div style="font-size: 9.5pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN</div>
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
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Tanggal Asesmen</td>
            <td colspan="3" style="padding-left: 6px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div style="font-size: 9pt; margin-bottom: 8px;">
        <em>Umpan balik dari Asesi (diisi oleh Asesi setelah pengambilan keputusan):</em>
    </div>

    {{-- Feedback Table --}}
    @php
        $defaultComponents = [
            1 => 'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi',
            2 => 'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya',
            3 => 'Asesor memberikan kesempatan untuk mendiskusikan/menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen',
            4 => 'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman yang saya miliki',
            5 => 'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen',
            6 => 'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen',
            7 => 'Asesor memberikan umpan balik yang mendukung setelah asesmen serta tindak lanjutnya',
            8 => 'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya',
            9 => 'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen',
            10 => 'Asesor menggunakan keterampilan komunikasi yang efektif selama asesmen',
        ];
        $ak03Record = $assessment->ak03Item;
        $ratings = $ak03Record?->ratings_json ?? [];
        $itemsByNumber = (isset($ak03Items) && $ak03Items instanceof \Illuminate\Support\Collection && $ak03Items->isNotEmpty()) ? $ak03Items->keyBy('component_number') : collect();
    @endphp

    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 58%;" rowspan="2">KOMPONEN</th>
                <th colspan="2" style="width: 16%;">Hasil</th>
                <th style="width: 26%;" rowspan="2">Catatan/Komentar Asesi</th>
            </tr>
            <tr style="font-size: 8pt;">
                <th style="width: 8%;">Ya</th>
                <th style="width: 8%;">Tidak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defaultComponents as $num => $compText)
                @php
                    $savedItem = $itemsByNumber->get($num);
                    if ($savedItem) {
                        $isYes = (bool) $savedItem->result;
                        $comment = $savedItem->comment ?? '';
                    } elseif (isset($ratings[$num])) {
                        $isYes = is_array($ratings[$num]) ? ($ratings[$num]['result'] ?? true) : (bool) $ratings[$num];
                        $comment = is_array($ratings[$num]) ? ($ratings[$num]['comment'] ?? '') : '';
                    } else {
                        $isYes = true;
                        $comment = '';
                    }
                @endphp
                <tr>
                    <td style="padding: 4px 6px;">
                        {{ $compText }}
                    </td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                        {!! $isYes ? '&radic;' : '' !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                        {!! !$isYes ? '&radic;' : '' !!}
                    </td>
                    <td style="padding: 4px 6px; font-size: 8pt; vertical-align: middle;">
                        {{ $comment }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Other Notes --}}
    <div style="border: 1px solid #000; padding: 6px; font-size: 9pt; margin-top: 6px; min-height: 40px;">
        <strong>Catatan/komentar lainnya (apabila ada) :</strong><br>
        <div style="margin-top: 4px; color: #1e293b;">
            {{ $assessment->ak03_notes ?? 'Pelaksanaan asesmen berjalan sangat baik, transparan, dan sesuai standar BNSP.' }}
        </div>
    </div>

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 20px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Asesi :</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                    <tr>
                        <td style="width: 32%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 64%;"><strong>{{ $asesi->full_name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px;">Tanda tangan &amp; Tanggal</td>
                        <td style="padding-top: 4px;">:</td>
                        <td style="padding-top: 4px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Asesor :</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                    <tr>
                        <td style="width: 32%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 64%;"><strong>{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>No. Reg</td>
                        <td>:</td>
                        <td>{{ $assessor?->profile?->met_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px;">Tanda tangan &amp; Tanggal</td>
                        <td style="padding-top: 4px;">:</td>
                        <td style="padding-top: 4px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
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
            </div>
        </div>
    </div>
</div>
@endsection
