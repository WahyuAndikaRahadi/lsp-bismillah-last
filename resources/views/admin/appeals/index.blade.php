@extends('layouts.app')

@section('title', 'Manajemen Banding Asesmen (SOP 07)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Banding Asesmen (SOP 07)</h1>
            <p class="text-sm text-gray-600 mt-1">Pengelolaan berkas permohonan banding (FR-SOP-07.1) dan investigasi independen (FR-SOP-07.2).</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Status --}}
    <div class="mb-6 flex items-center gap-2 text-xs">
        <a href="{{ route('admin.appeals.index') }}" class="px-3 py-1.5 rounded-md font-medium {{ empty($status) ? 'bg-primary text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Semua ({{ $appeals->total() }})
        </a>
        <a href="{{ route('admin.appeals.index', ['status' => 'SUBMITTED']) }}" class="px-3 py-1.5 rounded-md font-medium {{ $status === 'SUBMITTED' ? 'bg-primary text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Intake Masuk
        </a>
        <a href="{{ route('admin.appeals.index', ['status' => 'ACCEPTED']) }}" class="px-3 py-1.5 rounded-md font-medium {{ $status === 'ACCEPTED' ? 'bg-primary text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Diterima / Asesmen Ulang
        </a>
        <a href="{{ route('admin.appeals.index', ['status' => 'REJECTED']) }}" class="px-3 py-1.5 rounded-md font-medium {{ $status === 'REJECTED' ? 'bg-primary text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Ditolak / Keputusan Tetap
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Tanggal & ID</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesi Pemohon</th>
                        <th class="py-3 px-4 text-left font-semibold">Skema Sertifikasi</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesor Terkait</th>
                        <th class="py-3 px-4 text-left font-semibold">Status Banding</th>
                        <th class="py-3 px-4 text-left font-semibold">Investigator</th>
                        <th class="py-3 px-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($appeals as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $item->appeal_date?->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500 font-mono">BND-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $item->assessment?->asesi?->name }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $item->assessment?->asesi?->profile?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $item->assessment?->scheme?->name }}
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $item->assessment?->assessor?->name ?? 'Belum ditentukan' }}
                            </td>
                            <td class="py-3 px-4">
                                @if ($item->status === 'SUBMITTED')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">INTAKE MASUK</span>
                                @elseif ($item->status === 'UNDER_REVIEW')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">DALAM INVESTIGASI</span>
                                @elseif ($item->status === 'ACCEPTED')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">DITERIMA</span>
                                @elseif ($item->status === 'REJECTED')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-800">DITOLAK</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $item->latestInvestigation?->investigator?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.appeals.show', $item->id) }}" class="inline-flex items-center px-3 py-1 bg-primary text-white text-xs font-medium rounded hover:bg-primary/90">
                                    Kelola investigasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-sm text-gray-500">
                                Belum ada berkas pengajuan banding asesmen yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appeals->hasPages())
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50">
                {{ $appeals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
