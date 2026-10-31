@extends('layouts.print-muk')

@section('title', 'Berita Acara Pelaksanaan Uji Kompetensi - ' . $schedule->code)
@section('doc-title', 'Berita Acara Pelaksanaan Uji Kompetensi')

@section('content')
<div style="font-size: 10pt; line-height: 1.4;">
    {{-- Header Title --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 12pt; font-weight: bold; text-decoration: underline; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            BERITA ACARA PELAKSANAAN UJI KOMPETENSI
        </h2>
    </div>

    {{-- Opening Paragraph --}}
    <div style="text-align: justify; margin-bottom: 18px;">
        Pada hari ini, Hari/Tanggal : <strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l') }} / {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</strong> &nbsp;&nbsp; Waktu : <strong>Pkl. {{ \Carbon\Carbon::parse($schedule->start_time)->format('H.i') }} s/d {{ \Carbon\Carbon::parse($schedule->end_time)->format('H.i') }}</strong> bertempat di <strong>{{ $schedule->tuk_name }}</strong>, telah dilaksanakan proses asesmen Skema Sertifikasi <strong>{{ $scheme->name }}</strong> dengan perincian sebagai berikut :
    </div>

    {{-- Summary Statistics --}}
    <div style="margin-bottom: 20px; line-height: 1.8;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
            <tr>
                <td style="width: 48%;">Jumlah kandidat yang mengikuti</td>
                <td style="width: 3%; text-align: center;">:</td>
                <td style="width: 49%;"><strong>{{ $total_asesi }}</strong> ( <em>{{ ucwords($total_asesi_terbilang) }}</em> ) orang</td>
            </tr>
            <tr>
                <td>Jumlah kandidat yang dinyatakan Kompeten</td>
                <td style="text-align: center;">:</td>
                <td><strong>{{ $total_kompeten }}</strong> ( <em>{{ ucwords($total_kompeten_terbilang) }}</em> ) orang</td>
            </tr>
            <tr>
                <td>Jumlah kandidat yang dinyatakan Belum Kompeten</td>
                <td style="text-align: center;">:</td>
                <td><strong>{{ $total_belum_kompeten }}</strong> ( <em>{{ ucwords($total_belum_kompeten_terbilang) }}</em> ) orang</td>
            </tr>
        </table>
    </div>

    {{-- Assessment Candidates Table --}}
    <table class="bnsp-table" style="width: 100%; font-size: 9.5pt;">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 35%;">Nama Peserta</th>
                <th style="width: 15%;">Hasil Asesmen*)</th>
                <th style="width: 25%;">Rekomendasi/ Tindak Lanjut</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = max(10, count($assessments));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $assessment = $assessments[$i] ?? null;
                    if ($assessment) {
                        if ($assessment->final_result === 'TIDAK_HADIR') {
                            $res = 'Tidak Hadir';
                            $keterangan = $assessment->final_notes ?: 'Peserta Tidak Hadir';
                        } elseif (in_array($assessment->final_result, ['K', 'KOMPETEN'], true) || ($assessment->final_result === null && $assessment->status === 'COMPLETED')) {
                            $res = 'K';
                            $keterangan = $assessment->assessor_notes ?: 'Direkomendasikan Kompeten';
                        } elseif (in_array($assessment->final_result, ['BK', 'BELUM_KOMPETEN'], true)) {
                            $res = 'BK';
                            $keterangan = $assessment->assessor_notes ?: 'Perlu Asesmen Ulang';
                        } else {
                            $res = ($assessment->status === 'IN_PROGRESS') ? '-' : 'BELUM_KOMPETEN';
                            $keterangan = $assessment->assessor_notes ?: '-';
                        }
                    } else {
                        $res = '';
                        $keterangan = '';
                    }
                @endphp
                <tr>
                    <td style="text-align: center; height: 26px;">{{ $i + 1 }}</td>
                    <td>{{ $assessment?->asesi->full_name ?? '' }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($res === 'K')
                            <span style="color: #047857;">K</span>
                        @elseif($res === 'BK')
                            <span style="color: #b91c1c;">BK</span>
                        @elseif($res === 'Tidak Hadir')
                            <span style="color: #64748b; font-size: 8pt;">Tidak Hadir</span>
                        @else
                            {{ $res ?? '' }}
                        @endif
                    </td>
                    <td>
                        {{ $keterangan }}
                    </td>
                    <td>
                        @if($assessment)
                            {{ $scheme->name }}
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Footnote & Closing text --}}
    <div style="font-size: 9pt; margin-top: 4px; margin-bottom: 14px;">
        <em>Keterangan : *) Diisi dengan K (Kompeten) atau BK (Belum Kompeten)</em>
    </div>

    <div style="text-align: justify; margin-bottom: 24px;">
        Demikian berita acara ini dibuat dengan sebenarnya, untuk digunakan sebagaimana mestinya.
    </div>

    {{-- Signatures Block --}}
    <div class="sig-container" style="page-break-inside: avoid;">
        <div style="text-align: right; margin-bottom: 15px;">
            {{ $schedule->tuk_city ?? 'Karawang' }}, {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
        </div>
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0;">
                <div style="font-weight: bold; margin-bottom: 6px;">Asesor</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
                    <tr>
                        <td style="width: 28%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 68%;"><strong>{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '........................................' }}</strong></td>
                    </tr>
                    <tr>
                        <td>No. Reg</td>
                        <td>:</td>
                        <td>{{ $leadAssessor?->assessor_registration_number ?? $leadAssessor?->profile?->met_number ?? '........................................' }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-top: 6px;">Tanda tangan :</td>
                    </tr>
                </table>
                <div class="sig-box" style="margin-top: 8px;">
                    @if($leadAssessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessments->first()?->assessor_signature)
                        <img src="{{ $assessments->first()->assessor_signature }}" class="sig-img" alt="TTD Asesor">
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%; margin-top: 4px;"></div>
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0;">
                <div style="font-weight: bold; margin-bottom: 6px;">Penanggungjawab kegiatan</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
                    <tr>
                        <td style="width: 28%;">Nama</td>
                        <td style="width: 4%;">:</td>
                        <td style="width: 68%;"><strong>{{ $schedule->pic_tuk_name ?? '........................................' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $schedule->pic_tuk_position ?? 'Penanggung Jawab TUK' }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-top: 6px;">Tanda tangan :</td>
                    </tr>
                </table>
                <div class="sig-box" style="margin-top: 8px;">
                    @if($schedule->pic_tuk_signature)
                        <img src="{{ $schedule->pic_tuk_signature }}" class="sig-img" alt="TTD Penanggung Jawab">
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%; margin-top: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
