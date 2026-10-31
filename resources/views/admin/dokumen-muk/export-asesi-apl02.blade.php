@extends('layouts.print-muk')

@section('title', 'FR.APL.02 - ' . $asesi->full_name)
@section('doc-title', 'FR.APL.02 Asesmen Mandiri')

@section('content')
<div style="font-size: 9pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.APL.02. ASESMEN MANDIRI</div>
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
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Nama Asesi</td>
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $asesi->full_name }}</td>
        </tr>
    </table>

    <div style="background: #fafafa; border: 1px solid #cbd5e1; padding: 6px 10px; margin-bottom: 12px; font-size: 8.5pt;">
        <strong>PANDUAN ASESMEN MANDIRI:</strong>
        <ul style="margin: 3px 0 0 16px; padding: 0;">
            <li>Baca setiap pertanyaan di kolom sebelah kiri.</li>
            <li>Beri tanda centang (&radic;) pada kotak <strong>K</strong> jika Anda yakin dapat melakukan tugas yang dijelaskan.</li>
            <li>Isi kolom di sebelah kanan dengan menuliskan bukti relevan yang Anda miliki.</li>
        </ul>
    </div>

    {{-- Units Loop --}}
    @php
        $savedApl02 = $assessment->apl02Items->keyBy('kuk_id');
    @endphp

    @foreach($scheme->competencyUnits as $uIdx => $unit)
        <div style="margin-bottom: 12px;" class="avoid-break">
            <div style="background: #e2e8f0; border: 1px solid #000; border-bottom: none; padding: 4px 8px; font-weight: bold; font-size: 8.5pt;">
                Unit Kompetensi {{ $uIdx + 1 }} : {{ $unit->code }} - {{ $unit->title }}
            </div>

            <table class="bnsp-table" style="margin-top: 0; font-size: 8pt;">
                <thead>
                    <tr>
                        <th style="width: 60%;">Dapatkah Saya ................?</th>
                        <th style="width: 8%;">K</th>
                        <th style="width: 8%;">BK</th>
                        <th style="width: 24%;">Bukti yang relevan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unit->elements as $elem)
                        <tr style="background: #f8fafc; font-weight: bold;">
                            <td colspan="4" style="padding: 3px 6px;">
                                Elemen {{ $loop->iteration }}: {{ $elem->name }}
                            </td>
                        </tr>
                        @foreach($elem->kuks as $kuk)
                            @php
                                $item = $savedApl02->get($kuk->id);
                                $isCompetent = $item ? ($item->is_competent ?? true) : true;
                            @endphp
                            <tr>
                                <td style="padding: 3px 8px 3px 18px;">
                                    <strong>{{ $kuk->code }}</strong> {{ $kuk->statement }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                                    {!! $isCompetent ? '&radic;' : '' !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                                    {!! !$isCompetent ? '&radic;' : '' !!}
                                </td>
                                <td style="font-size: 7.5pt; color: #334155; vertical-align: middle; padding: 2px 6px;">
                                    Portofolio / Raport / Ijazah SMK
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Assessor Recommendation & Signatures --}}
    <table class="meta-grid avoid-break" style="border: 1px solid #000; margin-top: 15px;">
        <tr>
            <td style="width: 50%; border-right: 1px solid #000; padding: 6px; vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 4px;">Rekomendasi Asesor:</div>
                <div style="font-size: 8.5pt;">
                    Asesmen mandiri memenuhi syarat. Asesi direkomendasikan <strong>DAPAT DILANJUTKAN</strong> ke proses uji kompetensi (FR.AK.01 &amp; FR.IA).
                </div>
            </td>
            <td style="width: 50%; padding: 6px; vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 4px;">Pernyataan Asesi:</div>
                <div style="font-size: 8.5pt;">
                    Saya menyatakan bahwa seluruh isian asesmen mandiri di atas adalah benar sesuai kemampuan saya saat ini.
                </div>
            </td>
        </tr>
    </table>

    <div class="sig-container avoid-break" style="margin-top: 15px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-size: 8.5pt; font-weight: bold;">Asesi :</div>
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

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-size: 8.5pt; font-weight: bold;">Asesor :</div>
                <div class="sig-box">
                    @if($assessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $assessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessment->assessor_signature)
                        <img src="{{ $assessment->assessor_signature }}" class="sig-img" alt="TTD Asesor">
                    @else
                        <div style="height: 40px;"></div>
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
