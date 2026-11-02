<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Kompetensi - {{ $application->user->name }}</title>
    <style>
        @page {
            margin: 10mm 15mm 10mm 15mm;
            size: a4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .cert-border {
            border: 4px double #1e3a8a;
            padding: 20px 30px;
            box-sizing: border-box;
            height: 94%;
            text-align: center;
        }
        .cert-header {
            margin-bottom: 12px;
        }
        .garuda-title {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .cert-sub {
            font-size: 9pt;
            letter-spacing: 1px;
            color: #555;
            text-transform: uppercase;
        }
        .cert-heading {
            font-size: 20pt;
            font-weight: bold;
            color: #111827;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 2px;
        }
        .cert-heading-en {
            font-size: 11pt;
            font-style: italic;
            color: #4b5563;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .cert-no {
            font-family: monospace;
            font-size: 11pt;
            font-weight: bold;
            color: #1e40af;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            display: inline-block;
            padding: 3px 14px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .cert-body {
            font-size: 11pt;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .cert-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
            margin: 8px 0 2px 0;
        }
        .cert-nik {
            font-size: 10pt;
            font-family: monospace;
            color: #475569;
            margin-bottom: 10px;
        }
        .cert-scheme {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 6px 0 2px 0;
        }
        .cert-scheme-en {
            font-size: 10pt;
            font-style: italic;
            color: #475569;
        }
        .cert-footer {
            margin-top: 20px;
            width: 100%;
        }
        .cert-footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .cert-footer td {
            vertical-align: bottom;
            text-align: center;
            width: 50%;
        }
        .page-break {
            page-break-after: always;
        }
        table.unit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 15px;
            text-align: left;
        }
        table.unit-table th, table.unit-table td {
            border: 1px solid #333;
            padding: 5px 8px;
        }
        table.unit-table th {
            background-color: #f1f5f9;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- HALAMAN 1: MUKA SERTIFIKAT -->
    <div class="cert-border">
        <div class="cert-header">
            <div class="garuda-title">REPUBLIK INDONESIA</div>
            <div class="cert-sub">BADAN NASIONAL SERTIFIKASI PROFESI (BNSP)</div>
            <div style="font-size: 9pt; font-weight: bold; color: #1e3a8a; margin-top: 2px;">
                {{ $lsp->nama_lsp ?? 'LEMBAGA SERTIFIKASI PROFESI' }}
            </div>
            <div style="font-size: 7.5pt; color: #64748b;">
                Lisensi BNSP: {{ $lsp->no_lisensi_bnsp ?? 'BNSP-LSP-000-ID' }}
            </div>
        </div>

        <div class="cert-heading">SERTIFIKAT KOMPETENSI</div>
        <div class="cert-heading-en">CERTIFICATE OF COMPETENCE</div>

        <div class="cert-no">
            No. {{ $application->no_sertifikat ?? 'BNSP-000-2026-0001' }}
        </div>

        <div class="cert-body">
            Dengan ini menyatakan bahwa,<br>
            <span style="font-style: italic; font-size: 9.5pt; color: #64748b;">This is to certify that,</span>

            <div class="cert-name">{{ $application->user->name ?? '-' }}</div>
            <div class="cert-nik">NIK: {{ $application->user->asesiProfile->nik ?? '-' }} | No. Reg: {{ $application->nomor_permohonan ?? '-' }}</div>

            Telah kompeten pada kualifikasi / bidang profesi:<br>
            <span style="font-style: italic; font-size: 9.5pt; color: #64748b;">Is competent in the qualification / profession of:</span>

            <div class="cert-scheme">{{ $application->scheme->nama_skema ?? '-' }}</div>
            <div class="cert-scheme-en">Scheme Code: {{ $application->scheme->kode_skema ?? '-' }} ({{ $application->scheme->standar ?? 'SKKNI' }})</div>
        </div>

        <div class="cert-footer">
            <table>
                <tr>
                    <td>
                        <div style="font-size: 8.5pt; color: #475569;">
                            Tanggal Terbit / <em>Date of Issue</em>: {{ \Carbon\Carbon::parse($application->tgl_terbit_sertifikat ?? now())->isoFormat('D MMMM Y') }}<br>
                            Berlaku s/d / <em>Valid Until</em>: {{ \Carbon\Carbon::parse($application->masa_berlaku_sertifikat ?? now()->addYears(3))->isoFormat('D MMMM Y') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 9pt; font-weight: bold;">
                            Atas Nama Badan Nasional Sertifikasi Profesi<br>
                            {{ $lsp->nama_lsp ?? 'Lembaga Sertifikasi Profesi' }}
                        </div>
                        <div style="height: 45px; margin: 4px 0;">
                            @if(!empty($lsp->ttd_ketua) && file_exists(public_path('storage/' . $lsp->ttd_ketua)))
                                <img src="{{ public_path('storage/' . $lsp->ttd_ketua) }}" style="max-height: 45px;" alt="TTD">
                            @endif
                        </div>
                        <div style="font-weight: bold; font-size: 10pt; text-decoration: underline;">
                            {{ $lsp->nama_ketua ?? 'Ketua LSP' }}
                        </div>
                        <div style="font-size: 8pt; color: #64748b;">Ketua / Direktur LSP</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- HALAMAN 2: DAFTAR UNIT KOMPETENSI (TRANSKRIP) -->
    <div class="page-break"></div>

    <div class="cert-border">
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="font-size: 13pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">
                DAFTAR UNIT KOMPETENSI
            </div>
            <div style="font-size: 10pt; font-style: italic; color: #475569;">
                LIST OF COMPETENCY UNITS
            </div>
            <div style="font-size: 9pt; color: #334155; margin-top: 4px;">
                Pemegang Sertifikat: <strong>{{ $application->user->name }}</strong> (No. Reg: {{ $application->nomor_permohonan }})
            </div>
        </div>

        <table class="unit-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 150px;">Kode Unit / <em>Unit Code</em></th>
                    <th>Judul Unit Kompetensi / <em>Title of Competency Unit</em></th>
                    <th style="width: 90px;">Standar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($application->scheme->units ?? [] as $idx => $u)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $u->kode_unit }}</td>
                        <td>{{ $u->judul_unit }}</td>
                        <td style="text-align: center;">{{ $u->standar ?? 'SKKNI' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right; padding-right: 20px;">
            <div style="font-size: 8.5pt;">Ditetapkan di: {{ $lsp->alamat ? explode(',', $lsp->alamat)[0] : 'Jakarta' }}</div>
            <div style="font-size: 8.5pt;">Tanggal: {{ \Carbon\Carbon::parse($application->tgl_terbit_sertifikat ?? now())->isoFormat('D MMMM Y') }}</div>
            <div style="height: 40px;"></div>
            <div style="font-weight: bold; font-size: 9.5pt; text-decoration: underline;">{{ $lsp->nama_ketua ?? 'Ketua LSP' }}</div>
            <div style="font-size: 8pt; color: #64748b;">Direktur Pelaksana LSP</div>
        </div>
    </div>

</body>
</html>
