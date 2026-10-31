@extends('pdf.layout', ['title' => 'FR-APL-02 Asesmen Mandiri - ' . $application->nomor_permohonan])

@section('content')
<div class="doc-title-box">
    <div class="doc-title">FR-APL-02. ASESMEN MANDIRI</div>
    <div class="doc-number">Nomor: {{ $application->nomor_permohonan }}/APL-02</div>
</div>

<table class="clean-table" style="font-size: 8.5pt; margin-bottom: 8px;">
    <tr>
        <td style="width: 140px; font-weight: bold;">Skema Sertifikasi</td>
        <td style="width: 10px;">:</td>
        <td>{{ $application->scheme->nama_skema ?? '-' }} ({{ $application->scheme->kode_skema ?? '-' }})</td>
        <td style="width: 120px; font-weight: bold;">Nama Asesi</td>
        <td style="width: 10px;">:</td>
        <td style="font-weight: bold;">{{ $application->user->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Tempat Uji (TUK)</td>
        <td>:</td>
        <td>{{ $application->schedule->tuk->nama_tuk ?? 'TUK LSP' }}</td>
        <td style="font-weight: bold;">Nama Asesor</td>
        <td>:</td>
        <td>{{ $application->asesor->name ?? 'Tim Asesor LSP' }}</td>
    </tr>
</table>

<p style="text-align: justify; font-size: 8pt; margin-bottom: 6px;">
    <strong>Panduan Asesmen Mandiri:</strong> Pelajari seluruh Kriteria Unjuk Kerja (KUK) di bawah ini. Nyatakan kemampuan Anda dengan memilih opsi Kompeten (K) atau Belum Kompeten (BK) serta sebutkan bukti portofolio yang relevan.
</p>

@php
    $apl02 = $application->apl02;
    $itemsByUnit = $apl02 ? $apl02->items->groupBy(fn($item) => $item->criteria->element->competencyUnit->id ?? 0) : collect();
@endphp

@foreach ($application->scheme->units ?? [] as $unit)
    <div style="background-color: #e9ecef; font-weight: bold; padding: 4px 6px; font-size: 8.5pt; border: 1px solid #333; margin-top: 8px;">
        Unit {{ $unit->kode_unit }} : {{ $unit->judul_unit }}
    </div>

    <table class="data-table" style="font-size: 7.5pt; margin-top: 2px;">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Daftar Pertanyaan Asesmen Mandiri (Elemen & KUK)</th>
                <th style="width: 35px;">K</th>
                <th style="width: 35px;">BK</th>
                <th style="width: 120px;">Bukti Pendukung</th>
                <th style="width: 45px;">VATM</th>
            </tr>
        </thead>
        <tbody>
            @php $qIdx = 1; @endphp
            @foreach ($unit->elements ?? [] as $elem)
                @foreach ($elem->criterias ?? [] as $crit)
                    @php
                        $item = $apl02 ? $apl02->items->firstWhere('criteria_id', $crit->id) : null;
                        $isK = $item && $item->penilaian === 'K';
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $qIdx++ }}</td>
                        <td>
                            <strong>{{ $elem->nama_elemen }}:</strong> {{ $crit->pernyataan_kuk }}
                        </td>
                        <td style="text-align: center; font-weight: bold; color: #1e7e34;">{{ $isK ? '✓' : '' }}</td>
                        <td style="text-align: center; font-weight: bold; color: #c81e1e;">{{ !$isK && $item ? '✓' : '' }}</td>
                        <td style="font-size: 7pt;">{{ $item->bukti_pendukung ?? 'Portofolio / Ijazah / Sertifikat' }}</td>
                        <td style="text-align: center; font-weight: bold; color: #1e7e34;">
                            {{ $item && $item->is_valid && $item->is_authentic && $item->is_current && $item->is_sufficient ? '✓' : '-' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endforeach

<div style="margin-top: 8px; font-size: 8.5pt; border: 1px solid #333; padding: 6px 10px; background-color: #f9f9f9;">
    <strong>Rekomendasi Asesor:</strong>
    @if ($apl02 && $apl02->rekomendasi === 'dapat_dilanjutkan')
        <span style="color: #1e7e34; font-weight: bold;">[X] Asesmen dapat dilanjutkan ke tahap asesmen uji kompetensi</span>
    @else
        <span style="color: #c81e1e; font-weight: bold;">[ ] Asesmen belum dapat dilanjutkan (perlu perbaikan bukti)</span>
    @endif
    <div style="margin-top: 3px; font-size: 8pt; color: #555;">
        Catatan Asesor: {{ $apl02->catatan_asesor ?? 'Bukti asesmen mandiri memenuhi persyaratan untuk dilanjutkan.' }}
    </div>
</div>

<table class="sign-table" style="margin-top: 15px;">
    <tr>
        <td>
            <div>Asesi,</div>
            <div style="font-weight: bold;">Pemohon Asesmen Mandiri</div>
            <div class="sign-space">
                @if(!empty($application->user->signature_image))
                    <img src="{{ $application->user->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $application->user->name ?? '-' }}</div>
            <div style="font-size: 8pt;">Tanggal: {{ $apl02 ? \Carbon\Carbon::parse($apl02->tgl_asesi_sign ?? now())->format('d/m/Y') : date('d/m/Y') }}</div>
        </td>
        <td>
            <div>Asesor Kompetensi,</div>
            <div style="font-weight: bold;">Peninjau / Verifikator APL-02</div>
            <div class="sign-space">
                @php $asesorUser = $apl02?->asesor ?? $application->asesor; @endphp
                @if($asesorUser && !empty($asesorUser->signature_image))
                    <img src="{{ $asesorUser->signature_image }}" class="sign-img" alt="TTD">
                @endif
            </div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $asesorUser->name ?? 'Asesor Kompetensi' }}</div>
            <div style="font-size: 8pt;">No. MET: {{ $asesorUser?->asesorProfile?->no_reg_met ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
