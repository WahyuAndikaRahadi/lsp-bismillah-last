<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dokumen MUK') - LSP PrO Indo</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, "Liberation Sans", Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 14mm 10mm 14mm;
        }

        @media screen {
            body {
                background-color: #e2e8f0;
                padding-bottom: 50px;
            }
            .screen-toolbar {
                position: sticky;
                top: 0;
                z-index: 50;
                background: #0f172a;
                color: #fff;
                padding: 10px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            }
            .screen-toolbar a {
                color: #94a3b8;
                text-decoration: none;
                font-size: 13px;
                font-weight: 600;
            }
            .screen-toolbar a:hover {
                color: #fff;
            }
            .screen-toolbar button {
                background: #2563eb;
                color: #fff;
                border: none;
                padding: 7px 16px;
                font-size: 13px;
                font-weight: bold;
                border-radius: 6px;
                cursor: pointer;
                transition: background 0.15s;
            }
            .screen-toolbar button:hover {
                background: #1d4ed8;
            }
            .sheet {
                background: #ffffff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                margin: 20px auto;
                padding: 10mm 14mm;
                width: 210mm;
                min-height: 297mm;
            }
            .sheet.landscape {
                width: 297mm;
                min-height: 210mm;
                padding: 10mm 18mm;
            }
        }

        @media print {
            .no-print, .screen-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .sheet {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
            table {
                page-break-inside: auto;
                break-inside: auto;
            }
            thead {
                display: table-header-group !important;
            }
            tfoot {
                display: table-footer-group !important;
            }
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .page-break {
                page-break-after: always;
                break-after: page;
            }
            .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* Typography & Layout Helpers */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

        /* Standard Kop Surat Master */
        .master-kop {
            width: 100%;
            text-align: center;
            margin-bottom: 8px;
        }
        .master-kop img {
            width: 100%;
            max-width: 182mm;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Standard Master Grid Info Block */
        table.meta-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 8px;
        }
        table.meta-grid td {
            padding: 2px 3px;
            vertical-align: top;
        }
        table.meta-grid td.label {
            width: 28%;
            font-weight: normal;
        }
        table.meta-grid td.colon {
            width: 2%;
            text-align: center;
        }
        table.meta-grid td.value {
            width: 70%;
            font-weight: normal;
        }

        /* Standard BNSP Table */
        table.bnsp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin: 6px 0 10px 0;
        }
        table.bnsp-table th, table.bnsp-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        table.bnsp-table th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }

        /* Checkbox Box */
        .check-box {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid #000;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-right: 3px;
        }

        /* Signature block */
        .sig-container {
            width: 100%;
            margin-top: 12px;
            font-size: 10pt;
        }
        .sig-grid {
            display: table;
            width: 100%;
        }
        .sig-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
        }
        .sig-box {
            min-height: 55px;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        .sig-img {
            max-height: 55px;
            max-width: 180px;
            object-fit: contain;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="screen-toolbar no-print">
        <div>
            <a href="{{ route('admin.dokumen-muk.index') }}">&larr; Kembali ke Indeks Dokumen</a>
            <span style="margin: 0 10px; color: #475569;">|</span>
            <span style="font-size: 13px; font-weight: bold;">@yield('doc-title', 'Dokumen Cetak MUK')</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-size: 11px; color: #94a3b8;">Format: A4 Portrait</span>
            <button onclick="window.print()">Cetak Dokumen / Simpan PDF</button>
        </div>
    </div>

    <div class="sheet @yield('sheet-class')">
        @yield('content')
        @yield('print-content')
    </div>

    @stack('scripts')
</body>
</html>
