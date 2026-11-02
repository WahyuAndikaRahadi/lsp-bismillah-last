@extends('layouts.print-muk')

@section('title', 'FR.IA.01B Report Sheet - ' . ($asesi->full_name ?? ''))

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
        font-size: 9pt;
        line-height: 1.12;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-sheet {
        width: 210mm;
        max-height: 295mm;
        box-sizing: border-box;
        padding: 5.0mm 15.0mm 5.0mm 25.9mm;
        page-break-after: always;
        position: relative;
    }
    .page-sheet:last-child {
        page-break-after: avoid;
    }
    .page-number {
        position: absolute;
        bottom: 8.0mm;
        left: 25.9mm;
        font-size: 10pt;
    }
    .kop-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
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
        line-height: 1.2;
        margin-bottom: 2.0mm;
    }
    .meta-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.5mm;
        font-size: 9pt;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.5mm 1.5mm;
        vertical-align: top;
    }
    .report-table {
        width: 174.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
        font-size: 8.5pt;
        line-height: 1.12;
    }
    .report-table th, .report-table td {
        border: 1pt solid #000;
        padding: 0.3mm 0.8mm;
        vertical-align: top;
    }
    .report-table th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        font-size: 8.5pt;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    .nowrap { white-space: nowrap; }
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
    <div class="doc-main-title">
        FR.IA.01B – Report Sheet<br>
        <span style="font-weight: normal; font-size: 10pt;">Lembar : Peserta</span>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 45.0mm; font-weight: bold;">Skema Sertifikasi/<br>Okupasi Nasional</td>
            <td style="width: 18.0mm;">Judul<br>Nomor</td>
            <td style="width: 4.0mm;">:<br>:</td>
            <td>MEKANIK JUNIOR<br>05/LSP PrO Indo/I/2026</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TUK</td>
            <td colspan="3">☐ Sewaktu &nbsp;&nbsp;&nbsp;&nbsp; ☐ Tempat Kerja &nbsp;&nbsp;&nbsp;&nbsp; ☐ Mandiri</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Asesor</td>
            <td colspan="3">: {{ $leadAssessor->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Asesi</td>
            <td colspan="3">: {{ $asesi->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Tanggal</td>
            <td colspan="3">: {{ $assessmentDate ?? '05 Januari 2026' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Durasi Waktu</td>
            <td colspan="3">60 Menit</td>
        </tr>
    </table>

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>No <br>Kode dan Judul <br>Unit Kompetensi</td>
                <td>Item dan Hasil Pemeriksaan <br>(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4">Baik 
Tidak 
Baik 
Kelompok Pekerjaan : Persiapan Servis</td>
            </tr>
            <tr>
                <td>1. <br>G.45TSM01.018.2 <br>Melakukan <br>Perawatan <br>Karburator</td>
                <td>Lakukan Perawatan Karburator (Gunakan Buku Pedoman Reparasi) <br>- Persiapan &amp; K3                Wearpack, Sepatu, Alat disiapkan <br>- Melepas Karburator <br>Buka jok/bodi, melepas kabel gas, selang  <br>                                             bensin, intake manifold <br>- Membongkar                     Buka mangkuk karbu, main jet, pilot jet, <br>  Karburator <br>                pelampung, jarum pelampung, skep (throttle  <br>                                             valve) <br>- Pembersihan <br>               Bersihkan semua komponen dengan carb  <br>                                            cleaner dan udara bertekanan. Pastikan  <br>                                            saluran tidak tersumbat. <br>- Pemeriksaan                    Kondisi jarum pelampung (aus/tidak): <br>  Komponen <br>               Kondisi karet seal/paking: <br>                                             Kondisi main/pilot jet (sumbat/tidak):<br>Kondisi skep/piston valve (baret/tidak) <br>- Perakitan Kembali           Rakit komponen sesuai urutan (pastikan seal  <br>                                            tidak terjepit) <br>- Pemasangan <br>               Pasang kembali karburator ke mesin  <br>- Penyetelan                       Hidupkan mesin, atur stasioner (1400-1600  <br>  (Idle &amp; Mixture)                rpm), atur Hidupkan mesin, atur stasioner  <br>                                            (1400-1600 rpm), atur sekrup udara (putaran  <br>                                            standar)  <br>- Uji Fungsi (Final Test) <br>Pastikan putaran mesin stabil, respon gas baik, <br>                                             tidak ada bocor <br>- Pembersihan                     Membersihkan Alat dan area kerja <br>  Area Kerja</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
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

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2. <br>G.45TSM01.020.2 <br>Melakukan <br>perawatan renggang <br>klep</td>
                <td>Lakukan Perawatan Renggang Klep (Gunakan Buku Pedoman <br>Reparasi) <br>Periksa Celah Katup (Lakukan penyetelan jika diperlukan) <br>In <br>mm Ex  mm</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>3. <br>G.45TSM01.021.2 <br>Melakukan <br>perawatan free play <br>throttle gas</td>
                <td>Persiapan : <br>Pengecekan Tools &amp; Equipment <br>Kalibrasi peralatan <br>Pengecekan kebersihan<br>Lakukan Pemeriksaan Free Play Throttle Gas (Gunakan Buku <br>Pedoman Reparasi)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>Free Play <br>Throttle Gas <br>……… mm <br>Kelancaran <br>Gerak Kabel <br>Gas <br>4. <br>G.45TSM01.022.2 <br>Melakukan <br>perawatan filter <br>udara</td>
                <td>Lakukan Perawatan Filter Udara (Gunakan Buku Pedoman <br>Reparasi) <br>Identifikasi Jenis Filter Udara…….. <br>Prosedur Pembersihan Filter Udara <br>Penyetelan Putaran Stationer <br>(Type tertentu)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>5. <br>G.45TSM01.023.2 <br>Melakukan <br>Perawatan Sistem <br>Clucth / Kopling</td>
                <td>Lakukan Perawatan Unit Kopling Manual / Otomatik berikut <br>komponen-komponen Sistem Pengoperasiannya <br>Penggunaan Buku Bepoman Reparasi Merek /  <br>Type …. <br>Penggunaan peralatan kerja <br>Jarak main bebas tuas kopling (Type tertentu) <br>Periksa cara kerja perpindahan transmisi untuk type bebek</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>6. <br>G.45TSM01.024.2 <br>Melakukan <br>pemeriksaan dan <br>penggantian oli <br>mesin, oli transmisi, <br>dan filter oli</td>
                <td>Lakukan pemeriksaan dan penggantian oli mesin, oli transmisi, <br>dan filter oli (Gunakan Buku Pedoman Reparasi) <br>Oddo Meter .................. km <br>Hasil pemeriksaan oli mesin (lakukan penggantian jika sudah tidak <br>bagus) ……….. <br>Periksa Oli transmisi (lakukan penggantian jika sudah tidak bagus) <br>(Jika ada)…………………. <br>Periksa filter oli <br>(Type tertentu) <br>Kebocoran oli dipastikan tidak ada</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>7. <br>G.45TSM01.025.2 <br>Melakukan <br>Pemeriksaan Sistem <br>Pelumasan Mesin</td>
                <td>Lakukan Pemeriksaan Sistem Pelumasan (Gunakan Buku <br>Pedoman Reparasi) <br>Volume Oli………………….. <br>Warna Oli………………….. <br>Viskositas <br>(Kekentalan)………………. <br>Kontaminasi…………………………  <br>Periksa kebocoran pada mesin</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
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

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>8. <br>G.45TSM01.027.2 <br>Melakukan <br>Perawatan Sistem <br>Continuosly Variable <br>Tansmission (CVT)</td>
                <td>Lakukan Melakukan Perawatan Sistem Continuosly Variable <br>Tansmission (CVT) (Gunakan Buku Pedoman Reparasi) <br>Kondisi komponen : <br>                               Kondisi Baik         Perlu Perbaikan/Ganti <br>V-Belt                             [   ] <br>                [   ]<br>Roller                             [   ] <br>                [   ]<br>Rumah Roller <br>             [   ] <br>                [   ]<br>Kipas Pulley <br>             [   ] <br>                [   ]<br>Kampas Kopling              [   ] <br>                [   ]<br>Seal Oli                             [   ] <br>                [   ]<br>Kebersihan (Grease)       [   ] <br>                [   ]</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>9. <br>G.45TSM01.028.2 <br>Melakukan <br>Perawatan Sistem <br>Pendingin</td>
                <td>Lakukan Pemeliharaan Sistim Pendingin berikut komponen- <br>komponennya (Gunakan Buku Pedoman Reparasi) <br>Kipas, rusuk pendingin<br>Tekanan / kebocoran radiator  (jika ada)<br>Tutup Radiator <br>(jika ada)<br>Level Reservoir <br>(jika ada)<br>Kondisi Baut, Klem pada sistim pendingin sesuai standar<br>Kondisi Air Pendingin <br>(Jika ada)<br>Kondisi Thermostat <br>(Jika ada)<br>Kondisi Water Pump <br>    (Jika ada)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4">Kelompok Pekerjaan: Fuel injection service</td>
            </tr>
            <tr>
                <td>10. G.45TSM01.019.2 <br>Melakukan <br>perawatan sistem <br>injeksi</td>
                <td>Lakukan Perawatan Sistem Bahan Bakar Injeksi (Gunakan Buku <br>Pedoman Reparasi) <br>Periksa Throttle Body<br>Periksa Injektor<br>Periksa sensor IAT,MAP, dan sensor O2<br>Periksa tekanan pompa bahan bakar Periksa kebocoran saluran bahan<br>bakar</td>
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

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>11. G.45TSM01.053.2 <br>Melakukan <br>Pembersihan Injector</td>
                <td>Lakukan Pembersihan Injector<br>Penggunaan Buku Pedoman Reparasi Merek /<br>Type ….<br>Penggunaan alat pembersih injector<br>Pembongkaran cover body dan fuel tank<br>Membersihkan Injector  (Type tertentu)<br>Hasil pembersihan injector berfungsi normal (Type tertentu)<br>Pastikan Kondisi Konektor Selang Injector normal<br>Pemasangan cover body dan  fuel tank<br>Prosedur K3 pada proses kerja diterapkan</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>12. G.45TSM01.055.2 <br>Mengoperasikan <br>diagnostic tools</td>
                <td>Gunakan diagnostic tools sesuai dengan prosedur <br>Lakukan Analisis Data Dinamis (Live Data Stream) <br>Pemeriksaan Putaran Mesin <br>Rpm <br>Throttle Position Sensor kondisi terbuka <br> : ………………….<br>Throttle Position Sensor kondisi tertutup <br> : ………………….<br>Injector                                                                : ………………….<br>Engine Oil / Coolent Temperatur Sensor <br> : …………………<br>O2 Sensor <br>                                               : …………………<br>Crankshaft Position Sensor <br> : ………………….<br>Intake Air Temperature Sensor (type tertentu) : ………………….<br>Manifold Absolute Pressure (type tertentu)      : ………………….<br>Ignition Coil (type tertentu)                                 : ………………….<br>Kondisi ISC (type tertentu)                                 : ………………….<br>Ketinggian / Altitude (type tertentu)                  : ………………….<br>Pemeriksaan CO (type tertentu)                        : …………………</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4">Kelompok Pekerjaan: Electrik</td>
            </tr>
            <tr>
                <td>13. G.45TSM01.039.2 <br>Melakukan <br>perawatan baterai</td>
                <td>Periksa Kondisi Baterai (sesuai baterai pada unit kendaraan) <br>Kondisi terminal <br>Kondisi bodi <br>Tegangan ------------ Volt <br>Kesehatan baterai ................ % <br>Berat jenis ------------- (bila baterai basah) <br>Ketinggian air baterai (bila baterai basah)</td>
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

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>14. G.45TSM01.026.2 <br>Melakukan <br>Perawatan Busi</td>
                <td>Lakukan Perawatan Busi <br>Penggunaan Buku Pedoman Reparasi Merek /  <br>Type …. <br>a.  Celah Busi              : ..……   mm <br>b.  Tahanan Cup Busi : ..……. Ω / KΩ <br>c.  Kondisi Elektroda Busi : ………………………..</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>……………. <br>……………. <br>……………. <br>15. G.45TSM01.040.2 <br>Melakukan <br>perawatan <br>instrumen kelistrikan <br>(sistem sinyal dan <br>penerangan)</td>
                <td>Lakukan Perawatan Instrumen Sistem Kelistrikan ((Gunakan Buku <br>Pedoman Reparasi) <br>Lampu-lampu Indikator <br>(Kondisi) <br>Lampu Depan Jauh / Dekat <br>(Kondisi) <br>Lampu Sen <br>(Kondisi) <br>Lampu Rem <br>(Kondisi) <br>Lampu Belakang <br>(Kondisi) <br>Horn / Klakson <br>(Kondisi) <br>• <br>Saklar Sistem Lampu Penerangan dan tanda Isyarat (Kondisi)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4">Kelompok Pekerjaan Chassis</td>
            </tr>
            <tr>
                <td>16. G.45TSM01.029.2 <br>Melakukan <br>penggantian rantai <br>roda</td>
                <td></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>…………….. <br>…………….. <br>…………….. <br>…………….. <br>17 <br>G.45TSM01.030.2 <br>Melakukan <br>pemeriksaan <br>keolengan roda</td>
                <td>Lakukan Penggantian Rantai Roda (Gunakan Buku Pedoman <br>Reparasi) <br>Penyetelan jarak main bebas rantai roda ............... mm <br>Kondisi Rantai Roda (ganti rantai roda jika diperlukan) <br>Kondisi Gear Sprocket Set (Ganti gear sprocket set jika diperlukan) <br>Pelumas rantai roda menggunakan SAE : …….<br>Lakukan Pemeriksaan Keolengan Roda(Gunakan Buku Pedoman <br>Reparasi) <br>Keolengan Roda Depan mm <br>Keolengan Roda Belakang …………………mm</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>18. G.45TSM01.031.2 <br>Melakukan <br>perawatan sistem <br>pengereman</td>
                <td>Lakukan Perawatan Sistem Pengereman (Gunakan Buku <br>Pedoman Reparasi) <br>Jenis Rem Cakram (Depan dan Belakang)<br>• <br>Kinerja Rem <br>[ ] Pakem [ ] Keras <br>[ ] Terlalu Dalam <br>• <br>Level Minyak Rem(Cakram): [ ] Lower. <br>[ ] Upper <br>• <br>Jarak Main Bebas Pedal/Tuas: .............. mm <br>• <br>Ketebalan Kanvas Rem/Pad set .............. mm <br>Jenis Rem Tromol(Depan dan Belakang)<br>• <br>Jarak Main Bebas Pedal/Tuas: ..............mm <br>• <br>Ketebalan Kanvas Rem/Pad set .............. mm</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>19. G.45TSM01.032.2 <br>Melakukan <br>perawatan sistem <br>kemudi</td>
                <td>Lakukan Pemeriksaan Sistem Kemudi (Gunakan Buku Pedoman <br>Reparasi) <br>Periksa Gerak Stang Sistem Kemudi <br>Periksa Gerak Poros Kemudi <br>Periksa Gerak Poros Sistem Kemudi</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>…………… <br>…………… <br>…………… <br>20. G.45TSM01.033.2 <br>Melakukan <br>perawatan sistem <br>suspensi</td>
                <td>Lakukan Perawatan Sistem Suspensi (Gunakan Buku Pedoman <br>Reparasi) <br>Depan <br>Periksa inner tube/as shock <br> [ ] Baik <br> [ ] Baret/Bengkok <br>Periksa seal shock. <br>[ ] Baik <br>[ ] Bocor/Getas <br>Periksa kinerja suspensi <br>Belakang</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
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

    <table class="report-table">
        <colgroup>
            <col style="width: 40.8mm;">
            <col style="width: 103.2mm;">
            <col style="width: 15.8mm;">
            <col style="width: 14.6mm;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No Kode dan Judul<br>Unit Kompetensi</th>
                <th rowspan="2">Item dan Hasil Pemeriksaan<br><span style="font-weight: normal; font-size: 7.5pt;">(item apa saja yang perlu dilakukan pemeriksaan dan lihat manual perbaikan)</span></th>
                <th colspan="2">Hasil</th>
            </tr>
            <tr>
                <th>Baik</th>
                <th>Tidak<br>Baik</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>21. G.45TSM01.034.2 <br>Melakukan bongkar <br>pasang cover body</td>
                <td>Pelepasan Cover Body: <br>Identifikasi Jenis Pengikat baut, sekrup, dan plastic clip (rivet) <br>Lepas Cover Body Sesuai dengan SOP (Sesuai dengan kebutuhan)<br>Pemasangan Cover Body: <br>Bersihkan Jalur Pengait dan Pengikat Identifikasi Jenis Pengikat baut, <br>sekrup, dan plastic clip (rivet) <br>Pasang Cover Body Sesuai dengan SOP (Pastikan sambungan antar <br>bodi rapat dan tidak ada bagian yang goyang)</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>22. G.45TSM01.043.2 <br>Melakukan <br>Pemeriksaan <br>Chassis</td>
                <td>Lakukan Pemeriksaan Chassis (Gunakan Buku Pedoman Reparasi)<br>− Lakukan inspeksi visual menyeluruh pada rangka (frame) utama.<br>Periksa adanya retak, bengkok, bekas las yang tidak standar, atau <br>korosi parah. <br>− Periksa semua dudukan mesin (engine mounting) dan baut<br>pengikatnya. Pastikan terpasang kencang dan tidak ada keretakan <br>di sekitar area tersebut. <br>− Periksa fungsi dan kondisi standar samping dan standar tengah.<br>Pastikan pegas pengembali bekerja dengan baik. <br>− Periksa kondisi rantai dan sprocket (sistem penggerak akhir).</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>..........................................</td>
                <td></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>..........................................</td>
                <td></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>..........................................</td>
                <td></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        </tbody>
    </table>
    <div style="border: 1pt solid #000; padding: 2.0mm 3.0mm; font-size: 8.5pt; margin-top: 2.0mm;">
        <strong>Catatan Hasil Pemeriksaan :</strong>
        <div style="height: 5.0mm;"></div>
        <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%;">
                    Nama Asesi :<br>
                    <strong>{{ $asesi->full_name ?? '..........................................' }}</strong><br><br>
                    Ttd & Tanggal :<br>
                    ..........................................
                </td>
                <td style="width: 50%;">
                    Nama Asesor :<br>
                    <strong>{{ $leadAssessor->full_name ?? '..........................................' }}</strong><br><br>
                    Ttd & Tanggal :<br>
                    ..........................................
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
