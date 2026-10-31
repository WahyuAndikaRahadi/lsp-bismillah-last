@extends('layouts.print-muk')

@section('title', 'FR.AK.02 Rekaman Asesmen - ' . $asesi->full_name)
@section('doc-title', 'FR.AK.02 Rekaman Asesmen Kompetensi')

@section('content')
<div style="font-size: 8.5pt; line-height: 1.25;">
    {{-- Header Kop --}}
    <div class="header-kop" style="margin-bottom: 8px;">
        <div class="title-lsp" style="font-size: 10pt;">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code" style="font-size: 10pt;">FR.AK.02. REKAMAN ASESMEN KOMPETENSI</div>
    </div>

    {{-- Meta Grid --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-bottom: 8px; font-size: 8.5pt;">
        <tr>
            <td class="label" style="border-right: 1px solid #000; width: 25%; font-weight: bold; padding: 2px 4px;" rowspan="2">
                Skema Sertifikasi<br><span style="font-size: 7.5pt; font-weight: normal;">(KKNI/Okupasi/Klaster)</span>
            </td>
            <td style="width: 12%; padding: 2px 4px;">Judul</td>
            <td class="colon" style="padding: 2px 2px;">:</td>
            <td class="value" style="padding: 2px 4px;">{{ $scheme->name }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 4px;">Nomor</td>
            <td class="colon" style="padding: 2px 2px;">:</td>
            <td class="value" style="padding: 2px 4px;">{{ $scheme->code }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold; padding: 2px 4px;">TUK</td>
            <td colspan="3" style="padding: 2px 4px;">Sewaktu/Tempat Kerja/Mandiri* (<strong>{{ $schedule->tuk_name }}</strong>)</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold; padding: 2px 4px;">Nama Asesor</td>
            <td colspan="3" style="padding: 2px 4px; font-weight: bold;">{{ $assessor?->full_name ?? $assessor?->name ?? '-' }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold; padding: 2px 4px;">Nama Asesi</td>
            <td colspan="3" style="padding: 2px 4px; font-weight: bold;">{{ $asesi->full_name }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold; padding: 2px 4px;" rowspan="2">Tanggal Asesmen</td>
            <td style="padding: 2px 4px;">Mulai</td>
            <td class="colon" style="padding: 2px 2px;">:</td>
            <td style="padding: 2px 4px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 4px;">Selesai</td>
            <td class="colon" style="padding: 2px 2px;">:</td>
            <td style="padding: 2px 4px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div style="font-size: 8pt; margin-bottom: 6px;">
        Beri tanda centang (&radic;) di kolom yang sesuai untuk mencerminkan bukti yang diperoleh untuk menentukan Kompetensi Asesi untuk setiap Unit Kompetensi.
    </div>

    {{-- Matrix Table --}}
    <table class="bnsp-table" style="font-size: 7.5pt; width: 100%;">
        <thead>
            <tr>
                <th style="width: 36%; padding: 3px;" rowspan="2">Unit Kompetensi</th>
                <th colspan="8" style="padding: 3px;">Bukti yang Diperoleh</th>
            </tr>
            <tr style="font-size: 7pt;">
                <th style="width: 8%; padding: 2px;">Observasi Demonstrasi</th>
                <th style="width: 8%; padding: 2px;">Portofolio</th>
                <th style="width: 8%; padding: 2px;">Pernyataan Pihak Ketiga</th>
                <th style="width: 8%; padding: 2px;">Pertanyaan Wawancara</th>
                <th style="width: 8%; padding: 2px;">Pertanyaan Lisan</th>
                <th style="width: 8%; padding: 2px;">Pertanyaan Tertulis</th>
                <th style="width: 8%; padding: 2px;">Proyek Kerja</th>
                <th style="width: 8%; padding: 2px;">Lainnya</th>
            </tr>
        </thead>
        <tbody>
            @php
                $ak02Indexed = $ak02Items->keyBy('competency_unit_id');
                $units = $scheme->competencyUnits;
            @endphp
            @forelse($units as $unit)
                @php
                    $item = $ak02Indexed->get($unit->id);
                    $isObs = $item ? $item->is_observasi : true;
                    $isPorto = $item ? $item->is_portofolio : false;
                    $isPihakKetiga = $item ? $item->is_pihak_ketiga : false;
                    $isWawancara = $item ? $item->is_lisan : false;
                    $isLisan = $item ? $item->is_lisan : false;
                    $isTulis = $item ? $item->is_tulis : true;
                    $isProyek = $item ? $item->is_proyek : false;
                    $isLainnya = $item ? $item->is_lainnya : false;
                @endphp
                <tr>
                    <td style="padding: 2px 4px;">
                        <span style="font-weight: bold; font-size: 7.5pt; color: #1e293b;">{{ $unit->code }}</span>
                        <div style="font-size: 7pt; line-height: 1.15;">{{ $unit->title }}</div>
                    </td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isObs ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isPorto ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isPihakKetiga ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isWawancara ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isLisan ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isTulis ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isProyek ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; padding: 1px;">{!! $isLainnya ? '&radic;' : '' !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 8px;">Unit kompetensi belum diatur pada skema ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Result Section --}}
    <table class="meta-grid" style="border: 1px solid #000; margin-top: 8px; font-size: 8.5pt;">
        <tr>
            <td style="width: 32%; border-right: 1px solid #000; font-weight: bold; padding: 4px 6px;">
                Rekomendasi hasil asesmen
            </td>
            <td colspan="2" style="padding: 4px 6px; font-size: 9pt;">
                @php
                    $isK = in_array($assessment->final_result, ['K', 'KOMPETEN']) || ($assessment->final_result === null && $assessment->status === 'COMPLETED');
                    $isBK = in_array($assessment->final_result, ['BK', 'BELUM_KOMPETEN']);
                @endphp
                <span class="check-box">{!! $isK ? '&radic;' : '&nbsp;' !!}</span> <strong>Kompeten</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">{!! $isBK ? '&radic;' : '&nbsp;' !!}</span> <strong>Belum kompeten</strong>
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td style="border-right: 1px solid #000; font-weight: bold; padding: 4px 6px; vertical-align: top;">
                Tindak lanjut yang dibutuhkan<br>
                <span style="font-size: 7.5pt; font-weight: normal; color: #475569;">
                    (Masukkan pekerjaan tambahan dan asesmen yang diperlukan untuk mencapai kompetensi)
                </span>
            </td>
            <td colspan="2" style="padding: 4px 6px; min-height: 28px; vertical-align: top; font-size: 8pt;">
                {{ $assessment->assessor_follow_up ?? ($isK ? 'Tidak ada tindak lanjut, asesi direkomendasikan menerima sertifikat kompetensi.' : 'Perlu dilakukan uji ulang pada unit yang belum kompeten.') }}
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td style="border-right: 1px solid #000; font-weight: bold; padding: 4px 6px; vertical-align: top;">
                Komentar/ Observasi oleh asesor
            </td>
            <td colspan="2" style="padding: 4px 6px; min-height: 28px; vertical-align: top; font-size: 8pt;">
                {{ $assessment->assessor_notes ?? 'Asesi mendemonstrasikan seluruh instruksi kerja dengan baik sesuai SOP.' }}
            </td>
        </tr>
    </table>

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 10px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 2px;">Asesi :</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
                    <tr>
                        <td style="width: 32%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 64%;"><strong>{{ $asesi->full_name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 2px;">Tanda tangan &amp; Tanggal</td>
                        <td style="padding-top: 2px;">:</td>
                        <td style="padding-top: 2px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
                <div class="sig-box">
                    @if($assessment->asesi_signature)
                        <img src="{{ $assessment->asesi_signature }}" class="sig-img" alt="TTD Asesi">
                    @else
                        <div style="height: 35px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 2px;">Asesor :</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
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
                        <td style="padding-top: 2px;">Tanda tangan &amp; Tanggal</td>
                        <td style="padding-top: 2px;">:</td>
                        <td style="padding-top: 2px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
                <div class="sig-box">
                    @if($assessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $assessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessment->assessor_signature)
                        <img src="{{ $assessment->assessor_signature }}" class="sig-img" alt="TTD Asesor">
                    @else
                        <div style="height: 35px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lampiran Dokumen List --}}
    <div style="margin-top: 18px; font-size: 8.5pt; line-height: 1.5;" class="avoid-break">
        <strong>LAMPIRAN DOKUMEN:</strong><br>
        1. Dokumen APL 01 peserta<br>
        2. Dokumen APL 02 peserta<br>
        3. Bukti-bukti berkualitas peserta<br>
        4. Tinjauan proses asesi
    </div>
</div>
@endsection
