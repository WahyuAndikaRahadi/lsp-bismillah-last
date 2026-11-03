@extends('layouts.app')

@section('title', 'Manajemen Sertifikat Kompetensi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Sertifikat Kompetensi</h1>
            <p class="text-sm text-gray-600 mt-1">Pengelolaan berkas fisik sertifikat resmi LSP dan pemantauan tanda terima (FR-10.2).</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Daftar Asesi Siap Unggah Sertifikat --}}
    @if ($eligibleAssessments->isNotEmpty())
        <div class="mb-8 bg-amber-50 border border-amber-200 rounded-lg p-5">
            <h2 class="text-base font-semibold text-amber-900 mb-2">Asesi Lulus Menunggu Unggah Sertifikat ({{ $eligibleAssessments->count() }})</h2>
            <p class="text-xs text-amber-700 mb-4">Asesi berikut telah ditetapkan KOMPETEN oleh Komite Teknis dan siap diterbitkan sertifikatnya.</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-amber-200 text-xs">
                    <thead>
                        <tr class="text-left font-medium text-amber-800">
                            <th class="py-2 px-3">Nama Asesi</th>
                            <th class="py-2 px-3">NIK</th>
                            <th class="py-2 px-3">Skema Sertifikasi</th>
                            <th class="py-2 px-3">Keputusan Pleno</th>
                            <th class="py-2 px-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-200">
                        @foreach ($eligibleAssessments as $asesiItem)
                            <tr>
                                <td class="py-2 px-3 font-semibold text-gray-900">{{ $asesiItem->asesi?->name }}</td>
                                <td class="py-2 px-3 text-gray-700">{{ $asesiItem->asesi?->profile?->nik ?? '-' }}</td>
                                <td class="py-2 px-3 text-gray-700">{{ $asesiItem->scheme?->name }}</td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">KOMPETEN</span>
                                </td>
                                <td class="py-2 px-3 text-right">
                                    <a href="{{ route('admin.assessment.certificate.create', $asesiItem->id) }}" class="inline-flex items-center px-3 py-1 bg-primary text-white text-xs font-medium rounded hover:bg-primary/90 focus:outline-none">
                                        Unggah sertifikat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Tabel Riwayat Sertifikat --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Arsip Sertifikat Terbit</h2>
            <span class="text-xs text-gray-500">Total: {{ $certificates->total() }} sertifikat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Nomor Sertifikat</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesi</th>
                        <th class="py-3 px-4 text-left font-semibold">Skema</th>
                        <th class="py-3 px-4 text-left font-semibold">Masa Berlaku</th>
                        <th class="py-3 px-4 text-left font-semibold">Status / Rev</th>
                        <th class="py-3 px-4 text-left font-semibold">FR-10.2 Tanda Terima</th>
                        <th class="py-3 px-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($certificates as $cert)
                        <tr class="hover:bg-gray-50 {{ $cert->status !== 'ACTIVE' ? 'opacity-60 bg-gray-50' : '' }}">
                            <td class="py-3 px-4">
                                <div class="font-mono font-medium text-gray-900">{{ $cert->certificate_number }}</div>
                                @if ($cert->blanko_number)
                                    <div class="text-xs text-gray-500 font-mono">Blanko: {{ $cert->blanko_number }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $cert->assessment?->asesi?->name }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $cert->assessment?->asesi?->profile?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $cert->assessment?->scheme?->name }}
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                <div>Terbit: {{ $cert->issue_date?->format('d/m/Y') ?? '-' }}</div>
                                <div>s/d: {{ $cert->valid_until?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if ($cert->status === 'ACTIVE')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">AKTIF (Rev {{ $cert->revision }})</span>
                                @elseif ($cert->status === 'SUPERSEDED')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-700">TERGANTIKAN (Rev {{ $cert->revision }})</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">{{ $cert->status }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if ($cert->isAcknowledged())
                                    <span class="inline-flex items-center text-green-700 font-medium">
                                        ✓ Diterima ({{ $cert->acknowledgment()?->occurred_at?->format('d/m/Y') }})
                                    </span>
                                @else
                                    <span class="text-gray-400">Belum konfirmasi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                @if ($cert->status === 'ACTIVE')
                                    <a href="{{ route('admin.assessment.certificate.create', $cert->assessment_id) }}" class="text-xs text-blue-600 hover:underline">
                                        Perbarui
                                    </a>
                                    <a href="{{ route('admin.certificates.download', $cert->id) }}" class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded hover:bg-gray-200">
                                        Unduh sertifikat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-sm text-gray-500">
                                Belum ada berkas sertifikat yang diunggah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($certificates->hasPages())
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
