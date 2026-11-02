@extends('layouts.print-muk')

@section('title', 'FR.IA.02 Tugas Praktik Demonstrasi - ' . ($asesi->full_name ?? ''))

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
        font-size: 10pt;
        line-height: 1.18;
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
    .kop-table {
        width: 174.5mm;
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
        width: 140.0mm;
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
        width: 110.0mm;
        border-collapse: collapse;
        margin-bottom: 2.5mm;
        font-size: 10pt;
    }
    .meta-table td {
        padding: 0.5mm 1.0mm;
        vertical-align: top;
    }
    .meta-table td.col-left {
        width: 40.0mm;
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
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
        font-size: 9.5pt;
    }
    .content-table th, .content-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.2mm;
        vertical-align: middle;
    }
    .content-table th {
        font-weight: bold;
        text-align: center;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
</style>

{-- ==================== PAGE 1 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="doc-main-title">FR.IA.02. TPD - TUGAS PRAKTIK DEMONSTRASI</div>

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
        <tr>
            <td class="col-left">TUK</td>
            <td colspan="3">: Sewaktu/Tempat Kerja/Mandiri*</td>
        </tr>
        <tr>
            <td class="col-left">Nama Asesor</td>
            <td colspan="3">: {{ $leadAssessor->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-left">Nama Asesi</td>
            <td colspan="3">: {{ $asesi->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-left">Tanggal</td>
            <td colspan="3">: {{ $assessmentDate ?? '05 Januari 2026' }}</td>
        </tr>
    </table>
    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 2.0mm;">*Coret yang tidak perlu</div>

    <div style="font-weight: bold; margin-bottom: 1.0mm;">A. Petunjuk</div>
    <ol style="margin: 0 0 2.0mm 5.0mm; padding: 0;">
        <li>Baca dan pelajari setiap instruksi kerja di bawah ini dengan cermat sebelum melaksanakan praktek</li>
        <li>Klarifikasi kepada asesor kompetensi apabila ada hal-hal yang belum jelas</li>
        <li>Laksanakan pekerjaan sesuai dengan urutan proses yang sudah ditetapkan</li>
        <li>Seluruh proses kerja mengacu kepada SOP/WI yang dipersyaratkan (Jika Ada)</li>
    </ol>

    <div style="font-weight: bold; margin-bottom: 1.5mm;">B. Skenario Tugas Praktik Demonstrasi</div>

    {{-- Kelompok 1 Table --}}
    <table class="content-table" style="margin-bottom: 2.0mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 102.5mm;">
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

    <div style="font-size: 9.5pt; line-height: 1.2;">
        <strong>Skenario Tugas Praktik Demonstrasi:</strong><br>
        <strong>S.</strong> Anda seorang asesi mengajukan permohonan uji kompetensi Mekanik Junior dengan persyaratan memiliki :<br>
        - Ijazah SMK otomotif; atau<br>
        - Surat Keterangan Peserta didik SMK Jurusan Otomotif yang telah menyelesaikan mata pelajaran bidang Mekanik Junior sesuai unit kompetensi skema dengan dibuktikan nilai raport semester 1-4 dan fotocopy Surat Keterangan/Sertifikat mengikuti prakerin; atau<br>
        - Ijazah SMA sederajat dan fotocopy sertifikat pelatihan berbasis kompetensi Mekanik Junior dari lembaga pelatihan kerja yang Terakreditasi/Kredibel; atau<br>
        - Surat keterangan pengalaman kerja sesuai bidang Mekanik Junior minimal 1 tahun.<br>
        <strong>T.</strong> Anda diminta mendemonstrasikan atau Mempraktekan
    </div>
</div>

{-- ==================== PAGE 2 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div style="font-size: 9.5pt; line-height: 1.2; margin-bottom: 2.5mm;">
        − &nbsp;Kelompok Pekerjaan Engine,<br><br>
        <strong>A.</strong> Untuk melaksakanakan pekerjaan tersebut kepada anda diberikan :<br>
        Report sheet, Repair Manual ,SOP yang meliputi Penerapan K3, persiapan dan pengoperasian<br>
        <strong>Perlengkapan dan Peralatan :</strong><br>
        <strong>Peralatan :</strong> Peralatan tangan atau hand tool, Alat bertenaga listrik dan pneumatis,Diagnostic tool,Peralatan khusus atau special tool,Alat ukur dan alat uji kelistrikan, Alat pembersih Peralatan tangan/hand tool,Two post lift (jika ada),Peralatan khusus/special tool,Pompa pengganti pelumas Corong pengisi cairan (air dan minyak pelumas),Gelas ukur (untuk minyak pelumas) Peralatan tangan/hand tool,Two post lift (jika ada),<br>
        <strong>Perlengkapan :</strong> Oli pelumas,Kain lap/majun,pembersih karburator/sistem injeksi,Cairan pendingin (coolant),Grease,Bak penampung pelumas,Saringan udara, saringan bensin, saringan oli dan tali kipas, ring baut oil drainer.<br>
        <strong>R.</strong> Hasil dari pekerjaan tersebut performa engine meningkat.<br>
        <strong>Waktu :</strong> 30 Menit
    </div>

    {{-- Kelompok 2 Table --}}
    <table class="content-table" style="margin-bottom: 2.0mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 102.5mm;">
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
            <tr><td class="text-center">1.</td><td class="nowrap">G.45TSM01.019.2</td><td>Melakukan Perawatan Sistem Injeksi</td></tr>
            <tr><td class="text-center">2.</td><td class="nowrap">G.45TSM01.053.2</td><td>Melakukan Pembersihan Injector</td></tr>
            <tr><td class="text-center">3.</td><td class="nowrap">G.45TSM01.055.2</td><td>Mengoperasikan diagnostic tools</td></tr>
        </tbody>
    </table>

    <div style="font-size: 9.5pt; line-height: 1.2;">
        <strong>Skenario Tugas Praktik Demonstrasi:</strong><br>
        <strong>S.</strong> Anda seorang asesi mengajukan permohonan uji kompetensi Mekanik Junior dengan persyaratan memiliki :<br>
        - Ijazah SMK otomotif; atau<br>
        - Surat Keterangan Peserta didik SMK Jurusan Otomotif yang telah menyelesaikan mata pelajaran bidang Mekanik Junior sesuai unit kompetensi skema dengan dibuktikan nilai raport semester 1-4 dan fotocopy Surat Keterangan/Sertifikat mengikuti prakerin; atau<br>
        - Ijazah SMA sederajat dan fotocopy sertifikat pelatihan berbasis kompetensi Mekanik Junior dari lembaga pelatihan kerja yang Terakreditasi/Kredibel; atau<br>
        - Surat keterangan pengalaman kerja sesuai bidang Mekanik Junior minimal 1 tahun.<br>
        <strong>T.</strong> Anda diminta mendemonstrasikan atau Mempraktekan<br>
        − &nbsp;Kelompok Pekerjaan Fuel injection service<br>
        <strong>A.</strong> Untuk melaksakanakan pekerjaan tersebut kepada anda diberikan :<br>
        Report sheet, Repair Manual ,SOP yang meliputi Penerapan K3, persiapan dan pengoperasian<br>
        <strong>Perlengkapan dan Peralatan :</strong><br>
        <strong>Peralatan :</strong> Peralatan tangan atau hand tool, Alat bertenaga listrik dan pneumatis,Diagnostic tool,Peralatan khusus atau special tool,Alat ukur dan alat uji kelistrikan, Alat pembersih Peralatan tangan/hand tool,Two post lift (jika ada),Peralatan khusus/special tool,Pompa pengganti pelumas Corong pengisi cairan (air dan minyak pelumas),Gelas ukur (untuk minyak pelumas) Peralatan tangan/hand tool,Two post lift (jika ada),
    </div>
</div>

{-- ==================== PAGE 3 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div style="font-size: 9.5pt; line-height: 1.2; margin-bottom: 2.5mm;">
        <strong>Perlengkapan :</strong> Oli pelumas,Kain lap/majun, injektion cleaner.<br>
        <strong>R.</strong> Hasil dari pekerjaan tersebut Fuel injection berfungsi normal .<br>
        <strong>Waktu :</strong> 20 Menit
    </div>

    {{-- Kelompok 3 Table --}}
    <table class="content-table" style="margin-bottom: 2.0mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 102.5mm;">
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

    <div style="font-size: 9.5pt; line-height: 1.2;">
        <strong>Skenario Tugas  Praktik Demonstrasi:</strong><br>
        <strong>S.</strong> Anda seorang asesi mengajukan permohonan uji kompetensi Mekanik Junior dengan persyaratan memiliki :<br>
        - Ijazah SMK otomotif; atau<br>
        - Surat Keterangan Peserta didik SMK Jurusan Otomotif yang telah menyelesaikan mata pelajaran bidang Mekanik Junior sesuai unit kompetensi skema dengan dibuktikan nilai raport semester 1-4 dan fotocopy Surat Keterangan/Sertifikat mengikuti prakerin; atau<br>
        - Ijazah SMA sederajat dan fotocopy sertifikat pelatihan berbasis kompetensi Mekanik Junior dari lembaga pelatihan kerja yang Terakreditasi/Kredibel; atau<br>
        - Surat keterangan pengalaman kerja sesuai bidang Mekanik Junior minimal 1 tahun.<br>
        <strong>T.</strong> Anda diminta mendemonstrasikan atau Mempraktekan<br>
        − &nbsp;Kelompok Pekerjaan  Electrik<br>
        <strong>A.</strong> Untuk melaksakanakan pekerjaan tersebut kepada anda diberikan :<br>
        Report sheet, Repair Manual ,SOP yang meliputi Penerapan K3, persiapan dan pengoperasian<br>
        <strong>Perlengkapan dan Peralatan :</strong><br>
        <strong>Peralatan :</strong> Peralatan tangan atau hand tool, Alat bertenaga listrik dan pneumatis,Diagnostic tool,Peralatan khusus atau special tool,Alat ukur dan alat uji kelistrikan, Alat pembersih Peralatan tangan/hand tool,Two post lift (jika ada),Peralatan khusus/special tool,Pompa pengganti pelumas Corong pengisi cairan (air dan minyak pelumas),Gelas ukur (untuk minyak pelumas) Peralatan tangan/hand tool,Two post lift (jika ada),<br>
        <strong>Perlengkapan :</strong> Kain lap/majun ,WD 40, komponen electric cleaner<br>
        <strong>R.</strong> Hasil dari pekerjaan tersebut electric system berfungsi normal.<br>
        <strong>Waktu :</strong> 20 Menit
    </div>
</div>

{-- ==================== PAGE 4 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    {{-- Kelompok 4 Table --}}
    <table class="content-table" style="margin-bottom: 2.0mm;">
        <colgroup>
            <col style="width: 30.0mm;">
            <col style="width: 10.0mm;">
            <col style="width: 32.0mm;">
            <col style="width: 102.5mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="8" style="vertical-align: middle; text-align: center; font-weight: bold;">
                    Kelompok Pekerjaan 4<br><br>Chassis
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

    <div style="font-size: 9.5pt; line-height: 1.2;">
        <strong>Skenario Tugas Praktik Demonstrasi:</strong><br>
        <strong>S.</strong> Anda seorang asesi mengajukan permohonan uji kompetensi Mekanik Junior dengan persyaratan memiliki :<br>
        - Ijazah SMK otomotif; atau<br>
        - Surat Keterangan Peserta didik SMK Jurusan Otomotif yang telah menyelesaikan mata pelajaran bidang Mekanik Junior sesuai unit kompetensi skema dengan dibuktikan nilai raport semester 1-4 dan fotocopy Surat Keterangan/Sertifikat mengikuti prakerin; atau<br>
        - Ijazah SMA sederajat dan fotocopy sertifikat pelatihan berbasis kompetensi Mekanik Junior dari lembaga pelatihan kerja yang Terakreditasi/Kredibel; atau<br>
        - Surat keterangan pengalaman kerja sesuai bidang Mekanik Junior minimal 1 tahun.<br>
        <strong>T.</strong> Anda diminta mendemonstrasikan atau Mempraktekan<br>
        − &nbsp;Kelompok Pekerjaan Chassis<br>
        <strong>A.</strong> Untuk melaksakanakan pekerjaan tersebut kepada anda diberikan :<br>
        Report sheet, Repair Manual ,SOP yang meliputi Penerapan K3, persiapan dan pengoperasian<br>
        <strong>Perlengkapan dan Peralatan :</strong><br>
        <strong>Peralatan :</strong> Peralatan tangan atau hand tool, Alat bertenaga listrik dan pneumatis,Diagnostic tool,Peralatan khusus atau special tool,Alat ukur dan alat uji kelistrikan, Alat pembersih Peralatan tangan/hand tool,Two post lift (jika ada),Peralatan khusus/special tool,Pompa pengganti pelumas Corong pengisi cairan (air dan minyak pelumas),Gelas ukur (untuk minyak pelumas) Peralatan tangan/hand tool,Two post lift (jika ada),
    </div>
</div>

{-- ==================== PAGE 5 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div style="font-size: 9.5pt; line-height: 1.2; margin-bottom: 3.0mm;">
        <strong>Perlengkapan :</strong> Penetrant , Kain lap/majun<br>
        <strong>R.</strong> Hasil dari pekerjaan tersebut Chasis kondisi normal.<br>
        <strong>Waktu :</strong> 20 Menit
    </div>

    <table class="content-table" style="width: 174.5mm; margin-bottom: 4.0mm;">
        <tr>
            <td style="width: 50%; padding: 2.0mm;">
                <strong>ASESI :</strong><br>
                Nama : {{ $asesi->full_name ?? '' }}<br>
                Tanda tangan/ Tanggal :
            </td>
            <td style="width: 50%; padding: 2.0mm;">
                <strong>ASESOR :</strong><br>
                Nama : {{ $leadAssessor->full_name ?? '' }}<br>
                No. Reg : {{ $leadAssessor->profile?->no_reg_asesor ?? '' }}<br>
                Tanda tangan/ Tanggal :
            </td>
        </tr>
    </table>

    <div class="doc-main-title" style="text-align: center; margin-bottom: 2.5mm;">
        PENYUSUN DAN VALIDATOR
    </div>

    <table class="content-table" style="width: 174.5mm;">
        <colgroup>
            <col style="width: 35.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 40.0mm;">
            <col style="width: 37.5mm;">
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

{-- ==================== PAGE 6 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div style="height: 100mm;"></div>
</div>
@endsection
