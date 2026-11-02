@extends('layouts.print-muk')

@section('title', 'FR.MAPA.01 Merencanakan Aktivitas dan Proses Asesmen - ' . ($scheme->name ?? ''))

@section('content')
<style>
    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Calibri, 'Carlito', sans-serif;
        font-size: 8pt;
        line-height: 1.15;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        max-height: 295mm;
        box-sizing: border-box;
        padding: 5.0mm 15.0mm 5.0mm 25.5mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .page-number {
        position: absolute;
        bottom: 8.0mm;
        left: 25.5mm;
        font-size: 8pt;
    }
    .kop-table {
        width: 176.4mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 34.5mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 30mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 141.9mm;
        text-align: center;
        font-family: Tahoma, sans-serif;
        font-size: 11pt;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.25;
        margin-bottom: 2.5mm;
    }
    .meta-table {
        width: 106.0mm;
        border-collapse: collapse;
        margin-bottom: 2.5mm;
        font-size: 8pt;
    }
    .meta-table td {
        padding: 0.5mm 1.0mm;
        vertical-align: top;
    }
    .meta-table td.col-left {
        width: 38.0mm;
        font-weight: bold;
    }
    .meta-table td.col-mid {
        width: 16.0mm;
    }
    .meta-table td.col-colon {
        width: 4.0mm;
        text-align: center;
    }
    .meta-table td.col-val {
        font-weight: normal;
    }
    .content-table {
        width: 176.4mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
        font-size: 9.5pt;
    }
    .content-table th, .content-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.2mm;
        vertical-align: top;
    }
    .content-table th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
    .matrix-table {
        width: 176.4mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
        font-size: 8pt;
        line-height: 1.1;
    }
    .matrix-table th, .matrix-table td {
        border: 1pt solid #000;
        padding: 0.5mm 0.8mm;
        vertical-align: top;
    }
    .matrix-table th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        font-size: 8pt;
    }
</style>

{-- ==================== PAGE 1 ==================== --}
<div class="page-sheet">
    <div class="page-number">1</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="doc-main-title">FR.MAPA.01- MERENCANAKAN AKTIVITAS DAN PROSES ASESMEN</div>
    <table class="meta-table">
        <tr>
            <td class="col-left" rowspan="2">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
            <td class="col-mid">Judul</td>
            <td class="col-colon">:</td>
            <td class="col-val">{{ $scheme->name ?? 'Mekanik Junior' }}</td>
        </tr>
        <tr>
            <td class="col-mid">Nomor</td>
            <td class="col-colon">:</td>
            <td class="col-val">&nbsp;{{ $scheme->code ?? '05/LSP PrO Indo/I/2026' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <colgroup>
            <col style="width: 12.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 126.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="3" style="text-align: left; padding: 1.5mm 2.0mm; font-size: 8pt;">
                    1. &nbsp;Menentukan Pendekatan Asesmen
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-bold">1.1.</td>
                <td class="text-bold">Asesi</td>
                <td>
                    <div>☑ Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi</div>
                    <div>☐ Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.</div>
                    <div>☐ Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi</div>
                    <div>☐ Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</div>
                    <div>☐ Pelatihan / belajar mandiri atau otodidak.</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Tujuan<br>Asesmen</td>
                <td>
                    <div>☑ Sertifikasi</div>
                    <div>☐ Pengakuan Kompetensi Terkini (PKT)</div>
                    <div>☐ Rekognisi Pembelajaran Lampau</div>
                    <div>☐ Lainnya</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Konteks<br>Asesmen:<br>Lingkungan</td>
                <td>
                    ☐ Tempat kerja nyata &nbsp;&nbsp;&nbsp;&nbsp; ☑ Tempat kerja nsimulasi
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Peluang untuk<br>mengumpulkan<br>bukti dalam<br>sejumlah situasi</td>
                <td>
                    ☑ &nbsp;Tersedia &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Terbatas
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Hubungan antara<br>standar<br>kompetensi &nbsp;dan:</td>
                <td>
                    <div>☑ Bukti untuk mendukung asesmen : ☺  </div>
                    <div>☐ Aktivitas kerja di tempat kerja Asesi: ☺  </div>
                    <div>☐ Kegiatan Pembelajaran: ☺  </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Siapa yang<br>melakukan<br>asesmen/<br>RPL</td>
                <td>
                    <div>☑ Lembaga Sertifikasi LSP Praktisi Otomotif Indonesia</div>
                    <div>☐ Organisasi Pelatihan</div>
                    <div>☐ Asesor Perusahaan</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">Konfirmasi<br>dengan orang<br>yang relevan</td>
                <td>
                    <div>☑ Manajer sertifikasi</div>
                    <div>☐ Master Asesor / Master Trainer / Lead Asesor Kompetensi</div>
                    <div>☐ Manajer Pelatihan Lembaga Training terakreditasi / Lembaga Training terdaftar</div>
                    <div>☐ Manajer atau supervisor ditempat kerja</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 2 ==================== --}
<div class="page-sheet">
    <div class="page-number">2</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 12.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 126.4mm;">
        </colgroup>
        <tbody>
            <tr>
                <td class="text-bold">1.2</td>
                <td class="text-bold">Standar Industri<br>atau Tempat<br>Kerja</td>
                <td>
                    <div>☑ Standar Kompetensi: SKKNI Nomor 147 Tahun 2019</div>
                    <div>☐ Kriteria asesmen dari kurikulum pelatihan</div>
                    <div>☑ Spesifikasi kinerja suatu perusahaan atau industri: Buku Pedoman Reperasi</div>
                    <div>☐ Spesifikasi Produk:</div>
                    <div>☐ Pedoman khusus:</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="font-weight: bold; font-size: 8pt; margin-bottom: 1.5mm;">
        2. &nbsp;Mempersiapkan Rencana Asesmen
    </div>

    {-- Kelompok 1 Table --}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 104.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="10" style="vertical-align: middle; text-align: center; font-weight: bold;">
                    Kelompok Pekerjaan 1<br><br>Engine
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-center">1.</td><td class="nowrap">G.45TSM01.018.2</td><td>Melakukan Perawatan Karburator</td></tr>
            <tr><td class="text-center">2.</td><td class="nowrap">G.45TSM01.020.2</td><td>Melakukan Perawatan Renggang Klep</td></tr>
            <tr><td class="text-center">3.</td><td class="nowrap">G.45TSM01.021.2</td><td>Melakukan Perawatan Free Play Throttle Gas</td></tr>
            <tr><td class="text-center">4.</td><td class="nowrap">G.45TSM01.022.2</td><td>Melakukan Perawatan Filter Udara</td></tr>
            <tr><td class="text-center">5.</td><td class="nowrap">G.45TSM01.023.2</td><td>Melakukan Perawatan Sistem Clucth / Kopling</td></tr>
            <tr><td class="text-center">6.</td><td class="nowrap">G.45TSM01.024.2</td><td>Melakukan pemeriksaan dan penggantian oli</td></tr>
            <tr><td class="text-center">7.</td><td class="nowrap">G.45TSM01.025.2</td><td>Melakukan pemeriksaan sistem pelumasan mesin</td></tr>
            <tr><td class="text-center">8.</td><td class="nowrap">G.45TSM01.027.2</td><td>Melakukan Perawatan Sistem Continuosly Variable Tansmission (CVT)</td></tr>
            <tr><td class="text-center">9.</td><td class="nowrap">G.45TSM01.028.2</td><td>Melakukan Perawatan Sistem Pendinginan</td></tr>
        </tbody>
    </table>

    
    <table class="matrix-table">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28.0mm;">Unit<br>Kompetensi</th>
                <th rowspan="2" style="width: 38.0mm;">Bukti-Bukti<br>(Kinerja, Produk,<br>Portofolio, dan /<br>atau<br>Pengetahuan)<br>diidentifikasi<br>berdasarkan<br>Kriteria Unjuk<br>Kerja dan<br>Pendekatan<br>Asesmen.</th>
                <th colspan="3">Jenis<br>Bukti</th>
                <th colspan="6">Metode dan Perangkat<br>Asesmen<br><span style="font-size: 8pt; font-weight: normal;">CL (Ceklis Observasi), DIT (Daftar Instruksi<br>Terstruktur), DPL (Daftar Pertanyaan Lisan), DPT<br>(Daftar Pertanyaan Tertulis), VPK (Verifikasi Pihak<br>Ketiga), CVP (Ceklis Verifikasi Portofolio), CRP (Ceklis<br>Reviu Produk), PW (Pertanyaan Wawancara)</span></th>
            </tr>
            <tr>
                <th>L</th>
                <th>TL</th>
                <th>T</th>
                <th>Obsevasi langsung<br><span style="font-size: 7.5pt; font-weight: normal;">(kerja nyata/aktivitas waktu nyata<br>di tempat kerja di kingkungan<br>tempat kerja yang disimulasikan)</span></th>
                <th>Kegiatan Terstruktur<br><span style="font-size: 7.5pt; font-weight: normal;">(latihan simulasi dan bermain peran,<br>proyek, presentasi, lembar kegiatan)</span></th>
                <th>Tanya Jawab<br><span style="font-size: 7.5pt; font-weight: normal;">(pertanyaan tertulis, wawancara,<br>asesmen diri, tanya jawab lisan,<br>angket, ujian lisan atau tertulis)</span></th>
                <th>Verifikasi Portfolio<br><span style="font-size: 7.5pt; font-weight: normal;">(sampel pekerjaaan yang disusun oleh Asesi,<br>produk dengan dokumentasi pendukung,<br>bukti sejarah, jurnal atau buku catatan,<br>informasi tentang pengalaman hidup)</span></th>
                <th>Reviu Produk<br><span style="font-size: 7.5pt; font-weight: normal;">Produk hasil proyek, ciontoh hasil<br>kerja/produk</span></th>
                <th>Verifikasi Pihak Ketiga<br><span style="font-size: 7.5pt; font-weight: normal;">(testimoni dan laporan dari atasan, bukti<br>pelatihan, otentikasi pencapaian sebelumnya,<br>wawancara dengan atasan, atau rekan kerja)</span></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

{-- ==================== PAGE 3 ==================== --}
<div class="page-sheet">
    <div class="page-number">3</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="matrix-table">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <tbody>
            <tr>
                <td><strong>1. Melakukan Perawatan Karburator</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Karburator</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>2. Melakukan Perawatan Renggang Klep</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Renggang Klep</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>3. Melakukan Perawatan Free Play Throttle Gas</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Free Play Throttle Gas</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>4. Melakukan Perawatan Filter Udara</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Filter Udara</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>5. Melakukan Perawatan Sistem Clucth / Kopling</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Clucth / Kopling</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>6. Melakukan pemeriksaan dan penggantian oli</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan pemeriksaan dan penggantian oli</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>7. Melakukan pemeriksaan sistem pelumasan mesin</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan pemeriksaan sistem pelumasan mesin</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>8. Melakukan Perawatan Sistem Continuosly Variable Tansmission (CVT)</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Continuosly Variable Tansmission (CVT)</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 4 ==================== --}
<div class="page-sheet">
    <div class="page-number">4</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="matrix-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <tbody>
            <tr>
                <td><strong>9. Melakukan Perawatan Sistem Pendinginan</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Pendinginan</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>

    {-- Kelompok 2 Table --}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 104.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4" style="vertical-align: middle; text-align: center; font-weight: bold;">
                    Kelompok Pekerjaan 2<br><br>Fuel injection service
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-center">1</td><td class="nowrap">G.45TSM01.019.2</td><td>Melakukan Perawatan Sistem Injeksi</td></tr>
            <tr><td class="text-center">2</td><td class="nowrap">G.45TSM01.053.2</td><td>Melakukan Pembersihan Injector</td></tr>
            <tr><td class="text-center">3</td><td class="nowrap">G.45TSM01.055.2</td><td>Mengoperasikan diagnostic tools</td></tr>
        </tbody>
    </table>

    
    <table class="matrix-table">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28.0mm;">Unit<br>Kompetensi</th>
                <th rowspan="2" style="width: 38.0mm;">Bukti-Bukti<br>(Kinerja, Produk,<br>Portofolio, dan /<br>atau<br>Pengetahuan)<br>diidentifikasi<br>berdasarkan<br>Kriteria Unjuk<br>Kerja dan<br>Pendekatan<br>Asesmen.</th>
                <th colspan="3">Jenis<br>Bukti</th>
                <th colspan="6">Metode dan Perangkat<br>Asesmen<br><span style="font-size: 8pt; font-weight: normal;">CL (Ceklis Observasi), DIT (Daftar Instruksi<br>Terstruktur), DPL (Daftar Pertanyaan Lisan), DPT<br>(Daftar Pertanyaan Tertulis), VPK (Verifikasi Pihak<br>Ketiga), CVP (Ceklis Verifikasi Portofolio), CRP (Ceklis<br>Reviu Produk), PW (Pertanyaan Wawancara)</span></th>
            </tr>
            <tr>
                <th>L</th>
                <th>TL</th>
                <th>T</th>
                <th>Obsevasi langsung<br><span style="font-size: 7.5pt; font-weight: normal;">(kerja nyata/aktivitas waktu nyata<br>di tempat kerja di kingkungan<br>tempat kerja yang disimulasikan)</span></th>
                <th>Kegiatan Terstruktur<br><span style="font-size: 7.5pt; font-weight: normal;">(latihan simulasi dan bermain peran,<br>proyek, presentasi, lembar kegiatan)</span></th>
                <th>Tanya Jawab<br><span style="font-size: 7.5pt; font-weight: normal;">(pertanyaan tertulis, wawancara,<br>asesmen diri, tanya jawab lisan,<br>angket, ujian lisan atau tertulis)</span></th>
                <th>Verifikasi Portfolio<br><span style="font-size: 7.5pt; font-weight: normal;">(sampel pekerjaaan yang disusun oleh Asesi,<br>produk dengan dokumentasi pendukung,<br>bukti sejarah, jurnal atau buku catatan,<br>informasi tentang pengalaman hidup)</span></th>
                <th>Reviu Produk<br><span style="font-size: 7.5pt; font-weight: normal;">Produk hasil proyek, ciontoh hasil<br>kerja/produk</span></th>
                <th>Verifikasi Pihak Ketiga<br><span style="font-size: 7.5pt; font-weight: normal;">(testimoni dan laporan dari atasan, bukti<br>pelatihan, otentikasi pencapaian sebelumnya,<br>wawancara dengan atasan, atau rekan kerja)</span></th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td><strong>1. Melakukan Perawatan Sistem Injeksi</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Injeksi</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>2. Melakukan Pembersihan Injector</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Pembersihan Injector</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>3. Mengoperasikan diagnostic tools</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Mengoperasikan diagnostic tools</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 5 ==================== --}
<div class="page-sheet">
    <div class="page-number">5</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    {-- Kelompok 3 Table --}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 104.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4" style="vertical-align: middle; text-align: center; font-weight: bold;">
                    Kelompok Pekerjaan 3<br><br>Electrik
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-center">1.</td><td class="nowrap">G.45TSM01.039.2</td><td>Melakukan Perawatan Baterai</td></tr>
            <tr><td class="text-center">2.</td><td class="nowrap">G.45TSM01.026.2</td><td>Melakukan Perawatan Busi</td></tr>
            <tr><td class="text-center">3.</td><td class="nowrap">G.45TSM01.040.2</td><td>Melakukan perawatan instrumen kelistrikan (sistem sinyal dan penerangan)</td></tr>
        </tbody>
    </table>

    
    <table class="matrix-table">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28.0mm;">Unit<br>Kompetensi</th>
                <th rowspan="2" style="width: 38.0mm;">Bukti-Bukti<br>(Kinerja, Produk,<br>Portofolio, dan /<br>atau<br>Pengetahuan)<br>diidentifikasi<br>berdasarkan<br>Kriteria Unjuk<br>Kerja dan<br>Pendekatan<br>Asesmen.</th>
                <th colspan="3">Jenis<br>Bukti</th>
                <th colspan="6">Metode dan Perangkat<br>Asesmen<br><span style="font-size: 8pt; font-weight: normal;">CL (Ceklis Observasi), DIT (Daftar Instruksi<br>Terstruktur), DPL (Daftar Pertanyaan Lisan), DPT<br>(Daftar Pertanyaan Tertulis), VPK (Verifikasi Pihak<br>Ketiga), CVP (Ceklis Verifikasi Portofolio), CRP (Ceklis<br>Reviu Produk), PW (Pertanyaan Wawancara)</span></th>
            </tr>
            <tr>
                <th>L</th>
                <th>TL</th>
                <th>T</th>
                <th>Obsevasi langsung<br><span style="font-size: 7.5pt; font-weight: normal;">(kerja nyata/aktivitas waktu nyata<br>di tempat kerja di kingkungan<br>tempat kerja yang disimulasikan)</span></th>
                <th>Kegiatan Terstruktur<br><span style="font-size: 7.5pt; font-weight: normal;">(latihan simulasi dan bermain peran,<br>proyek, presentasi, lembar kegiatan)</span></th>
                <th>Tanya Jawab<br><span style="font-size: 7.5pt; font-weight: normal;">(pertanyaan tertulis, wawancara,<br>asesmen diri, tanya jawab lisan,<br>angket, ujian lisan atau tertulis)</span></th>
                <th>Verifikasi Portfolio<br><span style="font-size: 7.5pt; font-weight: normal;">(sampel pekerjaaan yang disusun oleh Asesi,<br>produk dengan dokumentasi pendukung,<br>bukti sejarah, jurnal atau buku catatan,<br>informasi tentang pengalaman hidup)</span></th>
                <th>Reviu Produk<br><span style="font-size: 7.5pt; font-weight: normal;">Produk hasil proyek, ciontoh hasil<br>kerja/produk</span></th>
                <th>Verifikasi Pihak Ketiga<br><span style="font-size: 7.5pt; font-weight: normal;">(testimoni dan laporan dari atasan, bukti<br>pelatihan, otentikasi pencapaian sebelumnya,<br>wawancara dengan atasan, atau rekan kerja)</span></th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td><strong>1. Melakukan Perawatan Baterai</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Baterai</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>2. Melakukan Perawatan Busi</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Busi</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>3. Melakukan perawatan instrumen kelistrikan (sistem sinyal dan penerangan)</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan perawatan instrumen kelistrikan (sistem sinyal dan penerangan)</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>

    <table class="content-table" style="margin-top: 2.5mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 104.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th style="vertical-align: middle; text-align: center; font-weight: bold;">Kelompok Pekerjaan 4</th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
    </table>
</div>

{-- ==================== PAGE 6 ==================== --}
<div class="page-sheet">
    <div class="page-number">6</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    {-- Kelompok 4 Table --}
    <table class="content-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 104.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="8" style="vertical-align: middle; text-align: center; font-weight: bold;">
                    Chassis
                </th>
                <th>No.</th>
                <th>Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-center">1.</td><td class="nowrap">G.45TSM01.029.2</td><td>Melakukan Penggantian Rantai Roda</td></tr>
            <tr><td class="text-center">2.</td><td class="nowrap">G.45TSM01.030.2</td><td>Melakukan Pemeriksaan Keolengan Roda</td></tr>
            <tr><td class="text-center">3.</td><td class="nowrap">G.45TSM01.031.2</td><td>Melakukan Perawatan Sistem Pengereman</td></tr>
            <tr><td class="text-center">4.</td><td class="nowrap">G.45TSM01.032.2</td><td>Melakukan Perawatan Sistem Kemudi</td></tr>
            <tr><td class="text-center">5.</td><td class="nowrap">G.45TSM01.033.2</td><td>Melakukan Perawatan Sistem Suspensi</td></tr>
            <tr><td class="text-center">6.</td><td class="nowrap">G.45TSM01.034.2</td><td>Melakukan Bongkar Pasang Cover Body</td></tr>
            <tr><td class="text-center">7.</td><td class="nowrap">G.45TSM01.043.2</td><td>Melakukan Pemeriksaan Chassis</td></tr>
        </tbody>
    </table>

    
    <table class="matrix-table">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28.0mm;">Unit<br>Kompetensi</th>
                <th rowspan="2" style="width: 38.0mm;">Bukti-Bukti<br>(Kinerja, Produk,<br>Portofolio, dan /<br>atau<br>Pengetahuan)<br>diidentifikasi<br>berdasarkan<br>Kriteria Unjuk<br>Kerja dan<br>Pendekatan<br>Asesmen.</th>
                <th colspan="3">Jenis<br>Bukti</th>
                <th colspan="6">Metode dan Perangkat<br>Asesmen<br><span style="font-size: 8pt; font-weight: normal;">CL (Ceklis Observasi), DIT (Daftar Instruksi<br>Terstruktur), DPL (Daftar Pertanyaan Lisan), DPT<br>(Daftar Pertanyaan Tertulis), VPK (Verifikasi Pihak<br>Ketiga), CVP (Ceklis Verifikasi Portofolio), CRP (Ceklis<br>Reviu Produk), PW (Pertanyaan Wawancara)</span></th>
            </tr>
            <tr>
                <th>L</th>
                <th>TL</th>
                <th>T</th>
                <th>Obsevasi langsung<br><span style="font-size: 7.5pt; font-weight: normal;">(kerja nyata/aktivitas waktu nyata<br>di tempat kerja di kingkungan<br>tempat kerja yang disimulasikan)</span></th>
                <th>Kegiatan Terstruktur<br><span style="font-size: 7.5pt; font-weight: normal;">(latihan simulasi dan bermain peran,<br>proyek, presentasi, lembar kegiatan)</span></th>
                <th>Tanya Jawab<br><span style="font-size: 7.5pt; font-weight: normal;">(pertanyaan tertulis, wawancara,<br>asesmen diri, tanya jawab lisan,<br>angket, ujian lisan atau tertulis)</span></th>
                <th>Verifikasi Portfolio<br><span style="font-size: 7.5pt; font-weight: normal;">(sampel pekerjaaan yang disusun oleh Asesi,<br>produk dengan dokumentasi pendukung,<br>bukti sejarah, jurnal atau buku catatan,<br>informasi tentang pengalaman hidup)</span></th>
                <th>Reviu Produk<br><span style="font-size: 7.5pt; font-weight: normal;">Produk hasil proyek, ciontoh hasil<br>kerja/produk</span></th>
                <th>Verifikasi Pihak Ketiga<br><span style="font-size: 7.5pt; font-weight: normal;">(testimoni dan laporan dari atasan, bukti<br>pelatihan, otentikasi pencapaian sebelumnya,<br>wawancara dengan atasan, atau rekan kerja)</span></th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td><strong>1. Melakukan Penggantian Rantai Roda</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Penggantian Rantai Roda</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>2. Melakukan Pemeriksaan Keolengan Roda</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Pemeriksaan Keolengan Roda</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>3. Melakukan Perawatan Sistem Pengereman</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Pengereman</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>4. Melakukan Perawatan Sistem Kemudi</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Kemudi</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>
</div>

{-- ==================== PAGE 7 ==================== --}
<div class="page-sheet">
    <div class="page-number">7</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="matrix-table" style="margin-bottom: 2.5mm;">
        <colgroup>
            <col style="width: 28.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 6.0mm;">
            <col style="width: 15.5mm;">
            <col style="width: 15.5mm;">
            <col style="width: 16.5mm;">
            <col style="width: 15.0mm;">
            <col style="width: 14.5mm;">
            <col style="width: 15.4mm;">
        </colgroup>
        <tbody>
            <tr>
                <td><strong>5. Melakukan Perawatan Sistem Suspensi</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Perawatan Sistem Suspensi</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>6. Melakukan Bongkar Pasang Cover Body</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Bongkar Pasang Cover Body</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td><strong>7. Melakukan Pemeriksaan Chassis</strong></td>
                <td>Hasil observasi langsung dan tanya jawab tentang Melakukan Pemeriksaan Chassis</td>
                <td class="text-center font-bold">L</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">T</td>
                <td class="text-center font-bold">CL</td>
                <td class="text-center"></td>
                <td class="text-center font-bold">DPT</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>

    {-- Section 3 --}
    <table class="content-table" style="margin-bottom: 2.0mm;">
        <colgroup>
            <col style="width: 10.0mm;">
            <col style="width: 78.0mm;">
            <col style="width: 88.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="3" style="text-align: left; padding: 1.0mm 2.0mm; font-size: 8pt;">
                    3. &nbsp;Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi:
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-bold">3.1.</td>
                <td>a. Karakteristik Asesi:</td>
                <td>Ada / tidak ada* karakteristik khusus Asesi<br>Jika Ada, tuliskan........................</td>
            </tr>
            <tr>
                <td></td>
                <td>b. Kebutuhan kontekstualisasi terkait tempat kerja:</td>
                <td>Ada / tidak ada* kebutuhan kontekstualisasi Jika Ada, tuliskan........................</td>
            </tr>
            <tr>
                <td class="text-bold">3.2.</td>
                <td>Saran yang diberikan oleh paket pelatihan atau pengembang pelatihan</td>
                <td>Ada / tidak ada* saran<br>Jika Ada, tuliskan........................</td>
            </tr>
            <tr>
                <td class="text-bold">3.3.</td>
                <td>Penyesuaian perangkat asesmen terkait kebutuhan kontekstualisasi</td>
                <td>Ada / tidak ada* penyesuaian perangkat Jika Ada, tuliskan........................</td>
            </tr>
            <tr>
                <td class="text-bold">3.4.</td>
                <td>Peluang untuk kegiatan asesmen terintegrasi dan mencatat setiap perubahan yang diperlukan untuk alat asesmen</td>
                <td>Ada / tidak ada* peluang<br>Jika Ada, tuliskan........................</td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 7.5pt; font-style: italic; margin-bottom: 2.0mm;">*Coret yang tidak perlu</div>

    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 1.5mm;">Konfirmasi dengan orang yang relevan</div>
    <table class="content-table" style="margin-left: 25.0mm; width: 135.0mm;">
        <colgroup>
            <col style="width: 50.0mm;">
            <col style="width: 45.0mm;">
            <col style="width: 40.0mm;">
        </colgroup>
        <thead>
            <tr>
                <th>Orang yang relevan</th>
                <th>Nama</th>
                <th>Tandatangan dan Tanggal</th>
            </tr>
        </thead>
    </table>
</div>

{-- ==================== PAGE 8 ==================== --}
<div class="page-sheet">
    <div class="page-number">8</div>
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>

    <table class="content-table" style="width: 176.4mm; margin-bottom: 6.0mm;">
        <colgroup>
            <col style="width: 75.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 51.4mm;">
        </colgroup>
        <tbody>
            <tr>
                <td style="padding: 2.0mm;">Manajer sertifikasi LSP</td>
                <td style="padding: 2.0mm; font-weight: bold;">KARYANTO</td>
                <td class="text-center" style="vertical-align: middle;">27 Januari 2026</td>
            </tr>
            <tr>
                <td style="padding: 2.0mm;">Master Asesor / Master Trainer / Lead Asesor Kompetensi</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="padding: 2.0mm;">Manajer pelatihan Lembaga Training terakreditasi / Lembaga Training terdaftar</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="padding: 2.0mm;">Manajer atau supervisor ditempat kerja</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="doc-main-title" style="text-align: center; margin-bottom: 3.0mm;">
        PENYUSUN DAN VALIDATOR
    </div>

    <table class="content-table" style="width: 176.4mm;">
        <colgroup>
            <col style="width: 35.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 40.0mm;">
            <col style="width: 39.4mm;">
        </colgroup>
        <thead>
            <tr>
                <th>STATUS</th>
                <th>NO</th>
                <th>NAMA</th>
                <th>NOMOR MET</th>
                <th>TANDA TANGAN DAN TANGGAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="2" class="text-center text-bold" style="vertical-align: middle;">PENYUSUN</td>
                <td class="text-center">1</td>
                <td><strong>GUNADI</strong></td>
                <td>000.000459 2007</td>
                <td class="text-center">27 Januari 2026</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td><strong>SAPTONO</strong></td>
                <td>000.003672 2016</td>
                <td class="text-center">27 Januari 2026</td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center text-bold" style="vertical-align: middle;">VALIDATOR</td>
                <td class="text-center">1</td>
                <td><strong>RANDI ARDIANSYAH</strong></td>
                <td>000.002498 2013</td>
                <td class="text-center">27 Januari 2026</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td><strong>KARYANTO</strong></td>
                <td>000.008260 2025</td>
                <td class="text-center">27 Januari 2026</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td><strong>RIDWAN</strong></td>
                <td>000.008265 2025</td>
                <td class="text-center">27 Januari 2026</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
