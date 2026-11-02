@extends('layouts.print-muk')

@section('title', 'Berita Acara Pelaksanaan Uji Kompetensi - ' . ($schedule->code ?? ''))
@section('doc-title', 'Berita Acara Pelaksanaan Uji Kompetensi')

@push('styles')
<style>
    @page {
        size: 210.26mm 297.04mm;
        margin: 9.13mm 12.0mm 15.0mm 15.9mm;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, 'Liberation Sans', sans-serif;
    }
    .sheet {
        margin: 0 !important;
        padding: 0 !important;
        width: 182.74mm !important;
    }
</style>
@endpush

@section('content')
<div style="width: 182.74mm; font-family: Arial, 'Liberation Sans', sans-serif; font-size: 10pt; color: #000;">
    {{-- Master Kop Surat --}}
    <div style="margin: 0; padding: 0; height: 31.49mm;">
        <img src="/images/muk/kop_surat.png" alt="Kop Surat LSP ProIndo" style="width: 182.74mm; height: 31.49mm; display: block;">
    </div>

    {{-- Title at y = 43.5mm, x = 70.5mm (offset 54.6mm from container left) --}}
    <div style="text-align: left; padding-left: 54.6mm; margin-top: 1.5mm; height: 4.0mm; margin-bottom: 8.2mm;">
        <span style="font-size: 10pt; font-weight: bold; line-height: 1;">BERITA ACARA PELAKSANAAN UJI KOMPETENSI</span>
    </div>

    {{-- Opening Paragraph at y = 55.7mm with exact master line breaks --}}
    <div style="text-align: justify; line-height: 7.4mm; margin-bottom: 2.5mm;">
        Pada hari ini, Hari/Tanggal : {{ ($mode ?? '') === 'empty' ? '......................' : \Carbon\Carbon::parse($schedule->date)->translatedFormat('l') }} / {{ ($mode ?? '') === 'empty' ? '.................... 2026' : \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }} &nbsp; Waktu : Pkl. 08.00 s/d 17.00 bertempat di {{ ($mode ?? '') === 'empty' ? 'SMK' : ($schedule->tuk_name ?: 'SMK') }}<br>
        {{ ($mode ?? '') === 'empty' ? 'Negeri 3 Karawang' : '' }}{{ ($mode ?? '') === 'empty' ? ',' : '' }} telah dilaksanakan proses asesmen Skema Sertifikasi {{ $scheme->name ?? 'Mekanik Junior' }} dengan perincian<br>
        sebagai berikut :
    </div>

    {{-- Statistics at y = 80.8mm --}}
    <table style="width: 182.74mm; border-collapse: collapse; font-size: 10pt; margin-bottom: 9.7mm;">
        <tr style="height: 7.4mm;">
            <td style="width: 85.7mm; padding: 0; vertical-align: middle;">Jumlah kandidat &nbsp;yang mengikuti</td>
            <td style="padding: 0; vertical-align: middle;">: {{ ($mode ?? '') === 'empty' ? '...........' : $total_asesi }} &nbsp;( {{ ($mode ?? '') === 'empty' ? '........................................' : ucwords($total_asesi_terbilang) }} ) &nbsp;orang</td>
        </tr>
        <tr style="height: 7.4mm;">
            <td style="padding: 0; vertical-align: middle;">Jumlah kandidat &nbsp;yang dinyatakan Kompeten</td>
            <td style="padding: 0; vertical-align: middle;">: {{ ($mode ?? '') === 'empty' ? '...........' : $total_kompeten }} &nbsp;( {{ ($mode ?? '') === 'empty' ? '........................................' : ucwords($total_kompeten_terbilang) }} ) &nbsp;orang</td>
        </tr>
        <tr style="height: 7.4mm;">
            <td style="padding: 0; vertical-align: middle;">Jumlah kandidat &nbsp;yang dinyatakan Belum Kompeten</td>
            <td style="padding: 0; vertical-align: middle;">: {{ ($mode ?? '') === 'empty' ? '...........' : $total_belum_kompeten }} &nbsp;( {{ ($mode ?? '') === 'empty' ? '........................................' : ucwords($total_belum_kompeten_terbilang) }} ) &nbsp;orang</td>
        </tr>
    </table>

    {{-- Candidate Table at y = 113.8mm --}}
    <table class="bnsp-table" style="width: 182.74mm; border-collapse: collapse; font-size: 10pt; margin: 0 0 7.0mm 0;">
        <colgroup>
            <col style="width: 14.29mm;">
            <col style="width: 65.09mm;">
            <col style="width: 30.16mm;">
            <col style="width: 42.86mm;">
            <col style="width: 30.34mm;">
        </colgroup>
        <thead>
            <tr style="height: 10.0mm;">
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 3px; background: #fff;">No.</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 3px; background: #fff;">Nama Peserta</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 3px; background: #fff; line-height: 1.1;">Hasil<br>Asesmen*)</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 3px; background: #fff; line-height: 1.1;">Rekomendasi/ Tindak<br>Lanjut</th>
                <th style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 1px 3px; background: #fff;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = max(10, count($assessments));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $a = $assessments[$i] ?? null;
                    $resultStr = '';
                    if ($a) {
                        $resultStr = ($a->final_result === 'BK' || $a->final_result === 'BELUM_KOMPETEN') ? 'BK' : 'K';
                    }
                @endphp
                <tr style="height: 5.3mm;">
                    <td style="border: 1px solid #000; text-align: center; padding: 0 3px;">{{ $i + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $a?->asesi->full_name ?? $a?->asesi->name ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; padding: 0 3px;">{{ $resultStr }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $a?->assessor_notes ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 0 3px;">{{ $a ? $scheme->name : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Footnote at y = 181.3mm, indent x = 25.4mm (9.5mm indent) --}}
    <div style="margin-left: 9.5mm; font-size: 10pt; height: 4.0mm; margin-bottom: 3.5mm;">
        Keterangan : *) &nbsp;Diisi dengan K (Kompeten) atau BK (Belum Kompeten)
    </div>

    {{-- Closing at y = 188.8mm, indent 9.5mm --}}
    <div style="margin-left: 9.5mm; font-size: 10pt; height: 4.0mm; margin-bottom: 2.9mm;">
        Demikian berita acara ini dibuat dengan sebenarnya, untuk digunakan sebagaimana mestinya.
    </div>

    {{-- Date at y = 195.7mm, indent x = 114.3mm (98.4mm indent) --}}
    <div style="margin-left: 98.4mm; font-size: 10pt; height: 4.0mm; margin-bottom: 16.5mm;">
        {{ ($mode ?? '') === 'empty' ? '………………………..,  ………………………….2026' : (($schedule->tuk_city ?? 'Karawang') . ', ' . \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y')) }}
    </div>

    {{-- Signatures at y = 216.2mm --}}
    <div style="width: 100%; font-size: 10pt;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
            <tr>
                <td style="width: 9.5mm;"></td>
                <td style="width: 88.9mm; vertical-align: top; padding: 0;">
                    <div style="height: 4.0mm; margin-bottom: 6.2mm;">Asesor</div>
                    <div style="height: 4.1mm;">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($leadAssessor?->full_name ?? $leadAssessor?->name ?? '') }}</div>
                    <div style="height: 4.1mm;">No. Reg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($leadAssessor?->assessor_registration_number ?? $leadAssessor?->profile?->met_number ?? '') }}</div>
                    <div style="height: 4.0mm;">Tanda tangan :</div>
                    <div style="margin-top: 0mm;">
                        @if(($mode ?? '') !== 'empty' && $leadAssessor?->profile?->signature_path)
                            <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" style="max-height: 8mm; position: absolute;" alt="TTD">
                        @endif
                    </div>
                    <div>………………………………….</div>
                </td>
                <td style="vertical-align: top; padding: 0;">
                    <div style="height: 4.0mm; margin-bottom: 6.2mm;">Penanggungjawab kegiatan</div>
                    <div style="height: 4.1mm;">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($schedule->pic_tuk_name ?? '') }}</div>
                    <div style="height: 4.1mm;">Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ ($mode ?? '') === 'empty' ? '' : ($schedule->pic_tuk_position ?? '') }}</div>
                    <div style="height: 4.0mm;">Tanda tangan :</div>
                    <div style="margin-top: 0mm;">
                        @if(($mode ?? '') !== 'empty' && $schedule->pic_tuk_signature)
                            <img src="{{ $schedule->pic_tuk_signature }}" style="max-height: 8mm; position: absolute;" alt="TTD">
                        @endif
                    </div>
                    <div style="padding-left: 12.7mm;">………………………………….</div>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
