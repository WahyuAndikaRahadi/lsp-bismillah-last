@extends('layouts.print-muk')

@section('title', 'Daftar Periksa Kelengkapan Dokumen - ' . $scheme->name)

@section('content')
<style>
    @page {
        size: 215.9mm 279.4mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Calibri, 'Carlito', sans-serif;
        color: #000;
        -webkit-print-color-adjust: exact;
    }
    .page-container {
        width: 215.9mm;
        box-sizing: border-box;
        padding-top: 6.4mm;
        padding-left: 23.7mm;
        padding-right: 19.6mm;
    }
    .doc-title {
        text-align: center;
        font-family: Calibri, 'Carlito', sans-serif;
        font-size: 14pt;
        font-weight: bold;
        line-height: 1.15;
        margin-bottom: 6.9mm;
    }
    .table-periksa {
        width: 172.6mm;
        border-collapse: collapse;
        border: 1pt solid #000;
        font-size: 10pt;
        line-height: 1.1;
    }
    .table-periksa th, .table-periksa td {
        border: 1pt solid #000;
        padding: 0.1mm 1.5mm;
        box-sizing: border-box;
    }
    .table-periksa th {
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    .sec-head {
        font-weight: bold;
        padding-left: 8.7mm !important;
    }
    .check-wingdings {
        font-family: 'Wingdings 2', 'Wingdings', 'DejaVu Sans', sans-serif;
        font-size: 11pt;
    }
    .check-box-sym {
        font-family: 'MS Gothic', 'DejaVu Sans', sans-serif;
        font-size: 11pt;
    }
    .center-cell {
        text-align: center;
        vertical-align: middle;
    }
</style>

@php
    $schemeTitleUpper = strtoupper($scheme->name ?? 'MEKANIK JUNIOR');
    if (str_contains($schemeTitleUpper, 'MEKANIK JUNIOR')) {
        $schemeDisplay = 'MEKANIK JUNIOR';
    } else {
        $schemeDisplay = $schemeTitleUpper;
    }
@endphp

<div class="page-container">
    <div class="doc-title">
        DAFTAR PERIKSA<br>
        KELENGKAPAN DOKUMEN SKEMA {{ $schemeDisplay }}
    </div>

    <table class="table-periksa">
        <colgroup>
            <col style="width: 17.1mm;">
            <col style="width: 89.8mm;">
            <col style="width: 17.6mm;">
            <col style="width: 16.8mm;">
            <col style="width: 31.3mm;">
        </colgroup>
        <thead>
            <tr style="height: 8.5mm;">
                <th rowspan="2" style="font-size: 12pt;">NO</th>
                <th rowspan="2" style="font-size: 12pt;">JENIS<br>DOKUMEN</th>
                <th colspan="2" style="font-size: 12pt; height: 5.2mm;">STATUS*</th>
                <th rowspan="2" style="font-size: 12pt;">KETERANGAN</th>
            </tr>
            <tr style="height: 5.2mm;">
                <th style="font-size: 9pt;">ADA</th>
                <th style="font-size: 9pt;">TIDAK</th>
            </tr>
        </thead>
        <tbody>
            {{-- A. PELAKSANAAN ASESMEN --}}
            <tr style="height: 5.5mm;">
                <td colspan="5" class="sec-head">A. PELAKSANAAN ASESMEN</td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">1</td>
                <td>FR.APL.01. Permohonan Sertifikasi Kompetensi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">2</td>
                <td>FR.APL.02. Asesmen Mandiri</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">3</td>
                <td>Portofolio Asesi</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">4</td>
                <td>FR.MAPA.01. Merencanakan Aktivitas dan Proses Asesmen</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell" rowspan="2">5</td>
                <td>a. Skema Sertifikasi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td>b. Standar Kompetensi & Peta Kompetensi Kerja</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 9.0mm;">
                <td class="center-cell">6</td>
                <td>FR-MAPA.02. Peta Instrumen Asesmen Hasil<br>Pendekatan Asesmen dan Perencanaan Asesmen</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">7</td>
                <td>FR AK 07. Ceklis Penyesuaian yang Wajar</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">8</td>
                <td>FR.AK.04. Formulir Banding</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">9</td>
                <td>FR.AK.01. Persetujuan Asesmen dan Kerahasiaan</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>

            {{-- INSTRUMEN ASESMEN --}}
            <tr style="height: 5.4mm;">
                <td colspan="5" style="font-weight: bold; padding-left: 2.4mm;">INSTRUMEN ASESMEN (disesuaikan dengan MAPA-01/MAPA-02)</td>
            </tr>
            <tr style="height: 7.0mm;">
                <td class="center-cell" rowspan="2">10</td>
                <td>FR.IA.01.CL -  Ceklis Observasi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 7.0mm;">
                <td>FR.IA.01. B Report Sheet</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">11</td>
                <td>FR.IA.02.  TPD - Tugas Praktik Demonstrasi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">12</td>
                <td>FR.IA.03.  PMO - Pertanyaan Untuk Mendukung Observasi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 9.0mm;">
                <td class="center-cell" rowspan="2">13</td>
                <td>
                    a. FR.IA.04. A DIT - Daftar Instruksi Terstruktur (Penjelasan<br>
                    <span style="display: inline-block; padding-left: 3.7mm;">Proyek Singkat/ Kegiatan Terstruktur Lainnya*)</span>
                </td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 8.9mm;">
                <td>
                    b. FR.IA.04B. DIT - Penilaian Proyek Singkat atau Kegiatan<br>
                    <span style="display: inline-block; padding-left: 3.7mm;">Terstruktur Lainnya</span>
                </td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 12.9mm;">
                <td class="center-cell">14</td>
                <td>
                    FR.IA.05. DPT - Pertanyaan Tertulis Pilihan Ganda<br>
                    FR.IA.05 B  Lembar Kunci Jawaban Pilihan Ganda<br>
                    FR.IA.05.C Lembar Jawaban Pilihan Ganda
                </td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 13.3mm;">
                <td class="center-cell">15</td>
                <td>
                    FR.IA.06. A. DPT - Pertanyaan Tertulis Esai<br>
                    FR .IA.06. B.Lembar Kunci Jawaban  Pertanyaan Tertulis Esai<br>
                    FR.IA.06. C. Lembar Jawab Pertanyaan Esai
                </td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">16</td>
                <td>FR.IA.07. DPL - Pertanyaan Lisan</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">17</td>
                <td>FR.IA.08. CVP - Ceklis Verifikasi Portofolio</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">18</td>
                <td>FR.IA.09. PW - Pertanyaan Wawancara</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">19</td>
                <td>FR.IA.10. VPK – Verifikasi Pihak Ketiga</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">20</td>
                <td>FR.IA.11. CRP - Ceklis Reviu Produk</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>

            {{-- B. KEPUTUSAN : --}}
            <tr style="height: 5.5mm;">
                <td colspan="5" class="sec-head">B. KEPUTUSAN :</td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">21</td>
                <td>FR.AK.02. Rekaman Asesmen Kompetensi</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">22</td>
                <td>FR.AK.03. Umpan Balik dan Catatan Asesmen</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>

            {{-- C. LAPORAN ASESMEN --}}
            <tr style="height: 5.5mm;">
                <td colspan="5" class="sec-head">C. LAPORAN ASESMEN</td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">23</td>
                <td>FR.AK.05. Laporan Asesmen</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">24</td>
                <td>FR.AK.06. Meninjau Proses Asesmen</td>
                <td class="center-cell"><span class="check-wingdings">&#61522;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>

            {{-- D. VALIDASI --}}
            <tr style="height: 5.4mm;">
                <td colspan="5" class="sec-head">D. VALIDASI</td>
            </tr>
            <tr style="height: 5.4mm;">
                <td class="center-cell">25.</td>
                <td>FR.VA. Memberikan Kontribusi dalam Validasi Asesmen</td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td class="center-cell"><span class="check-box-sym">&#9744;</span></td>
                <td></td>
            </tr>

            {{-- Signatures inside Table (Row 34, 35) --}}
            <tr style="height: 5.5mm;">
                <td colspan="2" style="border-right: 1pt solid #000; padding-left: 2.0mm;">Ketua TUK :</td>
                <td colspan="3" style="padding-left: 2.0mm;">Manager Sertifikasi LSP PrO Indo</td>
            </tr>
            <tr style="height: 14.8mm;">
                <td colspan="2" style="border-right: 1pt solid #000; border-top: none; padding-left: 2.4mm; vertical-align: top; padding-top: 1.0mm;">
                    Nama :<br>
                    <div style="margin-top: 2.2mm;">Paraf :</div>
                </td>
                <td colspan="3" style="border-top: none; padding-left: 2.4mm; vertical-align: top; padding-top: 1.0mm;">
                    Nama : Karyanto<br>
                    <div style="margin-top: 2.2mm;">Paraf :</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
