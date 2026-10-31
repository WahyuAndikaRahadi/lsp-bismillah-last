@extends('layouts.print-muk')

@section('title', 'FR.AK.05 Laporan Asesmen - ' . $schedule->code)
@section('doc-title', 'FR.AK.05 Laporan Asesmen')

@section('content')
<div style="font-size: 9.5pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.05. LAPORAN ASESMEN</div>
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
            <td colspan="3" style="padding-left: 6px; font-weight: bold;">{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '-' }}</td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td class="label" style="border-right: 1px solid #000; font-weight: bold;">Tanggal</td>
            <td colspan="3" style="padding-left: 6px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- Candidate Recommendations Table --}}
    <table class="bnsp-table" style="font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 6%;" rowspan="2">No.</th>
                <th style="width: 44%;" rowspan="2">Nama Asesi</th>
                <th colspan="2" style="width: 20%;">Rekomendasi</th>
                <th style="width: 30%;" rowspan="2">Keterangan**</th>
            </tr>
            <tr style="font-size: 8pt;">
                <th style="width: 10%;">K</th>
                <th style="width: 10%;">BK</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = max(5, count($assessments));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $a = $assessments[$i] ?? null;
                    $isAbsent = $a && $a->final_result === 'TIDAK_HADIR';
                    $isK = ($a && ! $isAbsent) ? (in_array($a->final_result, ['K', 'KOMPETEN'], true) || ($a->final_result === null && $a->status === 'COMPLETED')) : false;
                    $isBK = ($a && ! $isAbsent) ? in_array($a->final_result, ['BK', 'BELUM_KOMPETEN'], true) : false;
                @endphp
                <tr>
                    <td style="text-align: center; height: 28px; vertical-align: middle;">{{ $i + 1 }}.</td>
                    <td style="vertical-align: middle; font-weight: bold;">{{ $a?->asesi->full_name ?? '' }}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isK ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isBK ? '&radic;' : '' !!}</td>
                    <td style="vertical-align: middle; font-size: 8.5pt;">
                        @if($isAbsent)
                            <span style="font-style: italic; color: #475569;">Tidak Hadir</span>
                        @elseif($a && $isBK)
                            Perlu uji ulang
                        @elseif($a && $isK)
                            Memenuhi seluruh KUK
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div style="font-size: 8.5pt; font-style: italic; margin-top: 2px; margin-bottom: 12px;">
        ** tuliskan Kode dan Judul Unit Kompetensi yang dinyatakan BK bila mengases satu skema
    </div>

    {{-- Report Findings Block --}}
    @php
        $ak05Data = is_array($schedule->ak05_report) ? $schedule->ak05_report : [];
    @endphp
    <table class="bnsp-table" style="font-size: 9pt;">
        <tr>
            <td style="width: 30%; font-weight: bold; vertical-align: top; background: #f8fafc;">
                Aspek Negatif dan Positif dalam Asesmen
            </td>
            <td style="vertical-align: top; padding: 6px;">
                <div style="margin-bottom: 6px;">
                    <strong>Aspek Negatif :</strong><br>
                    <span>{{ $ak05Data['negative_aspects'] ?? 'Tidak ditemukan kendala teknis krusial; asesi sempat gugup pada pembukaan namun beradaptasi dengan baik.' }}</span>
                </div>
                <div>
                    <strong>Aspek Positif :</strong><br>
                    <span>{{ $ak05Data['positive_aspects'] ?? 'Asesi sangat tertib K3, menggunakan APD lengkap, dan menguasai manual book.' }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; background: #f8fafc;">
                Pencatatan Penolakan Hasil Asesmen
            </td>
            <td style="vertical-align: top; padding: 6px;">
                <strong>Ada Penolakan :</strong> {{ $ak05Data['rejection_notes'] ?? 'Tidak ada penolakan dari asesi atas keputusan asesmen.' }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; background: #f8fafc;">
                Saran Perbaikan :<br>
                <span style="font-size: 8pt; font-weight: normal; color: #64748b;">(Asesor/Personil Terkait)</span>
            </td>
            <td style="vertical-align: top; padding: 6px;">
                {{ $ak05Data['improvement_suggestions'] ?? 'Pertahankan kelengkapan alat ukur terkalibrasi di TUK agar efisiensi waktu asesmen terus terjaga.' }}
            </td>
        </tr>
    </table>

    {{-- Notes & Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 15px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Catatan :</div>
                <div style="font-size: 8.5pt; color: #475569; line-height: 1.5; border: 1px dotted #94a3b8; padding: 6px; min-height: 80px;">
                    Laporan asesmen ini dibuat berdasarkan seluruh bukti langsung, tidak langsung, dan tambahan yang dihimpun selama proses asesmen berlangsung di TUK.
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Asesor :</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                    <tr>
                        <td style="width: 32%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 64%;"><strong>{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>No. Reg</td>
                        <td>:</td>
                        <td>{{ $leadAssessor?->profile?->met_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px;">Tanda tangan &amp; Tanggal</td>
                        <td style="padding-top: 4px;">:</td>
                        <td style="padding-top: 4px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
                <div class="sig-box">
                    @if($leadAssessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessments->first()?->assessor_signature)
                        <img src="{{ $assessments->first()->assessor_signature }}" class="sig-img" alt="TTD Asesor">
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
