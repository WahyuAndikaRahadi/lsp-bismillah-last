@extends('layouts.print-muk')

@section('title', 'Daftar Hadir Asesi - ' . $schedule->code)
@section('doc-title', 'Daftar Hadir Asesi')

@section('content')
<div style="font-size: 10pt; line-height: 1.4;">
    {{-- Header Title --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 12pt; font-weight: bold; text-decoration: underline; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            DAFTAR HADIR
        </h2>
    </div>

    {{-- Meta Info --}}
    <div style="margin-bottom: 18px; line-height: 1.8;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
            <tr>
                <td style="width: 15%; vertical-align: top;">Hari / tanggal</td>
                <td style="width: 3%; vertical-align: top; text-align: center;">:</td>
                <td style="width: 82%; vertical-align: top;"><strong>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l / d F Y') }}</strong></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Tempat</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;"><strong>{{ $schedule->tuk_name }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Asesi Attendance Table with Alternating Signatures --}}
    <table class="bnsp-table" style="width: 100%; font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 22%;">Nama Peserta</th>
                <th style="width: 16%;">Institusi</th>
                <th style="width: 20%;">Alamat</th>
                <th style="width: 12%;">Pekerjaan</th>
                <th style="width: 12%;">No. Telp/HP</th>
                <th style="width: 14%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = max(10, count($assessments));
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $num = $i + 1;
                    $assessment = $assessments[$i] ?? null;
                    $asesi = $assessment?->asesi;
                    $profile = $asesi?->profile;
                    $isOdd = ($num % 2 !== 0);
                @endphp
                <tr>
                    <td style="text-align: center; height: 36px; vertical-align: middle;">{{ $num }}</td>
                    <td style="vertical-align: middle; font-weight: bold;">
                        {{ $asesi?->full_name ?? '' }}
                    </td>
                    <td style="vertical-align: middle;">
                        {{ $profile?->institution ?? $profile?->company ?? '' }}
                    </td>
                    <td style="vertical-align: middle; font-size: 8.5pt;">
                        {{ $profile?->address ?? '' }}
                    </td>
                    <td style="vertical-align: middle;">
                        {{ $profile?->job_title ?? $profile?->occupation ?? 'Pelajar / Teknisi' }}
                    </td>
                    <td style="vertical-align: middle; font-family: monospace;">
                        {{ $asesi?->phone ?? $profile?->phone ?? '' }}
                    </td>
                    <td style="vertical-align: middle; padding: 2px 4px;">
                        {{-- Alternating Signature layout matching master --}}
                        <div style="display: flex; align-items: center; justify-content: {{ $isOdd ? 'flex-start' : 'flex-end' }}; height: 100%;">
                            <div style="font-size: 8.5pt; font-weight: bold; width: 100%; text-align: {{ $isOdd ? 'left' : 'right' }};">
                                @if($isOdd)
                                    <span>{{ $num }}.</span>
                                    @if($assessment?->asesi_signature)
                                        <img src="{{ $assessment->asesi_signature }}" style="max-height: 28px; max-width: 65px; vertical-align: middle; margin-left: 2px;" alt="TTD">
                                    @endif
                                @else
                                    @if($assessment?->asesi_signature)
                                        <img src="{{ $assessment->asesi_signature }}" style="max-height: 28px; max-width: 65px; vertical-align: middle; margin-right: 2px;" alt="TTD">
                                    @endif
                                    <span>{{ $num }}.</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Assessor Signature Block on Right Side --}}
    <div style="margin-top: 30px; width: 100%; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 45%; margin-left: auto;">
            <div style="margin-bottom: 10px;">
                {{ $schedule->tuk_city ?? 'Karawang' }}, {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
            </div>
            <div style="font-weight: bold; margin-bottom: 6px;">Asesor :</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt; line-height: 1.6;">
                <tr>
                    <td style="width: 32%;">Nama</td>
                    <td style="width: 5%;">:</td>
                    <td style="width: 63%;"><strong>{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '........................................' }}</strong></td>
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
    </div>
</div>
@endsection
