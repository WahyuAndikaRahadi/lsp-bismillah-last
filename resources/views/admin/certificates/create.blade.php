@extends('layouts.app')

@section('title', 'Unggah Sertifikat Kompetensi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Unggah Sertifikat Kompetensi</h1>
            <p class="text-sm text-gray-600 mt-1">Penerbitan dokumen fisik sertifikat resmi LSP ProIndo.</p>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Detail Asesi & Keputusan Pleno --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6 text-sm">
        <h2 class="font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Informasi Asesi & Pleno</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500 block text-xs">Nama Asesi:</span>
                <span class="font-medium text-gray-900">{{ $assessment->asesi?->name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">NIK:</span>
                <span class="font-mono text-gray-900">{{ $assessment->asesi?->profile?->nik ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Skema Sertifikasi:</span>
                <span class="font-medium text-gray-900">{{ $assessment->scheme?->name }} ({{ $assessment->scheme?->code }})</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Keputusan Pleno:</span>
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                    {{ $latestDecision->decision }} (Disahkan: {{ $latestDecision->decided_at?->format('d/m/Y') }})
                </span>
            </div>
        </div>

        @if ($activeCert)
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                <strong>Catatan Penggantian:</strong> Asesi telah memiliki Sertifikat Aktif nomor <strong>{{ $activeCert->certificate_number }}</strong> (Revisi {{ $activeCert->revision }}). Pengunggahan ini akan menandai sertifikat lama sebagai SUPERSEDED dan menerbitkan Revisi {{ $activeCert->revision + 1 }}.
            </div>
        @endif
    </div>

    {{-- Form Unggah Sertifikat --}}
    <form action="{{ route('admin.assessment.certificate.store', $assessment->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-5">
        @csrf

        <div>
            <label for="certificate_number" class="block text-sm font-medium text-gray-700 mb-1">
                Nomor Sertifikat <span class="text-red-500">*</span>
            </label>
            <input type="text" name="certificate_number" id="certificate_number" required value="{{ old('certificate_number') }}"
                class="w-full px-3 py-2 border rounded-md font-mono text-sm @error('certificate_number') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary"
                placeholder="misal: 62090.2113.3.0001234.2026">
            @error('certificate_number')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Nomor unik sertifikat dari LSP/BNSP. Tidak boleh duplikat.</p>
        </div>

        <div>
            <label for="blanko_number" class="block text-sm font-medium text-gray-700 mb-1">
                Nomor Seri Blanko
            </label>
            <input type="text" name="blanko_number" id="blanko_number" value="{{ old('blanko_number') }}"
                class="w-full px-3 py-2 border rounded-md font-mono text-sm @error('blanko_number') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary"
                placeholder="misal: BLK-2026-98765">
            @error('blanko_number')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Terbit <span class="text-red-500">*</span>
                </label>
                <input type="date" name="issue_date" id="issue_date" required value="{{ old('issue_date', now()->toDateString()) }}"
                    class="w-full px-3 py-2 border rounded-md text-sm @error('issue_date') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary">
                @error('issue_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-1">
                    Masa Berlaku Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" name="valid_from" id="valid_from" required value="{{ old('valid_from', now()->toDateString()) }}"
                    class="w-full px-3 py-2 border rounded-md text-sm @error('valid_from') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary">
                @error('valid_from')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">
                    Masa Berlaku Selesai <span class="text-red-500">*</span>
                </label>
                <input type="date" name="valid_until" id="valid_until" required value="{{ old('valid_until', now()->addYears(3)->toDateString()) }}"
                    class="w-full px-3 py-2 border rounded-md text-sm @error('valid_until') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary">
                @error('valid_until')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="certificate_file" class="block text-sm font-medium text-gray-700 mb-1">
                Berkas Fisik Sertifikat (PDF, JPG, PNG) <span class="text-red-500">*</span>
            </label>
            <input type="file" name="certificate_file" id="certificate_file" required accept=".pdf,.jpg,.jpeg,.png"
                class="w-full px-3 py-2 border rounded-md text-sm @error('certificate_file') border-red-500 @else border-gray-300 @enderror focus:ring-primary focus:border-primary">
            @error('certificate_file')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Maksimum 10 MB. Berkas akan disimpan di media penyimpanan privat aman.</p>
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                Kembali
            </a>
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                Unggah sertifikat
            </button>
        </div>
    </form>
</div>
@endsection
