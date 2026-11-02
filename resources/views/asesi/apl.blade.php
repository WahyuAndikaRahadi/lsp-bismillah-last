@extends('layouts.app')

@section('title', 'FR.APL.01 & FR.APL.02 - Permohonan Sertifikasi & Asesmen Mandiri')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 py-6">
    {{-- Top Status Banner --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-300 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-300">
                    {{ $assessment->schedule->scheme->code }}
                </span>
                <span class="text-xs text-slate-500 font-medium">TUK: {{ $assessment->schedule->tuk_name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $assessment->schedule->scheme->name }}</h1>
            <p class="text-xs text-slate-600 mt-1">
                Jadwal Uji: <strong>{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }}</strong> ({{ substr($assessment->schedule->start_time, 0, 5) }} - {{ substr($assessment->schedule->end_time, 0, 5) }} WIB)
            </p>
        </div>
        <div class="shrink-0">
            @if($assessment->apl_status === 'APPROVED')
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white shadow-xs">
                        ✓ Berkas Disetujui Asesor
                    </span>
                    <div class="mt-2">
                        <a href="{{ route('asesi.assessment.ruang-uji', $assessment) }}" class="inline-flex items-center px-4 py-2 bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg text-xs font-bold shadow-xs transition gap-1.5">
                            <span>Masuk Ruang Uji</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @elseif($assessment->apl_status === 'SUBMITTED')
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 text-white shadow-xs">
                        ⏳ Menunggu Verifikasi Asesor
                    </span>
                    <p class="text-[11px] text-slate-500 mt-1">Formulir terkunci sampai diverifikasi</p>
                </div>
            @elseif($assessment->apl_status === 'REVISION_REQUESTED')
                <div class="bg-rose-50 border border-rose-300 rounded-lg p-3 text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-rose-600 text-white">
                        Perlu Perbaikan
                    </span>
                    <p class="text-xs text-rose-800 mt-1 font-semibold">Catatan Asesor:</p>
                    <p class="text-xs text-slate-700 italic">{{ $assessment->apl_revision_notes ?? $assessment->admin_notes }}</p>
                </div>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                    Draft Permohonan Sertifikasi
                </span>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-lg text-xs space-y-1">
            <p class="font-bold text-sm">Mohon periksa kembali formulir Anda:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('asesi.assessment.apl.store', $assessment) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="aplForm">
        @csrf

        {{-- ======================================================== --}}
        {{-- FORMULIR FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI     --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden">
            {{-- Header Dokumen MUK APL.01 --}}
            <div class="border-b border-slate-300 p-5 bg-slate-50 text-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3 mb-3">
                    <div class="font-extrabold text-sm tracking-wider uppercase text-lsp-navy">
                        Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo
                    </div>
                    <div class="text-[11px] font-mono text-slate-500">
                        Skema: {{ $assessment->schedule->scheme->code }}
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                            FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI
                        </h2>
                        <p class="text-xs text-slate-600 mt-1">Formulir Pendaftaran & Rincian Data Calon Peserta Uji Kompetensi</p>
                    </div>
                    <a href="{{ route('asesi.profile.edit') }}" class="text-xs bg-white hover:bg-slate-100 text-slate-700 px-3 py-1.5 rounded border border-slate-300 transition font-semibold shadow-xs">
                        ✎ Ubah Biodata Profil
                    </a>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-6">
                {{-- Bagian 1 : Rincian Data Pemohon Sertifikasi --}}
                <div>
                    <div class="bg-slate-100 border border-slate-300 px-3.5 py-2 font-bold text-xs text-slate-900 uppercase tracking-wider mb-2">
                        Bagian 1 : Rincian Data Pemohon Sertifikasi
                    </div>
                    <p class="text-xs text-slate-600 mb-3 italic">
                        Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data pekerjaan anda pada saat ini.
                    </p>

                    {{-- a. Data Pribadi --}}
                    <h3 class="text-xs font-bold text-slate-900 mb-1.5">a. Data Pribadi</h3>
                    <table class="w-full text-xs border border-slate-300 border-collapse mb-5">
                        <tbody>
                            <tr>
                                <td class="w-1/4 bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Nama lengkap</td>
                                <td class="w-1/4 px-3 py-2 border border-slate-300 font-bold text-slate-900">{{ auth()->user()->full_name }}</td>
                                <td class="w-1/4 bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">No. KTP/NIK/Paspor</td>
                                <td class="w-1/4 px-3 py-2 border border-slate-300 font-mono text-slate-900">{{ $profile?->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Tempat / tgl. Lahir</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">
                                    {{ $profile?->tempat_lahir ?? '-' }}, {{ $profile?->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Jenis kelamin</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">
                                    {{ $profile?->jenis_kelamin === 'L' ? 'Laki-laki' : ($profile?->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Kebangsaan</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">{{ $profile?->kebangsaan ?? 'Indonesia' }}</td>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Kualifikasi Pendidikan</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">{{ $profile?->pendidikan_terakhir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Alamat rumah</td>
                                <td colspan="3" class="px-3 py-2 border border-slate-300 text-slate-900">
                                    {{ $profile?->alamat_rumah ?? '-' }} (Kode Pos: {{ $profile?->kode_pos ?? '-' }})
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">No. Telepon/HP</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">{{ auth()->user()->phone ?? '-' }}</td>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">E-mail</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">{{ auth()->user()->email }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- b. Data Pekerjaan Sekarang --}}
                    <h3 class="text-xs font-bold text-slate-900 mb-1.5">b. Data Pekerjaan Sekarang</h3>
                    <table class="w-full text-xs border border-slate-300 border-collapse">
                        <tbody>
                            <tr>
                                <td class="w-1/4 bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Nama Institusi / Perusahaan</td>
                                <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-medium">
                                    {{ $profile?->nama_institusi ?? $profile?->nama_institusi_perusahaan ?? '-' }}
                                </td>
                                <td class="w-1/4 bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Jabatan</td>
                                <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-medium">{{ $profile?->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Alamat Kantor</td>
                                <td colspan="3" class="px-3 py-2 border border-slate-300 text-slate-900">
                                    {{ $profile?->alamat_kantor ?? '-' }} (Kode Pos: {{ $profile?->kode_pos ?? '-' }})
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Bagian 2 : Data Sertifikasi --}}
                <div>
                    <div class="bg-slate-100 border border-slate-300 px-3.5 py-2 font-bold text-xs text-slate-900 uppercase tracking-wider mb-2">
                        Bagian 2 : Data Sertifikasi
                    </div>
                    <p class="text-xs text-slate-600 mb-3 italic">
                        Tuliskan Judul dan Nomor Skema Sertifikasi yang anda ajukan berikut Daftar Unit Kompetensi sesuai kemasan pada skema sertifikasi untuk mendapatkan pengakuan sesuai dengan latar belakang pendidikan, pelatihan serta pengalaman kerja yang anda miliki.
                    </p>

                    <table class="w-full text-xs border border-slate-300 border-collapse mb-4">
                        <tbody>
                            <tr>
                                <td class="w-1/4 bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">
                                    Judul : <strong class="text-lsp-navy">{{ $assessment->schedule->scheme->name }}</strong><br>
                                    Nomor : <span class="font-mono text-slate-700">{{ $assessment->schedule->scheme->code }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 font-semibold px-3 py-2 border border-slate-300 text-slate-700">Tujuan Asesmen</td>
                                <td class="px-3 py-2 border border-slate-300 text-slate-900">
                                    <div class="flex items-center gap-4 flex-wrap">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="tujuan_asesmen" value="Sertifikasi" checked class="text-lsp-navy focus:ring-lsp-navy">
                                            <span>Sertifikasi</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="tujuan_asesmen" value="PKT" class="text-lsp-navy focus:ring-lsp-navy">
                                            <span>Pengakuan Kompetensi Terkini (PKT)</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="tujuan_asesmen" value="RPL" class="text-lsp-navy focus:ring-lsp-navy">
                                            <span>Rekognisi Pembelajaran Lampau (RPL)</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="tujuan_asesmen" value="Lainnya" class="text-lsp-navy focus:ring-lsp-navy">
                                            <span>Lainnya</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-xs font-bold text-slate-900 mb-1.5">Daftar Unit Kompetensi sesuai kemasan:</h3>
                    <table class="w-full text-xs border border-slate-300 border-collapse">
                        <thead>
                            <tr class="bg-slate-100 font-bold text-slate-800">
                                <th class="border border-slate-300 px-3 py-2 w-12 text-center">No.</th>
                                <th class="border border-slate-300 px-3 py-2 w-48 text-left">Kode Unit</th>
                                <th class="border border-slate-300 px-3 py-2 text-left">Judul Unit</th>
                                <th class="border border-slate-300 px-3 py-2 w-40 text-center">Standar Kompetensi Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessment->schedule->scheme->competencyUnits as $unit)
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-3 py-2 text-center font-mono text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="border border-slate-300 px-3 py-2 font-mono font-bold text-slate-800">{{ $unit->code }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-slate-800">{{ $unit->title }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-center text-slate-600">SKKNI</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pemilihan Asesor Penguji Dari Jadwal --}}
                @php
                    $scheduleAssessors = $assessment->schedule->assessors ?? collect();
                @endphp
                @if($scheduleAssessors->isNotEmpty())
                    <div class="p-3.5 bg-slate-50 border border-slate-300 rounded-lg">
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Pilih Asesor Penguji Yang Bertugas <span class="text-rose-600">*</span></span>
                            <span class="text-[11px] text-slate-500 font-normal lowercase">Asesor yang akan memverifikasi berkas & menguji Anda</span>
                        </label>
                        <select name="assessor_id" required class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-xs font-medium focus:ring-1 focus:ring-lsp-navy" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                            <option value="">-- Pilih Asesor Penguji --</option>
                            @foreach($scheduleAssessors as $asesor)
                                <option value="{{ $asesor->id }}" {{ old('assessor_id', $assessment->assessor_id) == $asesor->id || $scheduleAssessors->count() === 1 ? 'selected' : '' }}>
                                    {{ $asesor->full_name }} (No. Reg: {{ $asesor->assessor_registration_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('assessor_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Bagian 3 : Bukti Kelengkapan Pemohon --}}
                <div>
                    <div class="bg-slate-100 border border-slate-300 px-3.5 py-2 font-bold text-xs text-slate-900 uppercase tracking-wider mb-2">
                        Bagian 3 : Bukti Kelengkapan Pemohon
                    </div>
                    <p class="text-xs text-slate-600 mb-3 italic">
                        Lampirkan dokumen bukti persyaratan dasar dan administratif sesuai acuan skema sertifikasi BNSP.
                    </p>

                    @php
                        $existingProofs = $assessment->proofs->keyBy('proof_type');
                    @endphp

                    {{-- 3.1 Bukti Persyaratan Dasar Pemohon --}}
                    <h3 class="text-xs font-bold text-slate-900 mb-1.5">3.1 Bukti Persyaratan Dasar Pemohon (Pilih minimal salah satu)</h3>
                    <table class="w-full text-xs border border-slate-300 border-collapse mb-4">
                        <thead>
                            <tr class="bg-slate-100 font-bold text-slate-800">
                                <th class="border border-slate-300 px-3 py-2 w-12 text-center">No.</th>
                                <th class="border border-slate-300 px-3 py-2 text-left">Bukti Persyaratan Dasar</th>
                                <th class="border border-slate-300 px-3 py-2 w-72 text-left">Unggah Berkas (PDF/JPG/PNG max 5MB)</th>
                                <th class="border border-slate-300 px-3 py-2 w-28 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">1</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Fotocopy Ijazah SMK Otomotif</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[IJAZAH_SMK]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['IJAZAH_SMK']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">2</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Surat Keterangan Peserta Didik SMK Jurusan Otomotif / Siswa Aktif</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[SURAT_SISWA_AKTIF]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['SURAT_SISWA_AKTIF']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">3</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Fotocopy Ijazah SMA Sederajat + Sertifikat Pelatihan Otomotif Terakreditasi</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[IJAZAH_SMA_SERTIFIKAT]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['IJAZAH_SMA_SERTIFIKAT']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">4</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Surat Keterangan Pengalaman Kerja Bengkel Otomotif Minimal 1 Tahun</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[PENGALAMAN_KERJA]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['PENGALAMAN_KERJA']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- 3.2 Bukti Administratif --}}
                    <h3 class="text-xs font-bold text-slate-900 mb-1.5">3.2 Bukti Administratif (Wajib diunggah seluruhnya)</h3>
                    <table class="w-full text-xs border border-slate-300 border-collapse">
                        <thead>
                            <tr class="bg-slate-100 font-bold text-slate-800">
                                <th class="border border-slate-300 px-3 py-2 w-12 text-center">No.</th>
                                <th class="border border-slate-300 px-3 py-2 text-left">Bukti Administratif</th>
                                <th class="border border-slate-300 px-3 py-2 w-72 text-left">Unggah Berkas</th>
                                <th class="border border-slate-300 px-3 py-2 w-28 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">1</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Fotocopy Kartu Tanda Penduduk (KTP) <span class="text-rose-600 font-bold">*</span></td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[KTP]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['KTP']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-rose-600 font-bold text-[11px]">Wajib</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 text-center font-mono">2</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-800">Pas photo 3x4 background warna merah <span class="text-rose-600 font-bold">*</span></td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="file" name="proofs[PAS_FOTO]" accept=".jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:border-0 file:rounded file:text-xs file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    @if(isset($existingProofs['PAS_FOTO']))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Terunggah</span>
                                    @else
                                        <span class="text-rose-600 font-bold text-[11px]">Wajib</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- FORMULIR FR.APL.02. ASESMEN MANDIRI                      --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden" x-data="{
            setAll(val) {
                document.querySelectorAll('input.radio-kuk-' + val).forEach(el => el.checked = true);
            },
            setDefaultProof(val) {
                if (!val) return;
                document.querySelectorAll('select.select-proof').forEach(el => el.value = val);
            }
        }">
            {{-- Header Dokumen MUK APL.02 --}}
            <div class="border-b border-slate-300 p-5 bg-slate-50 text-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3 mb-3">
                    <div class="font-extrabold text-sm tracking-wider uppercase text-lsp-navy">
                        Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo
                    </div>
                    <div class="text-[11px] font-mono text-slate-500">
                        Skema: {{ $assessment->schedule->scheme->code }}
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                            FR.APL.02. ASESMEN MANDIRI
                        </h2>
                        <p class="text-xs text-slate-600 mt-1 italic">
                            Panduan Asesmen Mandiri: Nilai kesiapan Anda pada setiap Kriteria Unjuk Kerja (KUK) dengan memilih K (Kompeten) atau BK (Belum Kompeten).
                        </p>
                    </div>

                    @if($assessment->apl_status !== 'APPROVED' && $assessment->apl_status !== 'SUBMITTED')
                        <div class="flex items-center gap-2 flex-wrap shrink-0">
                            <button type="button" @click="setAll('K')" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-bold shadow-xs transition">
                                ✓ Pilih Semua Kompeten (K)
                            </button>
                            <button type="button" @click="setAll('BK')" class="px-3 py-1.5 bg-rose-700 hover:bg-rose-800 text-white rounded text-xs font-bold shadow-xs transition">
                                ✕ Semua BK
                            </button>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 border border-slate-300 rounded text-xs font-semibold">
                            🔒 Formulir Terkunci &amp; Terkirim
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-5 sm:p-6 overflow-x-auto space-y-6">
                @php
                    $existingApl02 = $assessment->apl02Items->keyBy('kuk_id');
                    $kukCounter = 1;
                @endphp

                @foreach($assessment->schedule->scheme->competencyUnits as $unit)
                    <div class="border border-slate-300 rounded-lg overflow-hidden">
                        <div class="bg-slate-100 border-b border-slate-300 px-4 py-2.5 font-bold text-xs text-slate-900 flex items-center justify-between">
                            <span>Unit {{ $loop->iteration }}: {{ $unit->code }} - {{ $unit->title }}</span>
                            <span class="text-[11px] font-mono text-slate-500">Standar: SKKNI</span>
                        </div>

                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-800 font-bold border-b border-slate-300">
                                    <th class="p-2.5 w-12 text-center border-r border-slate-300">No</th>
                                    <th class="p-2.5 text-left border-r border-slate-300">Elemen &amp; Kriteria Unjuk Kerja (KUK)</th>
                                    <th class="p-2.5 w-16 text-center border-r border-slate-300 bg-emerald-50 text-emerald-900">K</th>
                                    <th class="p-2.5 w-16 text-center border-r border-slate-300 bg-rose-50 text-rose-900">BK</th>
                                    <th class="p-2.5 w-60 text-left">Bukti Pendukung Relevan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($unit->elements as $element)
                                    <tr class="bg-slate-50/70 text-slate-700 font-bold border-b border-slate-200">
                                        <td class="p-2 text-center text-slate-400 border-r border-slate-300 font-mono">#</td>
                                        <td colspan="4" class="p-2 pl-3">
                                            Elemen {{ $loop->iteration }}: {{ $element->title }}
                                        </td>
                                    </tr>
                                    @foreach($element->kuks as $kuk)
                                        @php
                                            $savedItem = $existingApl02[$kuk->id] ?? null;
                                            $val = $savedItem?->assessment_value ?? 'K';
                                            $proof = $savedItem?->selected_proof_type ?? 'IJAZAH_SMK';
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="p-2.5 text-center text-slate-500 font-mono border-r border-slate-300">{{ $kukCounter++ }}</td>
                                            <td class="p-2.5 pl-4 border-r border-slate-300">
                                                <strong class="font-mono text-slate-800 mr-1.5">{{ $kuk->code_number }}</strong>
                                                <span class="text-slate-700 leading-relaxed">{{ $kuk->statement }}</span>
                                            </td>
                                            <td class="p-2.5 text-center bg-emerald-50/40 border-r border-slate-300">
                                                <input type="radio" name="kuks[{{ $kuk->id }}][value]" value="K" class="radio-kuk-K text-emerald-700 focus:ring-emerald-600 w-4 h-4 cursor-pointer" @checked($val === 'K') @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                            </td>
                                            <td class="p-2.5 text-center bg-rose-50/40 border-r border-slate-300">
                                                <input type="radio" name="kuks[{{ $kuk->id }}][value]" value="BK" class="radio-kuk-BK text-rose-700 focus:ring-rose-600 w-4 h-4 cursor-pointer" @checked($val === 'BK') @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                            </td>
                                            <td class="p-2.5">
                                                <select name="kuks[{{ $kuk->id }}][proof_type]" class="select-proof w-full text-xs border border-slate-300 rounded px-2 py-1 bg-white focus:ring-1 focus:ring-lsp-navy" @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                                                    <option value="IJAZAH_SMK" @selected($proof === 'IJAZAH_SMK')>Ijazah SMK Otomotif</option>
                                                    <option value="SURAT_SISWA_AKTIF" @selected($proof === 'SURAT_SISWA_AKTIF')>Surat Keterangan Siswa Aktif</option>
                                                    <option value="IJAZAH_SMA_SERTIFIKAT" @selected($proof === 'IJAZAH_SMA_SERTIFIKAT')>Ijazah SMA + Sertifikat Pelatihan</option>
                                                    <option value="PENGALAMAN_KERJA" @selected($proof === 'PENGALAMAN_KERJA')>Surat Pengalaman Kerja</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            {{-- Rekomendasi & Tanda Tangan Footer Sesuai MUK --}}
            <div class="p-5 sm:p-6 bg-slate-50 border-t border-slate-300 space-y-6">
                {{-- Pernyataan Integritas --}}
                <div class="p-3.5 bg-white border border-slate-300 rounded-lg">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="statement_integrity" value="1" required class="mt-0.5 rounded text-lsp-navy focus:ring-lsp-navy" {{ old('statement_integrity', '1') ? 'checked' : '' }} @disabled($assessment->apl_status === 'APPROVED' || $assessment->apl_status === 'SUBMITTED')>
                        <span class="text-xs text-slate-800 leading-relaxed">
                            <strong>Pernyataan Pemohon (Asesi):</strong> Saya menyatakan dengan sesungguhnya bahwa seluruh data permohonan sertifikasi (FR.APL.01) dan penilaian mandiri (FR.APL.02) ini saya isi secara jujur, mandiri, dan bertanggung jawab sesuai bukti yang sah dan dapat dipertanggungjawabkan.
                        </span>
                    </label>
                </div>

                {{-- Tabel Tanda Tangan Dua Kolom Resmi --}}
                <table class="w-full text-xs border border-slate-300 border-collapse bg-white">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                            <th class="w-1/2 p-3 text-center border-r border-slate-300">Pemohon / Kandidat (Asesi)</th>
                            <th class="w-1/2 p-3 text-center">Rekomendasi Admin / Asesor LSP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-4 border-r border-slate-300 align-top">
                                <div class="space-y-3">
                                    <x-signature-pad 
                                        name="signature_data" 
                                        :value="$assessment->asesi_signature ?? old('signature_data')" 
                                        :profileSignature="$profile?->signature_data"
                                        label="Tanda Tangan Pemohon" 
                                        :required="true"
                                        height="120" 
                                        :disabled="$assessment->apl_status === 'SUBMITTED' || $assessment->apl_status === 'APPROVED'"
                                    />
                                    <div class="text-slate-700">
                                        Nama : <strong>{{ auth()->user()->full_name }}</strong><br>
                                        Tanggal : <strong>{{ \Carbon\Carbon::parse($assessment->schedule->date)->format('d/m/Y') }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 align-top bg-slate-50/50">
                                <div class="space-y-3">
                                    <div class="p-2 border border-slate-300 rounded bg-white text-xs">
                                        <p class="font-bold text-slate-800 mb-1">Rekomendasi:</p>
                                        <p class="text-slate-600 italic">
                                            @if($assessment->apl_status === 'APPROVED')
                                                <strong class="text-emerald-700">DITERIMA</strong> sebagai peserta sertifikasi.
                                            @elseif($assessment->apl_status === 'SUBMITTED')
                                                Dalam proses verifikasi berkas oleh asesor penguji.
                                            @else
                                                Diisi oleh Asesor saat verifikasi permohonan.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-slate-700 pt-2">
                                        Nama Asesor : <strong>{{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}</strong><br>
                                        No. Reg MET : <strong>{{ $assessment->assessor?->assessor_registration_number ?? '-' }}</strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Single Prominent Primary Action Button --}}
                <div class="flex items-center justify-end pt-2">
                    @if($assessment->apl_status === 'APPROVED')
                        <a href="{{ route('asesi.assessment.ruang-uji', $assessment) }}" class="px-8 py-3 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold rounded-lg shadow-sm transition flex items-center gap-2">
                            <span>Masuk Ruang Uji</span>
                            <span>&rarr;</span>
                        </a>
                    @elseif($assessment->apl_status === 'SUBMITTED')
                        <button type="button" disabled class="px-8 py-3 bg-slate-400 text-white font-bold rounded-lg cursor-not-allowed">
                            Menunggu Verifikasi Asesor
                        </button>
                    @else
                        <button type="submit" class="px-8 py-3.5 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold rounded-lg shadow-sm transition flex items-center gap-2">
                            <span>Simpan &amp; Kirim Permohonan Asesmen (APL-01/02)</span>
                            <span>&rarr;</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
