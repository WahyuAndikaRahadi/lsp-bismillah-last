@extends('layouts.app')

@section('title', 'Permohonan Sertifikasi Ulang (Resertifikasi)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permohonan Resertifikasi (SOP 04 §5)</h1>
            <p class="text-sm text-gray-600 mt-1">Daftar permohonan asesmen sertifikasi ulang yang tertaut ke sertifikat sebelumnya pada skema terkait.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Daftar Asesmen Resertifikasi Tertaut</h2>
            <span class="text-xs text-gray-500">Total: {{ $recertifications->total() }} permohonan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Asesi Pemohon</th>
                        <th class="py-3 px-4 text-left font-semibold">Skema Sertifikasi</th>
                        <th class="py-3 px-4 text-left font-semibold">Sertifikat Sebelumnya (Prior Cert)</th>
                        <th class="py-3 px-4 text-left font-semibold">Jadwal Asesmen</th>
                        <th class="py-3 px-4 text-left font-semibold">Status APL / Asesmen</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesor Ditugaskan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($recertifications as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $item->asesi?->name }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $item->asesi?->profile?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $item->scheme?->name }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-xs font-semibold text-primary">
                                    {{ $item->priorCertificate?->certificate_number }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Berlaku s/d: {{ $item->priorCertificate?->valid_until?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                <div class="font-medium">{{ $item->schedule?->name }}</div>
                                <div class="text-gray-500">{{ $item->schedule?->date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $item->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $item->status }} (APL: {{ $item->apl_status }})
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $item->assessor?->name ?? 'Belum diplot' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-500">
                                Belum ada permohonan resertifikasi yang diajukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recertifications->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $recertifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
