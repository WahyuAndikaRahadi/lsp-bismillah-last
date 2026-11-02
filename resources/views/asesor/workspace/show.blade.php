@extends('layouts.app', ['title' => 'Workspace Asesi - ' . $assessment->asesi->full_name])

@section('content')
<div class="space-y-6 pb-12" x-data="{ 
    tab: '{{ $activeTab }}',
    modalOpen: false, 
    modalSrc: '', 
    modalTitle: '',
    viewImage(src, title) {
        this.modalSrc = src;
        this.modalTitle = title;
        this.modalOpen = true;
    }
}">
    {{-- Header & Candidate Information Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="space-y-2">
            <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="inline-flex items-center text-xs text-brand-600 hover:text-brand-700 font-bold mb-1">
                &larr; Kembali ke Daftar Asesi Jadwal
            </a>
            <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $scheme->code }}
                </span>
                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $assessment->schedule->code }}
                </span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded border {{ $assessment->apl_status === 'APPROVED' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                    APL: {{ str_replace('_', ' ', $assessment->apl_status ?? 'DRAFT') }}
                </span>
                @if($assessment->final_result)
                    <span class="text-xs font-black px-2.5 py-0.5 rounded border {{ in_array($assessment->final_result, ['KOMPETEN', 'K'], true) ? 'bg-emerald-100 text-emerald-800 border-emerald-400' : 'bg-rose-100 text-rose-800 border-rose-400' }}">
                        Hasil: {{ $assessment->final_result }}
                    </span>
                @endif
            </div>

            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $assessment->asesi->full_name }}
            </h1>

            <div class="flex items-center text-xs text-slate-500 space-x-4 flex-wrap gap-y-1">
                <span>NIK: <strong class="text-slate-800 font-mono">{{ $profile?->nik ?? '-' }}</strong></span>
                <span>&bull;</span>
                <span>Email: <strong class="text-slate-800">{{ $assessment->asesi->email }}</strong></span>
                <span>&bull;</span>
                <span>TUK: <strong class="text-slate-800">{{ $assessment->schedule->tuk_name }}</strong></span>
                <span>&bull;</span>
                <span>Tanggal: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($assessment->schedule->date)->isoFormat('D MMMM Y') }}</strong></span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
            <span class="text-xs px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200 text-center">
                Asesor: <strong>{{ auth()->user()->full_name }}</strong>
            </span>
        </div>
    </div>

    {{-- Alert Banner --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-900 rounded-lg text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-900 rounded-lg text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Unified Tab Navigation --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-1.5 flex items-center space-x-1 overflow-x-auto text-xs font-bold">
        <button type="button" @click="tab = 'apl'" 
            :class="tab === 'apl' ? 'bg-lsp-navy text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            class="px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center space-x-2">
            <span>{{ '1. Review FR.APL (01 & 02)' }}</span>
            @if($assessment->apl_status === 'APPROVED')
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            @elseif($assessment->apl_status === 'SUBMITTED')
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            @endif
        </button>

        <button type="button" @click="tab = 'ak01'" 
            :class="tab === 'ak01' ? 'bg-lsp-navy text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            class="px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center space-x-2">
            <span>2. FR.AK.01 Persetujuan</span>
            @if(!empty($assessment->assessor_signature) || !empty($assessment->approved_at))
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            @endif
        </button>

        <button type="button" @click="tab = 'ia05'" 
            :class="tab === 'ia05' ? 'bg-lsp-navy text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            class="px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center space-x-2">
            <span>3. Teori CBT (FR.IA.05)</span>
            @if($cbtScore > 0)
                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
            @endif
        </button>

        <button type="button" @click="tab = 'praktik'" 
            :class="tab === 'praktik' ? 'bg-lsp-navy text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            class="px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center space-x-2">
            <span>{{ '4. Observasi Praktik & PMO' }}</span>
            @if($ia01Done || $ia02Done)
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
            @endif
        </button>

        <button type="button" @click="tab = 'ak02'" 
            :class="tab === 'ak02' ? 'bg-lsp-navy text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            class="px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center space-x-2">
            <span>5. FR.AK.02 Rekaman Asesmen</span>
            @if($assessment->final_result)
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            @endif
        </button>
    </div>

    {{-- TAB 1: REVIEW FR.APL (01 & 02) --}}
    <div x-show="tab === 'apl'" class="space-y-6">
        {{-- Decision Action Box --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Keputusan Verifikasi Permohonan & Asesmen Mandiri</h3>
                    <p class="text-xs text-slate-500">Tentukan kelayakan dokumen APL-01 dan bukti asesmen mandiri APL-02 asesi</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $assessment->apl_status === 'APPROVED' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                    Status Saat Ini: {{ str_replace('_', ' ', $assessment->apl_status ?? 'DRAFT') }}
                </span>
            </div>

            <form action="{{ route('asesor.review-apl.update', $assessment->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Asesor (Wajib diisi bila meminta revisi / perbaikan):</label>
                    <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-lsp-navy" placeholder="Tuliskan catatan perbaikan berkas atau rekomendasi bila diperlukan...">{{ old('notes', $assessment->apl_revision_notes ?? '') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="submit" name="apl_status" value="REVISION_REQUESTED" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold shadow-xs transition">
                        ⚠ Minta Revisi Berkas
                    </button>
                    <button type="submit" name="apl_status" value="REJECTED" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-xs transition" onclick="return confirm('Apakah Anda yakin ingin menolak permohonan asesi ini?')">
                        ✕ Tolak Permohonan
                    </button>
                    <button type="submit" name="apl_status" value="APPROVED" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                        ✓ Setujui Permohonan (APL Disetujui)
                    </button>
                </div>
            </form>
        </div>

        {{-- APL-01 Summary (Data Pribadi & Unit) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="bg-slate-900 text-white px-5 py-3 font-bold flex items-center justify-between">
                <span>FR.APL.01 &bull; Data Pemohon &amp; Kelengkapan Dokumen</span>
                <span class="text-[11px] font-normal text-slate-300">Format Resmi BNSP</span>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-slate-500 block">Nama Lengkap:</span>
                        <strong class="text-slate-900">{{ $assessment->asesi->full_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">NIK:</span>
                        <strong class="text-slate-900 font-mono">{{ $profile?->nik ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Tempat, Tgl Lahir:</span>
                        <strong class="text-slate-900">{{ $profile?->tempat_lahir ?? '-' }}, {{ $profile?->tanggal_lahir ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">No. HP / Telepon:</span>
                        <strong class="text-slate-900">{{ $profile?->no_hp ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Pekerjaan / Institusi:</span>
                        <strong class="text-slate-900">{{ $profile?->nama_institusi_perusahaan ?? $profile?->nama_institusi ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Tanda Tangan Asesi:</span>
                        @if($profile?->signature_data || $assessment->asesi_signature)
                            <span class="text-emerald-700 font-bold">✓ Terdaftar & Sah</span>
                        @else
                            <span class="text-rose-600 font-bold">Belum Tersedia</span>
                        @endif
                    </div>
                </div>

                {{-- Bukti Dokumen --}}
                <div class="border-t border-slate-200 pt-3">
                    <span class="font-bold text-slate-800 block mb-2">Berkas Bukti Persyaratan yang Diunggah:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @forelse($proofs as $proof)
                            <div class="border border-slate-200 rounded-lg p-2.5 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 text-[11px] block">{{ $proof->proof_name ?? $proof->proof_type }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $proof->file_name ?? 'Dokumen Asesi' }}</span>
                                </div>
                                @if($proof->file_url ?? $proof->file_path)
                                    <a href="{{ Storage::url($proof->file_path ?? '') }}" target="_blank" class="px-2 py-1 rounded bg-white hover:bg-slate-100 text-brand-600 border border-slate-300 text-[10px] font-bold">
                                        Buka &rarr;
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="text-slate-400 italic text-[11px]">Belum ada bukti yang dilampirkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- APL-02 Asesmen Mandiri Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="bg-slate-900 text-white px-5 py-3 font-bold flex items-center justify-between">
                <span>FR.APL.02 &bull; Asesmen Mandiri (K/BK)</span>
                <span class="text-[11px] font-normal text-slate-300">Total KUK: {{ $apl02Items->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2.5 text-center w-12">No</th>
                            <th class="px-4 py-2.5 text-left">Kriteria Unjuk Kerja (KUK)</th>
                            <th class="px-3 py-2.5 text-center w-20">Pilihan Asesi</th>
                            <th class="px-4 py-2.5 text-left">Bukti yang Diacu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $counter = 1; @endphp
                        @foreach($scheme->competencyUnits as $unit)
                            <tr class="bg-slate-50/80 font-bold text-slate-800 border-t border-slate-200">
                                <td colspan="4" class="px-4 py-2 text-[11px]">
                                    Unit {{ $unit->code }}: {{ $unit->title }}
                                </td>
                            </tr>
                            @foreach($unit->elements as $elem)
                                @foreach($elem->kuks as $kuk)
                                    @php
                                        $item = $apl02Items->get($kuk->id);
                                        $val = $item?->assessment_value ?? 'K';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-2 text-center text-slate-400 font-mono">{{ $counter++ }}</td>
                                        <td class="px-4 py-2 text-slate-700">{{ $kuk->criteria }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $val === 'K' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $val }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-slate-500 text-[11px]">{{ $item?->proof_type ?? 'Bukti Portofolio / Ijazah' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TAB 2: FR.AK.01 PERSETUJUAN ASESMEN & KERAHASIAAN --}}
    <div x-show="tab === 'ak01'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">FR.AK.01 Persetujuan Asesmen dan Kerahasiaan</h3>
                    <p class="text-xs text-slate-500">Perjanjian pelaksanaan uji kompetensi dan komitmen kerahasiaan materi uji</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ (!empty($assessment->assessor_signature) || !empty($assessment->approved_at)) ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                    {{ (!empty($assessment->assessor_signature) || !empty($assessment->approved_at)) ? '✓ Disepakati Asesor & Asesi' : 'Menunggu TTD Asesor' }}
                </span>
            </div>

            <div class="rounded-lg bg-blue-50/60 border border-blue-200 p-4 text-blue-900 space-y-2">
                <h4 class="font-bold text-xs uppercase tracking-wider">Klausul Kesepakatan Asesmen:</h4>
                <p class="text-xs leading-relaxed text-blue-800">
                    Pelaksanaan asesmen kompetensi disepakati menggunakan instrumen uji resmi LSP PrO Indo berstandar BNSP. Asesi dan asesor menjamin kerahasiaan materi uji dan menyatakan bahwa seluruh bukti yang disampaikan adalah orisinal, valid, asli, terkini, dan memadai (VATM).
                </p>
            </div>

            {{-- Dual Signatures Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                {{-- Asesi Signature Box --}}
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">Tanda Tangan Asesi</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                    </div>
                    <div class="border border-slate-200 bg-white rounded-lg p-3 text-center min-h-[100px] flex items-center justify-center">
                        @if($assessment->asesi_signature ?? $profile?->signature_data)
                            <img src="{{ $assessment->asesi_signature ?? $profile?->signature_data }}" alt="TTD Asesi" class="max-h-20 max-w-full">
                        @else
                            <span class="text-slate-400 italic">Tanda tangan digital asesi</span>
                        @endif
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Nama: <strong class="text-slate-800">{{ $assessment->asesi->full_name }}</strong><br>
                        Tanggal: <span class="text-slate-700">{{ $assessment->ak01_agreed_at ? \Carbon\Carbon::parse($assessment->ak01_agreed_at)->isoFormat('D MMMM Y HH:mm') : 'Disetujui bersama pendaftaran' }}</span>
                    </div>
                </div>

                {{-- Asesor Signature Box --}}
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">Tanda Tangan Asesor</span>
                        @if(!empty($assessment->assessor_signature) || !empty($assessment->approved_at))
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ Sah &amp; Disepakati</span>
                        @else
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800">Belum Ditandatangani</span>
                        @endif
                    </div>
                    <div class="border border-slate-200 bg-white rounded-lg p-3 text-center min-h-[100px] flex items-center justify-center">
                        @if($assessment->assessor_signature ?? auth()->user()->profile?->signature_data)
                            <img src="{{ $assessment->assessor_signature ?? auth()->user()->profile?->signature_data }}" alt="TTD Asesor" class="max-h-20 max-w-full">
                        @else
                            <span class="text-slate-400 italic">Tanda tangan digital asesor</span>
                        @endif
                    </div>
                    <div class="text-[11px] text-slate-500 flex items-center justify-between">
                        <div>
                            Nama: <strong class="text-slate-800">{{ auth()->user()->full_name }}</strong><br>
                            Status: <span class="text-slate-700">{{ $assessment->assessor_signed_at ? \Carbon\Carbon::parse($assessment->assessor_signed_at)->isoFormat('D MMMM Y HH:mm') : 'Tanda tangan profil aktif' }}</span>
                        </div>
                        @if(empty($assessment->assessor_signature) && empty($assessment->approved_at))
                            <form action="{{ route('asesor.ak01.sign', $assessment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="assessor_signature" value="{{ auth()->user()->profile?->signature_data }}">
                                <button type="submit" class="px-3 py-1.5 bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded text-xs font-bold shadow-xs transition">
                                    Tandatangani FR.AK.01
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: TEORI CBT (FR.IA.05) --}}
    <div x-show="tab === 'ia05'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
                <div class="w-11 h-11 rounded-lg {{ $isCbtPassed ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nilai Akhir CBT</p>
                    <h3 class="text-xl font-black {{ $isCbtPassed ? 'text-emerald-600' : 'text-rose-600' }}">{{ $cbtScore }}%</h3>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
                <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Passing Grade</p>
                    <h3 class="text-xl font-bold text-slate-900">{{ $passingGrade }}%</h3>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center space-x-4">
                <div class="w-11 h-11 rounded-lg {{ $isCbtPassed ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
                    <span class="font-bold text-lg">{{ $isCbtPassed ? 'K' : 'BK' }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hasil Teori</p>
                    <h3 class="text-sm font-bold {{ $isCbtPassed ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $isCbtPassed ? 'Memenuhi Standar (K)' : 'Belum Memenuhi (BK)' }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- CBT Breakdown Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="bg-slate-900 text-white px-5 py-3 font-bold flex items-center justify-between">
                <span>FR.IA.05 &bull; Rincian Butir Jawaban Ujian Tertulis Pilihan Ganda</span>
                <span class="text-[11px] font-normal text-slate-300">Total Soal: {{ count($cbtBreakdown) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2.5 text-center w-12">No</th>
                            <th class="px-4 py-2.5 text-left">Pertanyaan CBT</th>
                            <th class="px-3 py-2.5 text-center w-24">Jawaban Asesi</th>
                            <th class="px-3 py-2.5 text-center w-24">Kunci</th>
                            <th class="px-3 py-2.5 text-center w-20">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($cbtBreakdown as $i => $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-2 text-center text-slate-400 font-mono">{{ $i + 1 }}</td>
                                <td class="px-4 py-2 text-slate-800">{{ $item->question ?? $item['question'] ?? 'Pertanyaan' }}</td>
                                <td class="px-3 py-2 text-center font-mono font-bold">{{ $item->user_answer ?? $item['user_answer'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-center font-mono font-bold text-slate-500">{{ $item->correct_answer ?? $item['correct_answer'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @if($item->is_correct ?? $item['is_correct'] ?? false)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Benar</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Salah</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400 italic">
                                    Asesi belum menyelesaikan ujian tertulis CBT FR.IA.05.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TAB 4: OBSERVASI PRAKTIK & PMO + AK.07 --}}
    <div x-show="tab === 'praktik'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- FR.IA.01: Ceklis Observasi --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">FR.IA.01</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $ia01Done ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $ia01Done ? '✓ Dinilai' : 'Belum Dinilai' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Ceklis Observasi Aktivitas di Tempat Kerja</h3>
                    <p class="text-xs text-slate-500">Penilaian observasi langsung terhadap Kriteria Unjuk Kerja (KUK) asesi pada setiap unit.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia01']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-xs transition">
                        Buka Penilaian IA.01 &rarr;
                    </a>
                </div>
            </div>

            {{-- FR.IA.01B: Lembar Pemeriksaan Teknis --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">FR.IA.01B</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $ia01bDone ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $ia01bDone ? '✓ Report Sheet Terisi' : 'Belum Terisi' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Lembar Pemeriksaan Teknis (Report Sheet)</h3>
                    <p class="text-xs text-slate-500">Pencatatan dan verifikasi hasil pengukuran parameter fisik komponen kendaraan.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia01b']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-xs transition">
                        Buka Lembar IA.01B &rarr;
                    </a>
                </div>
            </div>

            {{-- FR.IA.02: Tugas Praktik Demonstrasi --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">FR.IA.02</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $ia02Done ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $ia02Done ? '✓ Dinilai' : 'Belum Dinilai' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Tugas Praktik Demonstrasi (TPD)</h3>
                    <p class="text-xs text-slate-500">Penilaian pelaksanaan tugas praktik unjuk kerja berdasarkan skenario di workshop TUK.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia02']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-xs transition">
                        Buka Penilaian IA.02 &rarr;
                    </a>
                </div>
            </div>

            {{-- FR.IA.03: Pertanyaan Mendukung Observasi --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">FR.IA.03</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $ia03Done ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $ia03Done ? '✓ Dinilai' : 'Belum Dinilai' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Pertanyaan Mendukung Observasi (PMO / Lisan)</h3>
                    <p class="text-xs text-slate-500">Daftar pertanyaan lisan terstruktur untuk mengonfirmasi pemahaman teknis dan dasar teori asesi.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('asesor.assessment.instrument', [$assessment->id, 'ia03']) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-lsp-navy hover:bg-lsp-navy-dark text-white rounded-lg shadow-xs transition">
                        Buka Penilaian IA.03 &rarr;
                    </a>
                </div>
            </div>

            {{-- FR.AK.07: Penyesuaian yang Wajar dan Beralasan --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between md:col-span-2">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">FR.AK.07</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                            {{ $ak07Item ? ($ak07Item->adjustment_category ?? 'Standar') : 'Asesmen Standar' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">FR.AK.07 Penyesuaian yang Wajar dan Beralasan (Hanya Asesor)</h3>
                    <p class="text-xs text-slate-500">Diisi oleh Asesor bila diperlukan penyesuaian khusus (kandidat berkebutuhan khusus, kendala fasilitas TUK, dsb). Formulir ini tidak pernah diisi oleh asesi.</p>
                    <p class="text-xs text-slate-700 font-medium">
                        Kondisi: <strong>{{ $ak07Item->adjustment_detail ?? 'Tidak ada penyesuaian khusus yang diperlukan (asesmen standar).' }}</strong>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                    <a href="{{ route('asesor.ak07.create', $assessment->schedule_id) }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-xs transition">
                        Kelola FR.AK.07 Jadwal &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 5: FR.AK.02 REKAMAN ASESMEN KOMPETENSI RESMI --}}
    <div x-show="tab === 'ak02'" class="space-y-6">
        <form action="{{ route('asesor.assessment.ak02.update', $assessment->id) }}" method="POST" class="space-y-6" x-data="{
            setAllUnits(val) {
                document.querySelectorAll('.unit-result-select').forEach(el => el.value = val);
                const finalSel = document.getElementById('workspaceFinalResult');
                if (finalSel) {
                    finalSel.value = (val == 1) ? 'KOMPETEN' : 'BELUM_KOMPETEN';
                }
            },
            toggleMethod(name) {
                const boxes = document.querySelectorAll(`input[name*='[${name}]']`);
                const allChecked = Array.from(boxes).every(b => b.checked);
                boxes.forEach(b => b.checked = !allChecked);
            }
        }">
            @csrf

            {{-- Unit vs Methods Table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-xs">
                <div class="px-5 py-4 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold flex items-center space-x-2">
                            <span>FR.AK.02 REKAMAN ASESMEN KOMPETENSI</span>
                        </h2>
                        <p class="text-[11px] text-slate-300">Pemberian tanda centang bukti metode asesmen per unit kompetensi</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" @click="setAllUnits(1)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded text-xs font-bold shadow-xs transition flex items-center gap-1">
                            Semua K
                        </button>
                        <button type="button" @click="setAllUnits(0)" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded text-xs font-bold shadow-xs transition flex items-center gap-1">
                            Semua BK
                        </button>
                        <button type="button" @click="toggleMethod('observasi_demonstrasi')" class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded text-xs font-medium transition">
                            + Observasi
                        </button>
                        <button type="button" @click="toggleMethod('tulis')" class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded text-xs font-medium transition">
                            + Tulis (CBT)
                        </button>
                        <button type="button" @click="toggleMethod('lisan')" class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded text-xs font-medium transition">
                            + Lisan (PMO)
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">No</th>
                                <th class="px-4 py-3 text-left min-w-[200px]">Kode &amp; Judul Unit Kompetensi</th>
                                <th class="px-2 py-3 text-center">Observasi</th>
                                <th class="px-2 py-3 text-center">Portofolio</th>
                                <th class="px-2 py-3 text-center">Lisan (PMO)</th>
                                <th class="px-2 py-3 text-center">Tulis (CBT)</th>
                                <th class="px-2 py-3 text-center">Pihak Ketiga</th>
                                <th class="px-3 py-3 text-center w-36">Hasil Unit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($units as $idx => $unit)
                                @php
                                    $item = $ak02Items->get($unit->id);
                                    $methods = $item?->methods_json ?? [];
                                    $isCompetent = $item ? $item->is_competent : true;
                                @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-bold text-slate-800 text-[11px] block">{{ $unit->code }}</span>
                                        <span class="text-slate-700">{{ $unit->title }}</span>
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <input type="checkbox" name="units[{{ $unit->id }}][observasi_demonstrasi]" value="1" {{ !empty($methods['observasi_demonstrasi']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <input type="checkbox" name="units[{{ $unit->id }}][portofolio]" value="1" {{ !empty($methods['portofolio']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <input type="checkbox" name="units[{{ $unit->id }}][lisan]" value="1" {{ !empty($methods['lisan']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <input type="checkbox" name="units[{{ $unit->id }}][tulis]" value="1" {{ !empty($methods['tulis']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <input type="checkbox" name="units[{{ $unit->id }}][pihak_ketiga]" value="1" {{ !empty($methods['pihak_ketiga']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy cursor-pointer">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <select name="units[{{ $unit->id }}][is_competent]" class="unit-result-select text-xs font-bold rounded border-slate-300 focus:ring-lsp-navy py-1 px-2 w-full {{ $isCompetent ? 'text-emerald-700' : 'text-rose-700' }}">
                                            <option value="1" {{ $isCompetent ? 'selected' : '' }}>Kompeten (K)</option>
                                            <option value="0" {{ !$isCompetent ? 'selected' : '' }}>Belum Kompeten (BK)</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Final Recommendation & Signatures --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Penetapan Keputusan Hasil Asesmen Akhir</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Rekomendasi Hasil Asesmen Akhir (*):</label>
                        <select name="final_result" id="workspaceFinalResult" required class="w-full text-xs font-bold rounded-lg border-slate-300 focus:ring-lsp-navy">
                            <option value="" disabled {{ empty($assessment->final_result) ? 'selected' : '' }}>-- Tentukan Rekomendasi Asesor (Wajib) --</option>
                            <option value="KOMPETEN" {{ in_array($assessment->final_result, ['KOMPETEN', 'K'], true) ? 'selected' : '' }}>KOMPETEN (K)</option>
                            <option value="BELUM_KOMPETEN" {{ in_array($assessment->final_result, ['BELUM_KOMPETEN', 'BK'], true) ? 'selected' : '' }}>BELUM KOMPETEN (BK)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tindak Lanjut yang Diperlukan:</label>
                        <input type="text" name="follow_up_action" value="{{ old('follow_up_action', $assessment->follow_up_action ?? ($assessment->final_result === 'KOMPETEN' ? 'Diusulkan untuk penerbitan Sertifikat Kompetensi BNSP.' : 'Pelaksanaan asesmen ulang pada unit yang belum kompeten.')) }}" class="w-full text-xs rounded-lg border-slate-300 focus:ring-lsp-navy" placeholder="Tindak lanjut jika K atau pengayaan bila BK">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Komentar Objektif Asesor:</label>
                    <textarea name="final_notes" rows="3" class="w-full text-xs rounded-lg border-slate-300 focus:ring-lsp-navy" placeholder="Catatan unjuk kerja asesi...">{{ old('final_notes', $assessment->final_notes ?? 'Seluruh unjuk kerja memenuhi Kriteria Unjuk Kerja (KUK) dan standar SKKNI.') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                    <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-lsp-navy hover:bg-lsp-navy-dark rounded-lg shadow-sm transition">
                        Simpan Rekaman Asesmen (FR.AK.02)
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
