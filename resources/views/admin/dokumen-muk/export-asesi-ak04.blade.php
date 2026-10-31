@extends('layouts.print-muk')

@section('title', 'FR.AK.04 Banding Asesmen - ' . $asesi->full_name)
@section('doc-title', 'FR.AK.04 Banding Asesmen')

@section('content')
<div style="font-size: 9.5pt; line-height: 1.4;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.04. BANDING ASESMEN</div>
    </div>

    {{-- Meta Grid --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 15px;">
        <tr>
            <td class="label" style="border-right: 1px solid #000; width: 25%; font-weight: bold;">Nama Asesi</td>
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $asesi->full_name }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Nama Asesor</td>
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Tanggal Asesmen</td>
            <td colspan="3" style="padding-left: 6px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div style="font-size: 9pt; margin-bottom: 8px;">
        <strong>Jawablah dengan Ya atau Tidak pertanyaan-pertanyaan berikut ini :</strong>
    </div>

    @php
        $latestAppeal = $appeals->first();
    @endphp

    <table class="bnsp-table" style="font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 74%;">Pertanyaan</th>
                <th style="width: 13%;">YA</th>
                <th style="width: 13%;">TIDAK</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 6px 8px;">Apakah Proses Banding telah dijelaskan kepada Anda?</td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">&radic;</td>
                <td style="text-align: center; vertical-align: middle;"></td>
            </tr>
            <tr>
                <td style="padding: 6px 8px;">Apakah Anda telah mendiskusikan Banding dengan Asesor?</td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">&radic;</td>
                <td style="text-align: center; vertical-align: middle;"></td>
            </tr>
            <tr>
                <td style="padding: 6px 8px;">Apakah Anda mau melibatkan "orang lain" membantu Anda dalam Proses Banding?</td>
                <td style="text-align: center; vertical-align: middle;"></td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">&radic;</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 15px; margin-bottom: 8px;">
        Banding ini diajukan atas Keputusan Asesmen yang dibuat terhadap Skema Sertifikasi (Kualifikasi/Klaster/Okupasi) berikut :
    </div>

    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 15px;">
        <tr>
            <td class="label" style="width: 25%; font-weight: bold; border-right: 1px solid #000;">Skema Sertifikasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->name }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="font-weight: bold; border-right: 1px solid #000;">No. Skema Sertifikasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $scheme->code }}</td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-bottom: 4px;">
        Banding ini diajukan atas alasan sebagai berikut :
    </div>
    <div style="border: 1px solid #000; padding: 8px; min-height: 50px; margin-bottom: 12px; font-size: 9.5pt;">
        {{ $latestAppeal?->appeal_reasons ?? $latestAppeal?->reason ?? 'Asesi menyatakan mampu mendemonstrasikan unit kompetensi dan memohon peninjauan ulang atas hasil uji.' }}
    </div>

    @if($latestAppeal && $latestAppeal->contested_units)
        <div style="font-weight: bold; margin-bottom: 4px;">
            Unit kompetensi yang dipersoalkan / dibanding :
        </div>
        <div style="border: 1px solid #000; padding: 6px; margin-bottom: 12px; font-size: 9pt; background: #fafafa;">
            @foreach($latestAppeal->contested_units as $unitCode)
                <div>&bull; {{ $unitCode }}</div>
            @endforeach
        </div>
    @endif

    <div style="font-size: 9pt; font-style: italic; margin-bottom: 20px;">
        Anda mempunyai hak mengajukan banding jika Anda menilai Proses Asesmen tidak sesuai SOP dan tidak memenuhi Prinsip Asesmen.
    </div>

    {{-- Decision Block by Admin / Technical Committee --}}
    @if($latestAppeal && ($latestAppeal->decision || in_array($latestAppeal->status, ['ACCEPTED', 'REJECTED'])))
        <div style="border: 1px solid #000; padding: 8px; margin-bottom: 20px; background-color: #f8fafc;">
            <div style="font-weight: bold; text-decoration: underline; margin-bottom: 6px;">KEPUTUSAN BANDING OLEH ADMIN LSP / TIM BANDING:</div>
            <table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
                <tr>
                    <td style="width: 25%;">Status Keputusan</td>
                    <td style="width: 3%;">:</td>
                    <td style="width: 72%;"><strong>{{ in_array($latestAppeal->status, ['ACCEPTED', 'UPHELD']) || $latestAppeal->decision === 'UPHELD' ? 'DITERIMA (Hasil Asesmen Diubah/Diuji Ulang)' : 'DITOLAK (Keputusan Asesor Tetap)' }}</strong></td>
                </tr>
                @if($latestAppeal->appeals_committee_chair)
                    <tr>
                        <td>Ketua Tim Banding</td>
                        <td>:</td>
                        <td><strong>{{ $latestAppeal->appeals_committee_chair }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td style="vertical-align: top; padding-top: 4px;">Catatan Pertimbangan</td>
                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                    <td style="padding-top: 4px;">{{ $latestAppeal->decision_notes ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Putusan</td>
                    <td>:</td>
                    <td>{{ $latestAppeal->decision_date ? \Carbon\Carbon::parse($latestAppeal->decision_date)->translatedFormat('d F Y') : ($latestAppeal->decided_at ? \Carbon\Carbon::parse($latestAppeal->decided_at)->translatedFormat('d F Y') : '-') }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 25px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 6px;">Asesi :</div>
                <div style="font-size: 9pt;">
                    Tanda tangan Asesi :
                </div>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 9pt; margin-top: 4px;">
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 6px;">
                    {{ $latestAppeal?->appeals_committee_chair ? 'Ketua Tim Banding :' : 'Admin LSP / Tim Banding :' }}
                </div>
                <div style="font-size: 9pt;">
                    Tanda tangan :
                </div>
                <div class="sig-box">
                    <div style="height: 45px;"></div>
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 9pt; margin-top: 4px;">
                    @if($latestAppeal?->appeals_committee_chair)
                        <strong>{{ $latestAppeal->appeals_committee_chair }}</strong><br>
                    @endif
                    Tanggal : {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
