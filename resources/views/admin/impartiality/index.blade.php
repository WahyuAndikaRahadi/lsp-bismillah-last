@extends('layouts.app')

@section('title', 'Monitoring Ketidakberpihakan & Pergantian Asesor (SOP 01)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Ketidakberpihakan & Pergantian Asesor (SOP 01)</h1>
            <p class="text-sm text-gray-600 mt-1">Pemantauan komitmen integritas, resolusi benturan kepentingan, dan pergantian asesor (FR-SOP-01.7).</p>
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

    {{-- Daftar Benturan Kepentingan Terbuka (Memerlukan Pergantian Asesor) --}}
    <div class="bg-red-50 border border-red-200 rounded-lg p-5 mb-8">
        <h2 class="text-base font-bold text-red-900 mb-2">Deklarasi Benturan Kepentingan Terbuka ({{ $openConflicts->count() }})</h2>
        <p class="text-xs text-red-700 mb-4">Asesor di bawah ini telah mendeklarasikan benturan kepentingan terhadap asesi tertentu dan diblokir dari melakukan asesmen sampai pergantian resmi disahkan.</p>

        @if ($openConflicts->isNotEmpty())
            <div class="space-y-4">
                @foreach ($openConflicts as $conflict)
                    <div class="bg-white border border-red-200 rounded-lg p-4 shadow-xs text-sm">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <div>
                                <span class="text-xs text-gray-500 block">Asesor Berkonflik:</span>
                                <span class="font-bold text-red-700">{{ $conflict->declarant?->name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Asesi Terkait:</span>
                                <span class="font-medium text-gray-900">{{ $conflict->relatedAsesi?->name }}</span>
                                <span class="text-xs text-gray-500 block">{{ $conflict->assessment?->scheme?->name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Uraian Benturan:</span>
                                <span class="text-xs text-gray-700 italic">{{ $conflict->conflict_nature ?: 'Afiliasi langsung' }}</span>
                            </div>
                            <div class="text-right">
                                <details class="inline-block text-left">
                                    <summary class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded cursor-pointer hover:bg-red-700">
                                        Ganti Asesor (FR-SOP-01.7)
                                    </summary>
                                    <div class="absolute right-10 mt-2 w-80 bg-white border border-gray-200 rounded-lg p-4 shadow-xl z-20">
                                        <form action="{{ route('admin.assessments.replace-assessor', $conflict->assessment_id) }}" method="POST" class="space-y-3 text-xs">
                                            @csrf
                                            <h3 class="font-bold text-gray-900">Pergantian Asesor Resmi</h3>
                                            <div>
                                                <label class="block text-gray-700 font-medium mb-1">Pilih Asesor Pengganti:</label>
                                                <select name="replacement_assessor_id" required class="w-full px-2 py-1.5 border rounded border-gray-300">
                                                    <option value="">-- Pilih Asesor --</option>
                                                    @foreach (\App\Models\User::where('role', 'asesor')->where('id', '!=', $conflict->declarant_id)->get() as $u)
                                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 font-medium mb-1">Dasar/Alasan Pergantian:</label>
                                                <input type="text" name="reason" required value="Benturan kepentingan ({{ $conflict->conflict_nature }})"
                                                    class="w-full px-2 py-1.5 border rounded border-gray-300">
                                            </div>
                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="submit" class="px-3 py-1 bg-primary text-white rounded font-medium hover:bg-primary/90">
                                                    Sahkan Pergantian
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-500 italic">Tidak ada benturan kepentingan terbuka saat ini.</p>
        @endif
    </div>

    {{-- Log Riwayat Pergantian Asesor (FR-SOP-01.7) --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm mb-8">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Pergantian Asesor (FR-SOP-01.7)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesi & Skema</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesor Semula</th>
                        <th class="py-3 px-4 text-left font-semibold">Asesor Pengganti</th>
                        <th class="py-3 px-4 text-left font-semibold">Alasan Pergantian</th>
                        <th class="py-3 px-4 text-left font-semibold">Disahkan Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($replacementEvents as $ev)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-xs text-gray-600">{{ $ev->approved_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $ev->assessment?->asesi?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $ev->assessment?->scheme?->name }}</div>
                            </td>
                            <td class="py-3 px-4 text-red-600 font-medium text-xs">{{ $ev->previousAssessor?->name }}</td>
                            <td class="py-3 px-4 text-green-700 font-medium text-xs">{{ $ev->replacementAssessor?->name }}</td>
                            <td class="py-3 px-4 text-xs text-gray-700">{{ $ev->reason }}</td>
                            <td class="py-3 px-4 text-xs text-gray-500">{{ $ev->approver?->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 text-sm">
                                Belum ada catatan peristiwa pergantian asesor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
