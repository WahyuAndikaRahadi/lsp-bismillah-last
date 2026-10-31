@extends('layouts.print-muk')

@section('title', 'Daftar Periksa Kelengkapan Dokumen - ' . $scheme->name)
@section('doc-title', 'Daftar Periksa Kelengkapan Dokumen MUK')

@section('content')
<div style="font-size: 9pt; line-height: 1.35;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 11pt; font-weight: bold; margin: 0; text-transform: uppercase;">
            DAFTAR PERIKSA KELENGKAPAN DOKUMEN
        </h2>
        <div style="font-size: 10pt; font-weight: bold; margin-top: 3px; color: #1e3a8a;">
            SKEMA {{ $scheme->name }} ({{ $scheme->code }})
        </div>
        <div style="font-size: 8.5pt; color: #475569; margin-top: 2px;">
            TUK: {{ $schedule->tuk_name }} &bull; Tanggal: {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
        </div>
    </div>

    @php
        $docList = [
            'A. PELAKSANAAN ASESMEN' => [
                '1' => ['FR.APL.01. Permohonan Sertifikasi Kompetensi', 'Lengkap terverifikasi'],
                '2' => ['FR.APL.02. Asesmen Mandiri', 'Terisi seluruh elemen & KUK'],
                '3' => ['Portofolio Asesi (KTP, Ijazah, Surat Pengalaman/Magang)', 'Lengkap sesuai skema'],
                '4' => ['FR.MAPA.01. Merencanakan Aktivitas dan Proses Asesmen', 'Tersedia pada paket MUK'],
                '5' => ['Skema Sertifikasi & Standar Kompetensi (SKKNI)', 'Sesuai lisensi BNSP'],
                '6' => ['FR-MAPA.02. Peta Instrumen Asesmen', 'Tervalidasi'],
                '7' => ['FR.AK.07. Ceklis Penyesuaian yang Wajar dan Beralasan', 'Tersedia'],
                '8' => ['FR.AK.04. Formulir Banding Asesmen', 'Tersedia / Nihil jika tidak ada banding'],
                '9' => ['FR.AK.01. Persetujuan Asesmen dan Kerahasiaan', 'Ditandatangani asesi dan asesor'],
            ],
            'B. INSTRUMEN ASESMEN' => [
                '10' => ['FR.IA.01 Ceklis Observasi Aktivitas Di Tempat Kerja / Praktik', 'Terisi penilaian per KUK'],
                '11' => ['FR.IA.02 Tugas Praktik Demonstrasi (TPD)', 'Tersedia instruksi kerja asesi'],
                '12' => ['FR.IA.03 Pertanyaan Mendukung Observasi (PMO)', 'Terisi tanggapan wawancara'],
                '13' => ['FR.IA.05 Pertanyaan Tertulis Pilihan Ganda (CBT/DPT)', 'Kunci & Lembar Jawaban Lengkap'],
            ],
            'C. REKAMAN DAN LAPORAN ASESMEN' => [
                '14' => ['FR.AK.02 Rekaman Asesmen Kompetensi', 'Matriks 8 metode uji terisi lengkap'],
                '15' => ['FR.AK.03 Umpan Balik dan Catatan Asesmen', '10 butir evaluasi asesi terisi'],
                '16' => ['FR.AK.05 Laporan Asesmen oleh Asesor', 'Rekomendasi K/BK & temuan lengkap'],
                '17' => ['FR.AK.06 Meninjau Proses Asesmen', 'Matriks prinsip asesmen terisi'],
                '18' => ['Berita Acara Pelaksanaan Uji Kompetensi', 'Tercetak & ditandatangani'],
                '19' => ['Daftar Hadir Asesor dan Daftar Hadir Asesi', 'Tanda tangan lengkap'],
                '20' => ['Surat Pernyataan Pemegang Sertifikat', 'Tersedia untuk setiap asesi'],
            ],
        ];
    @endphp

    <table class="bnsp-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 5%;" rowspan="2">NO</th>
                <th style="width: 55%;" rowspan="2">JENIS DOKUMEN</th>
                <th colspan="2" style="width: 16%;">STATUS*</th>
                <th style="width: 24%;" rowspan="2">KETERANGAN</th>
            </tr>
            <tr style="font-size: 7.5pt;">
                <th style="width: 8%;">ADA</th>
                <th style="width: 8%;">TIDAK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($docList as $sectionTitle => $items)
                <tr style="background: #f1f5f9; font-weight: bold;">
                    <td colspan="5" style="padding: 4px 6px;">{{ $sectionTitle }}</td>
                </tr>
                @foreach($items as $num => $data)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">{{ $num }}</td>
                        <td style="padding: 3px 6px;">{{ $data[0] }}</td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;">&radic;</td>
                        <td style="text-align: center; vertical-align: middle;"></td>
                        <td style="padding: 3px 6px; font-size: 8pt; color: #334155;">{{ $data[1] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 8pt; font-style: italic; margin-bottom: 15px;">
        * Beri tanda centang (&radic;) pada kolom Status yang sesuai. Seluruh berkas harus tersimpan rapi dalam ordner portofolio asesmen.
    </div>

    {{-- Signatures --}}
    <div class="sig-container avoid-break" style="margin-top: 15px;">
        <div class="sig-grid">
            <div class="sig-col" style="padding-left: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Diverifikasi oleh:</div>
                <div style="font-size: 8.5pt;">Penanggungjawab TUK</div>
                <div class="sig-box">
                    @if($schedule->pic_tuk_signature)
                        <img src="{{ $schedule->pic_tuk_signature }}" class="sig-img" alt="TTD PIC">
                    @else
                        <div style="height: 40px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    <strong>{{ $schedule->pic_tuk_name ?? '........................................' }}</strong>
                </div>
            </div>

            <div class="sig-col" style="padding-right: 0; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 4px;">Diperiksa oleh:</div>
                <div style="font-size: 8.5pt;">Lead Asesor / Asesor Kompetensi</div>
                <div class="sig-box">
                    @if($leadAssessor?->profile?->signature_path)
                        <img src="{{ asset('storage/' . $leadAssessor->profile->signature_path) }}" class="sig-img" alt="TTD Asesor">
                    @elseif($assessments->first()?->assessor_signature)
                        <img src="{{ $assessments->first()->assessor_signature }}" class="sig-img" alt="TTD Asesor">
                    @else
                        <div style="height: 40px;"></div>
                    @endif
                    <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                </div>
                <div style="font-size: 8.5pt;">
                    <strong>{{ $leadAssessor?->full_name ?? $leadAssessor?->name ?? '........................................' }}</strong><br>
                    <span>No. MET: {{ $leadAssessor?->profile?->met_number ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
