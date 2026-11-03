@extends('layouts.app')

@section('title', 'Buat Rencana Surveilans Pemeliharaan Kompetensi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Rencana Surveilans</h1>
            <p class="text-sm text-gray-600 mt-1">Penerbitan program surveilans berkala untuk pemegang sertifikat (SOP 04 §4 / SOP 19).</p>
        </div>
        <a href="{{ route('admin.surveillance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali
        </a>
    </div>

    {{-- Detail Sertifikat --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6 text-sm">
        <h2 class="font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Informasi Pemegang & Sertifikat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500 block text-xs">Nama Pemegang:</span>
                <span class="font-bold text-gray-900">{{ $certificate->assessment?->asesi?->name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Nomor Sertifikat:</span>
                <span class="font-mono font-medium text-primary">{{ $certificate->certificate_number }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Skema Sertifikasi:</span>
                <span class="font-medium text-gray-900">{{ $certificate->assessment?->scheme?->name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Masa Berlaku:</span>
                <span class="text-gray-900">{{ $certificate->valid_from?->format('d/m/Y') }} s/d {{ $certificate->valid_until?->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Form Rencana --}}
    <form action="{{ route('admin.surveillance.store-plan', $certificate->id) }}" method="POST" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-5 text-sm">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                Judul Program Surveilans <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title" required value="{{ old('title', 'Surveilans Tahun ke-1: Pemeliharaan Kompetensi ' . $certificate->assessment?->scheme?->name) }}"
                class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
            @error('title')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="surveillance_year" class="block text-sm font-medium text-gray-700 mb-1">
                    Tahun Surveilans <span class="text-red-500">*</span>
                </label>
                <select name="surveillance_year" id="surveillance_year" required class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                    <option value="1" {{ old('surveillance_year') == '1' ? 'selected' : '' }}>Tahun ke-1 (Bulan ke-12)</option>
                    <option value="2" {{ old('surveillance_year') == '2' ? 'selected' : '' }}>Tahun ke-2 (Bulan ke-24)</option>
                    <option value="3" {{ old('surveillance_year') == '3' ? 'selected' : '' }}>Tahun ke-3 (Bulan ke-36)</option>
                </select>
                @error('surveillance_year')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Batas Waktu Pengumpulan Laporan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="due_date" id="due_date" required value="{{ old('due_date', now()->addMonths(6)->toDateString()) }}"
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                @error('due_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="required_evidence_description" class="block text-sm font-medium text-gray-700 mb-1">
                Uraian Bukti yang Dipersyaratkan (FR.SPS.01) <span class="text-red-500">*</span>
            </label>
            <textarea name="required_evidence_description" id="required_evidence_description" rows="4" required
                class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary"
                placeholder="Contoh: Logbook pekerjaan perbaikan kendaraan minimal 10 kali dalam 6 bulan terakhir, surat keterangan bekerja aktif dari bengkel, dan sertifikat pelatihan penyegaran bila ada.">{{ old('required_evidence_description', "1. Logbook pekerjaan praktik pemeliharaan/perbaikan kendaraan minimal 6 bulan terakhir.\n2. Surat keterangan bekerja atau menjalankan praktik profesi aktif dari tempat kerja/bengkel.\n3. Portofolio dokumentasi foto kegiatan kerja terkini.") }}</textarea>
            @error('required_evidence_description')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.surveillance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                Terbitkan rencana surveilans
            </button>
        </div>
    </form>
</div>
@endsection
