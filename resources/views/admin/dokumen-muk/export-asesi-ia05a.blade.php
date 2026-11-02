@extends('layouts.print-muk')

@section('title', 'FR.IA.05.A Lembar Soal Uji Tertulis - ' . ($asesi->full_name ?? ''))

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
        font-size: 9.5pt;
        line-height: 1.22;
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
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 2.0mm;
    }
    .kop-table td {
        border: 1pt solid #000;
        vertical-align: middle;
    }
    .kop-logo {
        width: 34.4mm;
        height: 17.6mm;
        text-align: center;
        padding: 0;
    }
    .kop-logo img {
        max-width: 30mm;
        max-height: 15mm;
    }
    .kop-text {
        width: 135.1mm;
        text-align: center;
        font-family: Tahoma, sans-serif;
        font-size: 11pt;
        font-weight: bold;
    }
    .doc-main-title {
        font-size: 11pt;
        font-weight: bold;
        line-height: 1.25;
        margin-bottom: 1.5mm;
    }
    .meta-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        margin-bottom: 1.5mm;
        font-size: 9.5pt;
    }
    .meta-table td {
        border: 1pt solid #000;
        padding: 0.6mm 1.5mm;
        vertical-align: top;
    }
    .line-item {
        font-size: 9.5pt;
        line-height: 1.22;
    }
    .q-start {
        margin-top: 1.2mm;
    }
    .opt-line {
        padding-left: 2.0mm;
    }
    .content-table {
        width: 169.5mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 9pt;
    }
    .content-table th, .content-table td {
        border: 1pt solid #000;
        padding: 0.8mm 1.2mm;
        vertical-align: middle;
    }
    .content-table th {
        font-weight: bold;
        text-align: center;
    }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
</style>

{-- ==================== PAGE 1 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="doc-main-title">FR.IA.05A. DPT - PERTANYAAN TERTULIS PILIHAN GANDA</div>

    <table class="meta-table">
        <colgroup>
            <col style="width: 45.7mm;">
            <col style="width: 18.2mm;">
            <col style="width: 4.6mm;">
            <col style="width: 101.0mm;">
        </colgroup>
        <tr>
            <td rowspan="2" style="font-weight: bold;">Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</td>
            <td style="font-weight: bold;">Judul</td>
            <td class="text-center">:</td>
            <td>&nbsp;{{ $scheme->name ?? 'Mekanik Junior' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor</td>
            <td class="text-center">:</td>
            <td>&nbsp;{{ $scheme->code ?? '05/LSP PrO Indo/I/2026' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TUK</td>
            <td colspan="3">: Sewaktu/Tempat Kerja/Mandiri*</td>
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
            <td colspan="3">: {{ $assessmentDate ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Waktu</td>
            <td colspan="3">:</td>
        </tr>
    </table>
    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 1.0mm;">*Coret yang tidak perlu</div>
    <div style="font-weight: bold; margin-bottom: 1.0mm; font-size: 9.5pt;">Jawab semua pertanyaan berikut:</div>
    <div class="line-item">Jawab semua pertanyaan berikut:</div>
    <div class="line-item q-start">1. Fungsi utama karburator pada mesin bensin adalah...</div>
    <div class="line-item opt-line">a) Mencampur bahan bakar dengan oli mesin.</div>
    <div class="line-item opt-line">b) Menyalurkan listrik ke busi.</div>
    <div class="line-item opt-line">c) Mencampur bahan bakar dan udara dengan perbandingan yang tepat sesuai kebutuhan mesin.</div>
    <div class="line-item opt-line">d) Mendinginkan suhu mesin saat bekerja.</div>
    <div class="line-item q-start">2. Apa yang terjadi jika setelan campuran udara dan bahan bakar (air-fuel mixture) pada karburator</div>
    <div class="line-item">terlalu kaya (terlalu banyak bensin)…</div>
    <div class="line-item opt-line">a) Mesin mudah panas dan busi berwarna putih bersih.</div>
    <div class="line-item opt-line">b) Mesin sulit hidup, knalpot mengeluarkan asap hitam pekat, dan konsumsi BBM boros.</div>
    <div class="line-item opt-line">c) Mesin mati mendadak saat digas penuh.</div>
    <div class="line-item opt-line">d) Putaran mesin (RPM) menjadi sangat tinggi secara terus-menerus.</div>
    <div class="line-item q-start">3. Komponen karburator yang berfungsi untuk menutup saluran udara masuk saat mesin dalam kondisi</div>
    <div class="line-item">dingin agar mudah dihidupkan adalah...</div>
    <div class="line-item opt-line">a) Pilot jet</div>
    <div class="line-item opt-line">b) Main jet</div>
    <div class="line-item opt-line">c) Choke (cuk)</div>
    <div class="line-item opt-line">d) Pelampung (float)</div>
    <div class="line-item q-start">4. Saat melakukan perawatan karburator, komponen mana yang paling sering tersumbat oleh kotoran</div>
    <div class="line-item">atau endapan bensin lama...</div>
    <div class="line-item opt-line">a) Pelampung</div>
    <div class="line-item opt-line">b) Jarum pelampung</div>
    <div class="line-item opt-line">c) Pilot jet dan Main jet (spuyer)</div>
    <div class="line-item opt-line">d) Katup gas (throttle valve)</div>
    <div class="line-item q-start">5. Perawatan rutin yang paling mendasar untuk mencegah masalah pada karburator adalah...</div>
    <div class="line-item opt-line">a) Mengganti oli mesin setiap bulan.</div>
    <div class="line-item opt-line">b) Memastikan filter udara selalu bersih dan tidak tersumbat.</div>
    <div class="line-item opt-line">c) Menyetel rantai roda secara berkala.</div>
    <div class="line-item opt-line">d) Memeriksa tekanan ban setiap hari.</div>
    <div class="line-item q-start">6. Pada mesin 4 langkah, apabila katup masuk terbuka dan katup buang tertutup, sementara piston</div>
    <div class="line-item">bergerak dari TMA ke TMB, maka proses langkah kerja ini disebut …</div>
    <div class="line-item opt-line">a) Langkah pembuangan</div>
    <div class="line-item opt-line">b) Langkah usaha</div>
    <div class="line-item opt-line">c) Langkah hisap</div>
    <div class="line-item opt-line">d) Langkah expansi</div>
</div>

{-- ==================== PAGE 2 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">7. Berikut adalah prosedur yang dilakukan sebelum melakukan penyetelan celah katup pada mesin</div>
    <div class="line-item">sepeda motor …</div>
    <div class="line-item opt-line">a) Melepas penutup lubang poros engkol, dan penutup lubang pemeriksaan waktu pengapian</div>
    <div class="line-item opt-line">b) Melepas penutup lubang penyetelan katup</div>
    <div class="line-item opt-line">c) Memutar rotor generator berlawanan arah jarum jam pastikan katup masuk bergerak turun dan</div>
    <div class="line-item">kemudian naik kembali, tepatkan tanda “T” pada rotor generator dengan tanda penyesuainya.</div>
    <div class="line-item opt-line">d) Pilihan jawaban a, b, c benar</div>
    <div class="line-item q-start">8. Standar pemeriksaan dan penyetelan celah katup dapat menggunakan …</div>
    <div class="line-item opt-line">a) Fuller Gauge</div>
    <div class="line-item opt-line">b) Adjuster Valve</div>
    <div class="line-item opt-line">c) Compression gauge</div>
    <div class="line-item opt-line">d) Vernier Caliper</div>
    <div class="line-item q-start">9. Bunyi mesin pada cylinder head berisik, kemungkinan penyebab kerusakan adalah ...</div>
    <div class="line-item opt-line">a) Ada kerusakan pada intake manifold</div>
    <div class="line-item opt-line">b) Celah Katup terlalu longgar</div>
    <div class="line-item opt-line">c) Mesin Overheating</div>
    <div class="line-item opt-line">d) Tekanan kompresi rendah</div>
    <div class="line-item q-start">10. Pemeriksaan yang harus di perhatikan untuk memastikan putaran stationer tidak berubah adalah …</div>
    <div class="line-item opt-line">a) Memutar keseluruhan stang kemudi ke arah kanan dan kiri</div>
    <div class="line-item opt-line">b) Memeriksa jarak main bebas handel gas</div>
    <div class="line-item opt-line">c) Memeriksa sambungan kabel gas</div>
    <div class="line-item opt-line">d) Pilihan jawaban a, b dan c benar</div>
    <div class="line-item q-start"><div style="float: right; margin-left: 2.0mm;"><img src="{{ asset('images/dokumen-muk/ia05a/p2_xref31.jpeg') }}" style="height: 18mm;"></div>11. Pemeriksaan free play throttle gas pada throttle body, yang perlu diperhatikan pada gambar berikut</div>
    <div class="line-item">adalah ...</div>
    <div class="line-item opt-line">a) Putaran mesin Idle</div>
    <div class="line-item opt-line">b) Kebersihan pegas throttle</div>
    <div class="line-item opt-line">c) Memastikan baut stopscrew tidak berubah</div>
    <div class="line-item opt-line">d) Kebersihan Throttle body</div>
    <div style="clear: both;"></div>
    <div class="line-item q-start">12. Berikut ini adalah pengerjaan yang tidak termasuk bagian dari perawatan kabel dan handel gas</div>
    <div class="line-item">adalah….</div>
    <div class="line-item opt-line">a) Pemeriksaan kondisi kelancaran handel gas</div>
    <div class="line-item opt-line">b) Melepas karet penutup debu dari penyetel</div>
    <div class="line-item opt-line">c) Jarak main bebas handle gas disetel pada bagian sisi stang kemudi kabel gas</div>
    <div class="line-item opt-line">d) Jarak main bebas handel gas tidak dapat disetel pada bagian ujung bawah kabel gas</div>
    <div class="line-item q-start">13. Pada jadwal perawatan berkala yang ditunjukan Buku Pedoman Reparasi tanda “G” untuk item</div>
    <div class="line-item">perawatan saringan udara memiliki arti …</div>
    <div class="line-item opt-line">a) Periksa</div>
    <div class="line-item opt-line">b) Lumasi</div>
    <div class="line-item opt-line">c) Ganti</div>
    <div class="line-item opt-line">d) Bersihkan</div>
    <div class="line-item q-start">14. Yang bukan termasuk bagian dari struktur saringan udara adalah …</div>
    <div class="line-item opt-line">a) Kotak Saringan Udara (cleaner case)</div>
    <div class="line-item opt-line">b) Katup Pernafasan (check valve)</div>
    <div class="line-item opt-line">c) Saringan Udara (air filter)</div>
    <div class="line-item opt-line">d) Lubang Pembuangan air (water draining outlet)</div>
    <div class="line-item q-start">15. Saringan jenis Viscous prosedur perawatan yang dianjurkan adalah …</div>
    <div class="line-item opt-line">a) Tidak boleh disemprot, tetapi tetap di buka dan dibersihkan dengan cara di lap</div>
    <div class="line-item opt-line">b)</div>
    <div class="line-item">Membersihkan filter udara dengan udara bertekanan</div>
    <div class="line-item opt-line">c) Menambahkan pelumas pada filter udara</div>
    <div class="line-item opt-line">d) Dibersihkan dengan cairan pembersih</div>
</div>

{-- ==================== PAGE 3 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">16. Akibat dari penyetelan kopling tidak benar adalah ...</div>
    <div class="line-item opt-line">a) Pegas kopling keras</div>
    <div class="line-item opt-line">b) Kopling selip sewaktu berakselerasi</div>
    <div class="line-item opt-line">c) Transmisi loncat dengan sendirinya</div>
    <div class="line-item opt-line">d) Daya mesin berkurang</div>
    <div class="line-item q-start">17. Berikut adalah cara penyetelan kopling sepeda motor tipe cub (bebek ) adalah....</div>
    <div class="line-item opt-line">a) Letakkan sepeda motor pada standar tengah</div>
    <div class="line-item opt-line">b) Longgarkan mur pengunci penyetel kopling satu putaran penuh</div>
    <div class="line-item opt-line">c) Putar adjuster berlawanan arah jarum jam sampai terasa ada tahanan, kemudian putar adjuster</div>
    <div class="line-item">searah jarum jam 3/4 putaran dan kencangkan lock nut.</div>
    <div class="line-item opt-line">d) Jawaban a,b dan c benar</div>
    <div class="line-item q-start">18. Prosedur pemeriksaan tinggi permukaan oli pada mesin sepeda motor adalah sebagai berikut …</div>
    <div class="line-item opt-line">a) Melepas tutup pengisi oli / batang pengukur dan periksa tinggi permukaan oli</div>
    <div class="line-item opt-line">b) Menghidupkan mesin dan biarkan berputar stasioner selama 2-3 menit dan kemudian matikan</div>
    <div class="line-item">mesin</div>
    <div class="line-item opt-line">c) Menempatkan sepeda motor pada standar tengannya</div>
    <div class="line-item opt-line">d) Membuka tutup gear sprocket</div>
    <div class="line-item q-start">19. Penggantian oli pelumas berdasarkan….</div>
    <div class="line-item opt-line">a) Lamanya kendaraan beroperasi</div>
    <div class="line-item opt-line">b) Kilometer jarak tempuh kendaran</div>
    <div class="line-item opt-line">c) Petunjuk teknisi</div>
    <div class="line-item opt-line">d) Temperatur kerja mesin</div>
    <div class="line-item q-start">20. Untuk menyaring partikel lembut pada system pelumasan diperlukan …</div>
    <div class="line-item opt-line">a) Oil treatment</div>
    <div class="line-item opt-line">b) Crancase Hole</div>
    <div class="line-item opt-line">c) Oil strainer</div>
    <div class="line-item opt-line">d) By Pas Valve</div>
    <div class="line-item q-start">21. Berikut adalah pemeliharaan dan perawatan filter oli yang benar …</div>
    <div class="line-item opt-line">a) Elemen Filter Oli tidak dapat di digunakan kembali dan harus dilakukan penggantian</div>
    <div class="line-item opt-line">b) Tipe Oil filter Assembly harus diganti baru berikut filter case</div>
    <div class="line-item opt-line">c) Oil filter elemen yang kotor harus diganti dengan yang baru berdasarkan jarak tempuh</div>
    <div class="line-item opt-line">d) Pilihan Jawaban a,b dan c benar</div>
    <div class="line-item q-start">22. Tanda Huruf “G” yang ditunjukan pada table pemeliharaan perawatan Buku Pedoman Reparasi</div>
    <div class="line-item">memiliki arti …</div>
    <div class="line-item opt-line">a) Periksa</div>
    <div class="line-item opt-line">b) Penggantian</div>
    <div class="line-item opt-line">c) Perbaikan</div>
    <div class="line-item opt-line">d) Peringatan</div>
    <div class="line-item q-start">23. Yang harus diperhatikan dalam melakukan pemeriksaan, penggantian oli pelumas adalah …</div>
    <div class="line-item opt-line">a) SAE/API Service</div>
    <div class="line-item opt-line">b) Jarak Tempuh kendaran</div>
    <div class="line-item opt-line">c) Kapasitas Oli</div>
    <div class="line-item opt-line">d) a,b dan c benar semua</div>
    <div class="line-item q-start">24. Apa fungsi utama dari sistem CVT pada kendaraan…</div>
    <div class="line-item opt-line">a) Mengubah energi listrik menjadi energi gerak.</div>
    <div class="line-item opt-line">b) Menghubungkan mesin dengan roda tanpa menggunakan gigi percepatan yang tetap,</div>
    <div class="line-item">memungkinkan rasio gigi yang bervariasi secara terus menerus.</div>
    <div class="line-item opt-line">c) Mendinginkan mesin saat beroperasi pada kecepatan tinggi.</div>
    <div class="line-item opt-line">d) Menggandakan torsi mesin hingga 10 kali lipat.</div>
</div>

{-- ==================== PAGE 4 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">25. Komponen utama pada CVT sepeda motor yang bergerak membuka dan menutup untuk mengubah</div>
    <div class="line-item">diameter puli dan rasio putaran adalah...</div>
    <div class="line-item opt-line">a) Kampas ganda (clutch shoe)</div>
    <div class="line-item opt-line">b) V-belt (sabuk V)</div>
    <div class="line-item opt-line">c) Roller (weight roller)</div>
    <div class="line-item opt-line">d) Per CVT</div>
    <div class="line-item q-start">26. Apa gejala yang paling umum terjadi jika V-belt pada sistem CVT sudah aus atau retak…</div>
    <div class="line-item opt-line">a) Mesin menjadi sangat irit bahan bakar.</div>
    <div class="line-item opt-line">b) Suara knalpot menjadi lebih keras.</div>
    <div class="line-item opt-line">c) Timbul suara berdecit saat akselerasi awal dan performa tenaga menurun.</div>
    <div class="line-item opt-line">d) Lampu depan menjadi redup.</div>
    <div class="line-item q-start">27. Jika roller (weight roller) pada CVT sudah aus atau peyang (pipih), apa dampaknya terhadap performa</div>
    <div class="line-item">kendaraan…</div>
    <div class="line-item opt-line">a) Putaran atas (top speed) meningkat drastis.</div>
    <div class="line-item opt-line">b) Akselerasi awal terasa berat atau lemot, dan kecepatan tertinggi sulit dicapai.</div>
    <div class="line-item opt-line">c) Rem belakang menjadi blong.</div>
    <div class="line-item opt-line">d) Suspensi menjadi keras.</div>
    <div class="line-item q-start">28. Pemeliharaan sistem pendingin udara yang benar adalah sebagai berikut...</div>
    <div class="line-item opt-line">a) Memberi lapisan cat pada rusuk pendingin secara merata</div>
    <div class="line-item opt-line">b) Membersihkan bersamaan dengan menghidupkan mesin</div>
    <div class="line-item opt-line">c) Membersihkan dengan menyikat bagian mesin dari debu dan kotoran</div>
    <div class="line-item opt-line">d) Membersihkan rusuk pendingin pada bagian cylinder block dan cylinder head dari debu dan</div>
    <div class="line-item">kotoran menggunakan udara bertekanan</div>
    <div class="line-item q-start">29. Alat tepat yang dipergunakan untuk menguji kebocoran sistem pendinginan air pada sepeda motor,</div>
    <div class="line-item">disebut …</div>
    <div class="line-item opt-line">a) Hydrometer</div>
    <div class="line-item opt-line">b) Radiator tester</div>
    <div class="line-item opt-line">c) Odometer</div>
    <div class="line-item opt-line">d) Tacho meter</div>
    <div class="line-item q-start">30. Hal yang harus dihindarkan pada saat melakukan pengecekan system pendingin adalah …</div>
    <div class="line-item opt-line">a) Membuka tutup radiator pada kondisi engine panas</div>
    <div class="line-item opt-line">b) Membuka tutup radiator pada kondisi engine dingin</div>
    <div class="line-item opt-line">c) Membuka tutup radiator pada kondisi engine hidup</div>
    <div class="line-item opt-line">d) Semua jawaban salah</div>
    <div class="line-item q-start">31. Fuel Suction Filter (filter pompa bensin) yang ditunjukan pada gambar memerlukan penggantian setiap</div>
    <div class="line-item">...</div>
    <div class="line-item opt-line">a) 15.000 km</div>
    <div class="line-item opt-line">b) 20.000 km</div>
    <div class="line-item opt-line">c) 30.000 km</div>
    <div class="line-item opt-line">d) 48.000 km</div>
    <div class="line-item q-start">32. Fuel Pressure pump pada electric fuel pump dibutuhkan tekanan konstan, prosedur pengukuran tekanan</div>
    <div class="line-item">pompa menggunakan alat ...</div>
    <div class="line-item opt-line">a) Injector Tester</div>
    <div class="line-item opt-line">b) Fuel Pump Tester</div>
    <div class="line-item opt-line">c) Fuel Cup Tester</div>
    <div class="line-item opt-line">d) Digital Fuel Pump</div>
    <div class="line-item q-start">33. Pemeriksaan saluran bahan bakar sistem injeksi adalah…</div>
    <div class="line-item opt-line">a) Pemeriksaan bracket fuel feed hose</div>
    <div class="line-item opt-line">b) Pemeriksaan tekanan fuell pump</div>
    <div class="line-item opt-line">c) Pemeriksaan kondisi socket sambungan pada selang bahan bakar</div>
    <div class="line-item opt-line">d) Jawaban a,b dan c benar</div>
</div>

{-- ==================== PAGE 5 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start"><div style="float: right; margin-left: 2.0mm;"><img src="{{ asset('images/dokumen-muk/ia05a/p5_xref38.jpeg') }}" style="height: 18mm;"></div>34. Prosedur yang benar di tunjukan pada gambar pengerjaan berikut adalah ...</div>
    <div class="line-item opt-line">a) Pembersihkan fuel pressure pump</div>
    <div class="line-item opt-line">b) Overhaul motor pompa</div>
    <div class="line-item opt-line">c) Membersihkan air filter</div>
    <div class="line-item opt-line">d) Melakukan penggantian/pelepasan komponen</div>
    <div style="clear: both;"></div>
    <div class="line-item">filter pompa bensin</div>
    <div class="line-item q-start">35. Apa fungsi utama dari komponen injektor (injector) pada sistem bahan bakar sepeda motor injeksi…</div>
    <div class="line-item opt-line">a) Mencampur udara dan bahan bakar secara manual.</div>
    <div class="line-item opt-line">b) Menghasilkan percikan api untuk membakar campuran udara dan bahan bakar.</div>
    <div class="line-item opt-line">c) Menyemprotkan (mengabutkan) bahan bakar ke ruang bakar atau intake manifold secara</div>
    <div class="line-item">elektronik sesuai kebutuhan mesin.</div>
    <div class="line-item opt-line">d) Membuang gas sisa pembakaran melalui knalpot.</div>
    <div class="line-item q-start">36. Apa gejala yang paling sering muncul jika injektor pada sepeda motor kotor atau tersumbat…</div>
    <div class="line-item opt-line">a) Suspensi terasa keras.</div>
    <div class="line-item opt-line">b) Mesin sulit langsam (stasioner tidak stabil), akselerasi tersendat (brebet), dan performa</div>
    <div class="line-item">menurun.</div>
    <div class="line-item opt-line">c) Lampu utama menjadi redup.</div>
    <div class="line-item opt-line">d) Tekanan angin ban berkurang.</div>
    <div class="line-item q-start">37. Metode pembersihan injektor yang paling umum dan dianjurkan dalam perawatan berkala di bengkel</div>
    <div class="line-item">resmi adalah...</div>
    <div class="line-item opt-line">a) Memukul-mukul injektor dengan palu kecil.</div>
    <div class="line-item opt-line">b) Membersihkan injektor menggunakan sikat kawat.</div>
    <div class="line-item opt-line">c) Menggunakan alat injector cleaner machine (tabung infus) yang dialiri cairan pembersih khusus.</div>
    <div class="line-item opt-line">d) Merendam injektor dalam bensin selama seminggu.</div>
    <div class="line-item q-start">38. Saat menggunakan metode &quot;infus&quot; (injector cleaner machine), cairan pembersih dimasukkan ke dalam</div>
    <div class="line-item">sistem bahan bakar melalui...</div>
    <div class="line-item opt-line">a) Lubang pengisian oli mesin.</div>
    <div class="line-item opt-line">b) Tangki bahan bakar utama.</div>
    <div class="line-item opt-line">c) Selang bahan bakar yang langsung menuju ke injektor, melewatkan tangki dan pompa bensin.</div>
    <div class="line-item opt-line">d) Knalpot.</div>
    <div class="line-item q-start"><div style="float: right; margin-left: 2.0mm;"><img src="{{ asset('images/dokumen-muk/ia05a/p5_xref39.jpeg') }}" style="height: 18mm;"></div>39. Filter bahan bakar (fuel filter) pada sepeda motor sistem injeksi berfungsi untuk....</div>
    <div class="line-item opt-line">a) Mendinginkan bensin sebelum masuk injektor.</div>
    <div class="line-item opt-line">b) Menyaring kotoran dan partikel halus dari bensin sebelum mencapai injektor dan pompa bensin.</div>
    <div class="line-item opt-line">c) Mencampur bensin dengan udara.</div>
    <div class="line-item opt-line">d) Mengukur volume bensin di tangki.</div>
    <div style="clear: both;"></div>
    <div class="line-item q-start">40. MIL pada panel indikator sepeda motor memiliki arti ...</div>
    <div class="line-item opt-line">a)</div>
    <div class="line-item">Indikator Tekanan Injeksi</div>
    <div class="line-item opt-line">b) Malfungtion Indicator Lamp</div>
    <div class="line-item opt-line">c) Mode Indicator Loss</div>
    <div class="line-item opt-line">d) Injection Control</div>
    <div class="line-item q-start">41. Kerusakan pada Cranksahft Position Sensor (CKP), kondisi mesin sepeda motor injeksi dapat</div>
    <div class="line-item">berakibat…</div>
    <div class="line-item opt-line">a) Mesin tidak dapat hidup</div>
    <div class="line-item opt-line">b) Temperatur mesin tinggi</div>
    <div class="line-item opt-line">c) Mesin hidup tersendat</div>
    <div class="line-item opt-line">d) Mesin tidak dapat stationer</div>
</div>

{-- ==================== PAGE 6 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">42. Standar putaran stationer pada sepeda motor tipe matic injeksi adalah…</div>
    <div class="line-item opt-line">a) 1000 rpm</div>
    <div class="line-item opt-line">b) 1300 rpm</div>
    <div class="line-item opt-line">c) 1400 rpm ±100</div>
    <div class="line-item opt-line">d) 1600 rpm ±100</div>
    <div class="line-item q-start">43. Fungsi utama ECM/ECU pada system engine managemen adalah …</div>
    <div class="line-item opt-line">a) Mengatur jumlah injeksi dan mengontrol sensor</div>
    <div class="line-item opt-line">b) Mengontrol bahan bakar dan sensor</div>
    <div class="line-item opt-line">c) Menambah tegangan ke injector</div>
    <div class="line-item opt-line">d) Mengatur speed engine dan secara terus menerus ke injector</div>
    <div class="line-item q-start">44. Penanggulangan kerusakan pada ECM/ECU sepeda motor injeksi adalah …</div>
    <div class="line-item opt-line">a) Perbaikan</div>
    <div class="line-item opt-line">b) Penggantian</div>
    <div class="line-item opt-line">c) Pembersihan</div>
    <div class="line-item opt-line">d) Reset ECM/ECU</div>
    <div class="line-item q-start">45. Apa fungsi utama dari baterai pada motor …</div>
    <div class="line-item opt-line">a) Menghidupkan mesin</div>
    <div class="line-item opt-line">b) Mengatur suhu mesin</div>
    <div class="line-item opt-line">c) Mengatur tekanan oli</div>
    <div class="line-item opt-line">d) Mengatur sistem pendingin</div>
    <div class="line-item q-start">46. Bagaimana cara mengukur tegangan baterai motor …</div>
    <div class="line-item opt-line">a) Dengan menggunakan multimeter</div>
    <div class="line-item opt-line">b) Dengan menggunakan kunci pas</div>
    <div class="line-item opt-line">c) Dengan menggunakan obeng</div>
    <div class="line-item opt-line">d) Dengan menggunakan tang</div>
    <div class="line-item q-start">47. Bagaimana cara memasang baterai motor yang benar …</div>
    <div class="line-item opt-line">a) Menghubungkan kabel positif terlebih dahulu</div>
    <div class="line-item opt-line">b) Menghubungkan kabel negatif terlebih dahulu</div>
    <div class="line-item opt-line">c) Menghubungkan kedua kabel secara bersamaan</div>
    <div class="line-item opt-line">d) Tidak perlu memperhatikan urutan kabel</div>
    <div class="line-item q-start">48. Apa fungsi utama busi pada mesin bensin…</div>
    <div class="line-item opt-line">a) Mendinginkan mesin saat overheat.</div>
    <div class="line-item opt-line">b) Mencampur bahan bakar dan udara di karburator.</div>
    <div class="line-item opt-line">c) Menghasilkan percikan api listrik untuk membakar campuran bahan bakar dan udara di ruang</div>
    <div class="line-item">bakar.</div>
    <div class="line-item opt-line">d) Mengatur masuknya bahan bakar ke dalam mesin.</div>
    <div class="line-item q-start">49. Kondisi ideal elektroda busi dari mesin yang sehat dan pembakaran sempurna biasanya berwarna</div>
    <div class="line-item opt-line">a) Hitam pekat dan basah oleh oli.</div>
    <div class="line-item opt-line">b) Putih bersih mengkilap seperti baru.</div>
    <div class="line-item opt-line">c) Coklat keabu-abuan atau merah bata kering.</div>
    <div class="line-item opt-line">d) Penuh dengan kerak karbon basah.</div>
    <div class="line-item q-start">50. Jika elektroda busi berwarna hitam pekat, basah, dan berminyak, hal ini mengindikasikan adanya</div>
    <div class="line-item">masalah...</div>
    <div class="line-item opt-line">a) Setelan bahan bakar terlalu miskin (kurang bensin).</div>
    <div class="line-item opt-line">b) Kebocoran oli mesin masuk ke ruang bakar.</div>
    <div class="line-item opt-line">c) Pengapian terlalu maju (timing advance).</div>
    <div class="line-item opt-line">d) Filter udara sangat bersih.</div>
</div>

{-- ==================== PAGE 7 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">51. Alat ukur apa yang digunakan untuk memeriksa celah (gap) antara elektroda pusat dan elektroda</div>
    <div class="line-item">massa pada busi…</div>
    <div class="line-item opt-line">a) Jangka sorong (dial caliper).</div>
    <div class="line-item opt-line">b) Multimeter (AVO meter).</div>
    <div class="line-item opt-line">c) Feeler gauge (alat ukur celah) atau wire gauge.</div>
    <div class="line-item opt-line">d) Kunci momen.</div>
    <div class="line-item q-start">52. Apa dampak jika celah busi terlalu lebar dari spesifikasi pabrikan…</div>
    <div class="line-item opt-line">a) Percikan api menjadi lebih kuat dan stabil di putaran tinggi.</div>
    <div class="line-item opt-line">b) Mesin lebih irit bahan bakar.</div>
    <div class="line-item opt-line">c) Percikan api sulit melompat, menyebabkan mesin sulit hidup atau misfire (pembakaran tidak</div>
    <div class="line-item">sempurna) pada putaran tinggi.</div>
    <div class="line-item opt-line">d) Busi menjadi lebih awet.</div>
    <div class="line-item q-start">53. Apa fungsi utama dari sistem sinyal pada kendaraan …</div>
    <div class="line-item opt-line">a) Mengatur suhu mesin</div>
    <div class="line-item opt-line">b) Mengatur tekanan oli</div>
    <div class="line-item opt-line">c) Memberikan tanda kepada pengemudi lain</div>
    <div class="line-item opt-line">d) Mengatur kinerja mesin</div>
    <div class="line-item q-start">54. Apa yang harus dilakukan jika lampu sein tidak berfungsi …</div>
    <div class="line-item opt-line">a) Mengganti bola lampu sein</div>
    <div class="line-item opt-line">b) Mengganti saklar sein</div>
    <div class="line-item opt-line">c) Memeriksa koneksi kabel sein</div>
    <div class="line-item opt-line">d) Semua jawaban benar</div>
    <div class="line-item q-start">55. Apa yang dapat terjadi jika sistem penerangan tidak berfungsi dengan baik …</div>
    <div class="line-item opt-line">a) Mengurangi visibilitas saat berkendara di malam hari</div>
    <div class="line-item opt-line">b) Meningkatkan kinerja mesin</div>
    <div class="line-item opt-line">c) Mengurangi konsumsi bahan bakar</div>
    <div class="line-item opt-line">d) Tidak ada efek sama sekali</div>
    <div class="line-item q-start">56. Jarak main bebas rantai roda umumnya berkisar antara ...</div>
    <div class="line-item opt-line">a) 30 – 40 mm</div>
    <div class="line-item opt-line">b) 20 – 30 mm</div>
    <div class="line-item opt-line">c) 15 - 20 mm</div>
    <div class="line-item opt-line">d) 10 – 20 mm</div>
    <div class="line-item q-start">57. Tegangan rantai saat bekerja kurang baik langkah yang tepat untuk mengatasi masalah adalah ...</div>
    <div class="line-item opt-line">a) Bersihkan rantai dari debu dan kotoran</div>
    <div class="line-item opt-line">b) Keringkan rantai setelah di lumasi</div>
    <div class="line-item opt-line">c) Perbaiki/Penyetelan atau ganti</div>
    <div class="line-item opt-line">d) Gigi pada gear sprocket rantai perhalus</div>
    <div class="line-item q-start"><div style="float: right; margin-left: 2.0mm;"><img src="{{ asset('images/dokumen-muk/ia05a/p7_xref46.jpeg') }}" style="height: 14mm;"></div>58. Perhatikan posisi clip sambungan rantai pada gambar dibawah ini, alasan yang paling tepat mengapa</div>
    <div class="line-item">posisi clip rantai harus menyesuaikan arah putaran adalah ...</div>
    <div class="line-item opt-line">a) Agar mudah saat memasang kembali</div>
    <div class="line-item opt-line">b) Agar mudah saat melepaskannya</div>
    <div class="line-item opt-line">c) Hanya sekedar variasi</div>
    <div class="line-item opt-line">d) Agar tidak lepas dan tersangkut pada komponen yang lain saat rantai berputar</div>
    <div style="clear: both;"></div>
</div>

{-- ==================== PAGE 8 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">59. Ukuran tekanan ban belakang untuk berboncengan adalah ...</div>
    <div class="line-item opt-line">a) 25 psi</div>
    <div class="line-item opt-line">b) 28 psi</div>
    <div class="line-item opt-line">c) 20 psi</div>
    <div class="line-item opt-line">d) 33 psi</div>
    <div class="line-item q-start">60. Roda depan terasa bergoyang kemungkinan penyebab adalah …</div>
    <div class="line-item opt-line">a) Pengikat as roda depan longgar</div>
    <div class="line-item opt-line">b) Pelek Bengkok</div>
    <div class="line-item opt-line">c) Bearing-bearing roda depan aus atau rusak</div>
    <div class="line-item opt-line">d) Pilihan jawaban a,b dan c benar</div>
    <div class="line-item q-start">61. Batas servis ukuran keolengan pelek roda pada pengukuran aksial dan radial adalah …</div>
    <div class="line-item opt-line">a) Aksial 1,0 mm, Radial 1,0 mm</div>
    <div class="line-item opt-line">b) Aksial 2,0 mm, Radial 2,0 mm</div>
    <div class="line-item opt-line">c) Aksial 3,0 mm, Radial 3,0 mm</div>
    <div class="line-item opt-line">d) Aksial 4,0 mm, Radial 4,0 mm</div>
    <div class="line-item q-start">62. Jarak main bebas penyetelan pedal rem belakang adalah …</div>
    <div class="line-item opt-line">a) 10 – 20 mm</div>
    <div class="line-item opt-line">b) 20 – 30 mm</div>
    <div class="line-item opt-line">c) 15 – 25 mm</div>
    <div class="line-item opt-line">d) 35 – 45 mm</div>
    <div class="line-item q-start">63. Jarak main bebas penyetelan tuas rem depan adalah …</div>
    <div class="line-item opt-line">a) 10 – 20 mm</div>
    <div class="line-item opt-line">b) 20 – 30 mm</div>
    <div class="line-item opt-line">c) 15 – 25 mm</div>
    <div class="line-item opt-line">d) 35 – 45 mm</div>
    <div class="line-item q-start">64. Pada sistem rem hidrolik, apabila pengereman kurang efektif, hal ini disebabkan oleh …</div>
    <div class="line-item opt-line">a) Kapasitas minyak rem kurang</div>
    <div class="line-item opt-line">b) Ada unsur udara pada sistem hydrolik</div>
    <div class="line-item opt-line">c) Jenis minyak rem tidak sama</div>
    <div class="line-item opt-line">d) Volume minyak rem berlebihan</div>
    <div class="line-item q-start">65. Apabila pengendalian setang/kemudi terasa berat untuk digerakkan, kemungkinan penyebabnya</div>
    <div class="line-item">adalah ….</div>
    <div class="line-item opt-line">a) Tekanan udara ban terlalu tinggi</div>
    <div class="line-item opt-line">b) Mur ulir atas poros kemudi terlalu kencang</div>
    <div class="line-item opt-line">c) Seal suspensi bocor</div>
    <div class="line-item opt-line">d) Rangka bengkok</div>
    <div class="line-item q-start">66. Apabila menarik ke satu sisiatau tidak dapat lurus, kemungkinan penyebab kerusakan adalah …</div>
    <div class="line-item opt-line">a) Bearing roda depan aus/rusak</div>
    <div class="line-item opt-line">b) Tekanan udara ban terlalu keras</div>
    <div class="line-item opt-line">c) Seal debu rusak</div>
    <div class="line-item opt-line">d) Pegas fork lemah</div>
</div>

{-- ==================== PAGE 9 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">67. Apa fungsi utama dari sistem suspensi pada kendaraan ...</div>
    <div class="line-item opt-line">a) Meredam getaran dan guncangan untuk meningkatkan kenyamanan berkendara.</div>
    <div class="line-item opt-line">b) Meningkatkan kecepatan maksimum kendaraan.</div>
    <div class="line-item opt-line">c) Mengurangi konsumsi bahan bakar.</div>
    <div class="line-item opt-line">d) Menghasilkan tenaga mesin yang lebih besar.</div>
    <div class="line-item q-start">68. Salah satu ciri-ciri kerusakan pada shock absorber adalah ...</div>
    <div class="line-item opt-line">a) Mobil terasa lebih stabil saat menikung.</div>
    <div class="line-item opt-line">b) Tidak ada bunyi saat melewati jalan bergelombang.</div>
    <div class="line-item opt-line">c) Adanya kebocoran oli pada tabung shock absorber.</div>
    <div class="line-item opt-line">d) Keausan ban merata pada semua sisi.</div>
    <div class="line-item q-start">69. Apa yang dapat terjadi jika tekanan angin ban tidak sesuai dengan spesifikasi, terutama dalam kaitannya</div>
    <div class="line-item">dengan perawatan suspensi ...</div>
    <div class="line-item opt-line">a) Kenyamanan berkendara meningkat.</div>
    <div class="line-item opt-line">b) Umur shock absorber menjadi lebih panjang.</div>
    <div class="line-item opt-line">c) Dapat menyebabkan keausan ban yang tidak merata dan mempengaruhi kinerja suspensi.</div>
    <div class="line-item opt-line">d) Tidak berdampak sama sekali pada sistem suspensi.</div>
    <div class="line-item q-start">70. Jika suspensi depan sepeda motor terasa terlalu keras atau kaku saat melewati jalan berlubang, apa</div>
    <div class="line-item">kemungkinan penyebabnya</div>
    <div class="line-item opt-line">a) Jumlah oli shock absorber kurang.</div>
    <div class="line-item opt-line">b) Tekanan angin pada ban terlalu rendah.</div>
    <div class="line-item opt-line">c) Pegas suspensi sudah terlalu lemah (lunak).</div>
    <div class="line-item opt-line">d) Penggunaan oli shock absorber yang terlalu kental atau jumlahnya berlebihan.</div>
    <div class="line-item q-start">71. Perawatan rutin apa yang paling krusial untuk menjaga kinerja Chassis (rangka) sepeda motor tetap</div>
    <div class="line-item">optimal dan aman</div>
    <div class="line-item opt-line">a) Mencuci motor setiap hari.</div>
    <div class="line-item opt-line">b) Memeriksa kekencangan baut-baut utama sasis dan memastikan tidak ada keretakan atau karat</div>
    <div class="line-item">parah.</div>
    <div class="line-item opt-line">c) Mengganti busi secara rutin.Men</div>
    <div class="line-item opt-line">d) yetel rantai roda belakang.</div>
    <div class="line-item q-start">72. Apa yang harus dilakukan jika cover body tidak pas dengan sempurna setelah dipasang …</div>
    <div class="line-item opt-line">a) Memaksakan cover body agar pas</div>
    <div class="line-item opt-line">b) Mengecek kembali kesesuaian dan melakukan penyesuaian</div>
    <div class="line-item opt-line">c) Meninggalkan cover body tidak terpasang</div>
    <div class="line-item opt-line">d) Menggunakan lem untuk menempelkannya</div>
    <div class="line-item q-start">73. Apa yang harus diperiksa setelah melakukan pemasangan cover body…</div>
    <div class="line-item opt-line">a) Kinerja mesin</div>
    <div class="line-item opt-line">b) Sistem pendingin</div>
    <div class="line-item opt-line">c) Kencangnya baut dan kesesuaian cover body</div>
    <div class="line-item opt-line">d) Sistem rem</div>
    <div class="line-item q-start">74. Apa tujuan utama melakukan pemeriksaan chassis secara berkala...</div>
    <div class="line-item opt-line">a) Untuk membersihkan kotoran yang menempel pada chassis.</div>
    <div class="line-item opt-line">b) Untuk memastikan tampilan luar kendaraan tetap estetis.</div>
    <div class="line-item opt-line">c) Untuk mendeteksi dini kerusakan, keausan, atau komponen yang longgar yang dapat</div>
    <div class="line-item">membahayakan keselamatan berkendara.</div>
    <div class="line-item opt-line">d) Untuk menambah kecepatan maksimum kendaraan.</div>
</div>

{-- ==================== PAGE 10 ==================== --}
<div class="page-sheet">
    <table class="kop-table">
        <tr>
            <td class="kop-logo"><img src="{{ asset('images/logo-lsp.png') }}" alt="LSP Logo"></td>
            <td class="kop-text">Lembaga Sertifikasi Profesi Praktisi Otomotif &nbsp;LSP-PrO Indo</td>
        </tr>
    </table>
    <div class="line-item q-start">75. Apabila ditemukan retakan pada rangka (frame) chassis, tindakan perbaikan yang paling tepat adalah...</div>
    <div class="line-item opt-line">a) Mengabaikannya jika retakan kecil.</div>
    <div class="line-item opt-line">b) Menambal dengan dempul atau perekat biasa.</div>
    <div class="line-item opt-line">c) Melakukan pengelasan oleh tenaga ahli yang bersertifikat.</div>
    <div class="line-item opt-line">d) Membiarkan saja karena tidak mempengaruhi kinerja mesin.</div>
    <div class="doc-main-title" style="text-align: center; margin-top: 8.0mm; margin-bottom: 2.5mm;">
        PENYUSUN DAN VALIDATOR
    </div>

    <table class="content-table" style="width: 169.5mm;">
        <colgroup>
            <col style="width: 35.0mm;">
            <col style="width: 12.0mm;">
            <col style="width: 50.0mm;">
            <col style="width: 38.0mm;">
            <col style="width: 34.5mm;">
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
