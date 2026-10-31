<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Dokumen Resmi LSP' }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-kop td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 70px;
            text-align: center;
        }
        .kop-text {
            text-align: center;
        }
        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-subtitle {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            margin: 2px 0;
        }
        .kop-address {
            font-size: 8pt;
            color: #555;
            margin: 0;
        }
        .doc-title-box {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .doc-number {
            font-size: 9pt;
            font-family: monospace;
            margin-top: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 7px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        table.clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 10px;
        }
        table.clean-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #555;
            border-radius: 3px;
        }
        .badge-k {
            background-color: #e6f7ec;
            color: #1e7e34;
            border-color: #1e7e34;
        }
        .badge-bk {
            background-color: #fde8e8;
            color: #c81e1e;
            border-color: #c81e1e;
        }
        .sign-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .sign-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .sign-space {
            height: 60px;
        }
        .sign-img {
            max-height: 60px;
            max-width: 150px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer-note {
            font-size: 7.5pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 4px;
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat LSP -->
    <div class="header-kop">
        <table>
            <tr>
                <td class="kop-logo">
                    @if(!empty($lsp->logo) && file_exists(public_path('storage/' . $lsp->logo)))
                        <img src="{{ public_path('storage/' . $lsp->logo) }}" style="max-height: 55px; max-width: 65px;" alt="Logo LSP">
                    @else
                        <div style="font-weight: bold; font-size: 14pt; border: 1.5px solid #000; padding: 6px 4px; border-radius: 4px;">BNSP</div>
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-title">{{ $lsp->nama_lsp ?? 'LEMBAGA SERTIFIKASI PROFESI' }}</div>
                    <div class="kop-subtitle">Lisensi BNSP No: {{ $lsp->no_lisensi_bnsp ?? 'BNSP-LSP-000-ID' }}</div>
                    <div class="kop-address">
                        {{ $lsp->alamat ?? 'Kantor LSP Pusat' }} | Telp: {{ $lsp->telepon ?? '-' }} | Email: {{ $lsp->email ?? 'info@lsp.id' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Document Content -->
    @yield('content')

    <!-- Footer Note -->
    <div class="footer-note">
        Dokumen Resmi Lembaga Sertifikasi Profesi • Dicetak secara digital pada {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
