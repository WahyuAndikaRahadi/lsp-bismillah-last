@extends('layouts.app')

@section('title', 'Manajemen Surveilans Pemegang Sertifikat (SOP 04 §4 / SOP 19)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surveilans Pemeliharaan Kompetensi (SOP 19)</h1>
            <p class="text-sm text-gray-600 mt-1">Pengelolaan rencana surveilans berkala pemegang sertifikat, evaluasi bukti (FR.SPS.01), dan penetapan keputusan resmi (FR.SK.01).</p>
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

    {{-- Sertifikat Aktif Menunggu Rencana Surveilans --}}
    @if ($activeCertificates->isNotEmpty())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-8">
            <h2 class="text-base font-bold text-blue-900 mb-2">Sertifikat Aktif Siap Dibuatkan Rencana Surveilans ({{ $activeCertificates->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-200 text-xs">
                    <thead>
                        <tr class="text-left font-semibold text-blue-800">
                            <th class="py-2 px-3">Nomor Sertifikat</th>
                            <th class="py-2 px-3">Pemegang</th>
                            <th class="py-2 px-3">Skema</th>
                            <th class="py-2 px-3">Masa Berlaku</th>
                            <th class="py-2 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-200">
                        @foreach ($activeCertificates as $c)
                            <tr>
                                <td class="py-2 px-3 font-mono font-medium text-gray-900">{{ $c->certificate_number }}</td>
                                <td class="py-2 px-3 font-semibold text-gray-900">{{ $c->assessment?->asesi?->name }}</td>
                                <td class="py-2 px-3 text-gray-700">{{ $c->assessment?->scheme?->name }}</td>
                                <td class="py-2 px-3 text-gray-600">{{ $c->valid_from?->format('d/m/Y') }} s/d {{ $c->valid_until?->format('d/m/Y') }}</td>
                                <td class="py-2 px-3 text-right">
                                    <a href="{{ route('admin.surveillance.create-plan', $c->id) }}" class="inline-flex items-center px-3 py-1 bg-primary text-white font-medium rounded hover:bg-primary/90">
                                        Buat rencana surveilans
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Daftar Rencana Surveilans Berjalan --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Rencana & Laporan Surveilans Berkala</h2>
            <span class="text-xs text-gray-500">Total: {{ $plans->total() }} rencana</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Program Surveilans</th>
                        <th class="py-3 px-4 text-left font-semibold">Pemegang Sertifikat</th>
                        <th class="py-3 px-4 text-left font-semibold">Batas Waktu</th>
                        <th class="py-3 px-4 text-left font-semibold">Status Rencana</th>
                        <th class="py-3 px-4 text-left font-semibold">Laporan Masuk</th>
                        <th class="py-3 px-4 text-right font-semibold">Evaluasi & Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($plans as $p)
                        @php $latestReport = $p->latestReport; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900">{{ $p->title }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $p->certificate?->certificate_number }} (Tahun ke-{{ $p->surveillance_year }})</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $p->certificate?->assessment?->asesi?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $p->certificate?->assessment?->scheme?->name }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-700">
                                {{ $p->due_date?->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $p->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($p->status === 'REPORTED' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if ($latestReport)
                                    <div>
                                        <span class="text-green-700 font-medium">✓ Masuk: {{ $latestReport->submitted_at?->format('d/m/Y') }}</span>
                                        @if ($latestReport->decision)
                                            <div class="mt-1 font-bold text-primary">{{ $latestReport->decision }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">Menunggu asesi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if ($latestReport && ! $latestReport->decision)
                                    <details class="inline-block text-left">
                                        <summary class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded cursor-pointer hover:bg-green-700">
                                            Evaluasi (FR.SK.01)
                                        </summary>
                                        <div class="absolute right-10 mt-2 w-96 bg-white border border-gray-200 rounded-lg p-5 shadow-2xl z-20">
                                            <form action="{{ route('admin.surveillance.evaluate-report', $latestReport->id) }}" method="POST" class="space-y-3 text-xs">
                                                @csrf
                                                <h3 class="font-bold text-gray-900 text-sm">Evaluasi Laporan Surveilans</h3>
                                                <div class="p-2 bg-gray-50 rounded border text-gray-700">
                                                    <strong>Kegiatan asesi:</strong>
                                                    <p class="whitespace-pre-wrap mt-1">{{ $latestReport->activity_summary }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 font-medium mb-1">Catatan Verifikasi Temuan (FR.SPS.01):</label>
                                                    <textarea name="evaluator_notes" required rows="3" class="w-full px-2 py-1.5 border rounded border-gray-300" placeholder="Bukti logbook dan surat keterangan bekerja telah diverifikasi sah..."></textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 font-medium mb-1">Keputusan Resmi (FR.SK.01):</label>
                                                    <select name="decision" required class="w-full px-2 py-1.5 border rounded border-gray-300">
                                                        <option value="COMPLIANT">Kompetensi Terpelihara (COMPLIANT)</option>
                                                        <option value="ACTION_REQUIRED">Perlu Tindakan Perbaikan (ACTION_REQUIRED)</option>
                                                        <option value="SUSPENDED">Sertifikat Dibekukan (SUSPENDED)</option>
                                                    </select>
                                                </div>
                                                <div class="flex justify-end space-x-2 pt-2">
                                                    <button type="submit" class="px-4 py-1.5 bg-primary text-white rounded font-medium hover:bg-primary/90">
                                                        Sahkan Keputusan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-500">
                                Belum ada rencana surveilans berkala yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($plans->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
