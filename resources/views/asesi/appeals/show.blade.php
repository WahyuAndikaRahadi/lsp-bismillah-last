@extends('layouts.app')

@section('title', 'Status Pengajuan Banding Asesmen')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Status Permohonan Banding (FR.AK.04)</h1>
            <p class="text-sm text-gray-600 mt-1">Informasi resmi penanganan banding atas rekomendasi asesmen kompetensi.</p>
        </div>
        <a href="{{ route('asesi.jadwal.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Beranda
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Rincian Pengajuan Banding</h2>
            <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-semibold
                {{ $appeal->status === 'ACCEPTED' ? 'bg-green-100 text-green-800' : ($appeal->status === 'REJECTED' ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800') }}">
                Status: {{ $appeal->status }}
            </span>
        </div>

        <div class="p-6 space-y-4 text-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-xs text-gray-500 block">Skema Sertifikasi:</span>
                    <span class="font-medium text-gray-900">{{ $appeal->assessment?->scheme?->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Tanggal Pengajuan:</span>
                    <span class="text-gray-900">{{ $appeal->appeal_date?->format('d/m/Y') }}</span>
                </div>
            </div>

            <div>
                <span class="text-xs text-gray-500 block mb-1">Alasan Banding yang Diajukan:</span>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded text-xs text-gray-800 whitespace-pre-wrap">{{ $appeal->appeal_reasons }}</div>
            </div>

            {{-- Hasil Investigasi Resmi --}}
            <div class="pt-4 border-t border-gray-200 space-y-3">
                <h3 class="font-semibold text-gray-900 text-sm">Keputusan & Temuan Investigasi Independen</h3>

                @if ($appeal->latestInvestigation)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-xs space-y-2 text-blue-900">
                        <div class="flex items-center justify-between font-semibold">
                            <span>Hasil Rekomendasi: {{ $appeal->latestInvestigation->recommendation }}</span>
                            <span>{{ $appeal->latestInvestigation->investigation_date?->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-blue-700 block font-medium">Temuan Faktual:</span>
                            <p class="whitespace-pre-wrap">{{ $appeal->latestInvestigation->factual_findings }}</p>
                        </div>
                        @if ($appeal->latestInvestigation->recommendation_notes)
                            <div>
                                <span class="text-blue-700 block font-medium">Tindak Lanjut:</span>
                                <p>{{ $appeal->latestInvestigation->recommendation_notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                        Permohonan banding Anda telah tercatat dan saat ini sedang ditugaskan kepada investigator banding independen LSP ProIndo.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
