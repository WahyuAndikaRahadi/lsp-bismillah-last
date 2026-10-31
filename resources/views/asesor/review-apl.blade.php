@extends('layouts.app', ['title' => 'Review APL - ' . $assessment->asesi->full_name])

@section('content')
@php
    $existingProofs = $proofs->keyBy('proof_type');
    $existingApl02 = $apl02Items ?? collect();
    $scheme = $assessment->schedule->scheme;
    $profile = $profile ?? $assessment->asesi->profile;

    $proofLabels = [
        'IJAZAH_SMK' => 'Ijazah SMK Otomotif',
        'SURAT_SISWA_AKTIF' => 'Surat Keterangan Siswa Aktif SMK',
        'IJAZAH_SMA_SERTIFIKAT' => 'Ijazah SMA + Sertifikat Pelatihan Otomotif',
        'PENGALAMAN_KERJA' => 'Surat Pengalaman Kerja Bengkel Otomotif',
        'KTP' => 'Fotokopi KTP / Identitas Diri',
        'PAS_FOTO' => 'Pas Foto 3x4 (Latar Belakang Merah)',
    ];

    $kCount = $existingApl02->where('assessment_value', 'K')->count();
    $bkCount = $existingApl02->where('assessment_value', 'BK')->count();
    $totalKuk = $existingApl02->count();
@endphp

<div class="max-w-6xl mx-auto space-y-8 pb-12" x-data="{ 
    modalOpen: false, 
    modalSrc: '', 
    modalTitle: '',
    viewImage(src, title) {
        this.modalSrc = src;
        this.modalTitle = title;
        this.modalOpen = true;
    }
}">
    {{-- Header & Navigation --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="inline-flex items-center text-xs text-brand-600 hover:text-brand-700 font-semibold mb-2">
                &larr; Kembali ke Daftar Asesi
            </a>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                    Skema: {{ $scheme->code }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 font-mono">
                    {{ $assessment->schedule->code }}
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $scheme->name }}</h1>
            <p class="text-sm text-slate-600 mt-1">
                Asesi: <strong class="text-slate-900">{{ $assessment->asesi->full_name }}</strong> ({{ $assessment->asesi->email }}) &bull;
                TUK: <span class="font-medium text-slate-700">{{ $assessment->schedule->tuk_name }}</span> &bull;
                Tanggal: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }}</span>
            </p>
        </div>

        <div class="text-left md:text-right shrink-0">
            @php
                $statusBadge = match($assessment->apl_status) {
                    'APPROVED' => 'bg-emerald-500 text-white',
                    'SUBMITTED' => 'bg-amber-500 text-white',
                    'REVISION_REQUESTED' => 'bg-rose-500 text-white',
                    'REJECTED' => 'bg-slate-700 text-white',
                    default => 'bg-slate-200 text-slate-700',
                };
                $statusLabel = match($assessment->apl_status) {
                    'APPROVED' => '✓ APL Disetujui Asesor',
                    'SUBMITTED' => '⏳ Menunggu Review Asesor',
                    'REVISION_REQUESTED' => '⚠ Perlu Revisi Asesi',
                    'REJECTED' => '✕ Ditolak',
                    default => 'Draft Permohonan',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm {{ $statusBadge }}">
                {{ $statusLabel }}
            </span>
            @if($assessment->apl_revision_notes)
                <p class="text-xs text-rose-600 mt-1 italic max-w-xs">
                    Catatan: {{ $assessment->apl_revision_notes }}
                </p>
            @endif
        </div>
    </div>

    {{-- Alert Banner --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- BAGIAN 1: FR.APL.01 DATA PEMOHON & BUKTI PENDUKUNG (VIEW ONLY) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-4 flex items-center justify-between text-white">
            <div>
                <h2 class="text-base font-bold flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-white/20 text-xs font-mono">FR.APL.01</span>
                    PERMOHONAN SERTIFIKASI KOMPETENSI
                </h2>
                <p class="text-xs text-slate-300">Bagian 1: Rincian Data Pribadi & Administratif Asesi (View Only)</p>
            </div>
            <span class="text-xs bg-white/10 text-slate-200 px-3 py-1 rounded font-semibold border border-white/10">
                Mode Tinjauan Asesor
            </span>
        </div>

        <div class="p-6 space-y-6">
            {{-- Data Pribadi Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div>
                    <span class="text-xs text-slate-500 block">Nama Lengkap Asesi</span>
                    <span class="font-bold text-slate-900">{{ $assessment->asesi->full_name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">NIK / No. KTP</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $profile?->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Tempat, Tanggal Lahir</span>
                    <span class="font-semibold text-slate-800">
                        {{ $profile?->tempat_lahir ?? '-' }}, {{ $profile?->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Jenis Kelamin / Kebangsaan</span>
                    <span class="font-semibold text-slate-800">
                        {{ $profile?->jenis_kelamin === 'L' ? 'Laki-laki' : ($profile?->jenis_kelamin === 'P' ? 'Perempuan' : '-') }} / {{ $profile?->kebangsaan ?? 'Indonesia' }}
                    </span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-xs text-slate-500 block">Alamat Rumah</span>
                    <span class="font-semibold text-slate-800">{{ $profile?->alamat_rumah ?? '-' }} (Kode Pos: {{ $profile?->kode_pos ?? '-' }})</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Kontak Telepon / Email</span>
                    <span class="font-semibold text-slate-800">{{ $assessment->asesi->phone ?? '-' }} / {{ $assessment->asesi->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Pendidikan Terakhir</span>
                    <span class="font-semibold text-slate-800">{{ $profile?->pendidikan_terakhir ?? '-' }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-xs text-slate-500 block">Institusi / Perusahaan & Jabatan</span>
                    <span class="font-semibold text-slate-800">{{ $profile?->nama_institusi_perusahaan ?? '-' }} ({{ $profile?->jabatan ?? '-' }})</span>
                </div>
            </div>

            {{-- Dokumen Bukti Kelengkapan --}}
            <div>
                <div class="border-b border-slate-200 pb-2 mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Bukti Kelengkapan Dokumen Pemohon</h3>
                    <p class="text-xs text-slate-500">Klik pada dokumen untuk melihat pratinjau gambar bukti pendukung.</p>
                </div>

                {{-- 1. Bukti Persyaratan Dasar --}}
                <div class="mb-6 p-4 rounded-xl border border-indigo-100 bg-indigo-50/40">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                            Bukti Persyaratan Dasar (Minimal 1 Opsi Terpenuhi)
                        </h4>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                            Persyaratan Kompetensi
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $basicProofOptions = [
                                'IJAZAH_SMK' => 'A. Ijazah SMK Otomotif',
                                'SURAT_SISWA_AKTIF' => 'B. Surat Keterangan Siswa Aktif SMK',
                                'IJAZAH_SMA_SERTIFIKAT' => 'C. Ijazah SMA + Sertifikat Pelatihan Otomotif',
                                'PENGALAMAN_KERJA' => 'D. Surat Pengalaman Kerja Bengkel Otomotif',
                            ];
                        @endphp

                        @foreach($basicProofOptions as $type => $label)
                            @php
                                $proof = $existingProofs[$type] ?? null;
                                $isImg = $proof ? preg_match('/\.(jpg|jpeg|png|webp)$/i', $proof->file_path) : false;
                                $fileUrl = $proof ? route('secure-file.proof', $proof) : '#';
                            @endphp
                            <div class="bg-white p-3.5 rounded-lg border {{ $proof ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-slate-200' }} flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-xs font-bold text-slate-800">{{ $label }}</span>
                                        @if($proof)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                                ✓ Terunggah
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] text-slate-400 bg-slate-100 shrink-0">
                                                Tidak Ada
                                            </span>
                                        @endif
                                    </div>

                                    @if($proof)
                                        <p class="text-xs text-slate-600 mt-1 truncate" title="{{ $proof->original_filename }}">
                                            Berkas: <span class="font-mono text-slate-800">{{ $proof->original_filename }}</span>
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-400 mt-1 italic">Asesi tidak mengunggah dokumen ini.</p>
                                    @endif
                                </div>

                                @if($proof)
                                    <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                        @if($isImg)
                                            <button type="button" @click="viewImage('{{ $fileUrl }}', '{{ $label }}')" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Lihat Gambar
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-500 font-medium">Dokumen File</span>
                                        @endif

                                        <a href="{{ $fileUrl }}" target="_blank" class="text-xs font-bold text-brand-600 hover:text-brand-800 inline-flex items-center gap-0.5">
                                            Buka Tab Baru &nearr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Bukti Administratif (KTP & Pas Foto) --}}
                <div class="p-4 rounded-xl border border-amber-100 bg-amber-50/40">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                            Bukti Administratif (Wajib Dilampirkan)
                        </h4>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                            Wajib Terpenuhi
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- KTP --}}
                        @php
                            $ktpProof = $existingProofs['KTP'] ?? null;
                            $ktpIsImg = $ktpProof ? preg_match('/\.(jpg|jpeg|png|webp)$/i', $ktpProof->file_path) : false;
                            $ktpUrl = $ktpProof ? route('secure-file.proof', $ktpProof) : '#';
                        @endphp
                        <div class="bg-white p-3.5 rounded-lg border {{ $ktpProof ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-rose-200 bg-rose-50/20' }} flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-800">Fotokopi KTP / Identitas Diri</span>
                                    @if($ktpProof)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                            ✓ Terunggah
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 shrink-0">
                                            ✕ Belum Diunggah
                                        </span>
                                    @endif
                                </div>

                                @if($ktpProof)
                                    <p class="text-xs text-slate-600 mt-1 truncate" title="{{ $ktpProof->original_filename }}">
                                        Berkas: <span class="font-mono text-slate-800">{{ $ktpProof->original_filename }}</span>
                                    </p>
                                    @if($ktpIsImg)
                                        <div class="mt-2.5 rounded-md overflow-hidden border border-slate-200 bg-slate-50 max-w-[200px] cursor-pointer" @click="viewImage('{{ $ktpUrl }}', 'Fotokopi KTP / Identitas Diri')">
                                            <img src="{{ $ktpUrl }}" alt="KTP Asesi" class="w-full h-24 object-cover hover:opacity-90 transition">
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-rose-500 mt-1">Dokumen KTP belum diunggah asesi.</p>
                                @endif
                            </div>

                            @if($ktpProof)
                                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                    @if($ktpIsImg)
                                        <button type="button" @click="viewImage('{{ $ktpUrl }}', 'Fotokopi KTP / Identitas Diri')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                            Perbesar Gambar
                                        </button>
                                    @endif
                                    <a href="{{ $ktpUrl }}" target="_blank" class="text-xs font-bold text-brand-600 hover:text-brand-800">
                                        Buka Tab Baru &nearr;
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Pas Foto --}}
                        @php
                            $fotoProof = $existingProofs['PAS_FOTO'] ?? null;
                            $fotoIsImg = $fotoProof ? preg_match('/\.(jpg|jpeg|png|webp)$/i', $fotoProof->file_path) : false;
                            $fotoUrl = $fotoProof ? route('secure-file.proof', $fotoProof) : '#';
                        @endphp
                        <div class="bg-white p-3.5 rounded-lg border {{ $fotoProof ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-rose-200 bg-rose-50/20' }} flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-800">Pas Foto 3x4 (Latar Belakang Merah)</span>
                                    @if($fotoProof)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                            ✓ Terunggah
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 shrink-0">
                                            ✕ Belum Diunggah
                                        </span>
                                    @endif
                                </div>

                                @if($fotoProof)
                                    <p class="text-xs text-slate-600 mt-1 truncate" title="{{ $fotoProof->original_filename }}">
                                        Berkas: <span class="font-mono text-slate-800">{{ $fotoProof->original_filename }}</span>
                                    </p>
                                    @if($fotoIsImg)
                                        <div class="mt-2.5 rounded-md overflow-hidden border border-slate-200 bg-slate-50 w-20 cursor-pointer" @click="viewImage('{{ $fotoUrl }}', 'Pas Foto 3x4')">
                                            <img src="{{ $fotoUrl }}" alt="Pas Foto Asesi" class="w-full h-24 object-cover hover:opacity-90 transition">
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-rose-500 mt-1">Pas Foto belum diunggah asesi.</p>
                                @endif
                            </div>

                            @if($fotoProof)
                                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                    @if($fotoIsImg)
                                        <button type="button" @click="viewImage('{{ $fotoUrl }}', 'Pas Foto 3x4')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                            Perbesar Gambar
                                        </button>
                                    @endif
                                    <a href="{{ $fotoUrl }}" target="_blank" class="text-xs font-bold text-brand-600 hover:text-brand-800">
                                        Buka Tab Baru &nearr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: FR.APL.02 ASESMEN MANDIRI (VIEW ONLY) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between text-white gap-3">
            <div>
                <h2 class="text-base font-bold flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-white/20 text-xs font-mono">FR.APL.02</span>
                    ASESMEN MANDIRI
                </h2>
                <p class="text-xs text-indigo-200">Hasil Penilaian Mandiri Asesi terhadap Unit & Kriteria Unjuk Kerja (KUK)</p>
            </div>

            {{-- Summary Badges --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 bg-white/10 rounded-lg text-xs font-semibold">
                    Total KUK: <strong class="text-white">{{ $totalKuk }}</strong>
                </span>
                <span class="px-3 py-1 bg-emerald-500/90 rounded-lg text-xs font-bold text-white shadow-sm">
                    K: {{ $kCount }}
                </span>
                <span class="px-3 py-1 bg-rose-500/90 rounded-lg text-xs font-bold text-white shadow-sm">
                    BK: {{ $bkCount }}
                </span>
            </div>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 border-b border-slate-200 uppercase font-bold tracking-wider">
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Unit Kompetensi / Elemen / Kriteria Unjuk Kerja (KUK)</th>
                        <th class="p-3 w-20 text-center bg-emerald-50 text-emerald-800">K</th>
                        <th class="p-3 w-20 text-center bg-rose-50 text-rose-800">BK</th>
                        <th class="p-3 w-64">Bukti Pendukung Terpilih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php $kukCounter = 1; @endphp
                    @forelse($scheme->competencyUnits as $unit)
                        <tr class="bg-slate-100 font-bold text-slate-800">
                            <td colspan="5" class="p-2.5 bg-slate-100 text-slate-900 border-t border-slate-300">
                                Unit {{ $loop->iteration }}: {{ $unit->code }} - {{ $unit->title }}
                            </td>
                        </tr>
                        @foreach($unit->elements as $element)
                            <tr class="bg-slate-50 text-slate-700 font-semibold italic">
                                <td class="p-2 text-center text-slate-400">#</td>
                                <td colspan="4" class="p-2 pl-4 text-slate-700">
                                    Elemen {{ $loop->iteration }}: {{ $element->title }}
                                </td>
                            </tr>
                            @foreach($element->kuks as $kuk)
                                @php
                                    $savedItem = $existingApl02[$kuk->id] ?? null;
                                    $val = $savedItem?->assessment_value ?? '-';
                                    $proofCode = $savedItem?->selected_proof_type ?? '-';
                                    $proofName = $proofLabels[$proofCode] ?? ($proofCode !== '-' ? $proofCode : 'Belum Ditentukan');
                                @endphp
                                <tr class="hover:bg-indigo-50/20 transition">
                                    <td class="p-3 text-center text-slate-500 font-mono">{{ $kukCounter++ }}</td>
                                    <td class="p-3 pl-6">
                                        <span class="font-bold text-slate-700 mr-1">{{ $kuk->code_number }}</span>
                                        <span class="text-slate-600">{{ $kuk->statement }}</span>
                                    </td>
                                    <td class="p-3 text-center bg-emerald-50/30">
                                        @if($val === 'K')
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white font-black text-xs shadow-sm">
                                                ✓
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-mono">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center bg-rose-50/30">
                                        @if($val === 'BK')
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-500 text-white font-black text-xs shadow-sm">
                                                ✕
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-mono">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $proofName }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">Tidak ada unit kompetensi pada skema ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Signature Display Section --}}
        <div class="p-6 bg-slate-50 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanda Tangan Pemohon (Asesi)</h4>
                @if($profile?->signature_data || $assessment->asesi_signature)
                    <div class="bg-white p-3 rounded-lg border border-slate-200 inline-block">
                        <img src="{{ $profile?->signature_data ?? $assessment->asesi_signature }}" alt="Tanda Tangan Asesi" class="max-h-24 object-contain">
                    </div>
                    <p class="text-xs font-semibold text-slate-800 mt-1">{{ $assessment->asesi->full_name }}</p>
                    <p class="text-[11px] text-slate-500">Tanda tangan digital tersimpan</p>
                @else
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                        Asesi belum merekam tanda tangan digital pada profil permohonan.
                    </div>
                @endif
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Asesor Penilai</h4>
                @if(auth()->user()->profile?->signature_data)
                    <div class="bg-white p-2.5 rounded-lg border border-slate-200 inline-block mb-2">
                        <img src="{{ auth()->user()->profile->signature_data }}" alt="Tanda Tangan Asesor" class="max-h-20 object-contain">
                    </div>
                @endif
                <div class="text-xs text-slate-700 space-y-1">
                    <p>Nama Asesor: <strong class="text-slate-900">{{ auth()->user()->full_name }}</strong></p>
                    <p>No. Registrasi: <span class="font-mono text-slate-900">{{ auth()->user()->assessor_registration_number ?? (auth()->user()->profile?->no_reg_asesor ?? 'Belum diatur') }}</span></p>
                    <p>Status Review: 
                        <span class="font-bold {{ $assessment->apl_status === 'APPROVED' ? 'text-emerald-600' : ($assessment->apl_status === 'REVISION_REQUESTED' ? 'text-rose-600' : 'text-amber-600') }}">
                            {{ $statusLabel }}
                        </span>
                    </p>
                    @if($assessment->approved_at)
                        <p class="text-[11px] text-slate-500">Disetujui pada: {{ \Carbon\Carbon::parse($assessment->approved_at)->translatedFormat('d F Y H:i') }} WIB</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 3: FORM KEPUTUSAN REVIEW ASESOR (INTERACTIVE ACTION PANEL) --}}
    <div class="bg-white rounded-xl shadow-md border-2 border-indigo-200 overflow-hidden" x-data="{
        decision: '{{ in_array($assessment->apl_status, ['APPROVED', 'REVISION_REQUESTED', 'REJECTED']) ? $assessment->apl_status : 'APPROVED' }}',
        notes: '{{ addslashes($assessment->apl_revision_notes ?? '') }}'
    }">
        <div class="bg-gradient-to-r from-lsp-navy to-lsp-navy-dark px-6 py-4 text-white flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold">Formulir Keputusan Review APL (Asesor)</h3>
                <p class="text-xs text-slate-200">Tetapkan kelayakan asesi untuk mengikuti proses asesmen kompetensi</p>
            </div>
            <span class="text-xs bg-lsp-red px-2.5 py-1 rounded font-bold text-white shadow-sm">
                Aksi Keputusan
            </span>
        </div>

        <form action="{{ route('asesor.review-apl.update', $assessment->id) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                    Pilih Keputusan Hasil Review:
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Setujui --}}
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                           :class="decision === 'APPROVED' ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-emerald-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Diterima (Disetujui)
                            </span>
                            <input type="radio" name="apl_status" value="APPROVED" x-model="decision" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        </div>
                        <p class="text-xs text-slate-600">
                            Berkas administratif lengkap dan asesmen mandiri memenuhi syarat untuk lanjut ke ruang uji.
                        </p>
                    </label>

                    {{-- Minta Revisi --}}
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                           :class="decision === 'REVISION_REQUESTED' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-amber-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Minta Revisi
                            </span>
                            <input type="radio" name="apl_status" value="REVISION_REQUESTED" x-model="decision" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                        </div>
                        <p class="text-xs text-slate-600">
                            Dokumen bukti belum lengkap atau ada KUK yang perlu diperbaiki oleh asesi.
                        </p>
                    </label>

                    {{-- Tolak --}}
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                           :class="decision === 'REJECTED' ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-rose-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                Tidak Diterima (Ditolak)
                            </span>
                            <input type="radio" name="apl_status" value="REJECTED" x-model="decision" class="text-rose-600 focus:ring-rose-500 w-4 h-4">
                        </div>
                        <p class="text-xs text-slate-600">
                            Asesi tidak memenuhi persyaratan dasar skema sertifikasi secara substansial.
                        </p>
                    </label>
                </div>
            </div>

            {{-- Catatan Review --}}
            <div x-show="decision === 'REVISION_REQUESTED' || decision === 'REJECTED'" x-transition class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Catatan Review Asesor <span class="text-rose-500">*</span>
                </label>
                <textarea name="notes" rows="3" x-model="notes"
                          class="w-full text-xs rounded-lg border border-slate-300 p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                          placeholder="Tuliskan catatan perbaikan dokumen atau alasan penolakan secara spesifik..."
                          :required="decision === 'REVISION_REQUESTED' || decision === 'REJECTED'"></textarea>
                <p class="text-[11px] text-slate-500">Catatan ini akan langsung ditampilkan kepada asesi pada halaman APL mereka.</p>
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">
                    &larr; Batalkan & Kembali
                </a>

                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-xl font-bold text-sm shadow-md transition transform active:scale-95 flex items-center justify-center gap-2"
                        onclick="return confirm('Apakah Anda yakin ingin menyimpan keputusan review APL ini?')">
                    <span>Simpan & Tetapkan Keputusan</span>
                    <span>→</span>
                </button>
            </div>
        </form>
    </div>

    {{-- MODAL LIGHTBOX IMAGE VIEWER --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-200 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 bg-slate-800 text-white flex items-center justify-between">
                <h3 class="text-sm font-bold truncate" x-text="modalTitle">Pratinjau Dokumen</h3>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 bg-slate-100 flex items-center justify-center overflow-auto flex-1">
                <img :src="modalSrc" alt="Preview Dokumen" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm">
            </div>
            <div class="px-6 py-3 bg-white border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500">Pratinjau Resolusi Asli</span>
                <div class="flex items-center gap-3">
                    <a :href="modalSrc" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Buka di Tab Baru &nearr;
                    </a>
                    <button type="button" @click="modalOpen = false" class="px-4 py-1.5 text-xs font-semibold bg-slate-200 hover:bg-slate-300 rounded-lg text-slate-800">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
