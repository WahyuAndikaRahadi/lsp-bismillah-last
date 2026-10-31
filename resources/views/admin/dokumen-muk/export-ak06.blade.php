@extends('layouts.print-muk')

@section('title', 'FR.AK.06 Meninjau Proses Asesmen - ' . $schedule->code)
@section('doc-title', 'FR.AK.06 Meninjau Proses Asesmen')

@section('content')
<div style="font-size: 9.5pt; line-height: 1.35;">
    {{-- Header Kop --}}
    <div class="header-kop">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
        <div class="doc-code">FR.AK.06. MENINJAU PROSES ASESMEN</div>
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

    {{-- Explanation points --}}
    <div style="font-size: 8.5pt; background: #fafafa; border: 1px solid #cbd5e1; padding: 6px 10px; margin-bottom: 10px; line-height: 1.4;">
        <strong>Penjelasan:</strong>
        <ol style="margin: 3px 0 0 16px; padding: 0;">
            <li>Peninjauan dapat dilakukan oleh lead asesor atau asesor yang melaksanakan asesmen.</li>
            <li>Peninjauan dapat dilakukan secara terpadu dalam skema sertifikasi dan / atau peserta kelompok yang homogen.</li>
            <li>Isilah pemenuhan dimensi kompetensi dengan menuliskan jenis bukti dan instrumen yang digunakan pada saat asesmen sebagai bukti terpenuhinya dimensi kompetensi.</li>
        </ol>
    </div>

    {{-- Table 1: Principles of Assessment --}}
    @php
        $ak06Data = is_array($schedule->ak06_review) ? $schedule->ak06_review : [];
        $procedures = [
            'plan' => 'Rencana asesmen',
            'prep' => 'Persiapan asesmen',
            'impl' => 'Implementasi asesmen',
            'decision' => 'Keputusan asesmen',
            'feedback' => 'Umpan balik asesmen',
        ];
    @endphp
    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 44%;" rowspan="2">Aspek yang ditinjau</th>
                <th colspan="4" style="width: 56%;">Kesesuaian dengan prinsip asesmen</th>
            </tr>
            <tr style="font-size: 8pt;">
                <th style="width: 14%;">Validitas</th>
                <th style="width: 14%;">Reliabel</th>
                <th style="width: 14%;">Fleksibel</th>
                <th style="width: 14%;">Adil</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: #f8fafc;">
                <td colspan="5" style="font-weight: bold; padding: 4px 6px;">Prosedur asesmen:</td>
            </tr>
            @foreach($procedures as $key => $label)
                @php
                    $pData = $ak06Data['procedures'][$key] ?? [];
                    $isValid = $pData['valid'] ?? true;
                    $isReliable = $pData['reliable'] ?? true;
                    $isFlexible = $pData['flexible'] ?? true;
                    $isFair = $pData['fair'] ?? true;
                @endphp
                <tr>
                    <td style="padding: 4px 10px;">&bull; {{ $label }}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isValid ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isReliable ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isFlexible ? '&radic;' : '' !!}</td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold;">{!! $isFair ? '&radic;' : '' !!}</td>
                </tr>
            @endforeach
            <tr>
                <td style="font-weight: bold; padding: 4px 6px;">Rekomendasi untuk peningkatan</td>
                <td colspan="4" style="padding: 4px 6px; font-size: 8.5pt;">
                    {{ $ak06Data['procedure_recommendation'] ?? 'Seluruh prosedur asesmen terlaksana secara valid, reliabel, fleksibel dan adil sesuai pedoman BNSP 301.' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Table 2: Dimensions of Competency --}}
    <div style="font-weight: bold; font-size: 9pt; margin-top: 10px; margin-bottom: 4px;">
        Pemenuhan dimensi kompetensi :
    </div>
    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 28%;" rowspan="2">Aspek yang ditinjau</th>
                <th colspan="5">Pemenuhan dimensi kompetensi</th>
            </tr>
            <tr style="font-size: 8pt;">
                <th style="width: 14%;">Task Skills</th>
                <th style="width: 14%;">Task Management Skills</th>
                <th style="width: 15%;">Contingency Management Skills</th>
                <th style="width: 15%;">Job Role / Environment Skills</th>
                <th style="width: 14%;">Transfer Skills</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 6px; font-size: 8.5pt;">
                    <strong>Konsistensi keputusan asesmen</strong><br>
                    <span style="font-size: 7.5pt; color: #475569;">Bukti dari berbagai asesmen diperiksa untuk konsistensi dimensi kompetensi</span>
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 4px; font-size: 8pt;">
                    <strong>FR.IA.01, 02</strong><br>(Demonstrasi)
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 4px; font-size: 8pt;">
                    <strong>FR.IA.01, 02</strong><br>(SOP &amp; Alat)
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 4px; font-size: 8pt;">
                    <strong>FR.IA.03</strong><br>(PMO Wawancara)
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 4px; font-size: 8pt;">
                    <strong>FR.IA.01, K3</strong><br>(Lingkungan Kerja)
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 4px; font-size: 8pt;">
                    <strong>FR.IA.05</strong><br>(CBT Tulis Teori)
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Page Break to Page 2 Matching Master FR.AK.06 --}}
    <div class="page-break" style="margin-top: 15px;"></div>

    <div class="header-kop" style="margin-bottom: 14px;">
        <div class="title-lsp">Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo</div>
    </div>

    {{-- Recommendation & Signature --}}
    <div style="border: 1px solid #000; padding: 10px; margin-top: 10px; font-size: 9.5pt;">
        <strong>Rekomendasi untuk peningkatan:</strong>
        <div style="margin-top: 8px; min-height: 80px; color: #1e293b; line-height: 1.5;">
            {{ $ak06Data['improvement_recommendations'] ?? 'MUK yang digunakan sangat komprehensif. Disarankan pembaruan berkelanjutan pada bank soal CBT mengikuti tren teknologi terkini di industri otomotif.' }}
        </div>
    </div>

    <div class="sig-container avoid-break" style="margin-top: 20px;">
        <table class="bnsp-table" style="font-size: 9.5pt;">
            <thead>
                <tr>
                    <th style="width: 35%;">Nama Lead Asesor / Asesor</th>
                    <th style="width: 25%;">Tanggal &amp; Tanda Tangan</th>
                    <th style="width: 40%;">Komentar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="vertical-align: middle; padding: 10px 8px;">
                        <strong>{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '-' }}</strong><br>
                        <span style="font-size: 8.5pt; color: #475569;">No. MET: {{ $leadAssessor?->profile?->met_number ?? '-' }}</span>
                    </td>
                    <td style="vertical-align: middle; text-align: center; padding: 8px;">
                        <div style="font-size: 8.5pt; margin-bottom: 6px;">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</div>
                        @if($leadAssessor?->profile?->signature_path)
                            <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" style="max-height: 48px; max-width: 120px;" alt="TTD">
                        @elseif(isset($assessments) && $assessments->first()?->assessor_signature)
                            <img src="{{ $assessments->first()->assessor_signature }}" style="max-height: 48px; max-width: 120px;" alt="TTD">
                        @else
                            <div style="height: 45px;"></div>
                        @endif
                    </td>
                    <td style="vertical-align: middle; padding: 8px; font-size: 9pt;">
                        {{ $schedule->ak06_assessor_comments ?? 'Proses asesmen telah memenuhi seluruh standar mutu penjaminan asesmen BNSP.' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
