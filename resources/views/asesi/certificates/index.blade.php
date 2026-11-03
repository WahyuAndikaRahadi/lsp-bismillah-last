@extends('layouts.app')

@section('title', 'Sertifikat Kompetensi Saya')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sertifikat Kompetensi Saya</h1>
            <p class="text-sm text-gray-600 mt-1">Daftar sertifikat kompetensi resmi BNSP yang Anda miliki di seluruh skema sertifikasi.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($assessments as $item)
            @php
                $activeCert = $item->activeCertificate();
                $isKompeten = $item->pleno_decision === 'KOMPETEN';
            @endphp

            @if ($isKompeten || $activeCert)
                <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                                    {{ $item->scheme?->code }}
                                </span>
                                @if ($activeCert)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-green-100 text-green-800">
                                        Sertifikat Tersedia
                                    </span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-800">
                                        Menunggu unggah sertifikat dari LSP
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $item->scheme?->name }}</h2>

                            @if ($activeCert)
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600">
                                    <div>
                                        <span class="text-gray-500">Nomor Sertifikat:</span>
                                        <span class="font-mono font-medium text-gray-900">{{ $activeCert->certificate_number }}</span>
                                    </div>
                                    @if ($activeCert->blanko_number)
                                        <div>
                                            <span class="text-gray-500">Nomor Blanko:</span>
                                            <span class="font-mono text-gray-900">{{ $activeCert->blanko_number }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-gray-500">Masa Berlaku:</span>
                                        <span>{{ $activeCert->valid_from?->format('d/m/Y') }} s/d {{ $activeCert->valid_until?->format('d/m/Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Status Tanda Terima (FR-10.2):</span>
                                        @if ($activeCert->isAcknowledged())
                                            <span class="text-green-700 font-medium">✓ Diterima</span>
                                        @else
                                            <span class="text-amber-700 font-medium">Belum konfirmasi penerimaan</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="mt-2 text-xs text-amber-800 bg-amber-50 p-2.5 rounded border border-amber-200">
                                    Keputusan pleno telah menetapkan Anda <strong>KOMPETEN</strong>. Berkas sertifikat fisik sedang dalam proses pencetakan dan pengunggahan oleh manajemen LSP.
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            @if ($activeCert)
                                <a href="{{ route('asesi.certificates.show', $activeCert->id) }}" class="px-3 py-2 border border-gray-300 text-gray-700 text-xs font-medium rounded text-center hover:bg-gray-50">
                                    Tanda terima (FR-10.2)
                                </a>
                                <a href="{{ route('asesi.certificates.download', $activeCert->id) }}" class="px-4 py-2 bg-primary text-white text-xs font-medium rounded text-center hover:bg-primary/90">
                                    Unduh sertifikat
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500 text-sm">
                Anda belum memiliki riwayat sertifikat kompetensi.
            </div>
        @endforelse
    </div>
</div>
@endsection
